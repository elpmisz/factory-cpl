<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\HelpdeskService;
use App\Classes\Validation;

$SERVICE = new HelpdeskService();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $line_token_id = (isset($_POST['line_token_id']) ? $VALIDATION->input($_POST['line_token_id']) : "");
    $asset = (isset($_POST['asset']) ? $VALIDATION->input($_POST['asset']) : "");
    $approve = (isset($_POST['approve']) ? $VALIDATION->input($_POST['approve']) : "");
    $check = (isset($_POST['check']) ? $VALIDATION->input($_POST['check']) : "");

    $count = $SERVICE->service_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/helpdesk/service");
    }

    $SERVICE->service_create([$name, $date, $line_token_id, $asset, $approve, $check]);
    $service_id = $SERVICE->last_insert_id();

    foreach ($_POST['item_name'] as $key => $value) {
      $item_name = (isset($_POST['item_name'][$key]) ? $VALIDATION->input($_POST['item_name'][$key]) : "");
      $item_type = (isset($_POST['item_type'][$key]) ? $VALIDATION->input($_POST['item_type'][$key]) : "");
      $item_text = (isset($_POST['item_text'][$key]) ? $VALIDATION->input($_POST['item_text'][$key]) : "");
      $item_required = (isset($_POST['item_required'][$key]) ? $VALIDATION->input($_POST['item_required'][$key]) : "");

      $item_count = $SERVICE->item_count([$service_id, $item_name]);
      if (!empty($item_name) && intval($item_count) === 0) {
        $SERVICE->item_create([$service_id, $item_name, $item_type, $item_text, $item_required]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk/service");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $line_token_id = (isset($_POST['line_token_id']) ? $VALIDATION->input($_POST['line_token_id']) : "");
    $asset = (isset($_POST['asset']) ? $VALIDATION->input($_POST['asset']) : "");
    $approve = (isset($_POST['approve']) ? $VALIDATION->input($_POST['approve']) : "");
    $check = (isset($_POST['check']) ? $VALIDATION->input($_POST['check']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $SERVICE->service_update([$name, $date, $line_token_id, $asset, $approve, $check, $status, $uuid]);

    foreach ($_POST['item_name'] as $key => $value) {
      $item_name = (isset($_POST['item_name'][$key]) ? $VALIDATION->input($_POST['item_name'][$key]) : "");
      $item_type = (isset($_POST['item_type'][$key]) ? $VALIDATION->input($_POST['item_type'][$key]) : "");
      $item_text = (isset($_POST['item_text'][$key]) ? $VALIDATION->input($_POST['item_text'][$key]) : "");
      $item_required = (isset($_POST['item_required'][$key]) ? $VALIDATION->input($_POST['item_required'][$key]) : "");

      $item_count = $SERVICE->item_count([$id, $item_name]);
      if (!empty($item_name) && intval($item_count) === 0) {
        $SERVICE->item_create([$id, $item_name, $item_type, $item_text, $item_required]);
      }
    }

    foreach ($_POST['item___id'] as $key => $value) {
      $item___id = (isset($_POST['item___id'][$key]) ? $VALIDATION->input($_POST['item___id'][$key]) : "");

      if (!empty($item___id)) {
        $SERVICE->item_delete([$item___id]);
      }
    }

    foreach ($_POST['item__id'] as $key => $value) {
      $item__id = (isset($_POST['item__id'][$key]) ? $VALIDATION->input($_POST['item__id'][$key]) : "");
      $item__name = (isset($_POST['item__name'][$key]) ? $VALIDATION->input($_POST['item__name'][$key]) : "");
      $item__type = (isset($_POST['item__type'][$key]) ? $VALIDATION->input($_POST['item__type'][$key]) : "");
      $item__text = (isset($_POST['item__text'][$key]) ? $VALIDATION->input($_POST['item__text'][$key]) : "");
      $item__required = (isset($_POST['item__required'][$key]) ? $VALIDATION->input($_POST['item__required'][$key]) : "");

      if (!empty($item__id)) {
        $SERVICE->item_update([$item__name, $item__type, $item__text, $item__required, $item__id]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk/service/edit/{$uuid}");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $uuid = $data['uuid'];

    if (!empty($uuid)) {
      $SERVICE->service_delete([$uuid]);
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
    $result = $SERVICE->service_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "line-token-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $SERVICE->line_token_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
