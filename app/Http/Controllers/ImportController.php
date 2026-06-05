<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class ImportController extends Controller
{
    public function students(Request $request): RedirectResponse
    {
        $rows = $this->readRows($request);
        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            if (empty($row['email']) || empty($row['student_number'])) {
                $skipped++;
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $row['email']],
                ['name' => $row['name'] ?? $row['email'], 'password' => Hash::make('password'), 'role' => 'student']
            );

            $user->update([
                'name' => $row['name'] ?? $user->name,
            ]);

            Student::updateOrCreate(
                ['student_number' => $row['student_number']],
                [
                    'user_id' => $user->id,
                    'course' => $row['course'] ?? null,
                    'year_level' => $row['year_level'] ?? null,
                    'contact_number' => $row['contact_number'] ?? $row['phone'] ?? null,
                ]
            );

            $imported++;
        }

        return back()->with('status', "Students imported: {$imported}. Skipped rows: {$skipped}.");
    }

    public function payments(Request $request): RedirectResponse
    {
        $rows = $this->readRows($request);
        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $student = Student::where('student_number', $row['student_number'] ?? null)->first();
            $tenant = $student ? Tenant::where('student_id', $student->id)->where('status', 'active')->first() : null;
            if (! $tenant || empty($row['amount'])) {
                $skipped++;
                continue;
            }

            Payment::create([
                'tenant_id' => $tenant->id,
                'amount' => $this->money($row['amount']),
                'payment_date' => $this->dateValue($row['payment_date'] ?? null) ?? now()->toDateString(),
                'due_date' => $this->dateValue($row['due_date'] ?? null),
                'payment_method' => $row['payment_method'] ?? $row['method'] ?? 'cash',
                'reference_number' => $row['reference_number'] ?? null,
                'status' => $row['status'] ?? 'paid',
                'notes' => $row['notes'] ?? null,
            ]);

            $imported++;
        }

        return back()->with('status', "Payments imported: {$imported}. Skipped rows: {$skipped}.");
    }

    private function readRows(Request $request): array
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt,xlsx']]);
        $file = $request->file('file');
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'xlsx') {
            if (! class_exists(ZipArchive::class)) {
                throw ValidationException::withMessages([
                    'file' => 'XLSX imports require the ZipArchive PHP extension. Please upload a CSV file instead.',
                ]);
            }

            return $this->readXlsx($path);
        }

        $handle = fopen($path, 'r');
        if (! $handle) {
            return [];
        }

        $headers = $this->headers(fgetcsv($handle) ?: []);
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            $row = $this->combineRow($headers, $line);
            if ($row !== []) {
                $rows[] = $row;
            }
        }

        fclose($handle);

        return $rows;
    }

    private function readXlsx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $shared = [];
        if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $strings = simplexml_load_string($xml);
            if ($strings !== false) {
                foreach ($strings->si ?? [] as $item) {
                    $shared[] = $this->xlsxText($item);
                }
            }
        }

        $sheet = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml') ?: '<worksheet/>');
        $zip->close();
        if ($sheet === false) {
            return [];
        }

        $rows = [];

        foreach ($sheet->sheetData->row ?? [] as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $column = $reference ? $this->columnNumber($reference) : count($values) + 1;
                $values[$column - 1] = $this->cellValue($cell, $shared);
            }
            ksort($values);
            $rows[] = $values;
        }

        $headers = $this->headers(array_shift($rows) ?? []);
        $data = [];

        foreach ($rows as $line) {
            $row = $this->combineRow($headers, $line);
            if ($row !== []) {
                $data[] = $row;
            }
        }

        return $data;
    }

    private function headers(array $headers): array
    {
        return array_map(function ($header): string {
            $header = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header);
            $header = strtolower(trim($header));
            $header = preg_replace('/[^a-z0-9]+/', '_', $header) ?: '';

            return trim($header, '_');
        }, $headers);
    }

    private function combineRow(array $headers, array $line): array
    {
        if ($headers === []) {
            return [];
        }

        $values = [];
        foreach (array_keys($headers) as $index) {
            $values[] = $line[$index] ?? null;
        }

        $row = array_combine($headers, $values) ?: [];
        $row = array_map(fn ($value) => is_string($value) ? trim($value) : $value, $row);

        return collect($row)->filter(fn ($value) => $value !== null && $value !== '')->isEmpty() ? [] : $row;
    }

    private function cellValue(\SimpleXMLElement $cell, array $shared): string
    {
        $type = (string) $cell['t'];

        if ($type === 's') {
            return $shared[(int) $cell->v] ?? '';
        }

        if ($type === 'inlineStr') {
            return $this->xlsxText($cell->is);
        }

        return (string) ($cell->v ?? '');
    }

    private function xlsxText(\SimpleXMLElement $element): string
    {
        $text = '';
        foreach ($element->xpath('.//*[local-name()="t"]') ?: [] as $node) {
            $text .= (string) $node;
        }

        return $text;
    }

    private function columnNumber(string $reference): int
    {
        preg_match('/[A-Z]+/i', $reference, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $number = 0;

        foreach (str_split($letters) as $letter) {
            $number = ($number * 26) + (ord($letter) - 64);
        }

        return $number;
    }

    private function money(string|float|int $value): float
    {
        return (float) preg_replace('/[^0-9.\-]/', '', (string) $value);
    }

    private function dateValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return now()->startOfDay()->setDate(1899, 12, 30)->addDays((int) $value)->toDateString();
        }

        $timestamp = strtotime((string) $value);

        return $timestamp ? date('Y-m-d', $timestamp) : null;
    }
}
