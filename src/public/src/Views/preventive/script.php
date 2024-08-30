<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

use App\Classes\Preventive;
use App\Classes\PreventiveScript;
use App\Classes\User;
use App\Classes\Validation;
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
$PREVENTIVE = new Preventive();
$SCRIPT = new PreventiveScript();
$VALIDATION = new Validation();

$user = $USER->user_view([$username]);

if ($action === "weekly") {
  try {
    $result = $SCRIPT->weekly();

    foreach ($result as $row) {
      $year = date("Y");
      $startDate = strtotime("{$year}-01-01");
      $endDate = strtotime("{$year}-12-28");

      $monday = [1]; // 0 = sunday, 1 = monday, 2 = tuesday, 3 = wednesday, 4 = thursday, 5 friday, 6 = saturday

      for ($i = $startDate; $i <= $endDate; $i = strtotime("+1 day", $i)) {
        $numDate = date("w", $i);
        $nameDate = date("Y-m-d", $i);
        if (in_array($numDate, $monday)) {
          $mondays[] = $nameDate;
        }
      }

      foreach ($mondays as $key => $monday) {
        $key++;
        $friday = date("Y-m-d", strtotime($monday . "+4 days"));
        $text = "แจ้งบำรุงรักษา{$row['type_name']} ประจำสัปดาห์ที่ {$key}";

        $count = $PREVENTIVE->preventive_count([$user['user_id'], $row['type_id'], $text]);
        $machine = $SCRIPT->machine_type([$row['type_id']]);
        if (intval($count) === 0 && !empty($row['type_id']) && COUNT($machine) > 0) {
          $last_id = $PREVENTIVE->preventive_last([$monday]);
          $PREVENTIVE->preventive_add([$last_id, $user['user_id'], $row['type_id'], $monday, $friday, $row['worker'], $text, 2]);
          $request_id = $PREVENTIVE->last_insert_id();
          $request[] = [
            "request" => $request_id,
            "type_id" => $row['type_id'],
          ];
        }
      }
    }

    if (COUNT($request) > 0) {
      foreach ($request as $req) {
        $machine = $SCRIPT->machine_type([$req['type_id']]);
        if (COUNT($machine) > 0) {
          foreach ($machine as $m) {
            $count = $PREVENTIVE->item_count([$req['request'], $m['machine_id']]);
            if (intval($count) === 0 && !empty($m['machine_id'])) {
              $PREVENTIVE->item_add([$req['request'], $m['machine_id'], "", "", ""]);
            }
          }
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "monthly") {
  try {
    $result = $SCRIPT->monthly();

    foreach ($result as $row) {
      $month = range(1, 12);
      foreach ($month as $m) {
        $month_number = STR_PAD($m, 2, "0", STR_PAD_LEFT);
        $month_name = $VALIDATION->month_th_name($m);
        $start = date("Y-{$month_number}-01");
        $end = date("Y-{$month_number}-25");
        $text = "แจ้งบำรุงรักษา{$row['type_name']} ประจำเดือน {$month_name}";

        $count = $PREVENTIVE->preventive_count([$user['user_id'], $row['type_id'], $text]);
        $machine = $SCRIPT->machine_type([$row['type_id']]);
        if (intval($count) === 0 && !empty($row['type_id']) && COUNT($machine) > 0) {
          $last_id = $PREVENTIVE->preventive_last([$start]);
          $PREVENTIVE->preventive_add([$last_id, $user['user_id'], $row['type_id'], $start, $end, $row['worker'], $text, 2]);
          $request_id = $PREVENTIVE->last_insert_id();
          $request[] = [
            "request" => $request_id,
            "type_id" => $row['type_id'],
          ];
        }
      }
    }

    if (COUNT($request) > 0) {
      foreach ($request as $req) {
        $machine = $SCRIPT->machine_type([$req['type_id']]);
        if (COUNT($machine) > 0) {
          foreach ($machine as $m) {
            $count = $PREVENTIVE->item_count([$req['request'], $m['machine_id']]);
            if (intval($count) === 0 && !empty($m['machine_id'])) {
              $PREVENTIVE->item_add([$req['request'], $m['machine_id'], "", "", ""]);
            }
          }
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "month") {
  try {
    $result = $SCRIPT->month();

    foreach ($result as $row) {
      $month = explode(",", $row['month']);
      foreach ($month as $m) {
        $month_number = STR_PAD($m, 2, "0", STR_PAD_LEFT);
        $month_name = $VALIDATION->month_th_name($m);
        $start = date("Y-{$month_number}-01");
        $end = date("Y-{$month_number}-25");
        $text = "แจ้งบำรุงรักษา{$row['type_name']} ประจำเดือน {$month_name}";

        $count = $PREVENTIVE->preventive_count([$user['user_id'], $row['type_id'], $text]);
        $machine = $SCRIPT->machine_type([$row['type_id']]);
        if (intval($count) === 0 && !empty($row['type_id']) && COUNT($machine) > 0) {
          $last_id = $PREVENTIVE->preventive_last([$start]);
          $PREVENTIVE->preventive_add([$last_id, $user['user_id'], $row['type_id'], $start, $end, $row['worker'], $text, 2]);
          $request_id = $PREVENTIVE->last_insert_id();
          $request[] = [
            "request" => $request_id,
            "type_id" => $row['type_id'],
          ];
        }
      }
    }

    if (COUNT($request) > 0) {
      foreach ($request as $req) {
        $machine = $SCRIPT->machine_type([$req['type_id']]);
        if (COUNT($machine) > 0) {
          foreach ($machine as $m) {
            $count = $PREVENTIVE->item_count([$req['request'], $m['machine_id']]);
            if (intval($count) === 0 && !empty($m['machine_id'])) {
              $PREVENTIVE->item_add([$req['request'], $m['machine_id'], "", "", ""]);
            }
          }
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
