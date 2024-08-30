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

if ($action === "change") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $password = (isset($_POST['password']) ? $VALIDATION->input($_POST['password']) : "");
    $password2 = (isset($_POST['password2']) ? $VALIDATION->input($_POST['password2']) : "");

    if ($password !== $password2) {
      $VALIDATION->alert("danger", "รหัสผ่านไม่ตรงกัน!", "/user/change");
    }

    $hash_password = sha1(md5($password));
    $USER->password_change([$password, $hash_password, $user_id]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/home");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "profile") {
  try {
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $firstname = (isset($_POST['firstname']) ? $VALIDATION->input($_POST['firstname']) : "");
    $lastname = (isset($_POST['lastname']) ? $VALIDATION->input($_POST['lastname']) : "");
    $contact = (isset($_POST['contact']) ? $VALIDATION->input($_POST['contact']) : "");

    $USER->user_update([$firstname, $lastname, $contact, $user_id]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/user/profile");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
