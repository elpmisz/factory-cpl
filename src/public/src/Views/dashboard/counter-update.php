<?php
require_once(__DIR__ . "/../../Publics/connection/mysql.php");

$url = "https://plan.smartsensedesign.net/api/job";
$json = file_get_contents($url);
$result = json_decode($json, true);

$data = [];
foreach ($result as $row) {
  $data[] = [
    $row['machine'],
    $row['req_job'],
    $row['bom_name'],
    $row['req_po'],
    $row['req_customer'],
    $row['req_shift'],
    $row['req_piece'],
    $row['count_out'],
    $row['weld_open'],
    $row['weld_close'],
    $row['weld_diff'],
    $row['drive_open'],
    $row['drive_close'],
    $row['drive_diff'],
    $row['energy_open'],
    $row['energy_close'],
    $row['energy_diff'],
    $row['open'],
    $row['close'],
  ];
}

$dbcon->exec("TRUNCATE TABLE factory.counter_data");

$sql = "INSERT INTO factory.counter_data (`machine`, `job`, `bom`, `po`, `customer`, `shift`, `target`, `actual`, `weld_open`, `weld_close`, `weld_diff`, `drive_open`, `drive_close`, `drive_diff`, `energy_open`, `energy_close`, `energy_diff`, `open`, `close`) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt = $dbcon->prepare($sql);

try {
  $dbcon->beginTransaction();
  foreach ($data as $row) {
    $stmt->execute($row);
  }
  $dbcon->commit();
} catch (Exception $e) {
  $dbcon->rollback();
  throw $e;
}

echo COUNT($data) . "<br>";
echo "Updated";
