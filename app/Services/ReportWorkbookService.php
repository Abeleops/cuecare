<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportWorkbookService
{
    public function create(string $type, string $details, array $headings, array $rows): string
    {
        $workbook = new Spreadsheet;
        $sheet = $workbook->getActiveSheet();
        $sheet->setTitle($type === 'sales' ? 'Sales Report' : 'Inventory Report');
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', 'GG BILLIARDS & AUTOCARE — '.strtoupper($type).' REPORT');
        $sheet->mergeCells('A2:E2');
        $sheet->setCellValueExplicit('A2', $details, DataType::TYPE_STRING);
        $sheet->mergeCells('A3:E3');
        $sheet->setCellValue('A3', 'Generated '.now()->format('M d, Y h:i A'));
        $sheet->getStyle('A1:E1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '171A21']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getStyle('A2:A3')->getFont()->getColor()->setRGB('5B6470');

        foreach ($headings as $column => $heading) {
            $address = Coordinate::stringFromColumnIndex($column + 1).'5';
            $sheet->setCellValueExplicit($address, $heading, DataType::TYPE_STRING);
        }
        $sheet->getStyle('A5:E5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '171A21']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFB800']],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(24);

        foreach ($rows as $index => $row) {
            foreach ($row as $column => $value) {
                $address = Coordinate::stringFromColumnIndex($column + 1).($index + 6);
                // Store text explicitly so names cannot become spreadsheet formulas
                if (is_int($value) || is_float($value)) {
                    $sheet->setCellValueExplicit($address, $value, DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValueExplicit($address, (string) ($value ?? ''), DataType::TYPE_STRING);
                }
            }
        }

        $lastRow = max(6, count($rows) + 5);
        $moneyColumn = $type === 'sales' ? 'D' : 'C';
        $sheet->getStyle($moneyColumn.'6:'.$moneyColumn.$lastRow)->getNumberFormat()->setFormatCode('"₱"#,##0.00');
        $sheet->freezePane('A6');
        $sheet->setAutoFilter('A5:E'.max(5, count($rows) + 5));
        foreach (['A' => 28, 'B' => 24, 'C' => 26, 'D' => 18, 'E' => 24] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        ob_start();
        try {
            (new Xlsx($workbook))->save('php://output');
            $contents = ob_get_contents();
            if ($contents === false) {
                throw new \RuntimeException('Unable to create the Excel report');
            }

            return $contents;
        } finally {
            ob_end_clean();
            $workbook->disconnectWorksheets();
        }
    }
}
