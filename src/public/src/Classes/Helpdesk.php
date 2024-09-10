<?php

namespace App\Classes;

use PDO;

class Helpdesk
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function helpdesk_card()
  {
    $sql = "SELECT COUNT(*) helpdesk_total,
    (SELECT COUNT(*) FROM factory.helpdesk_request WHERE status = 2) helpdesk_assign,
    (SELECT COUNT(*) FROM factory.helpdesk_request WHERE status IN (3,4,5,6)) helpdesk_work,
    (SELECT COUNT(*) FROM factory.helpdesk_request WHERE status IN (1,7)) helpdesk_approve,
    (SELECT COUNT(*) FROM factory.helpdesk_request WHERE status IN (8,9)) helpdesk_complete
    FROM factory.helpdesk_request a";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function helpdesk_authorize($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.helpdesk_authorize 
    WHERE user_id = ?
    AND type = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function worker_authorize($data)
  {
    $sql = "SELECT COUNT(*)
    FROM factory.helpdesk_request a
    WHERE a.`status` IN (3,4,5,6)
    AND (
      SELECT user_id
      FROM factory.helpdesk_request_process
      WHERE `status` IN (3,4,5,7) 
      AND request_id = a.id
      ORDER BY `status` DESC LIMIT 1
    ) = ? ";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function helpdesk_last()
  {
    $sql = "SELECT last 
    FROM factory.helpdesk_request 
    WHERE YEAR(created) = YEAR(NOW())
    ORDER BY created DESC
    LIMIT 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (!empty($row['last']) ? intval($row['last']) + 1 : 1);
  }

  public function helpdesk_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.helpdesk_request 
    WHERE user_id = ?
    AND service_id = ?
    AND text = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function helpdesk_uuid($data)
  {
    $sql = "SELECT uuid 
    FROM factory.helpdesk_request 
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['uuid']) ? $row['uuid'] : "");
  }

  public function line_token($data)
  {
    $sql = "SELECT b.token
    FROM factory.helpdesk_service a
    LEFT JOIN factory.line_token b
    ON a.line_token_id = b.id
    WHERE a.id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['token']) ? $row['token'] : "");
  }

  public function helpdesk_add($data)
  {
    $sql = "INSERT INTO factory.helpdesk_request(`uuid`, `last`, `user_id`, `service_id`, `asset_id`, `contact`, `text`, `status`) VALUES(uuid(),?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function helpdesk_view($data)
  {
    $sql = "SELECT a.*,CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) ticket,
    CONCAT('คุณ',b.Emp_Name,' ',b.Emp_Surname) username,c.`name` service_name,c.asset,
    CONCAT('[',d.code,'] ',d.`name`) asset_name,d.asset_code asset_assetcode,d.`code` asset_code,
    e.`name` asset_department,f.`name` asset_location,g.`name` asset_brand,h.`name` asset_model,
    (
      SELECT DATE_FORMAT(end,'%d/%m/%Y')
      FROM factory.helpdesk_request_process
      WHERE status IN (3,4,5,7) 
      AND request_id = a.id
      ORDER BY status DESC LIMIT 1
    ) finish,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'รอรับเรื่อง'
        WHEN a.status = 3 THEN 'รับเรื่อง'
        WHEN a.status = 4 THEN 'อยู่ระหว่างดำเนินการ'
        WHEN a.status = 5 THEN 'รออะไหล่ / อุปกรณ์'
        WHEN a.status = 6 THEN 'รอแก้ไข'
        WHEN a.status = 7 THEN 'รอตรวจสอบ'
        WHEN a.status = 8 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 9 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'warning'
        WHEN a.status = 6 THEN 'info'
        WHEN a.status = 7 THEN 'danger'
        WHEN a.status = 8 THEN 'success'
        WHEN a.status = 9 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM factory.helpdesk_request a
    LEFT JOIN demo_erp_new.employee_detail b
    ON a.user_id = b.Emp_ID
    LEFT JOIN factory.helpdesk_service c
    ON a.service_id = c.id 
    LEFT JOIN factory.asset d
    ON a.asset_id = d.id
    LEFT JOIN factory.asset_department e
    ON d.department_id = e.id
    LEFT JOIN factory.asset_location f
    ON d.location_id = f.id
    LEFT JOIN factory.asset_brand g
    ON d.brand_id = g.id 
    LEFT JOIN factory.asset_brand h
    ON d.model_id = h.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function helpdesk_view_update($data)
  {
    $sql = "UPDATE factory.helpdesk_request SET
    asset_id = ?,
    contact = ?,
    text = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function helpdesk_work_update($data)
  {
    $sql = "UPDATE factory.helpdesk_request SET
    oil = ?,
    cable = ?,
    tool = ?,
    work = ?,
    fix = ?,
    `what` = ?,
    `why` = ?,
    `when` = ?,
    pay = ?,
    pr = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function status_update($data)
  {
    $sql = "UPDATE factory.helpdesk_request SET
    status = ?
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function process_add($data)
  {
    $sql = "INSERT INTO factory.helpdesk_request_process(`request_id`, `user_id`, `text`, `end`, `cost`, `file`, `status`) VALUES(?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function process_view($data)
  {
    $sql = "SELECT DATE_FORMAT(b.created,'%d/%m/%Y') `start`,DATE_FORMAT(b.`end`,'%d/%m/%Y') `end`,
    b.text,CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) worker,b.`file`,b.cost,
    (
      CASE
        WHEN b.status = 1 THEN 'รออนุมัติ'
        WHEN b.status = 2 THEN 'รอรับเรื่อง'
        WHEN b.status = 3 THEN 'รับเรื่อง'
        WHEN b.status = 4 THEN 'อยู่ระหว่างดำเนินการ'
        WHEN b.status = 5 THEN 'รออะไหล่ / อุปกรณ์'
        WHEN b.status = 6 THEN 'รอแก้ไข'
        WHEN b.status = 7 THEN 'แก้ไขปัญหาเรียบร้อย'
        WHEN b.status = 8 THEN 'ดำเนินการเรียบร้อย'
        WHEN b.status = 9 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN b.status = 1 THEN 'danger'
        WHEN b.status = 2 THEN 'danger'
        WHEN b.status = 3 THEN 'warning'
        WHEN b.status = 4 THEN 'primary'
        WHEN b.status = 5 THEN 'warning'
        WHEN b.status = 6 THEN 'info'
        WHEN b.status = 7 THEN 'danger'
        WHEN b.status = 8 THEN 'success'
        WHEN b.status = 9 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(b.created, '%d/%m/%Y, %H:%i น.') created
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_request_process b
    ON a.id = b.request_id
    LEFT JOIN demo_erp_new.employee_detail c
    ON b.user_id = c.Emp_ID
    WHERE a.uuid = ?
    ORDER BY b.created DESC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function spare_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.helpdesk_request_spare 
    WHERE request_id = ?
    AND itemcode = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function spare_add($data)
  {
    $sql = "INSERT INTO factory.helpdesk_request_spare(`request_id`, `itemcode`, `quantity`) VALUES(?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function spares_view($data)
  {
    $sql = "SELECT b.id,CONCAT('[',b.itemcode,'] ',c.`name`) itemcode,b.quantity 
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_request_spare b
    ON a.id = b.request_id
    LEFT JOIN factory.spare_item c
    ON b.itemcode = c.code
    WHERE a.uuid =  ?
    AND b.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function spare_delete($data)
  {
    $sql = "UPDATE factory.helpdesk_request_spare SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.helpdesk_request_item 
    WHERE request_id = ?
    AND item_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function item_add($data)
  {
    $sql = "INSERT INTO factory.helpdesk_request_item(`request_id`, `item_id`, `item_type`, `item_value`) VALUES(?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function items_view($data)
  {
    $sql = "SELECT b.id item_id,c.`name` item_name,b.item_type,c.text item_text,
    IF(b.item_type = 4,DATE_FORMAT(b.item_value,'%d/%m/%Y'),b.item_value) item_value
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_request_item b
    ON a.id = b.request_id
    LEFT JOIN factory.helpdesk_service_item c
    ON b.item_id = c.id
    WHERE a.uuid = ?
    AND b.id != ''";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function item_update($data)
  {
    $sql = "UPDATE factory.helpdesk_request_item SET
    item_value = ?
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function file_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.helpdesk_request_file 
    WHERE request_id = ?
    AND name = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function file_add($data)
  {
    $sql = "INSERT INTO factory.helpdesk_request_file( `request_id`, `name`) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function files_view($data)
  {
    $sql = "SELECT b.id file_id,b.`name` file_name
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_request_file b
    ON a.id = b.request_id
    WHERE a.uuid = ?
    AND b.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function file_delete($data)
  {
    $sql = "UPDATE factory.helpdesk_request_file SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function service_date($data)
  {
    $sql = "SELECT b.date
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_service b
    ON a.service_id = b.id
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (!empty($row['date']) ? $row['date'] : "");
  }

  public function approve_check($data)
  {
    $sql = "SELECT approve FROM factory.helpdesk_service WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (!empty($row['approve']) ? $row['approve'] : "");
  }

  public function checker_check($data)
  {
    $sql = "SELECT `check` FROM factory.helpdesk_service WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (!empty($row['check']) ? $row['check'] : "");
  }

  public function asset_check($data)
  {
    $sql = "SELECT asset
    FROM factory.helpdesk_service a
    WHERE a.id = ? ";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch();
    return (isset($row['asset']) ? $row['asset'] : "");
  }

  public function service_item($data)
  {
    $sql = "SELECT a.id,a.name,a.type,a.text,a.required,
    (
      CASE
        WHEN a.required = 1 THEN 'required'
        WHEN a.required = 2 THEN ''
        ELSE NULL
      END
    ) required_name
    FROM factory.helpdesk_service_item a
    WHERE a.service_id = ?
    AND a.status = 1 
    ORDER BY a.created ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function asset_detail($data)
  {
    $sql = "SELECT a.*,
    IF(a.purchase = '0000-00-00', '', DATE_FORMAT(a.purchase, '%d/%m/%Y')) purchase,
    IF(a.expire = '0000-00-00', '', DATE_FORMAT(a.expire, '%d/%m/%Y')) expire,
    b.name type_name,c.name department_name,d.name location_name,
    e.name brand_name,f.name model_name
    FROM factory.asset a
    LEFT JOIN factory.asset_type b 
    ON a.type_id = b.id
    LEFT JOIN factory.asset_department c
    ON a.department_id = c.id
    LEFT JOIN factory.asset_location d
    ON a.location_id = d.id
    LEFT JOIN factory.asset_brand e
    ON a.brand_id = e.id
    LEFT JOIN factory.asset_brand f
    ON a.model_id = f.id
    WHERE a.id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function request_data($user)
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "b.name", "a.text", "c.firstname", "a.id", "a.id", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.uuid,CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) ticket,
    b.`name` service_name,a.text,
    CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) username,
    (
      SELECT CONCAT('คุณ',y.Emp_Name,' ',y.Emp_Surname)
      FROM factory.helpdesk_request_process x
      LEFT JOIN demo_erp_new.employee_detail y
      ON x.user_id = y.Emp_ID
      WHERE x.status IN (3,4,5,7) 
      AND x.request_id = a.id
      ORDER BY x.status DESC LIMIT 1
    ) worker,
    (
      SELECT DATE_FORMAT(end,'%d/%m/%Y')
      FROM factory.helpdesk_request_process
      WHERE status IN (3,4,5,7) 
      AND request_id = a.id
      ORDER BY status DESC LIMIT 1
    ) finish,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'รอรับเรื่อง'
        WHEN a.status = 3 THEN 'รับเรื่อง'
        WHEN a.status = 4 THEN 'อยู่ระหว่างดำเนินการ'
        WHEN a.status = 5 THEN 'รออะไหล่ / อุปกรณ์'
        WHEN a.status = 6 THEN 'รอแก้ไข'
        WHEN a.status = 7 THEN 'รอตรวจสอบ'
        WHEN a.status = 8 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 9 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'warning'
        WHEN a.status = 6 THEN 'info'
        WHEN a.status = 7 THEN 'danger'
        WHEN a.status = 8 THEN 'success'
        WHEN a.status = 9 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    (
      CASE
        WHEN a.status = 1 AND b.approve = 1 THEN 'view'
        WHEN a.status = 2 AND b.approve = 2 THEN 'view'
        ELSE 'complete'
      END
    ) status_page,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_service b
    ON a.service_id = b.id
    LEFT JOIN demo_erp_new.employee_detail c
    ON a.user_id = c.Emp_ID
    LEFT JOIN factory.asset d
    ON a.asset_id = d.id
    WHERE a.id != '' ";
    if (!empty($user)) {
      $sql .= " AND a.user_id = '{$user}' ";
    }
    if (!empty($keyword)) {
      $sql .= " AND (a.text LIKE '%{$keyword}%' OR d.name LIKE '%{$keyword}%' OR d.asset_code LIKE '%{$keyword}%' OR d.code LIKE '%{$keyword}%' OR d.serial_number LIKE '%{$keyword}%' OR CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last DESC ";
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
      $action = "<a href='/helpdesk/{$row['status_page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $action,
        $row['ticket'],
        $row['service_name'],
        str_replace("\n", "<br>", $row['text']),
        $row['username'],
        $row['worker'],
        $row['finish'],
        $row['created']
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

  public function approve_data()
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "b.name", "a.text", "c.firstname", "a.id", "a.id", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.uuid,CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) ticket,
    b.`name` service_name,a.text,
    CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) username,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'รอรับเรื่อง'
        WHEN a.status = 3 THEN 'รับเรื่อง'
        WHEN a.status = 4 THEN 'อยู่ระหว่างดำเนินการ'
        WHEN a.status = 5 THEN 'รออะไหล่ / อุปกรณ์'
        WHEN a.status = 6 THEN 'รอแก้ไข'
        WHEN a.status = 7 THEN 'รอตรวจสอบ'
        WHEN a.status = 8 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 9 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'warning'
        WHEN a.status = 6 THEN 'info'
        WHEN a.status = 7 THEN 'danger'
        WHEN a.status = 8 THEN 'success'
        WHEN a.status = 9 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    (
      CASE
        WHEN a.status = 1 THEN 'approve'
        WHEN a.status = 7 THEN 'check'
        ELSE NULL
      END
    ) status_page,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_service b
    ON a.service_id = b.id
    LEFT JOIN demo_erp_new.employee_detail c
    ON a.user_id = c.Emp_ID
    LEFT JOIN factory.asset d
    ON a.asset_id = d.id
    WHERE a.status IN (1,7) ";
    if (!empty($keyword)) {
      $sql .= " AND (a.text LIKE '%{$keyword}%' OR d.name LIKE '%{$keyword}%' OR d.asset_code LIKE '%{$keyword}%' OR d.code LIKE '%{$keyword}%' OR d.serial_number LIKE '%{$keyword}%' OR CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last DESC ";
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
      $action = "<a href='/helpdesk/{$row['status_page']}/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $action,
        $row['ticket'],
        $row['service_name'],
        str_replace("\n", "<br>", $row['text']),
        $row['username'],
        "",
        "",
        $row['created']
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

  public function assign_data()
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "b.name", "a.text", "c.firstname", "a.id", "a.id", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.uuid,CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) ticket,
    b.`name` service_name,a.text,
    CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) username,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'รอรับเรื่อง'
        WHEN a.status = 3 THEN 'รับเรื่อง'
        WHEN a.status = 4 THEN 'อยู่ระหว่างดำเนินการ'
        WHEN a.status = 5 THEN 'รออะไหล่ / อุปกรณ์'
        WHEN a.status = 6 THEN 'รอแก้ไข'
        WHEN a.status = 7 THEN 'รอตรวจสอบ'
        WHEN a.status = 8 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 9 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'warning'
        WHEN a.status = 6 THEN 'info'
        WHEN a.status = 7 THEN 'danger'
        WHEN a.status = 8 THEN 'success'
        WHEN a.status = 9 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_service b
    ON a.service_id = b.id
    LEFT JOIN demo_erp_new.employee_detail c
    ON a.user_id = c.Emp_ID
    LEFT JOIN factory.asset d
    ON a.asset_id = d.id
    WHERE a.status = 2 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.text LIKE '%{$keyword}%' OR d.name LIKE '%{$keyword}%' OR d.asset_code LIKE '%{$keyword}%' OR d.code LIKE '%{$keyword}%' OR d.serial_number LIKE '%{$keyword}%' OR CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last DESC ";
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
      $action = "<a href='/helpdesk/assign/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $action,
        $row['ticket'],
        $row['service_name'],
        str_replace("\n", "<br>", $row['text']),
        $row['username'],
        "",
        "",
        $row['created']
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

  public function work_data($user)
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "b.name", "a.text", "c.firstname", "a.id", "a.id", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.uuid,CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) ticket,
    b.`name` service_name,a.text,
    CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) username,
    (
      SELECT CONCAT('คุณ',y.Emp_Name,' ',y.Emp_Surname)
      FROM factory.helpdesk_request_process x
      LEFT JOIN demo_erp_new.employee_detail y
      ON x.user_id = y.Emp_ID
      WHERE x.status IN (3,4,5,7) 
      AND x.request_id = a.id
      ORDER BY x.status DESC LIMIT 1
    ) worker,
    (
      SELECT DATE_FORMAT(end,'%d/%m/%Y')
      FROM factory.helpdesk_request_process
      WHERE status IN (3,4,5,7) 
      AND request_id = a.id
      ORDER BY status DESC LIMIT 1
    ) finish,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'รอรับเรื่อง'
        WHEN a.status = 3 THEN 'รับเรื่อง'
        WHEN a.status = 4 THEN 'อยู่ระหว่างดำเนินการ'
        WHEN a.status = 5 THEN 'รออะไหล่ / อุปกรณ์'
        WHEN a.status = 6 THEN 'รอแก้ไข'
        WHEN a.status = 7 THEN 'รอตรวจสอบ'
        WHEN a.status = 8 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 9 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'warning'
        WHEN a.status = 6 THEN 'info'
        WHEN a.status = 7 THEN 'danger'
        WHEN a.status = 8 THEN 'success'
        WHEN a.status = 9 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_service b
    ON a.service_id = b.id
    LEFT JOIN demo_erp_new.employee_detail c
    ON a.user_id = c.Emp_ID
    LEFT JOIN factory.asset d
    ON a.asset_id = d.id
    WHERE a.status IN (3,4,5,6) ";
    if (!empty($user)) {
      $sql .= " AND (
        SELECT user_id
        FROM factory.helpdesk_request_process
        WHERE `status` IN (3,4,5,7) 
        AND request_id = a.id
        ORDER BY `status` DESC LIMIT 1
      ) = '{$user}' ";
    }
    if (!empty($keyword)) {
      $sql .= " AND (a.text LIKE '%{$keyword}%' OR d.name LIKE '%{$keyword}%' OR d.asset_code LIKE '%{$keyword}%' OR d.code LIKE '%{$keyword}%' OR d.serial_number LIKE '%{$keyword}%' OR CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last DESC ";
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
      $action = "<a href='/helpdesk/work/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $action,
        $row['ticket'],
        $row['service_name'],
        str_replace("\n", "<br>", $row['text']),
        $row['username'],
        $row['worker'],
        $row['finish'],
        $row['created']
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

  public function manage_data($status, $service)
  {
    $sql = "SELECT COUNT(*) FROM factory.helpdesk_request";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.last", "b.name", "a.text", "c.firstname", "a.id", "a.id", "a.created"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.uuid,CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) ticket,
    b.`name` service_name,a.text,
    CONCAT('คุณ',c.Emp_Name,' ',c.Emp_Surname) username,
    (
      SELECT CONCAT('คุณ',y.Emp_Name,' ',y.Emp_Surname)
      FROM factory.helpdesk_request_process x
      LEFT JOIN demo_erp_new.employee_detail y
      ON x.user_id = y.Emp_ID
      WHERE x.status IN (3,4,5,7) 
      AND x.request_id = a.id
      ORDER BY x.status DESC LIMIT 1
    ) worker,
    (
      SELECT DATE_FORMAT(end,'%d/%m/%Y')
      FROM factory.helpdesk_request_process
      WHERE status IN (3,4,5,7) 
      AND request_id = a.id
      ORDER BY status DESC LIMIT 1
    ) finish,
    (
      CASE
        WHEN a.status = 1 THEN 'รออนุมัติ'
        WHEN a.status = 2 THEN 'รอรับเรื่อง'
        WHEN a.status = 3 THEN 'รับเรื่อง'
        WHEN a.status = 4 THEN 'อยู่ระหว่างดำเนินการ'
        WHEN a.status = 5 THEN 'รออะไหล่ / อุปกรณ์'
        WHEN a.status = 6 THEN 'รอแก้ไข'
        WHEN a.status = 7 THEN 'รอตรวจสอบ'
        WHEN a.status = 8 THEN 'ดำเนินการเรียบร้อย'
        WHEN a.status = 9 THEN 'รายการถูกยกเลิก'
        ELSE NULL
      END
    ) status_name,
    (
      CASE
        WHEN a.status = 1 THEN 'danger'
        WHEN a.status = 2 THEN 'primary'
        WHEN a.status = 3 THEN 'warning'
        WHEN a.status = 4 THEN 'primary'
        WHEN a.status = 5 THEN 'warning'
        WHEN a.status = 6 THEN 'info'
        WHEN a.status = 7 THEN 'danger'
        WHEN a.status = 8 THEN 'success'
        WHEN a.status = 9 THEN 'danger'
        ELSE NULL
      END
    ) status_color,
    DATE_FORMAT(a.created, '%d/%m/%Y, %H:%i น.') created
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_service b
    ON a.service_id = b.id
    LEFT JOIN demo_erp_new.employee_detail c
    ON a.user_id = c.Emp_ID
    LEFT JOIN factory.asset d
    ON a.asset_id = d.id
    WHERE a.id != '' ";
    if (!empty($keyword)) {
      $sql .= " AND (a.text LIKE '%{$keyword}%' OR d.name LIKE '%{$keyword}%' OR d.asset_code LIKE '%{$keyword}%' OR d.code LIKE '%{$keyword}%' OR d.serial_number LIKE '%{$keyword}%' OR CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) LIKE '%{$keyword}%') ";
    }
    if (!empty($status)) {
      $sql .= " AND a.status = {$status} ";
    }
    if (!empty($service)) {
      $sql .= " AND a.service_id = {$service} ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, a.last DESC ";
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
      $action = "<a href='/helpdesk/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a>";
      $data[] = [
        $action,
        $row['ticket'],
        $row['service_name'],
        str_replace("\n", "<br>", $row['text']),
        $row['username'],
        $row['worker'],
        $row['finish'],
        $row['created']
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

  public function service_select($keyword)
  {
    $sql = "SELECT a.id,a.name `text`
    FROM factory.helpdesk_service a
    WHERE a.`status` = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function asset_select($keyword)
  {
    $sql = "SELECT a.id, CONCAT('[',a.`code`,'] ',a.`name`) text
    FROM factory.asset a
    WHERE a.`status` = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR a.asset_code LIKE '%{$keyword}%' OR a.code LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function spare_select($keyword)
  {
    $sql = "SELECT a.code `id`,CONCAT('[',a.code,'] ',a.name) `text`
    FROM raw_material.items a
    LEFT JOIN raw_material.item_group b 
    ON LEFT(a.code,2) = b.item
    AND a.warehouse = b.warehouse
    WHERE b.checked = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.code LIKE '%{$keyword}%' OR a.name LIKE '%{$keyword}%' ) ";
    }
    $sql .= " LIMIT 50 ";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function pay_select($keyword)
  {
    $sql = "SELECT a.run_no id,a.run_no `text`
    FROM raw_material.issue_slip a
    WHERE a.`status` = 2 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.run_no LIKE '%{$keyword}%' OR a.objective LIKE '%{$keyword}%' OR a.item_remark LIKE '%{$keyword}%') ";
    }
    $sql .= " GROUP BY a.run_no ORDER BY a.run_no DESC LIMIT 50";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function last_insert_id()
  {
    return $this->dbcon->lastInsertId();
  }
}
