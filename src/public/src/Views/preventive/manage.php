<?php
$menu = "service";
$page = "service-preventive";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Preventive;

$PREVENTIVE = new Preventive();
$card = $PREVENTIVE->preventive_card();
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">จัดการปัญหาการใช้งาน</h4>
      </div>
      <div class="card-body">

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

        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <a href="/preventive/authorize" class="btn btn-sm btn-info btn-block">
              <i class="fa fa-file-lines pr-2"></i>สิทธิ์การจัดการ
            </a>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover manage-data">
                <thead>
                  <tr>
                    <th width="10%">#</th>
                    <th width="10%">เลขที่เอกสาร</th>
                    <th width="10%">วันที่นัดหมาย</th>
                    <th width="20%">ผู้ดำเนินการ</th>
                    <th width="10%">จำนวนเครื่องจักร</th>
                    <th width="10%">ประเภทเครื่องจักร</th>
                    <th width="20%">รายละเอียด</th>
                    <th width="10%">วันที่แจ้ง</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <div class="row justify-content-center mb-2">
          <div class="col-xl-3">
            <a href="/preventive" class="btn btn-sm btn-danger btn-block">
              <i class="fa fa-arrow-left pr-2"></i>กลับ
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  filter_datatable();

  function filter_datatable() {
    $(".manage-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/preventive/manage-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1, 2, 4, 5, 7],
        className: "text-center",
      }],
      "oLanguage": {
        "sLengthMenu": "แสดง _MENU_ ลำดับ ต่อหน้า",
        "sZeroRecords": "ไม่พบข้อมูลที่ค้นหา",
        "sInfo": "แสดง _START_ ถึง _END_ ของ _TOTAL_ ลำดับ",
        "sInfoEmpty": "แสดง 0 ถึง 0 ของ 0 ลำดับ",
        "sInfoFiltered": "",
        "sSearch": "ค้นหา :",
        "oPaginate": {
          "sFirst": "หน้าแรก",
          "sLast": "หน้าสุดท้าย",
          "sNext": "ถัดไป",
          "sPrevious": "ก่อนหน้า"
        }
      },
    });
  };
</script>