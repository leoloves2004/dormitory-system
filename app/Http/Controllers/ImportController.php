<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ImportController extends Controller
{
    public function students(Request $request): RedirectResponse
    {
        $rows = $this->readRows($request);
        foreach ($rows as $row) {
            if (empty($row['email']) || empty($row['student_number'])) {
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $row['email']],
                ['name' => $row['name'] ?? $row['email'], 'password' => Hash::make('password'), 'role' => 'student']
            );

            Student::updateOrCreate(
                ['student_number' => $row['student_number']],
                [
                    'user_id' => $user->id,
                    'course' => $row['course'] ?? null,
                    'year_level' => $row['year_level'] ?? null,
                    'phone' => $row['phone'] ?? null,
                ]
            );
        }

        return back()->with('status', 'Students imported.');
    }

    public function payments(Request $request): RedirectResponse
    {
        $rows = $this->readRows($request);
        foreach ($rows as $row) {
            $student = Student::where('student_number', $row['student_number'] ?? null)->first();
            if (! $student || empty($row['amount'])) {
                continue;
            }

            Payment::create([
                'student_id' => $student->id,
                'amount' => $row['amount'],
                'payment_date' => $row['payment_date'] ?? now()->toDateString(),
                'due_date' => $row['due_date'] ?? null,
                'method' => $row['method'] ?? 'cash',
                'reference_number' => $row['reference_number'] ?? null,
                'status' => $row['status'] ?? 'paid',
                'notes' => $row['notes'] ?? null,
            ]);
        }

        return back()->with('status', 'Payments imported.');
    }

    private function readRows(Request $request): array
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt,xlsx']]);
        $file = $request->file('file');
        $path = $file->getRealPath();

        if ($file->getClientOriginalExtension() === 'xlsx') {
            return $this->readXlsx($path);
        }

        $handle = fopen($path, 'r');
        $headers = array_map('trim', fgetcsv($handle) ?: []);
        $rows = [];

        while (($line = fgetcsv($handle)) !== false) {
            $rows[] = array_combine($headers, array_pad($line, count($headers), null));
        }

        fclose($handle);

        return $rows;
    }

    private function readXlsx(string $path): array
    {
        if (! class_exists(\ZipArchive::class)) {
            return [];
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        $shared = [];
        if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            foreach (simplexml_load_string($xml)->si as $item) {
                $shared[] = (string) ($item->t ?? $item->r->t ?? '');
            }
        }

        $sheet = simplexml_load_string($zip->getFromName('xl/worksheets/sheet1.xml') ?: '<worksheet/>');
        $zip->close();
        $rows = [];

        foreach ($sheet->sheetData->row ?? [] as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $value = (string) $cell->v;
                $values[] = ((string) $cell['t'] === 's') ? ($shared[(int) $value] ?? '') : $value;
            }
            $rows[] = $values;
        }

        $headers = array_map('trim', array_shift($rows) ?? []);

        return array_map(fn ($line) => array_combine($headers, array_pad($line, count($headers), null)), $rows);
    }
}
