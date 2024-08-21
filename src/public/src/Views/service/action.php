<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Service;
use App\Classes\Validation;

$SERVICE = new Service();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    foreach ($_POST['item_sequence'] as $key => $value) {
      $item_sequence = (isset($_POST['item_sequence'][$key]) ? $VALIDATION->input($_POST['item_sequence'][$key]) : "");
      $item_name = (isset($_POST['item_name'][$key]) ? $VALIDATION->input($_POST['item_name'][$key]) : "");
      $item_link = (isset($_POST['item_link'][$key]) ? $VALIDATION->input($_POST['item_link'][$key]) : "");
      $item_table = (isset($_POST['item_table'][$key]) ? $VALIDATION->input($_POST['item_table'][$key]) : "");

      $count = $SERVICE->service_count([$item_name]);
      if (intval($count) === 0 && !empty($item_name)) {
        $SERVICE->service_add([$item_sequence, $item_name, $item_link, $item_table]);
      }
    }

    foreach ($_POST['item__uuid'] as $key => $value) {
      $item__uuid = (isset($_POST['item__uuid'][$key]) ? $VALIDATION->input($_POST['item__uuid'][$key]) : "");
      $item__sequence = (isset($_POST['item__sequence'][$key]) ? $VALIDATION->input($_POST['item__sequence'][$key]) : "");
      $item__name = (isset($_POST['item__name'][$key]) ? $VALIDATION->input($_POST['item__name'][$key]) : "");
      $item__link = (isset($_POST['item__link'][$key]) ? $VALIDATION->input($_POST['item__link'][$key]) : "");
      $item__table = (isset($_POST['item__table'][$key]) ? $VALIDATION->input($_POST['item__table'][$key]) : "");

      $SERVICE->service_update([$item__sequence, $item__name, $item__link, $item__table, $item__uuid]);
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/service");
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
