<?php

namespace App\Classes;

use PDO;

class Service
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "SERVICE CLASS";
  }

  public function service_count($data)
  {
    $sql = "SELECT COUNT(*) 
    FROM factory.service a
    WHERE a.name = ?";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetchColumn();
  }

  public function service_add($data)
  {
    $sql = "INSERT INTO factory.service(`uuid`, `sequence`, `name`, `link`, `table_name`) VALUES(uuid(),?,?,?,?)";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function service_update($data)
  {
    $sql = "UPDATE factory.service SET
    sequence = ?,
    name = ?,
    link = ?,
    table_name = ?,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }

  public function service_read()
  {
    $sql = "SELECT * FROM factory.service a WHERE a.status = 1 ORDER BY a.sequence ASC";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function service_delete($data)
  {
    $sql = "UPDATE factory.service SET
    status = 2,
    updated = NOW()
    WHERE uuid = ?";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }
}
