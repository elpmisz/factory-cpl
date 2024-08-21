<?php

namespace App\Classes;

use PDO;

class DashboardMachine
{
  public $dbcon;

  public function __construct()
  {
    $database = new Database();
    $this->dbcon = $database->getConnection();
  }

  public function hello()
  {
    return "DASHBOARD-MACHINE CLASS";
  }

  public function counter_view($data)
  {
    $sql = "SELECT RIGHT(a.count_machine,2) machine,a.count_cnt1 input,a.count_cnt2 output
    FROM planner.count_meter a
    WHERE RIGHT(a.count_machine,2) = ?
    ORDER BY a.count_id DESC
    LIMIT 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function plan_view($data)
  {
    $sql = "SELECT b.req_job job,a.req_piece target
    FROM planner.inspection_request a
    LEFT JOIN planner.inspection_log b 
    ON a.req_id = b.req_id
    WHERE b.machine = ?
    ORDER BY a.req_id DESC 
    LIMIT 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function energy_view($data)
  {
    $sql = "SELECT ROUND((a.electric_imptwh / 1000),4) energy
    FROM planner.electric_meter a
    WHERE RIGHT(a.electric_machine,2) = ?
    ORDER BY a.electric_id DESC
    LIMIT 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return (!empty($row['energy']) ? $row['energy'] : 0);
  }

  public function status_view($data)
  {
    $sql = "SELECT
    (
      CASE
        WHEN status_number = 0 THEN 'NO STATUS'
        WHEN status_number = 10 THEN 'MACHINE WORK PRODUCT'
        WHEN status_number = 11 THEN 'BREAKDOWN'
        WHEN status_number = 12 THEN 'SETUP MACHINE'
        WHEN status_number = 13 THEN 'MINOR STOP'
        WHEN status_number = 14 THEN 'SHUTDOWN'
        WHEN status_number = 20 THEN 'NO ERROR'
        WHEN status_number = 21 THEN 'REWORK'
        WHEN status_number = 22 THEN 'MEETING'
        WHEN status_number = 23 THEN 'INSPECTION & QUALITY'
        WHEN status_number = 24 THEN 'MATERIAL'
        ELSE NULL 
      END
    ) status_name, 
    (
      CASE
        WHEN status_number = 0 THEN 'warning'
        WHEN status_number = 10 THEN 'success'
        WHEN status_number = 11 THEN 'danger'
        WHEN status_number = 12 THEN 'primary'
        WHEN status_number = 13 THEN 'danger'
        WHEN status_number = 14 THEN 'danger'
        WHEN status_number = 20 THEN 'primary'
        WHEN status_number = 21 THEN 'primary'
        WHEN status_number = 22 THEN 'primary'
        WHEN status_number = 23 THEN 'primary'
        WHEN status_number = 24 THEN 'primary'
        ELSE NULL 
      END
    ) status_color
    FROM planner.status_meter a
    WHERE RIGHT(a.status_machine,2) = ?
    ORDER BY a.status_id DESC
    LIMIT 1";
    $stmt = $this->dbcon->prepare($sql);
    $stmt->execute($data);
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
