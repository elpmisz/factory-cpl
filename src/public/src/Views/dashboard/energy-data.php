<?php
require_once(__DIR__ . "/../../Publics/connection/mysql.php");
include_once(__DIR__ . "/../../../vendor/autoload.php");

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\DashboardEnergy;
use App\Classes\Validation;

$DASHBOARD = new DashboardEnergy();
$VALIDATION = new Validation();

if ($action === "data") {
  try {
    $sql = "SELECT COUNT(*) FROM factory.energy_data";
    $stmt = $dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.machine_number", "a.machine_name", "a.date_start", "a.kwh_diff"];

    $machine = (isset($_POST['machine']) ? $_POST['machine'] : "");
    $date = (isset($_POST['date']) ? $_POST['date'] : "");
    $dates = (!empty($date) ? explode("-", $date) : "");
    $start = (!empty($date) ? trim($dates[0]) : "");
    $end = (!empty($date) ? trim($dates[1]) : "");
    $end = ($start === $end ? "" : $end);

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : "");
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : "");
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : "");
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : "");
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : "");
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : "");
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : "");

    $sql = "SELECT a.machine_number,a.machine_name,DATE_FORMAT(a.date_start, '%d/%m/%Y') start,a.kwh_diff
    FROM factory.energy_data a
    WHERE a.date_start != ''
    AND YEAR(a.date_start) = YEAR(NOW()) ";

    if (!empty($machine)) {
      $sql .= " AND (a.machine_number = '{$machine}') ";
    }
    if (!empty($start) && !empty($end)) {
      $sql .= " AND (DATE(a.date_start) BETWEEN STR_TO_DATE('{$start}', '%d/%m/%Y') AND STR_TO_DATE('{$end}', '%d/%m/%Y')) ";
    }
    if (!empty($start) && empty($end)) {
      $sql .= " AND (DATE(a.date_start) = STR_TO_DATE('{$start}', '%d/%m/%Y')) ";
    }
    if (!empty($keyword)) {
      $sql .= " AND (a.machine_number LIKE '%{$keyword}%' OR b.machine_name LIKE '%{$keyword}%') ";
    }
    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.date_start DESC, a.machine_number ASC ";
    }

    $sql2 = "";
    if ($limit_length) {
      $sql2 .= "LIMIT {$limit_start}, {$limit_length}";
    }

    $stmt = $dbcon->prepare($sql);
    $stmt->execute();
    $filter = $stmt->rowCount();
    $stmt = $dbcon->prepare($sql . $sql2);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $output = [
      "draw"    => $draw,
      "recordsTotal"  =>  $total,
      "recordsFiltered" => $filter,
      "data"    => $result
    ];

    echo json_encode($output);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "energy-daily") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $machine = (!empty($data['machine']) ? $data['machine'] : "");
    $date = (!empty($data['date']) ? $data['date'] : "");
    $dates = (!empty($date) ? explode("-", $date) : "");
    $start = (!empty($date) ? trim($dates[0]) : "");
    $end = (!empty($date) ? trim($dates[1]) : "");
    $end = ($start === $end ? "" : $end);

    $result = $DASHBOARD->energy_daily($machine, $start, $end);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "machine-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $DASHBOARD->machine_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
