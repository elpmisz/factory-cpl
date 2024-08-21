<?php

namespace App\Classes;

use PDO;

class HelpdeskService
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function service_count($data)
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_service
    WHERE name = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function service_create($data)
  {
    $sql = "INSERT INTO factory.helpdesk_service(`uuid`, `name`, `date`, `line_token_id`, `asset`, `approve`, `check`) VALUES(uuid(),?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function service_view($data)
  {
    $sql = "SELECT a.*,b.`name` line_token_name 
    FROM factory.helpdesk_service a
    LEFT JOIN factory.line_token b
    ON a.line_token_id = b.id 
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function service_update($data)
  {
    $sql = "UPDATE factory.helpdesk_service SET
    name = ?,
    date = ?,
    line_token_id = ?,
    asset = ?,
    approve = ?,
    `check` = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function service_delete($data)
  {
    $sql = "UPDATE factory.helpdesk_service SET
    status = 0,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.helpdesk_service_item
    WHERE service_id = ?
    AND name = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_create($data)
  {
    $sql = "INSERT INTO factory.helpdesk_service_item(service_id,name,type,text,required) VALUES(?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_view($data)
  {
    $sql = "SELECT b.*
    FROM factory.helpdesk_service a
    LEFT JOIN factory.helpdesk_service_item b
    ON a.id = b.service_id
    WHERE a.uuid =  ?
    AND b.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function item_update($data)
  {
    $sql = "UPDATE factory.helpdesk_service_item SET
    name = ?,
    type = ?,
    text = ?,
    required = ?
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_delete($data)
  {
    $sql = "UPDATE factory.helpdesk_service_item SET
    status = 0
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function service_data()
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_service a WHERE a.status IN (1,2)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ['a.id', 'a.type_id', 'b.Emp_Name'];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.id,a.uuid,a.name,a.date,a.asset,a.approve,a.check,
    (
      CASE
        WHEN a.status = 1 THEN 'ใช้งาน'
        WHEN a.status = 2 THEN 'ระงับการใช้งาน'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'primary'
        WHEN a.status = 2 THEN 'danger'
        ELSE NULL
      END
    ) status_color
    FROM factory.helpdesk_service a
    WHERE a.status IN (1,2) ";

    if ($keyword) {
      $sql .= " AND (b.firstname LIKE '%{$keyword}%' OR b.lastname LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC,a.id ASC ";
    }

    $sql2 = "";
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
      $action = "<a href='/helpdesk/service/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a> <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['uuid']}'>ลบ</a>";
      $data[] = [
        $action,
        $row['name'],
        (intval($row['date']) === 0 ? "-" : $row['date']),
        (intval($row['asset']) === 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'),
        (intval($row['approve']) === 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'),
        (intval($row['check']) === 1 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>'),
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

  public function line_token_select($keyword)
  {
    $sql = "SELECT a.id,a.`name` `text`
    FROM factory.line_token a
    WHERE a.`status` = 1";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function last_insert_id()
  {
    return $this->dbcon->lastInsertId();
  }
}
