<?php

namespace App\Classes;

use PDO;

class Preventive
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "Preventive CLASS";
  }

  public function preventive_card()
  {
    $sql = "SELECT COUNT(*) preventive_total,
    (SELECT COUNT(*) FROM factory.preventive_request WHERE status IN (2,4)) preventive_work,
    (SELECT COUNT(*) FROM factory.preventive_request WHERE status IN (1,3)) preventive_approve,
    (SELECT COUNT(*) FROM factory.preventive_request WHERE status IN (5,6)) preventive_complete
    FROM factory.preventive_request a";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function preventive_authorize($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.preventive_authorize 
    WHERE user_id = ?
    AND type = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function worker_authorize($data)
  {
    $sql = "SELECT COUNT(*)
    FROM factory.preventive_request a
    WHERE a.`status` IN (2,3,4)
    AND FIND_IN_SET(?, a.worker) ";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function preventive_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.preventive_request 
    WHERE user_id = ?
    AND type_id = ?
    AND text = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function preventive_last()
  {
    $sql = "SELECT last 
    FROM factory.preventive_request 
    WHERE YEAR(created) = YEAR(NOW())
    ORDER BY created DESC
    LIMIT 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (!empty($row['last']) ? intval($row['last']) + 1 : 1);
  }

  public function preventive_add($data)
  {
    $sql = "INSERT INTO factory.preventive_request(`uuid`, `last`, `user_id`, `type_id`, `start`, `end`, `worker`, `text`, `status`) VALUES(uuid(),?,?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function preventive_view($data)
  {
    $sql = "SELECT a.id,a.uuid,a.text,a.type_id,d.name type_name,
    CONCAT('PM',YEAR(a.`start`),LPAD(a.`last`,4,'0')) ticket,
    (SELECT COUNT(*) FROM factory.preventive_request_item x WHERE x.request_id = a.id) amount,
    CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) username,
    CONCAT(DATE_FORMAT(a.`start`,'%d/%m/%Y'),' - ',DATE_FORMAT(a.`end`,'%d/%m/%Y')) appointment,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'กำลังดำเนินการ'
        WHEN a.status = 3 THEN 'รอตรวจสอบ'
        WHEN a.status = 4 THEN 'รอแก้ไข'
        WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 6 THEN 'รายการถูกกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'danger'
        WHEN a.status = 5 THEN 'success'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM factory.preventive_request a
    LEFT JOIN demo_erp_new.employee_detail b
    ON a.user_id = b.Emp_ID
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function preventive_update($data)
  {
    $sql = "UPDATE factory.preventive_request SET
    start = ?,
    end = ?,
    worker =?,
    text = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function preventive_approve($data)
  {
    $sql = "UPDATE factory.preventive_request SET
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function preventive_work($data)
  {
    $sql = "UPDATE factory.preventive_request SET
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function worker_view($data)
  {
    $sql = "SELECT b.Emp_ID user_id,CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) username
    FROM factory.preventive_request a
    LEFT JOIN demo_erp_new.employee_detail b
    ON FIND_IN_SET(b.Emp_ID, a.worker)
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function machine_view($data)
  {
    $sql = "SELECT b.id,b.machine_id,b.`process`,b.text,b.`file`,c.asset_code,c.`code`,
    c.`name`,d.`name` type_name,e.`name` department_name,f.`name` location_name
    FROM factory.preventive_request a
    LEFT JOIN factory.preventive_request_item b
    ON a.id = b.request_id
    LEFT JOIN factory.asset c
    ON b.machine_id = c.id
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id
    LEFT JOIN factory.asset_department e
    ON c.department_id = e.id
    LEFT JOIN factory.asset_location f
    ON c.location_id = f.id
    WHERE a.uuid = ?
    AND b.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function process_add($data)
  {
    $sql = "INSERT INTO factory.preventive_request_process(`request_id`, `user_id`, `text`, `status`) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function process_view($data)
  {
    $sql = "SELECT b.text,CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) worker,
    (
      CASE
        WHEN b.status = 1 THEN 'รออนุมัติ'
        WHEN b.status = 2 THEN 'กำลังดำเนินการ'
        WHEN b.status = 3 THEN 'รอตรวจสอบ'
        WHEN b.status = 4 THEN 'รอแก้ไข'
        WHEN b.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN b.status = 6 THEN 'รายการถูกกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN b.status = 1 THEN 'danger'
        WHEN b.status = 2 THEN 'primary'
        WHEN b.status = 3 THEN 'warning'
        WHEN b.status = 4 THEN 'danger'
        WHEN b.status = 5 THEN 'success'
        WHEN b.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(b.created, '%d/%m/%Y, %H:%i น.') created
    FROM factory.preventive_request a
    LEFT JOIN factory.preventive_request_process b
    ON a.id = b.request_id
    LEFT JOIN demo_erp_new.employee_detail c
    ON b.user_id = c.Emp_ID
    WHERE a.uuid = ?
    ORDER BY b.id DESC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function item_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.preventive_request_item 
    WHERE request_id = ?
    AND machine_id = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_add($data)
  {
    $sql = "INSERT INTO factory.preventive_request_item(`request_id`, `machine_id`, `process`, `text`, `file`) VALUES(?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_update($data)
  {
    $sql = "UPDATE factory.preventive_request_item SET
    process = ?,
    text = ?,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_delete($data)
  {
    $sql = "UPDATE factory.preventive_request_item SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function file_add($data)
  {
    $sql = "UPDATE factory.preventive_request_item SET
    file = ?,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function file_delete($data)
  {
    $sql = "UPDATE factory.preventive_request_item SET
    file = '',
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function checklist_check($data)
  {
    $sql = "SELECT c.id
    FROM factory.asset_type a
    LEFT JOIN factory.asset_checklist b
    ON a.checklist = b.id
    LEFT JOIN factory.asset_checklist c
    ON b.id = c.reference_id
    WHERE a.id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (!empty($row['id']) ? $row['id'] : "");
  }

  public function checklist_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.preventive_request_checklist 
    WHERE item_id = ?
    AND checklist_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function checklist_add($data)
  {
    $sql = "INSERT INTO factory.preventive_request_checklist(`item_id`, `checklist_id`, `result`) VALUES(?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function checklist_update($data)
  {
    $sql = "UPDATE factory.preventive_request_checklist SET
    result = ?,
    updated = NOW()
    WHERE id = ?
    AND item_id = ?
    AND checklist_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function asset_worker($data)
  {
    $sql = "SELECT b.Emp_ID id,CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) username
    FROM factory.asset_type a
    LEFT JOIN demo_erp_new.employee_detail b
    ON FIND_IN_SET(b.Emp_ID, a.worker)
    WHERE a.id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function asset_by_type($data)
  {
    $sql = "SELECT a.id,a.`code`,a.asset_code,a.`name` asset_name,b.`name` type_name,c.`name` department_name,d.`name` location_name
    FROM factory.asset a
    LEFT JOIN factory.asset_type b
    ON a.type_id = b.id
    LEFT JOIN factory.asset_department c
    ON a.department_id = c.id
    LEFT JOIN factory.asset_location d
    ON a.location_id = d.id
    WHERE a.type_id = ?
    AND a.`status` = 1
    ORDER BY a.code ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function asset_detail($data)
  {
    $sql = "SELECT a.id,a.`code`,a.asset_code,a.`name` asset_name,b.`name` type_name,c.`name` department_name,d.`name` location_name
    FROM factory.asset a
    LEFT JOIN factory.asset_type b
    ON a.type_id = b.id
    LEFT JOIN factory.asset_department c
    ON a.department_id = c.id
    LEFT JOIN factory.asset_location d
    ON a.location_id = d.id
    WHERE a.id = ?
    AND a.`status` = 1
    ORDER BY a.code ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function checklist_detail($data)
  {
    $sql = "SELECT y.id,x.item_id,x.checklist_id,x.checklist_name,y.result,y.updated
    FROM (
      SELECT a.id item_id,d.id checklist_id,d.`name` checklist_name
      FROM factory.preventive_request_item a
      LEFT JOIN factory.asset b
      ON a.machine_id = b.id
      LEFT JOIN factory.asset_type c
      ON b.type_id = c.id
      LEFT JOIN factory.asset_checklist d
      ON c.checklist = d.reference_id
      WHERE a.id = ?
    ) x
    LEFT JOIN factory.preventive_request_checklist y
    ON x.item_id = y.item_id
    AND x.checklist_id = y.checklist_id
    ORDER BY x.checklist_name";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function request_data($user)
  {
    $sql = "SELECT COUNT(*) FROM factory.preventive_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "a.start", "c.Emp_Name", "a.type_id", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.id,a.uuid,a.text,d.name type_name,
    CONCAT('PM',YEAR(a.`start`),LPAD(a.`last`,4,'0')) ticket,
    (SELECT COUNT(*) FROM factory.preventive_request_item x WHERE x.request_id = a.id) amount,
    CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) username,
    GROUP_CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) worker,
    CONCAT(DATE_FORMAT(a.`start`,'%d/%m/%Y'),',',DATE_FORMAT(a.`end`,'%d/%m/%Y')) appointment,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'กำลังดำเนินการ'
        WHEN a.status = 3 THEN 'รอตรวจสอบ'
        WHEN a.status = 4 THEN 'รอแก้ไข'
        WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 6 THEN 'รายการถูกกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'danger'
        WHEN a.status = 5 THEN 'success'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    (
      CASE
        WHEN a.status = 1 THEN 'view'
        ELSE 'complete'
      END
    ) status_page,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM factory.preventive_request a
    LEFT JOIN demo_erp_new.employee_detail b
    ON a.user_id = b.Emp_ID
    LEFT JOIN demo_erp_new.employee_detail c
    ON FIND_IN_SET(c.Emp_ID, a.worker)
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id
    WHERE a.user_id = '{$user}' ";

    if (!empty($keyword)) {
      $sql .= " AND (CONCAT('PM',YEAR(a.created),LPAD(a.`last`,4,0)) LIKE '%{$keyword}%' OR DATE_FORMAT(a.`start`, '%d/%m/%Y') LIKE '%{$keyword}%' OR DATE_FORMAT(a.`end`, '%d/%m/%Y') LIKE '%{$keyword}%' OR b.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%' OR DATE_FORMAT(a.created, '%d/%m/%Y') LIKE '%{$keyword}%') ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last ASC ";
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
      $action = "<a href='/preventive/{$row['status_page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      if (!empty($row['id'])) {
        $data[] = [
          $action,
          $row['ticket'],
          str_replace(",", "<br>", $row['appointment']),
          str_replace(",", "<br>", $row['worker']),
          $row['amount'],
          str_replace("(", "<br>(", $row['type_name']),
          str_replace("\n", "<br>", $row['text']),
          str_replace(",", "<br>", $row['created']),
        ];
      }
    }

    $output = [
      "draw"    => $draw,
      "recordsTotal"  =>  $total,
      "recordsFiltered" => $filter,
      "data"    => $data
    ];

    return $output;
  }

  public function approve_data()
  {
    $sql = "SELECT COUNT(*) FROM factory.preventive_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "a.start", "c.Emp_Name", "a.type_id", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.id,a.uuid,a.text,d.name type_name,
    CONCAT('PM',YEAR(a.`start`),LPAD(a.`last`,4,'0')) ticket,
    (SELECT COUNT(*) FROM factory.preventive_request_item x WHERE x.request_id = a.id) amount,
    CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) username,
    GROUP_CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) worker,
    CONCAT(DATE_FORMAT(a.`start`,'%d/%m/%Y'),',',DATE_FORMAT(a.`end`,'%d/%m/%Y')) appointment,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'กำลังดำเนินการ'
        WHEN a.status = 3 THEN 'รอตรวจสอบ'
        WHEN a.status = 4 THEN 'รอแก้ไข'
        WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 6 THEN 'รายการถูกกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'danger'
        WHEN a.status = 5 THEN 'success'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    (
      CASE
        WHEN a.status = 1 THEN 'approve'
        WHEN a.status = 3 THEN 'check'
        ELSE NULL
      END
    ) status_page,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM factory.preventive_request a
    LEFT JOIN demo_erp_new.employee_detail b
    ON a.user_id = b.Emp_ID
    LEFT JOIN demo_erp_new.employee_detail c
    ON FIND_IN_SET(c.Emp_ID, a.worker)
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id
    WHERE a.status IN (1,3) ";

    if (!empty($keyword)) {
      $sql .= " AND (CONCAT('PM',YEAR(a.created),LPAD(a.`last`,4,0)) LIKE '%{$keyword}%' OR DATE_FORMAT(a.`start`, '%d/%m/%Y') LIKE '%{$keyword}%' OR DATE_FORMAT(a.`end`, '%d/%m/%Y') LIKE '%{$keyword}%' OR b.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%' OR DATE_FORMAT(a.created, '%d/%m/%Y') LIKE '%{$keyword}%') ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last ASC ";
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
      $action = "<a href='/preventive/{$row['status_page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      if (!empty($row['id'])) {
        $data[] = [
          $action,
          $row['ticket'],
          str_replace(",", "<br>", $row['appointment']),
          str_replace(",", "<br>", $row['worker']),
          $row['amount'],
          str_replace("(", "<br>(", $row['type_name']),
          str_replace("\n", "<br>", $row['text']),
          str_replace(",", "<br>", $row['created']),
        ];
      }
    }

    $output = [
      "draw"    => $draw,
      "recordsTotal"  =>  $total,
      "recordsFiltered" => $filter,
      "data"    => $data
    ];

    return $output;
  }

  public function work_data($user)
  {
    $sql = "SELECT COUNT(*) FROM factory.preventive_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "a.start", "c.Emp_Name", "a.type_id", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.id,a.uuid,a.text,d.name type_name,
    CONCAT('PM',YEAR(a.`start`),LPAD(a.`last`,4,'0')) ticket,
    (SELECT COUNT(*) FROM factory.preventive_request_item x WHERE x.request_id = a.id) amount,
    CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) username,
    GROUP_CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) worker,
    CONCAT(DATE_FORMAT(a.`start`,'%d/%m/%Y'),',',DATE_FORMAT(a.`end`,'%d/%m/%Y')) appointment,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'กำลังดำเนินการ'
        WHEN a.status = 3 THEN 'รอตรวจสอบ'
        WHEN a.status = 4 THEN 'รอแก้ไข'
        WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 6 THEN 'รายการถูกกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'danger'
        WHEN a.status = 5 THEN 'success'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM factory.preventive_request a
    LEFT JOIN demo_erp_new.employee_detail b
    ON a.user_id = b.Emp_ID
    LEFT JOIN demo_erp_new.employee_detail c
    ON FIND_IN_SET(c.Emp_ID, a.worker)
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id
    WHERE FIND_IN_SET({$user}, a.worker) ";

    if (!empty($keyword)) {
      $sql .= " AND (CONCAT('PM',YEAR(a.created),LPAD(a.`last`,4,0)) LIKE '%{$keyword}%' OR DATE_FORMAT(a.`start`, '%d/%m/%Y') LIKE '%{$keyword}%' OR DATE_FORMAT(a.`end`, '%d/%m/%Y') LIKE '%{$keyword}%' OR b.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%' OR DATE_FORMAT(a.created, '%d/%m/%Y') LIKE '%{$keyword}%') ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last ASC ";
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
      $action = "<a href='/preventive/work/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      if (!empty($row['id'])) {
        $data[] = [
          $action,
          $row['ticket'],
          str_replace(",", "<br>", $row['appointment']),
          str_replace(",", "<br>", $row['worker']),
          $row['amount'],
          str_replace("(", "<br>(", $row['type_name']),
          str_replace("\n", "<br>", $row['text']),
          str_replace(",", "<br>", $row['created']),
        ];
      }
    }

    $output = [
      "draw"    => $draw,
      "recordsTotal"  =>  $total,
      "recordsFiltered" => $filter,
      "data"    => $data
    ];

    return $output;
  }

  public function manage_data()
  {
    $sql = "SELECT COUNT(*) FROM factory.preventive_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "a.start", "c.Emp_Name", "a.type_id", "a.text", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.id,a.uuid,a.text,d.name type_name,
    CONCAT('PM',YEAR(a.`start`),LPAD(a.`last`,4,'0')) ticket,
    (SELECT COUNT(*) FROM factory.preventive_request_item x WHERE x.request_id = a.id) amount,
    CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) username,
    GROUP_CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) worker,
    CONCAT(DATE_FORMAT(a.`start`,'%d/%m/%Y'),',',DATE_FORMAT(a.`end`,'%d/%m/%Y')) appointment,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'กำลังดำเนินการ'
        WHEN a.status = 3 THEN 'รอตรวจสอบ'
        WHEN a.status = 4 THEN 'รอแก้ไข'
        WHEN a.status = 5 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 6 THEN 'รายการถูกกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'danger'
        WHEN a.status = 5 THEN 'success'
        WHEN a.status = 6 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM factory.preventive_request a
    LEFT JOIN demo_erp_new.employee_detail b
    ON a.user_id = b.Emp_ID
    LEFT JOIN demo_erp_new.employee_detail c
    ON FIND_IN_SET(c.Emp_ID, a.worker)
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id
    WHERE a.id != '' ";

    if (!empty($keyword)) {
      $sql .= " AND (CONCAT('PM',YEAR(a.created),LPAD(a.`last`,4,0)) LIKE '%{$keyword}%' OR DATE_FORMAT(a.`start`, '%d/%m/%Y') LIKE '%{$keyword}%' OR DATE_FORMAT(a.`end`, '%d/%m/%Y') LIKE '%{$keyword}%' OR b.name LIKE '%{$keyword}%' OR a.text LIKE '%{$keyword}%' OR DATE_FORMAT(a.created, '%d/%m/%Y') LIKE '%{$keyword}%') ";
    }

    $sql .= " GROUP BY a.id ";

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last ASC ";
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
      $action = "<a href='/preventive/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      if (!empty($row['id'])) {
        $data[] = [
          $action,
          $row['ticket'],
          str_replace(",", "<br>", $row['appointment']),
          str_replace(",", "<br>", $row['worker']),
          $row['amount'],
          str_replace("(", "<br>(", $row['type_name']),
          str_replace("\n", "<br>", $row['text']),
          str_replace(",", "<br>", $row['created']),
        ];
      }
    }

    $output = [
      "draw"    => $draw,
      "recordsTotal"  =>  $total,
      "recordsFiltered" => $filter,
      "data"    => $data
    ];

    return $output;
  }

  public function machine_select($keyword, $id, $type)
  {
    $sql = "SELECT a.id,CONCAT('[',a.code,'] ',a.name) `text`
    FROM factory.asset a
    WHERE a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.code LIKE '%{$keyword}%' OR a.name LIKE '%{$keyword}%' OR a.asset_code LIKE '%{$keyword}%')";
    }
    if (!empty($type)) {
      $sql .= " AND a.id NOT IN (SELECT machine_id FROM factory.preventive_request_item WHERE request_id = '{$id}' AND status = 1) ";
    }
    if (!empty($type)) {
      $sql .= " AND a.type_id = '{$type}' ";
    }
    $sql .= " ORDER BY a.code ASC ,a.name ASC LIMIT 20";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function last_insert_id()
  {
    return $this->dbcon->lastInsertId();
  }
}
