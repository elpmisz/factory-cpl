<?php

namespace App\Classes;

use PDO;

class Api
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "API CLASS";
  }

  public function asset_read()
  {
    $sql = "SELECT a.id,a.name asset_name,a.asset_code,a.`code`,a.serial_number,a.kw,
    IF(a.purchase = '0000-00-00', '', DATE_FORMAT(a.purchase, '%d/%m/%Y')) purchase,
    IF(a.expire = '0000-00-00', '', DATE_FORMAT(a.expire, '%d/%m/%Y')) expire,
    b.name type_name,c.name department_name,d.name location_name,
    e.name brand_name,f.name model_name,a.text,
    IF(a.`status` = 1,'ใช้งาน','ระงับการใช้งาน') status_name
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
    ON a.model_id = f.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function preventive_read()
  {
    $sql = "SELECT a.id,
    CONCAT('PM',YEAR(a.`start`),LPAD(a.`last`,4,'0')) ticket,
    CONCAT(b.firstname,' ',b.lastname) username,d.name type_name,
    a.`start`, a.`end`,a.text,
    e.`name` asset_name,c.`process` preventive_process,c.text preventive_text,
    (
      CASE
        WHEN a.status IN (1,3) THEN 'รออนุมัติ / ตรวจสอบ'
        WHEN a.status IN (2,4) THEN 'กำลังดำเนินการ'
        WHEN a.status IN (5,6) THEN 'ดำเนินการเรียบร้อย'
        ELSE NULL
      END
    ) status_name,a.created
    FROM factory.preventive_request a
    LEFT JOIN factory.user b
    ON a.user_id = b.id
    LEFT JOIN factory.preventive_request_item c
    ON a.id = c.request_id
    LEFT JOIN factory.asset_type d
    ON a.type_id = d.id
    LEFT JOIN factory.asset e
    ON c.machine_id = e.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function helpdesk_read()
  {
    $sql = "SELECT a.id,a.contact,a.text,
    CONCAT(b.firstname,' ',b.lastname) username,c.`name` service_name,
    d.`name` asset_name,
    (
      CASE
        WHEN a.status IN (1,7) THEN 'รออนุมัติ / ตรวจสอบ'
        WHEN a.status = 2 THEN 'รอรับเรื่อง'
        WHEN a.status IN (3,4,5,6) THEN 'กำลังดำเนินการ'
        WHEN a.status IN (8,9) THEN 'ดำเนินการเรียบร้อย'
        ELSE NULL
      END
    ) status_name,
    (
      SELECT end
      FROM factory.helpdesk_request_process
      WHERE status IN (3,4,5,7) 
      AND request_id = a.id
      ORDER BY status DESC LIMIT 1
    ) finish,a.created
    FROM factory.helpdesk_request a
    LEFT JOIN factory.user b
    ON a.user_id = b.id
    LEFT JOIN factory.helpdesk_service c
    ON a.service_id = c.id 
    LEFT JOIN factory.asset d
    ON a.asset_id = d.id";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
