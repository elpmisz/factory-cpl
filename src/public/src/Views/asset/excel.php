<?php

use App\Classes\Asset;
use App\Classes\Validation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once(__DIR__ . "/../../vendor/autoload.php");
require_once(__DIR__ . "/../../../../vendor/autoload.php");

$ASSET = new Asset();
$VALIDATION = new Validation();
$SPREADSHEET = new Spreadsheet();
$WRITER = new Xlsx($SPREADSHEET);

$param = (isset($params) ? explode("/", $params) : header("location: /error"));
$type = (!empty($param[0]) ? $param[0] : "");
$department = (!empty($param[1]) ? $param[1] : "");
$location = (!empty($param[2]) ? $param[2] : "");

$result = $ASSET->report($type, $department, $location);

$SPREADSHEET->setActiveSheetIndex(0);
$ACTIVESHEET = $SPREADSHEET->getActiveSheet();

$STYLEHEADER = [
  'font' => [
    'bold' => true,
  ],
  'alignment' => [
    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
  ],
  'borders' => [
    'allBorders' => [
      'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
    ],
  ]
];

$columns = ["CODE", "ASSET", "SERIAL NUMBER", "NAME", "TYPE", "DEPARTMENT", "LOCATION", "BRAND", "MODEL", "KW", "PURCHASE", "EXPIRE", "REMARK", "WORKER", "STATUS"];
$letter = $VALIDATION->letters((COUNT($columns) + 1));
$letters = [];
for ($i = "A"; $i !== $letter; $i++) {
  $letters[] = $i;
}
$ACTIVESHEET->getStyle("A1:" . end($letters) . "1")->applyFromArray($STYLEHEADER);

$arr_columns = array_combine($letters, $columns);
foreach ($arr_columns as $key => $value) {
  $ACTIVESHEET->setCellValue($key . "1", $value);
  $ACTIVESHEET->getColumnDimension($key)->setAutoSize(true);
}

foreach ($result as $key => $value) {
  $key++;
  foreach ($letters as $k => $v) {
    $ACTIVESHEET->setCellValue($v . ($key + 1), html_entity_decode(str_replace("\r\n", " ", $value[$k])));
  }
}

$date = date('Ym');
$filename = "{$date}_machine.xlsx";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment;filename=" . $filename);
header("Cache-Control: max-age=0");
$WRITER->save("php://output");
exit();
