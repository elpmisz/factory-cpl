<?php

namespace App\Classes;

use PDO;

class AssetChecklist
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "ASSET-CHECKLIST CLASS";
  }

  public function checklist_create($data)
  {
    $sql = "INSERT INTO factory.asset_checklist(uuid,name,type_id,reference_id) VALUES(UUID(),?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function checklist_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.asset_checklist
    WHERE name = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function checklist_view($data)
  {
    $sql = "SELECT a.id,a.uuid,a.name,a.type_id,a.reference_id,b.name reference_name,a.status
    FROM factory.asset_checklist a
    LEFT JOIN factory.asset_checklist b
    ON a.reference_id = b.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function checklist_update($data)
  {
    $sql = "UPDATE factory.asset_checklist SET
    name = ?,
    type_id = ?,
    reference_id = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function checklist_delete($data)
  {
    $sql = "UPDATE factory.asset_checklist SET
    status = 0,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function checklist_export()
  {
    $sql = "SELECT a.id,a.uuid,a.`name`,IF(a.type_id = 1,'หัวข้อ','รายการตรวจสอบ') type_name,b.`name` subject,
    IF(a.`status` = 1,'ใช้งาน','ระงับการใช้งาน') status
    FROM factory.asset_checklist a
    LEFT JOIN factory.asset_checklist b
    ON a.reference_id = b.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function checklist_data($checklist = null)
  {
    $sql = "SELECT COUNT(*) FROM factory.asset_checklist WHERE status IN (1,2)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.name", "b.name"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.id,a.uuid,a.name,b.name reference_name,
    (
      CASE
        WHEN a.status = 1 THEN 'รายละเอียด'
        WHEN a.status = 2 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'primary'
        WHEN a.status = 2 THEN 'warning'
        ELSE NULL
      END
    ) status_color
    FROM factory.asset_checklist a
    LEFT JOIN factory.asset_checklist b
    ON a.reference_id = b.id
    WHERE a.status IN (1,2) ";

    if (!empty($checklist)) {
      $sql .= " AND (a.id = '{$checklist}' OR a.reference_id = '{$checklist}') ";
    }

    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR b.name LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.type_id ASC, a.name ASC ";
    }

    $sql2 = '';
    if ($limit_length) {
      $sql2 .= "LIMIT {$limit_start}, {$limit_length}";
    }

    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $filter = $stmt->rowCount();
    $stmt = $this->dbcon->prepare($sql . $sql2);
    $stmt->execute();
    $result = $stmt->fetchAll();

    $data = [];
    foreach ($result as $row) {
      $action = "<a href='/asset/checklist/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a> <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['uuid']}'>ลบ</a>";
      $data[] = [
        $action,
        $row['name'],
        $row['reference_name']
      ];
    }

    $output = [
      "draw"    => $draw,
      "recordsTotal"  =>  $total,
      "recordsFiltered" => $filter,
      "data"    => $data
    ];

    return $output;
  }

  public function checklist_select($keyword)
  {
    $sql = "SELECT a.id,a.name `text` 
    FROM factory.asset_checklist a
    WHERE a.type_id = 1
    AND a.status = 1";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
