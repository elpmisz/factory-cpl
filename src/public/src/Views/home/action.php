<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

define("JWT_SECRET", "SECRET-KEY");
define("JWT_ALGO", "HS512");

use App\Classes\User;
use App\Classes\System;
use App\Classes\Validation;
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$USER = new User();
$SYSTEM = new System();
$VALIDATION = new Validation();

$system = $SYSTEM->system_read();
$system_name = (!empty($system['name']) ? $system['name'] : "");
$system_email = (!empty($system['email']) ? $system['email'] : "");
$password_email = (!empty($system['password_email']) ? $system['password_email'] : "");

$MAIL = new PHPMailer(true);
$MAIL->SMTPDebug = SMTP::DEBUG_OFF;
$MAIL->isSMTP();
$MAIL->Host = "smtp.gmail.com";
$MAIL->SMTPAuth = true;
$MAIL->Username = $system_email;
$MAIL->Password = $password_email;
$MAIL->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
$MAIL->Port = 465;
$MAIL->CharSet = "UTF-8";
$MAIL->SMTPOptions = [
  "ssl" => [
    "verify_peer" => false,
    "verify_peer_name" => false,
    "allow_self_signed" => true
  ]
];

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "login") {
  try {
    $username = (isset($_POST['username']) ? $VALIDATION->input($_POST['username']) : "");
    $password = (isset($_POST['password']) ? $VALIDATION->input($_POST['password']) : "");
    $password = (!empty($password) ? sha1(md5($password)) : "");

    $count = $USER->user_count([$username]);
    $user = $USER->user_view([$username]);

    if (intval($count) === 0 || ($password !== $user['P5ssword'])) {
      $VALIDATION->alert("danger", "อีเมล หรือรหัสผ่านไม่ถูกต้อง!", "/");
    }

    $status = $USER->user_status([$username]);
    if (intval($status) === 2) {
      $VALIDATION->alert("danger", "กรุณาติดต่อผู้ดูแลระบบ!", "/");
    }

    $now = time();
    $payload = [
      "iat" => $now,
      "exp" => $now + (12 * (60 * 60)),
      "data" => $username,
    ];
    $encode = JWT::encode($payload, JWT_SECRET, JWT_ALGO);
    setcookie("jwt", $encode);

    $VALIDATION->alert("success", "เข้าสู่ระบบเรียบร้อยแล้ว!", "/dashboard-machine");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "forgot") {
  try {
    $email = (isset($_POST['email']) ? $VALIDATION->input($_POST['email']) : "");
    $random_password = $VALIDATION->get_rand_numbers(6);
    $hash_password = password_hash($random_password, PASSWORD_DEFAULT);
    $user = $USER->user_view($email);

    $USER->password_random([$hash_password, $email]);

    try {
      $MAIL->setFrom($system_email, "E-MAIL NOTIFICATION");
      $MAIL->addAddress($email, $user['fullname']);

      $MAIL->isHTML(true);
      $MAIL->Subject = "[รหัสผ่านใหม่] {$system_name} SYSTEM";
      $MAIL->Body = $VALIDATION->forgot_email($random_password);
      $MAIL->send();

      $VALIDATION->alert("success", "ระบบส่งรหัสผ่านใหม่ เรียบร้อยแล้ว!", "/");
    } catch (Exception $e) {
      $VALIDATION->alert("danger", "ระบบอีเมลมีปัญหา กรุณาลองใหม่อีกครั้ง!", "/");
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "logout") {
  try {
    $VALIDATION->logout();
    $VALIDATION->alert("success", "ออกจากระบบเรียบร้อยแล้ว!", "/");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
