<?php
$menu = "dashboard";
$page = "dashboard-energy";
include_once(__DIR__ . "/../layout/header.php");
?>
<div class="row mb-2">
  <div class="col-xl-3 mb-2">
    <div class="card bg-primary text-white shadow">
      <div class="card-body">
        <h3 class="text-right"><?php echo (!empty($card['total']) ? number_format($card['total'], 2) : 0) ?></h3>
        <h5 class="text-right">ยอดใช้ทั้งหมด</h5>
      </div>
    </div>
  </div>
  <div class="col-xl-3 mb-2">
    <div class="card bg-info text-white shadow">
      <div class="card-body">
        <h3 class="text-right"><?php echo (!empty($card['year']) ? number_format($card['year'], 2) : 0) ?></h3>
        <h5 class="text-right">ยอดใช้รายปี</h5>
      </div>
    </div>
  </div>
  <div class="col-xl-3 mb-2">
    <div class="card bg-success text-white shadow">
      <div class="card-body">
        <h3 class="text-right"><?php echo (!empty($card['month']) ? number_format($card['month'], 2) : 0) ?></h3>
        <h5 class="text-right">ยอดใช้รายเดือน</h5>
      </div>
    </div>
  </div>
  <div class="col-xl-3 mb-2">
    <div class="card bg-danger text-white shadow">
      <div class="card-body">
        <h3 class="text-right"><?php echo (!empty($card['date']) ? number_format($card['date'], 2) : 0) ?></h3>
        <h5 class="text-right">ยอดใช้รายวัน</h5>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-body">
        <iframe title="sms-energy" width="100%" height="800" src="https://app.powerbi.com/view?r=eyJrIjoiNTA4YmU3ZjEtMDY0OC00ODRlLTg4MGEtYTBlODVhNjMwYzY3IiwidCI6IjcxMTY3NDI2LTFiZDYtNDY0MS1iNmUwLTY5YjRkOWI3YWU4ZCIsImMiOjEwfQ%3D%3D" frameborder="0" allowFullScreen="true"></iframe>
      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>