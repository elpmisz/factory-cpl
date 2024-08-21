<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\AssetType;
use App\Classes\User;
use App\Classes\VALIDATION;

$TYPE = new AssetType();
$USER = new User();
$VALIDATION = new VALIDATION();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "add") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $checklist = (isset($_POST['checklist']) ? implode(",", $_POST['checklist']) : "");
    $worker = (isset($_POST['worker']) ? implode(",", $_POST['worker']) : "");
    $weekly = (isset($_POST['weekly']) ? $VALIDATION->input($_POST['weekly']) : "");
    $monthly = (isset($_POST['monthly']) ? $VALIDATION->input($_POST['monthly']) : "");
    $month = (isset($_POST['month']) ? implode(",", $_POST['month']) : "");

    $count = $TYPE->type_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/asset/type");
    }

    $TYPE->type_create([$name, $checklist, $worker, $weekly, $monthly, $month]);
    $type_id = $TYPE->last_insert_id();

    foreach ($_POST['item_name'] as $key => $value) {
      $item_name = (isset($_POST['item_name'][$key]) ? $VALIDATION->input($_POST['item_name'][$key]) : "");
      $item_type = (isset($_POST['item_type'][$key]) ? $VALIDATION->input($_POST['item_type'][$key]) : "");
      $item_text = (isset($_POST['item_text'][$key]) ? $VALIDATION->input($_POST['item_text'][$key]) : "");
      $item_required = (isset($_POST['item_required'][$key]) ? $VALIDATION->input($_POST['item_required'][$key]) : "");

      $item_count = $TYPE->item_count([$type_id, $item_name]);
      if (!empty($item_name) && intval($item_count) === 0) {
        $TYPE->item_add([$type_id, $item_name, $item_type, $item_text, $item_required]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/asset/type");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $checklist = (isset($_POST['checklist']) ? implode(",", $_POST['checklist']) : "");
    $worker = (isset($_POST['worker']) ? implode(",", $_POST['worker']) : "");
    $weekly = (isset($_POST['weekly']) ? $VALIDATION->input($_POST['weekly']) : "");
    $monthly = (isset($_POST['monthly']) ? $VALIDATION->input($_POST['monthly']) : "");
    $month = (isset($_POST['month']) ? implode(",", $_POST['month']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $TYPE->type_update([$name, $checklist, $worker, $weekly, $monthly, $month, $status, $uuid]);

    foreach ($_POST['item_name'] as $key => $value) {
      $item_name = (isset($_POST['item_name'][$key]) ? $VALIDATION->input($_POST['item_name'][$key]) : "");
      $item_type = (isset($_POST['item_type'][$key]) ? $VALIDATION->input($_POST['item_type'][$key]) : "");
      $item_text = (isset($_POST['item_text'][$key]) ? $VALIDATION->input($_POST['item_text'][$key]) : "");
      $item_required = (isset($_POST['item_required'][$key]) ? $VALIDATION->input($_POST['item_required'][$key]) : "");

      $item_count = $TYPE->item_count([$id, $item_name]);
      if (!empty($item_name) && intval($item_count) === 0) {
        $TYPE->item_add([$id, $item_name, $item_type, $item_text, $item_required]);
      }
    }

    foreach ($_POST['item___id'] as $key => $value) {
      $item___id = (isset($_POST['item___id'][$key]) ? $VALIDATION->input($_POST['item___id'][$key]) : "");

      if (!empty($item___id)) {
        $TYPE->item_delete([$item___id]);
      }
    }

    foreach ($_POST['item__id'] as $key => $value) {
      $item__id = (isset($_POST['item__id'][$key]) ? $VALIDATION->input($_POST['item__id'][$key]) : "");
      $item__name = (isset($_POST['item__name'][$key]) ? $VALIDATION->input($_POST['item__name'][$key]) : "");
      $item__type = (isset($_POST['item__type'][$key]) ? $VALIDATION->input($_POST['item__type'][$key]) : "");
      $item__text = (isset($_POST['item__text'][$key]) ? $VALIDATION->input($_POST['item__text'][$key]) : "");
      $item__required = (isset($_POST['item__required'][$key]) ? $VALIDATION->input($_POST['item__required'][$key]) : "");

      if (!empty($item__id)) {
        $TYPE->item_update([$item__name, $item__type, $item__text, $item__required, $item__id]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/asset/type/edit/{$uuid}");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $uuid = $data['uuid'];

    if (!empty($uuid)) {
      $TYPE->type_delete([$uuid]);
      $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!");
      echo json_encode(200);
    } else {
      $VALIDATION->alert("danger", "ระบบมีปัญหา กรุณาลองใหม่อีกครั้ง!");
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "data") {
  try {
    $result = $TYPE->type_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "checklist-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $TYPE->checklist_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "worker-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $TYPE->worker_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "month-select") {
  try {
    $result = $VALIDATION->month_th();
    $data = [];
    foreach ($result as $key => $value) {
      $key++;
      $data[] = [
        "id" => $key,
        "text" => $value
      ];
    }
    echo json_encode($data);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "input-type-select") {
  try {
    $result = $VALIDATION->input_th();
    $data = [];
    foreach ($result as $key => $value) {
      $key++;
      $data[] = [
        "id" => $key,
        "text" => $value
      ];
    }
    echo json_encode($data);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "input-require-select") {
  try {
    $result = $VALIDATION->require_th();
    $data = [];
    foreach ($result as $key => $value) {
      $key++;
      $data[] = [
        "id" => $key,
        "text" => $value
      ];
    }
    echo json_encode($data);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
