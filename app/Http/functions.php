<?php

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

function get_sidebar_links()
{
    $links = array();
    $links[] = array(
        'title' => 'Dashboard',
        'route' => '/dashboard',
        'icon' => 'zmdi zmdi-home',
    );
    $links[] = array(
        'title' => 'Department',
        'icon' => 'zmdi zmdi-folder',
        'children' => array(
            array(
                'title' => 'Departments',
                'route' => '/department-list',
            ),
        ),
    );
    $links[] = array(
        'title' => 'Category',
        'icon' => 'zmdi zmdi-folder',
        'children' => array(
            array(
                'title' => 'Categories',
                'route' => '/category-list',
            ),
        ),
    );
    $links[] = array(
        'title' => 'Subject',
        'icon' => 'zmdi zmdi-view-week',
        'children' => array(
            array(
                'title' => 'Subjects',
                'route' => '/subject-list',
            ),
        ),
    );
    return $links;
}

function extract_search_field($search_data)
{
    $array = '';
    if ($search_data) {
        parse_str($search_data, $array);
    }
    return $array;
}
function export_report($spreadsheet, $fileName = 'download.xlsx')
{
    ob_start();
    IOFactory::createWriter($spreadsheet, 'Xlsx')->save('php://output');
    $pdfData = ob_get_contents();
    ob_end_clean();
    return array(
        'op' => 'ok',
        'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($pdfData),
        'fileName' => $fileName,
    );
}

function export_file_generate($export_data_structure, $export_data, $extra)
{
    if (!empty($export_data)) {
        $rowIndex = 0;
        $colIndex = 0;
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($extra['sheetTitle']);

        $styleArray = [
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => "ffffff"
                ]
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'right' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
                'left' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'rotation' => 90,
                'startColor' => [
                    'rgb' => '4659d4',
                ],
                'endColor' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
        ];

        $styleUrlArray = [
            'font' => [
                'underline' => true,
                'color' => [
                    'rgb' => "1a58c7"
                ]
            ],
        ];
        $headerCells = array();
        foreach ($export_data_structure as $key => $value) {
            foreach ($value as $key1 => $value1) {
                $header[] = $value1['title'];
                $sheet->setCellValue(chr(65 + $key) . '1', $value1['title']);
                $sheet->getStyle(chr(65 + $key) . '1')->applyFromArray($styleArray);
                $sheet->getColumnDimension(chr(65 + $key))->setAutoSize(true);
            }
            $headerCells[] = chr(65 + $key) . '1';
        }
        $rowIndex++;

        $sheet->insertNewRowBefore(1, 2);
        $rowIndex += 2;
        $sheet->mergeCells($headerCells[0] . ":" . $headerCells[count($headerCells) - 1]);
        $sheet->setCellValue("A1", "Report {$extra['headerDate']}");
        $sheet->getStyle("A1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A1")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $sheet->getRowDimension(1)->setRowHeight(30);
        $cellvalue = array();
        foreach ($export_data as $rowcount => $value) {
            $rowcount += $rowIndex;
            foreach ($export_data_structure as $colcount => $value1) {
                $cellHeight = 15;
                $value1 = array_values($value1)[0];
                if (is_array($value[$value1['name']])) {
                    $cellHeight = count($value[$value1['name']]) * 15;
                    $value[$value1['name']] = implode("\n", $value[$value1['name']]);
                }

                if (isset($value1['datatype'])) {
                    switch ($value1['datatype']) {
                        case 'email':
                            $sheet->getCell(chr(65 + $colcount) . ($rowcount + 1))->getHyperlink()->setUrl("mailto:" . $value[$value1['name']]);
                            $sheet->getStyle(chr(65 + $colcount) . ($rowcount + 1))->applyFromArray($styleUrlArray);
                            break;

                        case 'date':
                            $sheet->getStyle(chr(65 + $colcount) . ($rowcount + 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            $value[$value1['name']] = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($value[$value1['name']]);
                            break;

                        case 'currency':
                            $sheet->getStyle(chr(65 + $colcount) . ($rowcount + 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);
                            break;

                        default:
                            break;
                    }
                }
                if (isset($value1['total'])) {
                    if ($value1['total'] == TRUE) {
                        $cellvalue[chr(65 + $colcount)][] = chr(65 + $colcount) . ($rowcount + 1);
                        $cellvalue[chr(65 + $colcount)]['lastcell'] = chr(65 + $colcount) . ($rowcount + 1 + 2);
                    }
                }
                $sheet->setCellValue(chr(65 + $colcount) . ($rowcount + 1), $value[$value1['name']]);

                $sheet->getStyle(chr(65 + $colcount) . ($rowcount + 1))->getAlignment()->setWrapText(true);
                $sheet->getRowDimension($rowcount + 1)->setRowHeight($cellHeight);
                $colIndextemp = $colcount + 1;
            }
            $colIndex += $colIndextemp;
            $rowIndextemp = $rowcount;
        }
        $rowIndex += $rowIndextemp;

        foreach ($cellvalue as $key => $value) {
            $sheet->setCellValue($value['lastcell'], "=SUM({$cellvalue[$key][0]}:" . array_pop($cellvalue[$key]) . ")");
        }
        foreach ($headerCells as $key => $value) {
            $sheet->getStyle(chr(65 + $key) . $rowIndex)->applyFromArray($styleArray);
        }
        return $spreadsheet;
    }
}

function extract_export_table(Request $req) {
    $params = $req->input('tableParams');
    $returnArr = array();
    foreach ($params as $key => $value) {
        $returnArr[$key] = $value;
    }
    return $returnArr;
}
?>