<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Helpdesk;
use App\Classes\Validation;
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
  $email = (isset($decode->data) ? $decode->data : "");
} catch (Exception $e) {
  $msg = $e->getMessage();
  if ($msg === "Expired token") {
    die(header("Location: /logout"));
  }
}

$USER = new User();
$HELPDESK = new Helpdesk();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");
$user = $USER->user_view($email);

if ($action === "create") {
  try {
    $last = $HELPDESK->helpdesk_last();
    $user_id = (isset($_POST['user_id']) ? $VALIDATION->input($_POST['user_id']) : "");
    $contact = (isset($_POST['contact']) ? $VALIDATION->input($_POST['contact']) : "");
    $service_id = (isset($_POST['service_id']) ? $VALIDATION->input($_POST['service_id']) : "");
    $asset = (isset($_POST['asset']) ? $VALIDATION->input($_POST['asset']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $approve_check = $HELPDESK->approve_check([$service_id]);
    $status = (intval($approve_check) === 1 ? 1 : 2);
    $line_token = $HELPDESK->line_token([$service_id]);

    $count = $HELPDESK->helpdesk_count([$user_id, $service, $text]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/helpdesk");
    }

    $HELPDESK->helpdesk_add([$last, $user_id, $service_id, $asset, $contact, $text, $status]);
    $request_id = $HELPDESK->last_insert_id();

    foreach ($_POST['item_id'] as $key => $value) {
      $item_id = (isset($_POST['item_id'][$key]) ? $VALIDATION->input($_POST['item_id'][$key]) : "");
      $item_type = (isset($_POST['item_type'][$key]) ? $VALIDATION->input($_POST['item_type'][$key]) : "");
      $item_value = (isset($_POST['item_value'][$key]) ? $VALIDATION->input($_POST['item_value'][$key]) : "");
      $item_value = (intval($item_type) === 4 ?
        date("Y-m-d", strtotime(str_replace("/", "-", $item_value))) : $item_value);

      $count = $HELPDESK->item_count([$request_id, $item_id]);
      if (intval($count) === 0 && !empty($item_value)) {
        $HELPDESK->item_add([$request_id, $item_id, $item_type, $item_value]);
      }
    }

    foreach ($_FILES['file']['name'] as $key => $row) {
      $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'][$key] : "");
      $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'][$key] : "");
      $file_random = md5(microtime());
      $file_image = ["png", "jpeg", "jpg"];
      $file_document = ["pdf", "doc", "docx", "xls", "xlsx"];
      $file_allow = array_merge($file_image, $file_document);
      $file_extension = pathinfo(strtolower($file_name), PATHINFO_EXTENSION);

      if (!empty($file_name) && in_array($file_extension, $file_allow)) {
        if (in_array($file_extension, $file_document)) {
          $file_rename = "{$file_random}.{$file_extension}";
          $file_path = (__DIR__ . "/../../Publics/helpdesk/{$file_rename}");
          move_uploaded_file($file_tmp, $file_path);
        }
        if (in_array($file_extension, $file_image)) {
          $file_rename = "{$file_random}.webp";
          $file_path = (__DIR__ . "/../../Publics/helpdesk/{$file_rename}");
          $VALIDATION->image_upload($file_tmp, $file_path);
        }

        $count = $HELPDESK->file_count([$request_id, $file_rename]);
        if (intval($count) === 0 && !empty($file_rename)) {
          $HELPDESK->file_add([$request_id, $file_rename]);
        }
      }
    }

    $uuid = $HELPDESK->helpdesk_uuid([$request_id]);
    $row = $HELPDESK->helpdesk_view([$uuid]);

    $texts = "
ผู้ใช้บริการ:
{$row['username']}
เบอร์ติดต่อ:
{$row['contact']}
บริการ:
{$row['service_name']}
เครื่องจักร:
{$row['asset_name']}
รายละเอียด:
{$row['text']}
วันที่:
{$row['created']}
";

    $VALIDATION->line_notify($line_token, $texts);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $service_id = (isset($_POST['service_id']) ? $VALIDATION->input($_POST['service_id']) : "");
    $contact = (isset($_POST['contact']) ? $VALIDATION->input($_POST['contact']) : "");
    $asset = (isset($_POST['asset']) ? $VALIDATION->input($_POST['asset']) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $HELPDESK->helpdesk_update([$asset, $contact, $text, $uuid]);

    foreach ($_POST['item_id'] as $key => $value) {
      $item_id = (isset($_POST['item_id'][$key]) ? $VALIDATION->input($_POST['item_id'][$key]) : "");
      $item_type = (isset($_POST['item_type'][$key]) ? $VALIDATION->input($_POST['item_type'][$key]) : "");
      $item_value = (isset($_POST['item_value'][$key]) ? $VALIDATION->input($_POST['item_value'][$key]) : "");
      $item_value = (intval($item_type) === 4 ?
        date("Y-m-d", strtotime(str_replace("/", "-", $item_value))) : $item_value);

      if (!empty($item_id)) {
        $HELPDESK->item_update([$item_value, $item_id]);
      }
    }

    foreach ($_FILES['file']['name'] as $key => $row) {
      $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'][$key] : "");
      $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'][$key] : "");
      $file_random = md5(microtime());
      $file_image = ["png", "jpeg", "jpg"];
      $file_document = ["pdf", "doc", "docx", "xls", "xlsx"];
      $file_allow = array_merge($file_image, $file_document);
      $file_extension = pathinfo(strtolower($file_name), PATHINFO_EXTENSION);

      if (!empty($file_name) && in_array($file_extension, $file_allow)) {
        if (in_array($file_extension, $file_document)) {
          $file_rename = "{$file_random}.{$file_extension}";
          $file_path = (__DIR__ . "/../../Publics/helpdesk/{$file_rename}");
          move_uploaded_file($file_tmp, $file_path);
        }
        if (in_array($file_extension, $file_image)) {
          $file_rename = "{$file_random}.webp";
          $file_path = (__DIR__ . "/../../Publics/helpdesk/{$file_rename}");
          $VALIDATION->image_upload($file_tmp, $file_path);
        }

        $count = $HELPDESK->file_count([$id, $file_rename]);
        if (intval($count) === 0 && !empty($file_rename)) {
          $HELPDESK->file_add([$id, $file_rename]);
        }
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");
    $remark = (intval($status) === 2 ? "ผ่านการอนุมัติ" : $remark);
    $date = date("Y-m-d");

    $HELPDESK->status_update([$status, $uuid]);
    $HELPDESK->process_add([$id, $user['user_id'], $remark, $date, "", "", $status]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "assign") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $worker = (isset($_POST['user']) ? $VALIDATION->input($_POST['user']) : "");
    $service_date = $HELPDESK->service_date([$uuid]);
    $date = date("Y-m-d", strtotime("+{$service_date} days"));

    $HELPDESK->status_update([3, $uuid]);
    $HELPDESK->process_add([$id, $worker, "รอดำเนินการ", $date, "", "", 3]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "work") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $service_id = (isset($_POST['service_id']) ? $VALIDATION->input($_POST['service_id']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");
    $date = (isset($_POST['date']) ? $VALIDATION->input($_POST['date']) : "");
    $date = (!empty($date) ? date("Y-m-d", strtotime(str_replace("/", "-", $date))) : "");
    $cost = (isset($_POST['cost']) ? $VALIDATION->input($_POST['cost']) : "");
    $worker_id = (isset($_POST['worker_id']) ? $VALIDATION->input($_POST['worker_id']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $checker_check = $HELPDESK->checker_check([$service_id]);
    $status = (intval($status) !== 7 ? $status : (intval($status) === 7 && intval($checker_check) === 1 ? 7 : 8));

    foreach ($_POST['item_code'] as $key => $value) {
      $item_code = (isset($_POST['item_code'][$key]) ? $VALIDATION->input($_POST['item_code'][$key]) : "");
      $item_quantity = (isset($_POST['item_quantity'][$key]) ? $VALIDATION->input($_POST['item_quantity'][$key]) : "");

      $count = $HELPDESK->spare_count([$id, $item_code]);
      if (intval($count) === 0 && !empty($item_code)) {
        $HELPDESK->spare_add([$id, $item_code, $item_quantity]);
      }
    }

    $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'] : "");
    if (!empty($file_name)) {
      $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'] : "");
      $file_random = md5(microtime());
      $file_image = ["png", "jpeg", "jpg"];
      $file_document = ["pdf", "doc", "docx", "xls", "xlsx"];
      $file_allow = array_merge($file_image, $file_document);
      $file_extension = pathinfo(strtolower($file_name), PATHINFO_EXTENSION);

      if (in_array($file_extension, $file_allow)) {
        if (in_array($file_extension, $file_document)) {
          $file_rename = "{$file_random}.{$file_extension}";
          $file_path = (__DIR__ . "/../../Publics/helpdesk/{$file_rename}");
          move_uploaded_file($file_tmp, $file_path);
        }
        if (in_array($file_extension, $file_image)) {
          $file_rename = "{$file_random}.webp";
          $file_path = (__DIR__ . "/../../Publics/helpdesk/{$file_rename}");
          $VALIDATION->image_upload($file_tmp, $file_path);
        }
      }
    }
    $file_rename = (!empty($file_rename) ? $file_rename : "");

    $HELPDESK->status_update([$status, $uuid]);
    $HELPDESK->process_add([$id, $worker_id, $remark, $date, $cost, $file_rename, $status]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "check") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $service_id = (isset($_POST['service_id']) ? $VALIDATION->input($_POST['service_id']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");
    $remark = (intval($status) === 8 ? "ผ่านการตรวจสอบ" : $remark);
    $date = date("Y-m-d");

    $HELPDESK->status_update([$status, $uuid]);
    $HELPDESK->process_add([$id, $user['user_id'], $remark, $date, "", "", $status]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "edit") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");
    $date = date("Y-m-d");

    $HELPDESK->status_update([$status, $uuid]);
    $HELPDESK->process_add([$id, $user['user_id'], $remark, $date, NULL, "", $status]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/helpdesk/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "file-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    if (!empty($id)) {
      $HELPDESK->file_delete([$id]);
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

if ($action === "spare-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    if (!empty($id)) {
      $HELPDESK->spare_delete([$id]);
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

if ($action === "request-data") {
  try {
    $result = $HELPDESK->request_data($user['user_id']);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve-data") {
  try {
    $result = $HELPDESK->approve_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "assign-data") {
  try {
    $result = $HELPDESK->assign_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "work-data") {
  try {
    $result = $HELPDESK->work_data($user['user_id']);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-data") {
  try {
    $result = $HELPDESK->manage_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "asset-check") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $service = $data['service'];
    $result = $HELPDESK->asset_check([$service]);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "service-item") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $service = $data['service'];
    $result = $HELPDESK->service_item([$service]);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "asset-detail") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $asset = $data['asset'];
    $result = $HELPDESK->asset_detail([$asset]);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "service-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $HELPDESK->service_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "asset-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $HELPDESK->asset_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "spare-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $HELPDESK->spare_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
