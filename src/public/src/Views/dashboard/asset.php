<?php
$menu = "dashboard";
$page = "dashboard-asset";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Asset;

$ASSET = new Asset();
$card = $ASSET->asset_card();
?>
<div class="row mb-2">
  <div class="col mb-2">
    <div class="card h-100 bg-primary text-white shadow card-summary" id="1">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['asset_total']) ? $card['asset_total'] : 0) ?></h3>
        <h5 class="text-right">ทรัพย์สินทั้งหมด</h5>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-success text-white shadow card-summary" id="2">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['asset_type']) ? $card['asset_type'] : 0) ?></h3>
        <h5 class="text-right">ประเภท</h5>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-warning text-white shadow card-summary" id="3">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['asset_department']) ? $card['asset_department'] : 0) ?></h3>
        <h5 class="text-right">แผนก/ฝ่าย</h5>
      </div>
    </div>
  </div>
  <div class="col mb-2">
    <div class="card h-100 bg-danger text-white shadow card-summary" id="4">
      <div class="card-body">
        <h3 class="text-right"><?php echo (isset($card['asset_location']) ? $card['asset_location'] : 0) ?></h3>
        <h5 class="text-right">สถานที่</h5>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-body">
        <iframe title="factory-asset" width="100%" height="800" src="https://app.powerbi.com/view?r=eyJrIjoiZWViNDE1M2MtYmYzMC00OTFmLTk3NGQtMjkwZTAxY2NmMDgwIiwidCI6IjcxMTY3NDI2LTFiZDYtNDY0MS1iNmUwLTY5YjRkOWI3YWU4ZCIsImMiOjEwfQ%3D%3D" frameborder="0" allowFullScreen="true"></iframe>
      </div>
    </div>
  </div>
</div>



<?php include_once(__DIR__ . "/../layout/footer.php"); ?>