<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Room;
use App\Models\Student;
use App\Models\Tenant;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use ZipArchive;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index');
    }

    public function export(string $type, string $format): Response
    {
        abort_unless(in_array($type, ['occupancy', 'tenants', 'payments', 'assignments'], true), 404);
        abort_unless(in_array($format, ['pdf', 'xlsx', 'csv', 'json'], true), 404);

        $rows = $this->rows($type);
        $filename = "{$type}-report.{$format}";

        if ($format === 'json') {
            return response($rows->toJson(JSON_PRETTY_PRINT), 200, [
                'Content-Type' => 'application/json',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]);
        }

        if ($format === 'pdf') {
            return response($this->pdf(str($type)->headline().' Report', $rows), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]);
        }

        if ($format === 'xlsx' && class_exists(ZipArchive::class)) {
            return response($this->xlsx(str($type)->headline().' Report', $rows), 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename={$filename}",
            ]);
        }

        $csv = $this->csv($rows->toArray());

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }

    private function rows(string $type)
    {
        return match ($type) {
            'occupancy' => Room::withCount('students')->get()->map(fn ($room) => [
                'room' => $room->room_number,
                'building' => $room->building,
                'capacity' => $room->capacity,
                'occupied' => $room->students_count,
                'available_beds' => max(0, $room->capacity - $room->students_count),
                'status' => $room->status,
            ]),
            'tenants' => Tenant::with('student.user', 'room')->get()->map(fn ($tenant) => [
                'student' => $tenant->student?->user?->name,
                'room' => $tenant->room?->room_number,
                'move_in_date' => optional($tenant->move_in_date)->toDateString(),
                'move_out_date' => optional($tenant->move_out_date)->toDateString(),
                'status' => $tenant->status,
            ]),
            'payments' => Payment::with('student.user')->get()->map(fn ($payment) => [
                'student' => $payment->student?->user?->name,
                'amount' => $payment->amount,
                'payment_date' => optional($payment->payment_date)->toDateString(),
                'method' => $payment->method,
                'status' => $payment->status,
                'reference_number' => $payment->reference_number,
            ]),
            default => Student::with('user', 'room')->get()->map(fn ($student) => [
                'student_number' => $student->student_number,
                'name' => $student->user?->name,
                'room' => $student->room?->room_number ?? 'Unassigned',
                'course' => $student->course,
                'year_level' => $student->year_level,
            ]),
        };
    }

    private function csv(array $rows): string
    {
        if ($rows === []) {
            return '';
        }

        $lines = [implode(',', array_keys($rows[0]))];
        foreach ($rows as $row) {
            $lines[] = implode(',', array_map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"', $row));
        }

        return implode("\n", $lines);
    }

    private function xlsx(string $title, Collection $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'dms-xlsx-');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="'.htmlspecialchars(substr($title, 0, 31), ENT_XML1).'" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml($rows));
        $zip->close();

        $contents = file_get_contents($path) ?: '';
        @unlink($path);

        return $contents;
    }

    private function sheetXml(Collection $rows): string
    {
        $data = $rows->toArray();
        $headings = array_keys($data[0] ?? ['message' => 'No records found']);
        $sheetRows = [array_values($headings), ...array_map('array_values', $data)];

        $xml = '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
        foreach ($sheetRows as $index => $row) {
            $xml .= '<row r="'.($index + 1).'">';
            foreach ($row as $value) {
                $xml .= '<c t="inlineStr"><is><t>'.htmlspecialchars((string) $value, ENT_XML1).'</t></is></c>';
            }
            $xml .= '</row>';
        }

        return $xml.'</sheetData></worksheet>';
    }

    private function pdf(string $title, Collection $rows): string
    {
        $lines = [$title, 'Generated '.now()->toDateTimeString(), ''];
        $data = $rows->toArray();
        $headings = array_keys($data[0] ?? []);

        if ($headings !== []) {
            $lines[] = implode(' | ', array_map(fn ($heading) => str($heading)->headline(), $headings));
        }

        foreach (array_slice($data, 0, 40) as $row) {
            $lines[] = implode(' | ', array_map(fn ($value) => (string) $value, $row));
        }

        $stream = "BT\n/F1 10 Tf\n50 790 Td\n";
        foreach ($lines as $line) {
            $stream .= '('.$this->pdfText($line).") Tj\n0 -16 Td\n";
        }
        $stream .= 'ET';

        $objects = [
            '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj',
            '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj',
            '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj',
            '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj',
            '5 0 obj << /Length '.strlen($stream).' >> stream'."\n".$stream."\nendstream endobj",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object."\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".count($offsets)."\n0000000000 65535 f \n";
        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT)." 00000 n \n";
        }

        return $pdf.'trailer << /Size '.count($offsets).' /Root 1 0 R >>'."\nstartxref\n{$xref}\n%%EOF";
    }

    private function pdfText(string $text): string
    {
        return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\(', '\)', ' ', ' '], $text);
    }
}
