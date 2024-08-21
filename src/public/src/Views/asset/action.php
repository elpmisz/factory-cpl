<?php
session_start();
ini_set("display_errors", 1);
error_reporting(E_ALL);
date_default_timezone_set("Asia/Bangkok");
include_once(__DIR__ . "/../../../vendor/autoload.php");

use App\Classes\Asset;
use App\Classes\Validation;

$ASSET = new Asset();
$VALIDATION = new Validation();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$action = (isset($param[0]) ? $param[0] : die(header("Location: /error")));
$param1 = (isset($param[1]) ? $param[1] : "");
$param2 = (isset($param[2]) ? $param[2] : "");

if ($action === "create") {
  try {
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $asset_code = (isset($_POST['asset_code']) ? $VALIDATION->input($_POST['asset_code']) : "");
    $type_id = (isset($_POST['type_id']) ? $VALIDATION->input($_POST['type_id']) : "");
    $department_id = (isset($_POST['department_id']) ? $VALIDATION->input($_POST['department_id']) : "");
    $location_id = (isset($_POST['location_id']) ? $VALIDATION->input($_POST['location_id']) : "");
    $serial_number = (isset($_POST['serial_number']) ? $VALIDATION->input($_POST['serial_number']) : "");
    $code = (isset($_POST['code']) ? $VALIDATION->input($_POST['code']) : "");
    $kw = (isset($_POST['kw']) ? $VALIDATION->input($_POST['kw']) : "");
    $brand_id = (isset($_POST['brand_id']) ? $VALIDATION->input($_POST['brand_id']) : "");
    $model_id = (isset($_POST['model_id']) ? $VALIDATION->input($_POST['model_id']) : "");
    $purchase_date = (isset($_POST['purchase_date']) ? $VALIDATION->input($_POST['purchase_date']) : "");
    $purchase_date = (!empty($purchase_date) ? date("Y-m-d", strtotime(str_replace("/", "-", $purchase_date))) : "");
    $expire_date = (isset($_POST['expire_date']) ? $VALIDATION->input($_POST['expire_date']) : "");
    $expire_date = (!empty($expire_date) ? date("Y-m-d", strtotime(str_replace("/", "-", $expire_date))) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");

    $count = $ASSET->asset_count([$name, $asset_code]);
    if (intval($count) > 0) {
      $VALIDATION->alert("danger", "ข้อมูลซ้ำในระบบ!", "/asset/create");
    }

    $ASSET->asset_create([$name, $asset_code, $type_id, $department_id, $location_id, $brand_id, $model_id, $serial_number, $code, $kw, $purchase_date, $expire_date, $text]);
    $asset_id = $ASSET->last_insert_id();

    foreach ($_POST['item_id'] as $key => $row) {
      $item_id = (isset($_POST['item_id']) ? $_POST['item_id'][$key] : "");
      $item_type = (isset($_POST['item_type']) ? $_POST['item_type'][$key] : "");
      $item_value = (isset($_POST['item_value']) ? $_POST['item_value'][$key] : "");
      $item_value = (intval($item_type) === 4 ?
        date("Y-m-d", strtotime(str_replace("/", "-", $item_value))) : $item_value);

      if (!empty($item_id)) {
        $ASSET->item_create([$asset_id, $item_id, $item_value]);
      }
    }

    foreach ($_FILES['file']['name'] as $key => $row) {
      $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'][$key] : "");
      $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'][$key] : "");
      $file_random = md5(microtime());
      $file_image = ["png", "jpeg", "jpg"];
      $file_document = ["pdf", "doc", "docx", "xls", "xlsx"];
      $file_allow = array_merge($file_image);
      $file_extension = pathinfo(strtolower($file_name), PATHINFO_EXTENSION);

      if (!empty($file_name) && in_array($file_extension, $file_allow)) {
        if (in_array($file_extension, $file_document)) {
          $file_rename = "{$file_random}.{$file_extension}";
          $file_path = (__DIR__ . "/../../Publics/asset/{$file_rename}");
          move_uploaded_file($file_tmp, $file_path);
        }
        if (in_array($file_extension, $file_image)) {
          $file_rename = "{$file_random}.webp";
          $file_path = (__DIR__ . "/../../Publics/asset/{$file_rename}");
          $VALIDATION->image_upload($file_tmp, $file_path);
        }
        $ASSET->file_create([$asset_id, $file_rename]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/asset");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "update") {
  try {
    $asset_id = (isset($_POST['id']) ? $VALIDATION->input($_POST['id']) : "");
    $uuid = (isset($_POST['uuid']) ? $VALIDATION->input($_POST['uuid']) : "");
    $name = (isset($_POST['name']) ? $VALIDATION->input($_POST['name']) : "");
    $asset_code = (isset($_POST['code']) ? $VALIDATION->input($_POST['code']) : "");
    $department_id = (isset($_POST['department_id']) ? $VALIDATION->input($_POST['department_id']) : "");
    $location_id = (isset($_POST['location_id']) ? $VALIDATION->input($_POST['location_id']) : "");
    $serial_number = (isset($_POST['serial_number']) ? $VALIDATION->input($_POST['serial_number']) : "");
    $code = (isset($_POST['code']) ? $VALIDATION->input($_POST['code']) : "");
    $kw = (isset($_POST['kw']) ? $VALIDATION->input($_POST['kw']) : "");
    $brand_id = (isset($_POST['brand_id']) ? $VALIDATION->input($_POST['brand_id']) : "");
    $model_id = (isset($_POST['model_id']) ? $VALIDATION->input($_POST['model_id']) : "");
    $purchase_date = (isset($_POST['purchase_date']) ? $VALIDATION->input($_POST['purchase_date']) : "");
    $purchase_date = (!empty($purchase_date) ? date("Y-m-d", strtotime(str_replace("/", "-", $purchase_date))) : "");
    $expire_date = (isset($_POST['expire_date']) ? $VALIDATION->input($_POST['expire_date']) : "");
    $expire_date = (!empty($expire_date) ? date("Y-m-d", strtotime(str_replace("/", "-", $expire_date))) : "");
    $text = (isset($_POST['text']) ? $VALIDATION->input($_POST['text']) : "");
    $status = (isset($_POST['status']) ? $VALIDATION->input($_POST['status']) : 1);

    $ASSET->asset_update([$name, $asset_code, $department_id, $location_id, $brand_id, $model_id, $serial_number, $code, $kw, $purchase_date, $expire_date, $text, $status, $uuid]);

    foreach ($_POST['item__value'] as $key => $row) {
      $item__id = (isset($_POST['item__id']) ? $_POST['item__id'][$key] : "");
      $item__type = (isset($_POST['item__type']) ? $_POST['item__type'][$key] : "");
      $item__value = (isset($_POST['item__value']) ? $_POST['item__value'][$key] : "");
      $item__value = (intval($item__type) === 4 ?
        date("Y-m-d", strtotime(str_replace("/", "-", $item__value))) : $item__value);

      if (!empty($item__id)) {
        $ASSET->item_update([$item__value, $item__id]);
      } else {
        $ASSET->item_create([$asset_id, $item__type, $item__value]);
      }
    }

    foreach ($_FILES['file']['name'] as $key => $row) {
      $file_name = (isset($_FILES['file']['name']) ? $_FILES['file']['name'][$key] : "");
      $file_tmp = (isset($_FILES['file']['tmp_name']) ? $_FILES['file']['tmp_name'][$key] : "");
      $file_random = md5(microtime());
      $file_image = ["png", "jpeg", "jpg"];
      $file_document = ["pdf", "doc", "docx", "xls", "xlsx"];
      $file_allow = array_merge($file_image);
      $file_extension = pathinfo(strtolower($file_name), PATHINFO_EXTENSION);

      if (!empty($file_name) && in_array($file_extension, $file_allow)) {
        if (in_array($file_extension, $file_document)) {
          $file_rename = "{$file_random}.{$file_extension}";
          $file_path = (__DIR__ . "/../../Publics/asset/{$file_rename}");
          move_uploaded_file($file_tmp, $file_path);
        }
        if (in_array($file_extension, $file_image)) {
          $file_rename = "{$file_random}.webp";
          $file_path = (__DIR__ . "/../../Publics/asset/{$file_rename}");
          $VALIDATION->image_upload($file_tmp, $file_path);
        }
        $ASSET->file_create([$asset_id, $file_rename]);
      }
    }

    $VALIDATION->alert("success", "ดำเนินการเรียบร้อย!", "/asset/edit/{$uuid}");
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "asset-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $uuid = $data['uuid'];
    if (!empty($uuid)) {
      $ASSET->asset_delete([$uuid]);
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

if ($action === "file-delete") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    if (!empty($id)) {
      $ASSET->file_delete([$id]);
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
    $type = (isset($_POST['type']) ? $VALIDATION->input($_POST['type']) : "");
    $department = (isset($_POST['department']) ? $VALIDATION->input($_POST['department']) : "");
    $location = (isset($_POST['location']) ? $VALIDATION->input($_POST['location']) : "");
    $result = $ASSET->asset_data($type, $department, $location);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "type-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ASSET->type_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "type-item") {
  try {
    $data = json_decode(file_get_contents("php://input"), true);
    $type = $data['type'];
    $result = $ASSET->type_item([$type]);

    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "department-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ASSET->department_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "location-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ASSET->location_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "brand-select") {
  try {
    $keyword = (isset($_POST['q']) ? $VALIDATION->input($_POST['q']) : "");
    $result = $ASSET->brand_select($keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

if ($action === "model-select") {
  try {
    $keyword = (isset($_POST['keyword']) ? $VALIDATION->input($_POST['keyword']) : "");
    $brand = (isset($_POST['brand']) ? $VALIDATION->input($_POST['brand']) : "");
    $result = $ASSET->model_select($brand, $keyword);
    echo json_encode($result);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}
