<?php
require_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\AssetDepartment;
use App\Classes\Validation;

$ASSET = new AssetDepartment();
$VALIDATION = new Validation();

$result = $ASSET->department_export();

array_walk_recursive($result, "htmldecode");

function htmldecode(&$item, $key)
{
  $item = html_entity_decode($item, ENT_COMPAT, 'UTF-8');
}

$columns = ["ID", "UUID", "ชื่อ", "สถานะ"];

$letters = [];
for ($i = "A"; $i != $VALIDATION->letters(COUNT($columns) + 1); $i++) {
  $letters[] = $i;
}

$columns = array_combine($letters, $columns);

ob_start();
$date = date('Ym');
$filename = $date . "-asset-department.csv";
header("Content-Encoding: UTF-8");
header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename={$filename}");
ob_end_clean();

$output = fopen("php://output", "w");
fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
fputcsv($output, $columns);

foreach ($result as $data) {
  fputcsv($output, $data);
}

fclose($output);
die();
