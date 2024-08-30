<?php

namespace App\Classes;

use PDO;

class PreventiveAuthorize
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function authorize_count($data)
  {
    $sql = "SELECT COUNT(*) FROM factory.preventive_authorize
    WHERE user_id = ?
    AND type = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function authorize_create($data)
  {
    $sql = "INSERT INTO factory.preventive_authorize( `user_id`, `type`) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function authorize_delete($data)
  {
    $sql = "UPDATE factory.preventive_authorize SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function authorize_data()
  {
    $sql = "SELECT COUNT(*) FROM factory.preventive_authorize a WHERE a.status = 1";
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

    $sql = "SELECT a.id,a.user_id,CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) fullname,
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
    FROM factory.preventive_authorize a
    LEFT JOIN demo_erp_new.employee_detail b
    ON a.user_id = b.Emp_ID
    WHERE a.status = 1 ";

    if ($keyword) {
      $sql .= " AND (b.Emp_Name LIKE '%{$keyword}%' OR b.Emp_Surname LIKE '%{$keyword}%') ";
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
        $row['fullname'],
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
}
