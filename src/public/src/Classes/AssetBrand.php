<?php

namespace App\Classes;

use PDO;

class AssetBrand
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "ASSET-BRAND CLASS";
  }

  public function brand_create($data)
  {
    $sql = "INSERT INTO factory.asset_brand(uuid,name,type_id,reference_id) VALUES(UUID(),?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function brand_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.asset_brand
    WHERE name = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function brand_view($data)
  {
    $sql = "SELECT a.id,a.uuid,a.name,a.type_id,a.reference_id,b.name reference_name,a.status
    FROM factory.asset_brand a
    LEFT JOIN factory.asset_brand b
    ON a.reference_id = b.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function brand_update($data)
  {
    $sql = "UPDATE factory.asset_brand SET
    name = ?,
    type_id = ?,
    reference_id = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function brand_delete($data)
  {
    $sql = "UPDATE factory.asset_brand SET
    status = 0,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function brand_export()
  {
    $sql = "SELECT a.id,a.uuid,a.`name`,IF(a.type_id = 1,'ยี่ห้อ','รุ่น') type_name,b.`name` brand,
    IF(a.`status` = 1,'ใช้งาน','ระงับการใช้งาน') status
    FROM factory.asset_brand a
    LEFT JOIN factory.asset_brand b
    ON a.reference_id = b.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function brand_data($brand = null)
  {
    $sql = "SELECT COUNT(*) FROM factory.asset_brand WHERE status IN (1,2)";
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
    FROM factory.asset_brand a
    LEFT JOIN factory.asset_brand b
    ON a.reference_id = b.id
    WHERE a.status IN (1,2) ";

    if (!empty($brand)) {
      $sql .= " AND (a.id = '{$brand}' OR a.reference_id = '{$brand}') ";
    }

    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR b.name LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.type_id ASC, b.name ASC ";
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
      $action = "<a href='/asset/brand/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a> <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['uuid']}'>ลบ</a>";
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

  public function brand_select($keyword)
  {
    $sql = "SELECT a.id,a.name `text` 
    FROM factory.asset_brand a
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
