<?php
$menu = "dashboard";
$page = "dashboard-helpdesk";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Helpdesk;

$HELPDESK = new Helpdesk();
$card = $HELPDESK->helpdesk_card();
?>
<div class="row mb-2">
  <div class="col mb-2">
    <div class="card h-100 bg-primary text-white shadow card-summary" id="1">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['helpdesk_total']) ? $card['helpdesk_total'] : 0) ?></h3>
        <h6 class="text-right">รายการทั้งหมด</h6>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-info text-white shadow card-summary" id="2">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['helpdesk_assign']) ? $card['helpdesk_assign'] : 0) ?></h3>
        <h6 class="text-right">รอรับเรื่อง</h6>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-warning text-white shadow card-summary" id="3">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['helpdesk_work']) ? $card['helpdesk_work'] : 0) ?></h3>
        <h6 class="text-right">กำลังดำเนินการ</h6>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-danger text-white shadow card-summary" id="4">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['helpdesk_approve']) ? $card['helpdesk_approve'] : 0) ?></h3>
        <h6 class="text-right">รออนุมัติ / ตรวจสอบ</h6>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-success text-white shadow card-summary" id="5">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['helpdesk_complete']) ? $card['helpdesk_complete'] : 0) ?></h3>
        <h6 class="text-right">ดำเนินการเรียบร้อย</h6>
      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>