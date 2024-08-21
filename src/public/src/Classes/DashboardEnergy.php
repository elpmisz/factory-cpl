<?php

namespace App\Classes;

use PDO;

class DashboardEnergy
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "DASHBOARD-ENERGY CLASS";
  }

  public function energy_card()
  {
    $sql = "SELECT SUM(a.kwh_diff) total,
    SUM(IF(YEAR(a.date_start) = YEAR(DATE_SUB(NOW(), INTERVAL 1 DAY)),a.kwh_diff,0)) `year`,
    SUM(IF(YEAR(a.date_start) = YEAR(DATE_SUB(NOW(), INTERVAL 1 DAY)) AND MONTH(a.date_start) = MONTH(DATE_SUB(NOW(), INTERVAL 1 DAY)),a.kwh_diff,0)) `month`,
    SUM(IF(DATE(a.date_start) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)),a.kwh_diff,0)) `date`
    FROM factory.energy_data a";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function energy_daily($machine, $start, $end)
  {
    $sql = "SELECT DATE_FORMAT(a.date_start, '%d/%m/%Y') `date`,SUM(a.kwh_diff) total
    FROM factory.energy_data a
    WHERE a.date_start != ''
    AND YEAR(a.date_start) = YEAR(NOW()) ";
    if (!empty($machine)) {
      $sql .= " AND (a.machine_number = '{$machine}') ";
    }
    if (!empty($start) && !empty($end)) {
      $sql .= " AND (DATE(a.date_start) BETWEEN STR_TO_DATE('{$start}', '%d/%m/%Y') AND STR_TO_DATE('{$end}', '%d/%m/%Y')) ";
    }
    if (!empty($start) && empty($end)) {
      $sql .= " AND (DATE(a.date_start) = STR_TO_DATE('{$start}', '%d/%m/%Y')) ";
    }
    $sql .= "GROUP BY a.date_start";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function machine_select($keyword)
  {
    $sql = "SELECT a.machine_number id, CONCAT('[',a.machine_number,'] ',a.machine_name) text
    FROM factory.energy_data a ";
    if (!empty($keyword)) {
      $sql .= " WHERE a.machine_number LIKE '%{$keyword}%' ";
    }
    $sql .= "GROUP BY a.machine_number";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function job_select($keyword)
  {
    $sql = "SELECT a.job id, CONCAT('[',a.job,'] ',a.bom) text
    FROM factory.counter_data a ";
    if (!empty($keyword)) {
      $sql .= " WHERE a.job LIKE '%{$keyword}%' ";
    }
    $sql .= "GROUP BY a.job";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function shift_select()
  {
    $sql = "SELECT a.shift id, a.shift text
    FROM factory.counter_data a
    WHERE a.shift IS NOT NULL
    GROUP BY a.shift ";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
