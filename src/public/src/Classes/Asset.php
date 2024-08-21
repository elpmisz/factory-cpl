<?php

namespace App\Classes;

use PDO;

class Asset
{
  private $dbcon;

  public function __construct()
  {
    $db = new Database();
    $this->dbcon = $db->getConnection();
  }

  public function asset_card()
  {
    $sql = "SELECT COUNT(*) asset_total,
    (SELECT COUNT(*) FROM factory.asset_type WHERE status = 1) asset_type,
    (SELECT COUNT(*) FROM factory.asset_department WHERE status = 1) asset_department,
    (SELECT COUNT(*) FROM factory.asset_location WHERE status = 1) asset_location
    FROM factory.asset a
    WHERE a.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch();
  }

  public function asset_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.asset
    WHERE name = ?
    AND asset_code = ?
    AND status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function asset_create($data)
  {
    $sql = "INSERT INTO factory.asset(`uuid`, `name`, `asset_code`, `type_id`, `department_id`, `location_id`, `brand_id`, `model_id`, `serial_number`, `code`, `kw`, `purchase`, `expire`, `text`) VALUES(UUID(),?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function asset_view($data)
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
    WHERE a.uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch();
  }

  public function asset_update($data)
  {
    $sql = "UPDATE factory.asset SET 
    name = ?,
    asset_code = ?,
    department_id = ?,
    location_id = ?,
    brand_id = ?,
    model_id = ?,
    serial_number = ?,
    code = ?,
    kw = ?,
    purchase = ?,
    expire = ?,
    text = ?,
    status = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function asset_delete($data)
  {
    $sql = "UPDATE factory.asset SET 
    status = 0,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_create($data)
  {
    $sql = "INSERT INTO factory.asset_item(`asset_id`, `type_item_id`, `item_value`) VALUES(?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function item_view($data)
  {
    $sql = "SELECT c.id,b.id type_item_id,b.`name` type_item_name,b.type type_item_type,b.text type_item_text,
    (
      CASE
        WHEN b.required = 1 THEN 'required'
        WHEN b.required = 2 THEN ''
        ELSE NULL
      END
    ) item_required,
    IF(b.`type` = 4,DATE_FORMAT(c.item_value,'%d/%m/%Y'),c.item_value) item_value
    FROM factory.asset a
    LEFT JOIN factory.asset_type_item b
    ON a.type_id = b.type_id
    LEFT JOIN factory.asset_item c
    ON b.id = c.type_item_id
    WHERE a.id = ?
    ORDER BY b.created ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function item_update($data)
  {
    $sql = "UPDATE factory.asset_item SET 
    item_value = ?,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function file_create($data)
  {
    $sql = "INSERT INTO factory.asset_file(`asset_id`, `name`) VALUES(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function file_view($data)
  {
    $sql = "SELECT * 
    FROM factory.asset_file a
    WHERE a.asset_id = ?
    AND a.status = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function file_delete($data)
  {
    $sql = "UPDATE factory.asset_file SET
    status = 0,
    updated = NOW()
    WHERE id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function helpdesk_view($data)
  {
    $sql = "SELECT a.id,a.uuid,CONCAT('HD',YEAR(a.created),LPAD(a.`last`,4,'0')) ticket,b.`name` service_name,
    a.text,DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM factory.helpdesk_request a
    LEFT JOIN factory.helpdesk_service b
    ON a.service_id = b.id
    WHERE a.asset_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function preventive_view($data)
  {
    $sql = "SELECT a.id,a.uuid,CONCAT('PM',YEAR(a.`start`),LPAD(a.`last`,4,'0')) ticket,b.`process`,b.text,
    DATE_FORMAT(a.created,'%d/%m/%Y, %H:%i น.') created
    FROM factory.preventive_request a
    LEFT JOIN factory.preventive_request_item b
    ON a.id = b.request_id
    WHERE b.machine_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function type_select($keyword)
  {
    $sql = "SELECT a.id, a.name `text`
    FROM factory.asset_type a
    WHERE a.`status` = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function department_select($keyword)
  {
    $sql = "SELECT id, name text
    FROM factory.asset_department
    WHERE status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY name ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function location_select($keyword)
  {
    $sql = "SELECT id, name text
    FROM factory.asset_location
    WHERE status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY name ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function user_select($keyword)
  {
    $sql = "SELECT a.Emp_ID id,
    CONCAT('คุณ',a.Emp_Name,' ',a.Emp_Surname,IF(a.Emp_Nickname = '','',CONCAT(' [',a.Emp_Nickname,'] '))) text
    FROM demo_erp_new.employee_detail a
    LEFT JOIN demo_erp_new.employee_work b
    ON a.Emp_ID = b.Emp_ID
    WHERE b.Emp_Status = 1
    AND a.Emp_ID NOT IN ('010101','020202') ";
    if (!empty($keyword)) {
      $sql .= " AND (a.Emp_Name LIKE '%{$keyword}%' OR a.Emp_Surname LIKE '%{$keyword}%' OR a.Emp_Nickname LIKE '%{$keyword}%') ";
    }
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function brand_select($keyword)
  {
    $sql = "SELECT a.id id, a.name text
    FROM factory.asset_brand a
    LEFT JOIN factory.asset_brand b
    ON a.reference_id = b.id
    WHERE a.type_id = 1
    AND a.status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (a.name LIKE '%{$keyword}%' OR b.name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY a.name ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function model_select($brand = null, $keyword)
  {
    $sql = "SELECT id,name text
    FROM factory.asset_brand
    WHERE type_id = 2
    AND reference_id = '{$brand}' 
    AND status = 1 ";
    if (!empty($keyword)) {
      $sql .= " AND (name LIKE '%{$keyword}%') ";
    }
    $sql .= " ORDER BY created ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function type_item($data)
  {
    $sql = "SELECT id,name,type,text,required,
    (
      CASE
        WHEN required = 1 THEN 'required'
        WHEN required = 2 THEN ''
        ELSE NULL
      END
    ) required_name
    FROM factory.asset_type_item
    WHERE type_id = ?
    AND status = 1 
    ORDER BY created ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function report($type = null, $department = null, $location = null)
  {
    $sql = "SELECT a.code,a.asset_code asset,a.serial_number serial,a.name,
    d.name `type`,b.name department,c.name location,
    e.name brand,f.name model,
    CAST(a.kw AS DECIMAL(10,2)) kw,
    IF(a.purchase != '0000-00-00',DATE_FORMAT(a.purchase, '%d/%m/%Y'),'') purchase,
    IF(a.expire != '0000-00-00',DATE_FORMAT(a.expire, '%d/%m/%Y'),'') `expire`,
    a.text,GROUP_CONCAT(DISTINCT CONCAT('คุณ',g.Emp_Name,' ',g.Emp_Surname,IF(g.Emp_Nickname = '','',CONCAT(' [',g.Emp_Nickname,'] ')))) worker,
    (
    CASE
      WHEN a.status = 1 THEN 'ACTIVE'
      WHEN a.status = 2 THEN 'INACTIVE'
      ELSE NULL
    END
    ) status
    FROM factory.asset a
    LEFT JOIN factory.asset_department b
    ON a.department_id = b.id
    LEFT JOIN factory.asset_location c
    ON a.location_id = c.id
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id 
    LEFT JOIN factory.asset_brand e
    ON a.brand_id = e.id
    LEFT JOIN factory.asset_brand f
    ON a.model_id = f.id
    LEFT JOIN demo_erp_new.employee_detail g
    ON FIND_IN_SET(g.Emp_ID, d.worker)
    WHERE a.status IN (1,2) ";

    if (!empty($type)) {
      $sql .= " AND a.type_id = '{$type}' ";
    }
    if (!empty($department)) {
      $sql .= " AND a.department_id = '{$department}' ";
    }
    if (!empty($location)) {
      $sql .= " AND a.location_id = '{$location}' ";
    }

    $sql .= " GROUP BY a.id ORDER BY a.status ASC, d.name ASC, b.name ASC  ";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_NUM);
  }

  public function asset_data($type = null, $department = null, $location = null)
  {
    $sql = "SELECT COUNT(*) FROM factory.asset WHERE status IN (1,2)";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetchColumn();

    $column = ["a.id", "a.code", "a.asset_code", "a.name", "d.name", "b.name", "c.name"];

    $keyword = (isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '');
    $filter_order = (isset($_POST['order']) ? $_POST['order'] : '');
    $order_column = (isset($_POST['order']['0']['column']) ? $_POST['order']['0']['column'] : '');
    $order_dir = (isset($_POST['order']['0']['dir']) ? $_POST['order']['0']['dir'] : '');
    $limit_start = (isset($_POST['start']) ? $_POST['start'] : '');
    $limit_length = (isset($_POST['length']) ? $_POST['length'] : '');
    $draw = (isset($_POST['draw']) ? $_POST['draw'] : '');

    $sql = "SELECT a.id,a.uuid,a.name,a.asset_code,a.serial_number,a.code,d.name type_name,b.name department_name,c.name location_name,
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
    FROM factory.asset a
    LEFT JOIN factory.asset_department b
    ON a.department_id = b.id
    LEFT JOIN factory.asset_location c
    ON a.location_id = c.id
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id 
    WHERE a.status IN (1,2) ";

    if (!empty($type)) {
      $sql .= " AND a.type_id = '{$type}' ";
    }
    if (!empty($department)) {
      $sql .= " AND a.department_id = '{$department}' ";
    }
    if (!empty($location)) {
      $sql .= " AND a.location_id = '{$location}' ";
    }

    if (!empty($keyword)) {
      $sql .= " AND (a.code LIKE '%{$keyword}%' OR a.name LIKE '%{$keyword}%' OR a.serial_number LIKE '%{$keyword}%' OR a.asset_code LIKE '%{$keyword}%') ";
    }

    if ($filter_order) {
      $sql .= " ORDER BY {$column[$order_column]} {$order_dir} ";
    } else {
      $sql .= " ORDER BY a.status ASC, d.name ASC, a.code ASC ";
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
      $action = "<a href='/asset/edit/{$row['uuid']}' class='badge badge-{$row['status_color']} font-weight-light'>{$row['status_name']}</a> <a href='javascript:void(0)' class='badge badge-danger font-weight-light btn-delete' id='{$row['uuid']}'>ลบ</a>";
      $data[] = [
        $action,
        $row['name'],
        $row['asset_code'],
        $row['code'],
        $row['type_name'],
        $row['department_name'],
        $row['location_name']
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

  public function last_insert_id()
  {
    return $this->dbcon->lastInsertId();
  }
}
