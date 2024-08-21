<?php

namespace App\Classes;

use PDO;

class System
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "SYSTEM CLASS";
  }

  public function system_read()
  {
    $sql = "SELECT * FROM factory.system WHERE id = 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function system_update($data)
  {
    $sql = "UPDATE factory.system SET
    name = ?,
    email = ?,
    password_email = ?,
    password_default = ?,
    updated = NOW()
    WHERE id = 1";
    $stmt = $this->dbcon->prepare($sql);
    return $stmt->execute($data);
  }
}
