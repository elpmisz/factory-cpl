<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Service;
use App\Classes\System;
use App\Classes\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

try {
  define("JWT_SECRET", "SECRET-KEY");
  define("JWT_ALGO", "HS512");
  $jwt = (isset($_COOKIE['jwt']) ? $_COOKIE['jwt'] : "");
  if (empty($jwt)) {
    die(header("Location: /"));
  }
  $decode = JWT::decode($jwt, new Key(JWT_SECRET, JWT_ALGO));
  $username = (isset($decode->data) ? $decode->data : "");
} catch (Exception $e) {
  $msg = $e->getMessage();
  if ($msg === "Expired token") {
    die(header("Location: /logout"));
  }
}

$USER = new User();
$SYSTEM = new System();
$SERVICE = new Service();
$system = $SYSTEM->system_read();
$user_count = $USER->user_count([$username]);
if ($user_count === 0) {
  die(header("Location: /logout"));
}
$user = $USER->user_view([$username]);
$system_name = (isset($system['name']) ? $system['name'] : "");
$firstname = (!empty($user['firstname']) ? $user['firstname'] : "");
$fullname = (!empty($user['fullname']) ? $user['fullname'] : "");
$user_id = (!empty($user['user_id']) ? $user['user_id'] : "");

$services = $SERVICE->service_read();
$user_auth = explode(",", $user['service']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $system_name ?></title>
  <link rel="stylesheet" href="/vendor/twitter/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/vendor/fortawesome/font-awesome/css/all.min.css">
  <link rel="stylesheet" href="/vendor/datatables/datatables/media/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="/vendor/select2/select2/dist/css/select2.min.css">
  <link rel="stylesheet" href="/vendor/pnikolov/bootstrap-daterangepicker/css/daterangepicker.min.css">
  <link rel="stylesheet" href="/styles/css/style.css">
  <link rel="stylesheet" href="/styles/css/apexcharts.css">
</head>

<body>
  <div class="wrapper">
    <?php include_once(__DIR__ . "/sidebar.php") ?>

    <div id="content">
      <?php include_once(__DIR__ . "/navbar.php") ?>