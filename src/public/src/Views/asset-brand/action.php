<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\AssetBrand;
use App\Classes\Validation;

$BRAND = new AssetBrand();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $reference = (isset($_POST['reference']) ? $VALIDATION->input($_POST['reference']) : "");

    $count = $BRAND->brand_count([$name]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/asset/brand");
    }

    $BRAND->brand_create([$name, $type, $reference]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/asset/brand");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $reference = (isset($_POST['reference']) ? $VALIDATION->input($_POST['reference']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    $BRAND->brand_update([$name, $type, $reference, $status, $uuid]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/asset/brand");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $uuid = $data['uuid'];

    if (!empty($uuid)) {
      $BRAND->brand_delete([$uuid]);
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
    $brand = (isset($_POST['brand']) ? $VALIDATION->input($_POST['brand']) : "");
    $result = $BRAND->brand_data($brand);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "brand-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $BRAND->brand_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
