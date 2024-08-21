<?php
require_once(__DIR__ . "/../../Publics/connection/mysql.php");
include_once(__DIR__ . "/../../../vendor/autoload.php");

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\DashboardCounter;
use App\Classes\Validation;

$DASHBOARD = new DashboardCounter();
$VALIDATION = new Validation();

if ($action === "job-daily-data") {
  try {
    $sql = "SELECT COUNT(*) FROM factory.counter_data";
    $stmt = $dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.machine", "a.job", "a.shift", "a.target", "a.actual", "a.energy_diff", "a.open", "a.close"];

    $machine = (isset($_POST['machine']) ? $_POST['machine'] : "");
    $job = (isset($_POST['job']) ? $_POST['job'] : "");
    $shift = (isset($_POST['shift']) ? $_POST['shift'] : "");
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

    $sql = "SELECT a.machine,CONCAT('[',a.job,'] ',a.bom) job,a.shift,a.target,a.actual,a.energy_diff,
    DATE_FORMAT(a.`open`, '%d/%m/%Y, %H:%i น.') open,DATE_FORMAT(a.`close`, '%d/%m/%Y, %H:%i น.') close
    FROM factory.counter_data a
    WHERE a.actual != ''
    AND YEAR(a.open) = YEAR(NOW()) ";

    if (!empty($machine)) {
      $sql .= " AND (a.machine = '{$machine}') ";
    }
    if (!empty($job)) {
      $sql .= " AND (a.job = '{$job}') ";
    }
    if (!empty($shift)) {
      $sql .= " AND (a.shift = '{$shift}') ";
    }
    if (!empty($start) && !empty($end)) {
      $sql .= " AND (DATE(a.open) BETWEEN STR_TO_DATE('{$start}', '%d/%m/%Y') AND STR_TO_DATE('{$end}', '%d/%m/%Y')) ";
    }
    if (!empty($start) && empty($end)) {
      $sql .= " AND (DATE(a.open) = STR_TO_DATE('{$start}', '%d/%m/%Y')) ";
    }
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR b.name LIKE '%{$keyword}%') ";
    }
    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.open DESC, a.machine ASC ";
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

if ($action === "job-daily") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $machine = (!empty($data['machine']) ? $data['machine'] : "");
    $job = (!empty($data['job']) ? $data['job'] : "");
    $shift = (!empty($data['shift']) ? $data['shift'] : "");
    $date = (!empty($data['date']) ? $data['date'] : "");
    $dates = (!empty($date) ? explode("-", $date) : "");
    $start = (!empty($date) ? trim($dates[0]) : "");
    $end = (!empty($date) ? trim($dates[1]) : "");
    $end = ($start === $end ? "" : $end);

    $result = $DASHBOARD->job_daily($machine, $job, $shift, $start, $end);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "monthly-daily") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    $result = $DASHBOARD->job_monthly();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "machine-all") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);

    $result = $DASHBOARD->machine_all();
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

if ($action === "job-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $DASHBOARD->job_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "shift-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $DASHBOARD->shift_select();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
