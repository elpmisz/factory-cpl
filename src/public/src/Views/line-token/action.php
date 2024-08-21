<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\LineToken;
use App\Classes\Validation;

$LINE = new LineToken();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $token = (isset($_POST['token']) ? $VALIDATION->input($_POST['token']) : "");

    $count = $LINE->line_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/line-token");
    }

    $LINE->line_create([$name, $token]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/line-token");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $token = (isset($_POST['token']) ? $VALIDATION->input($_POST['token']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $LINE->line_update([$name, $token, $status, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/line-token");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $uuid = $data['uuid'];
    if (!empty($uuid)) {
      $LINE->line_delete([$uuid]);
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
    $result = $LINE->line_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
