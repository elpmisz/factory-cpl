<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Preventive;
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
  $username = (isset($decode->data) ? $decode->data : "");
} catch (Exception $e) {
  $msg = $e->getMessage();
  if ($msg === "Expired token") {
    die(header("Location: /logout"));
  }
}

$USER = new User();
$PREVENTIVE = new Preventive();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");
$user = $USER->user_view([$username]);

if ($action === "create") {
  try {
    $last = $PREVENTIVE->preventive_last();
    $type_id = (isset($_POST['type_id']) ? $VALIDATION->input($_POST['type_id']) : "");
    $worker_id = (isset($_POST['worker_id']) ? implode(",", $_POST['worker_id']) : "");
    $date = (isset($_POST['date']) ? explode("-", $_POST['date']) : "");
    $start = (!empty($date[0]) ? date("Y-m-d", strtotime(str_replace("/", "-", trim($date[0])))) : "");
    $end = (!empty($date[1]) ? date("Y-m-d", strtotime(str_replace("/", "-", trim($date[1])))) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $machine = COUNT($_POST['machine']);
    if ($machine === 0) {
      $VALIDATION->alert("danger", "กรุณาเลือกเครื่องจักร!", "/preventive/create");
    }

    $count = $PREVENTIVE->preventive_count([$user['user_id'], $type_id, $text]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/preventive");
    }

    $PREVENTIVE->preventive_add([$last, $user['user_id'], $type_id, $start, $end, $worker_id, $text, 1]);
    $request_id = $PREVENTIVE->last_insert_id();

    foreach ($_POST['machine'] as $key => $row) {
      $machine = (isset($_POST['machine']) ? $_POST['machine'][$key] : "");

      $count = $PREVENTIVE->item_count([$request_id, $machine]);
      if (intval($count) === 0 && !empty($machine)) {
        $PREVENTIVE->item_add([$request_id, $machine, "", "", ""]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "view") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $type_id = (isset($_POST['type_id']) ? $VALIDATION->input($_POST['type_id']) : "");
    $worker_id = (isset($_POST['worker_id']) ? implode(",", $_POST['worker_id']) : "");
    $date = (isset($_POST['date']) ? explode("-", $_POST['date']) : "");
    $start = (!empty($date[0]) ? date("Y-m-d", strtotime(str_replace("/", "-", trim($date[0])))) : "");
    $end = (!empty($date[1]) ? date("Y-m-d", strtotime(str_replace("/", "-", trim($date[1])))) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $PREVENTIVE->preventive_update([$start, $end, $worker_id, $text, $uuid]);

    foreach ($_POST['machine'] as $key => $row) {
      $machine = (isset($_POST['machine']) ? $_POST['machine'][$key] : "");

      $count = $PREVENTIVE->item_count([$id, $machine]);
      if (intval($count) === 0 && !empty($machine)) {
        $PREVENTIVE->item_add([$id, $machine, "", "", ""]);
      }
    }

    foreach ($_POST['machine__id'] as $key => $row) {
      $machine__id = (isset($_POST['machine__id']) ? $_POST['machine__id'][$key] : "");

      if (!empty($machine__id)) {
        $PREVENTIVE->item_delete([$machine__id]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive");
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

    $PREVENTIVE->preventive_approve([$status, $uuid]);
    $PREVENTIVE->process_add([$id, $user['user_id'], $remark, $status]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "work") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");

    foreach ($_POST['item_id'] as $key => $row) {
      $item_id = (isset($_POST['item_id']) ? $_POST['item_id'][$key] : "");
      $item_process = (isset($_POST['item_process']) ? $_POST['item_process'][$key] : "");
      $item_text = (isset($_POST['item_text']) ? $_POST['item_text'][$key] : "");

      $PREVENTIVE->item_update([$item_process, $item_text, $item_id]);
    }

    foreach ($_POST['machine'] as $key => $row) {
      $machine = (isset($_POST['machine']) ? $_POST['machine'][$key] : "");

      $count = $PREVENTIVE->item_count([$id, $machine]);
      if (intval($count) === 0 && !empty($machine)) {
        $PREVENTIVE->item_add([$id, $machine, "", "", ""]);
      }
    }

    if (intval($status) === 3) {
      $PREVENTIVE->preventive_work([$status, $uuid]);
      $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive");
    } else {
      $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive/work/{$uuid}");
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "check") {
  try {
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : "");
    $remark = (isset($_POST['remark']) ? $VALIDATION->input($_POST['remark']) : "");
    $remark = (intval($status) === 5 ? "ผ่านการตรวจสอบ" : $remark);

    $PREVENTIVE->preventive_approve([$status, $uuid]);
    $PREVENTIVE->process_add([$id, $user['user_id'], $remark, $status]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive");
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

    $PREVENTIVE->preventive_approve([$status, $uuid]);
    $PREVENTIVE->process_add([$id, $user['user_id'], $remark, $status]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive/manage");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "file-add") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $item = (isset($_POST['item']) ? $VALIDATION->input($_POST['item']) : "");
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
          $file_path = (__DIR__ . "/../../Publics/preventive/{$file_rename}");
          move_uploaded_file($file_tmp, $file_path);
        }
        if (in_array($file_extension, $file_image)) {
          $file_rename = "{$file_random}.webp";
          $file_path = (__DIR__ . "/../../Publics/preventive/{$file_rename}");
          $VALIDATION->image_upload($file_tmp, $file_path);
        }
      }
    }
    $file_rename = (!empty($file_rename) ? $file_rename : "");

    $PREVENTIVE->file_add([$file_rename, $item]);
    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive/work/{$uuid}");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "file-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = $data['item'];

    if (!empty($item)) {
      $PREVENTIVE->file_delete([$item]);
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

if ($action === "checklist-add") {
  try {
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $item = (isset($_POST['item']) ? $VALIDATION->input($_POST['item']) : "");

    foreach ($_POST['checklist_checklist'] as $key => $row) {
      $checklist_id = (isset($_POST['checklist_id']) ? $_POST['checklist_id'][$key] : "");
      $checklist_checklist = (isset($_POST['checklist_checklist']) ? $_POST['checklist_checklist'][$key] : "");
      $checklist_result = (isset($_POST['checklist_result']) ? $_POST['checklist_result'][$key] : "");

      $count = $PREVENTIVE->checklist_count([$item, $checklist_checklist]);
      if (intval($count) === 0 && empty($checklist_id)) {
        $PREVENTIVE->checklist_add([$item, $checklist_checklist, $checklist_result]);
      }
      if (!empty($checklist_id)) {
        $PREVENTIVE->checklist_update([$checklist_result, $checklist_id, $item, $checklist_checklist]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/preventive/work/{$uuid}");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "request-data") {
  try {
    $result = $PREVENTIVE->request_data($user['user_id']);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "approve-data") {
  try {
    $result = $PREVENTIVE->approve_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "work-data") {
  try {
    $result = $PREVENTIVE->work_data($user['user_id']);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "manage-data") {
  try {
    $result = $PREVENTIVE->manage_data();
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}


if ($action === "asset-worker") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $type = $data['type'];
    if (!empty($type)) {
      $result = $PREVENTIVE->asset_worker([$type]);
      echo json_encode($result);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "asset-machine") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $type = $data['type'];
    if (!empty($type)) {
      $result = $PREVENTIVE->asset_by_type([$type]);
      echo json_encode($result);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "asset-detail") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $machine = $data['machine'];
    if (!empty($machine)) {
      $result = $PREVENTIVE->asset_detail([$machine]);
      echo json_encode($result);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "checklist-detail") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $item = $data['item'];
    if (!empty($item)) {
      $result = $PREVENTIVE->checklist_detail([$item]);
      echo json_encode($result);
    } else {
      echo json_encode(500);
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "machine-select") {
  try {
    $keyword = (isset($_POST['keyword']) ? $VALIDATION->input($_POST['keyword']) : "");
    $id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $result = $PREVENTIVE->machine_select($keyword, $id, $type);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
