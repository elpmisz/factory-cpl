<?php
require_once(__DIR__ . "/../../Publics/connection/mysql.php");

$url = "https://plan.smartsensedesign.net/api/energy";
$json = file_get_contents($url);
$result = json_decode($json, true);

$data = [];
foreach ($result as $row) {
  $data[] = [
    $row['machine_number'],
    $row['machine_name'],
    $row['date_start'],
    $row['date_stop'],
    ($row['kwh_start'] / 1000),
    ($row['kwh_stop'] / 1000),
    ($row['kwh_diff'] / 1000),
  ];
}

$dbcon->exec("TRUNCATE TABLE factory.energy_data");

$sql = "INSERT INTO factory.energy_data (`machine_number`, `machine_name`, `date_start`, `date_stop`, `kwh_start`, `kwh_stop`, `kwh_diff`) VALUES(?,?,?,?,?,?,?)";
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
