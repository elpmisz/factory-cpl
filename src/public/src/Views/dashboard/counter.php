<?php
$menu = "dashboard";
$page = "dashboard-counter";
include_once(__DIR__ . "/../layout/header.php");
?>
<div class="row mb-2">
  <div class="col-xl-3 mb-2">
    <div class="card bg-primary text-white shadow">
      <div class="card-body">
        <h3 class="text-right"><?php echo (!empty($card['total']) ? number_format($card['total'], 0) : 0) ?></h3>
        <h5 class="text-right">ยอดผลิตทั้งหมด</h5>
      </div>
    </div>
  </div>
  <div class="col-xl-3 mb-2">
    <div class="card bg-info text-white shadow">
      <div class="card-body">
        <h3 class="text-right"><?php echo (!empty($card['year']) ? number_format($card['year'], 0) : 0) ?></h3>
        <h5 class="text-right">ยอดผลิตรายปี</h5>
      </div>
    </div>
  </div>
  <div class="col-xl-3 mb-2">
    <div class="card bg-success text-white shadow">
      <div class="card-body">
        <h3 class="text-right"><?php echo (!empty($card['month']) ? number_format($card['month'], 0) : 0) ?></h3>
        <h5 class="text-right">ยอดผลิตรายเดือน</h5>
      </div>
    </div>
  </div>
  <div class="col-xl-3 mb-2">
    <div class="card bg-danger text-white shadow">
      <div class="card-body">
        <h3 class="text-right"><?php echo (!empty($card['date']) ? number_format($card['date'], 0) : 0) ?></h3>
        <h5 class="text-right">ยอดผลิตรายวัน</h5>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-body">
        <iframe title="sms-counter" width="100%" height="800" src="https://app.powerbi.com/view?r=eyJrIjoiMWE0ZjIzZGEtY2M5Yy00ZjQzLWFiMWEtNmI2MDIyYzQ0MTFlIiwidCI6IjcxMTY3NDI2LTFiZDYtNDY0MS1iNmUwLTY5YjRkOWI3YWU4ZCIsImMiOjEwfQ%3D%3D" frameborder="0" allowFullScreen="true"></iframe>
      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>