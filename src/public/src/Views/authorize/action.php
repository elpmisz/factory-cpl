<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\User;
use App\Classes\Validation;

$USER = new User();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "update") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $service = implode(',', $_POST['service']);

    $count = $USER->auth_count([$user_id]);
    if (intval($count) === 0) {
      $USER->auth_add([$user_id, $service]);
    } else {
      $USER->auth_update([$service, $user_id]);
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/auth");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
