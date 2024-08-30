<?php
$menu = "dashboard";
$page = "dashboard-preventive";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Preventive;

$PREVENTIVE = new Preventive();
$card = $PREVENTIVE->preventive_card();
?>
<div class="row mb-2">
  <div class="col mb-2">
    <div class="card h-100 bg-primary text-white shadow card-summary" id="1">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['preventive_total']) ? $card['preventive_total'] : 0) ?></h3>
        <h6 class="text-right">รายการทั้งหมด</h6>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-info text-white shadow card-summary" id="3">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['preventive_work']) ? $card['preventive_work'] : 0) ?></h3>
        <h6 class="text-right">กำลังดำเนินการ</h6>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-danger text-white shadow card-summary" id="4">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['preventive_approve']) ? $card['preventive_approve'] : 0) ?></h3>
        <h6 class="text-right">รออนุมัติ / ตรวจสอบ</h6>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-success text-white shadow card-summary" id="5">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['preventive_complete']) ? $card['preventive_complete'] : 0) ?></h3>
        <h6 class="text-right">ดำเนินการเรียบร้อย</h6>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-body">
        <iframe title="sms-counter" width="100%" height="800" src="https://app.powerbi.com/view?r=eyJrIjoiNjY3Y2VlMWQtYWJkMi00YWY3LThkZDctZDhmMzI4OWQxYTAxIiwidCI6IjcxMTY3NDI2LTFiZDYtNDY0MS1iNmUwLTY5YjRkOWI3YWU4ZCIsImMiOjEwfQ%3D%3D" frameborder="0" allowFullScreen="true"></iframe>
      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>