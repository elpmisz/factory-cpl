<?php

namespace App\Classes;

use PDO;

class PreventiveScript
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "Preventive Script CLASS";
  }

  public function machine_type($data)
  {
    $sql = "SELECT a.id machine_id,a.name machine_name
    FROM factory.asset a
    WHERE a.type_id = ?
    AND a.`status` = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchAll();
  }

  public function weekly()
  {
    $sql = "SELECT a.id type_id,a.`name` type_name,a.worker
    FROM factory.asset_type a
    WHERE a.weekly = 1
    AND a.`status` = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function monthly()
  {
    $sql = "SELECT a.id type_id,a.`name` type_name,a.worker
    FROM factory.asset_type a
    WHERE a.monthly = 1
    AND a.`status` = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function month()
  {
    $sql = "SELECT a.id type_id,a.`name` type_name,a.worker,a.month
    FROM factory.asset_type a
    WHERE a.weekly != 1
    AND a.monthly != 1
    AND a.`month` != ''
    AND a.`status` = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }
}
