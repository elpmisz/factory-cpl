<?php

namespace App\Classes;

use PDO;

class User
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "USER CLASS";
  }

  public function user_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM demo_erp_new.employee_user a
    WHERE a.Username = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function user_status($data)
  {
    $sql = "SELECT b.Emp_Status
    FROM demo_erp_new.employee_detail a
    LEFT JOIN demo_erp_new.employee_work b
    ON a.Emp_ID = b.Emp_ID
    LEFT JOIN demo_erp_new.department c
    ON b.Emp_Department = c.Dep_ID
    LEFT JOIN demo_erp_new.employee_user d
    ON a.Emp_ID = d.Emp_ID
    WHERE d.Username = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (!empty($row['status']) ? $row['status'] : "");
  }

  public function user_view($data)
  {
    $sql = "SELECT a.Emp_ID user_id,a.Emp_Name firstname,a.Emp_Surname lastname,
    CONCAT('คุณ',a.Emp_Name,' ',a.Emp_Surname) fullname,b.Emp_Department department,
    a.Emp_Mobile contact,d.Email email,d.Username,d.P5ssword,e.service
    FROM demo_erp_new.employee_detail a
    LEFT JOIN demo_erp_new.employee_work b
    ON a.Emp_ID = b.Emp_ID
    LEFT JOIN demo_erp_new.department c
    ON b.Emp_Department = c.Dep_ID
    LEFT JOIN demo_erp_new.employee_user d
    ON a.Emp_ID = d.Emp_ID
    LEFT JOIN factory.service_authorize e
    ON a.Emp_ID = e.user_id
    WHERE d.Username = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function user_update($data)
  {
    $sql = "UPDATE demo_erp_new.employee_detail SET 
    Emp_Name = ?,
    Emp_Surname = ?,
    Emp_Mobile = ?
    WHERE Emp_ID = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function user_read()
  {
    $sql = "SELECT a.Emp_ID user_id,a.Emp_Name firstname,a.Emp_Surname lastname,
    CONCAT('คุณ',a.Emp_Name,' ',a.Emp_Surname) fullname,
    b.Emp_Department department_id,c.Dep_Name department_name,e.service
    FROM demo_erp_new.employee_detail a
    LEFT JOIN demo_erp_new.employee_work b
    ON a.Emp_ID = b.Emp_ID
    LEFT JOIN demo_erp_new.department c
    ON b.Emp_Department = c.Dep_ID
    LEFT JOIN demo_erp_new.employee_user d
    ON a.Emp_ID = d.Emp_ID
    LEFT JOIN factory.service_authorize e
    ON a.Emp_ID = e.user_id
    WHERE b.Emp_Status = 1
    AND a.Emp_ID NOT IN ('010101','020202')
    AND (c.Company_ID = 7 OR (c.Company_ID = 2 AND (b.Emp_Department = 45 || b.position_id IN ('T'))))
    ORDER BY c.Company_ID ASC,b.Emp_Department ASC,FIELD(b.position_id,'T','M','S','O','W'),b.Emp_Po_ID DESC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function auth_count($data)
  {
    $sql = "SELECT COUNT(*) FROM factory.service_authorize WHERE user_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function auth_add($data)
  {
    $sql = "INSERT INTO factory.service_authorize(user_id,service) VALUEs(?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function auth_update($data)
  {
    $sql = "UPDATE factory.service_authorize SET
    service = ?
    WHERE user_id = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function password_random($data)
  {
    $sql = "UPDATE factory.login SET
    password = ?
    WHERE email = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function password_change($data)
  {
    $sql = "UPDATE demo_erp_new.employee_user SET
    Password = ?,
    P5ssword = ?
    WHERE Emp_ID = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }
}
