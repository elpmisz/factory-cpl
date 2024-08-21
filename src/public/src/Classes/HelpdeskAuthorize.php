<?php

namespace App\Classes;

use PDO;

class HelpdeskAuthorize
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function authorize_count($data)
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_authorize
    WHERE user = ?
    AND type = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function authorize_create($data)
  {
    $sql = "INSERT INTO factory.helpdesk_authorize( `user`, `type`) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function authorize_delete($data)
  {
    $sql = "UPDATE factory.helpdesk_authorize SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function authorize_data()
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_authorize a WHERE a.status = 1";
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

    $sql = "SELECT a.id,CONCAT(b.firstname,' ',b.lastname) username,
    (
    CASE
      WHEN a.type = 1 THEN 'ผู้อนุมัติ / ผู้ตรวจสอบ'
      WHEN a.type = 2 THEN 'ผู้จัดการระบบ'
      ELSE NULL
    END
    ) type_name,
    (
    CASE
      WHEN a.type = 1 THEN 'success'
      WHEN a.type = 2 THEN 'primary'
      ELSE NULL
    END
    ) type_color
    FROM factory.helpdesk_authorize a
    LEFT JOIN factory.user b
    ON a.user = b.id
    WHERE a.status = 1 ";

    if ($keyword) {
      $sql .= " AND (b.firstname LIKE '%{$keyword}%' OR b.lastname LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC,a.type ASC ";
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
      $action = "<a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['id']}'>ลบ</a>";
      $type = "<span class='badge badge-{$row['type_color']} font-weight-light'>{$row['type_name']}</span>";
      $data[] = [
        $action,
        $type,
        $row['username'],
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

  public function user_select($keyword)
  {
    $sql = "SELECT a.login id,CONCAT(a.firstname,' ',a.lastname) text
    FROM factory.user a
    LEFT JOIN factory.login b
    ON a.login = b.id
    WHERE b.status = 1";
    if (!empty($keyword)) {
      $sql .= " AND (a.firstname LIKE '%{$keyword}%' OR a.lastname LIKE '{$keyword}') ";
    }
    $sql .= " ORDER BY a.firstname";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
