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

        if ($format === 'xlsx') {
            $filename = "{$type}-report.csv";
        }

        $csv = $this->csv($rows->toArray());

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
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
                'check_in_date' => optional($tenant->check_in_date)->toDateString(),
                'check_out_date' => optional($tenant->check_out_date)->toDateString(),
                'status' => $tenant->status,
            ]),
            'payments' => Payment::with('tenant.student.user', 'tenant.room')->get()->map(fn ($payment) => [
                'student' => $payment->tenant?->student?->user?->name,
                'room' => $payment->tenant?->room?->room_number,
                'amount' => $payment->amount,
                'payment_date' => optional($payment->payment_date)->toDateString(),
                'payment_method' => $payment->payment_method,
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
        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="'.htmlspecialchars(substr($title, 0, 31), ENT_XML1).'" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/styles.xml', $this->xlsxStyles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheetXml($title, $rows));
        $zip->close();

        $contents = file_get_contents($path) ?: '';
        @unlink($path);

        return $contents;
    }

    private function xlsxStyles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="3"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="14"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font></fonts>'
            .'<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"><color rgb="FFCBD5E1"/></left><right style="thin"><color rgb="FFCBD5E1"/></right><top style="thin"><color rgb="FFCBD5E1"/></top><bottom style="thin"><color rgb="FFCBD5E1"/></bottom><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="4"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="center"/></xf><xf numFmtId="0" fontId="2" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/><xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/></cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';
    }

    private function sheetXml(string $title, Collection $rows): string
    {
        $data = $rows->toArray();
        $headings = array_keys($data[0] ?? ['message' => 'No records found']);
        $sheetRows = [
            ['report' => $title],
            ['generated' => 'Generated '.now()->toDateTimeString()],
            [],
            array_values(array_map(fn ($heading) => str($heading)->headline(), $headings)),
            ...array_map('array_values', $data === [] ? [['message' => 'No records found']] : $data),
        ];
        $lastColumn = $this->excelColumn(max(1, count($headings)));

        $xml = '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="4" topLeftCell="A5" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews><cols>';
        for ($column = 1; $column <= max(1, count($headings)); $column++) {
            $xml .= '<col min="'.$column.'" max="'.$column.'" width="20" customWidth="1"/>';
        }

        $xml .= '</cols><sheetData>';
        foreach ($sheetRows as $index => $row) {
            $rowNumber = $index + 1;
            $xml .= '<row r="'.$rowNumber.'">';
            foreach (array_values($row) as $columnIndex => $value) {
                $style = $rowNumber === 1 ? 1 : ($rowNumber === 4 ? 2 : 3);
                $cell = $this->excelColumn($columnIndex + 1).$rowNumber;
                $xml .= '<c r="'.$cell.'" s="'.$style.'" t="inlineStr"><is><t>'.htmlspecialchars((string) $value, ENT_XML1).'</t></is></c>';
            }
            $xml .= '</row>';
        }

        return $xml.'</sheetData><mergeCells count="1"><mergeCell ref="A1:'.$lastColumn.'1"/></mergeCells><autoFilter ref="A4:'.$lastColumn.max(4, count($sheetRows)).'"/></worksheet>';
    }

    private function pdf(string $title, Collection $rows): string
    {
        $data = $rows->toArray();
        $headings = array_keys($data[0] ?? ['message' => 'No records found']);
        $bodyRows = $data === [] ? [['message' => 'No records found']] : array_slice($data, 0, 24);
        $columnCount = max(1, count($headings));
        $pageWidth = 842;
        $pageHeight = 595;
        $margin = 36;
        $tableWidth = $pageWidth - ($margin * 2);
        $cellWidth = $tableWidth / $columnCount;
        $rowHeight = 22;
        $y = 500;
        $stream = "0.2 w\nBT\n/F2 18 Tf\n{$margin} 552 Td\n(".$this->pdfText($title).") Tj\nET\n";
        $stream .= "BT\n/F1 9 Tf\n{$margin} 532 Td\n(".$this->pdfText('Generated '.now()->toDateTimeString()).") Tj\nET\n";
        $stream .= "0.94 0.96 0.99 rg {$margin} ".($y - $rowHeight)." {$tableWidth} {$rowHeight} re f\n0 g\n";

        foreach ($headings as $index => $heading) {
            $x = $margin + ($index * $cellWidth);
            $stream .= "{$x} ".($y - $rowHeight)." {$cellWidth} {$rowHeight} re S\n";
            $stream .= "BT\n/F2 7 Tf\n".($x + 4).' '.($y - 14)." Td\n(".$this->pdfText(str($heading)->headline()).") Tj\nET\n";
        }

        $y -= $rowHeight;
        foreach ($bodyRows as $row) {
            $y -= $rowHeight;
            foreach (array_values($row) as $index => $value) {
                $x = $margin + ($index * $cellWidth);
                $stream .= "{$x} {$y} {$cellWidth} {$rowHeight} re S\n";
                $stream .= "BT\n/F1 7 Tf\n".($x + 4).' '.($y + 8)." Td\n(".$this->pdfText($this->limitPdfText((string) $value, $cellWidth)).") Tj\nET\n";
            }
        }

        $objects = [
            '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj',
            '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj',
            '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 '.$pageWidth.' '.$pageHeight.'] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >> endobj',
            '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj',
            '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >> endobj',
            '6 0 obj << /Length '.strlen($stream).' >> stream'."\n".$stream."\nendstream endobj",
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

    private function excelColumn(int $index): string
    {
        $name = '';

        while ($index > 0) {
            $index--;
            $name = chr(65 + ($index % 26)).$name;
            $index = intdiv($index, 26);
        }

        return $name;
    }

    private function limitPdfText(string $text, float $cellWidth): string
    {
        $limit = max(8, (int) floor($cellWidth / 4.2));

        return strlen($text) > $limit ? substr($text, 0, $limit - 3).'...' : $text;
    }
}
