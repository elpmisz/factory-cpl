<?php
$menu = "service";
$page = "service-helpdesk";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Helpdesk;

$HELPDESK = new Helpdesk();
$card = $HELPDESK->helpdesk_card();
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

        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm status-select"></select>
          </div>
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm service-select"></select>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/helpdesk/service" class="btn btn-sm btn-info btn-block">
              <i class="fa fa-file-lines pr-2"></i>หัวข้อบริการ
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/helpdesk/authorize" class="btn btn-sm btn-info btn-block">
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
                    <th width="10%">หัวข้อบริการ</th>
                    <th width="40%">ปัญหาที่พบ</th>
                    <th width="10%">ผู้ใช้บริการ</th>
                    <th width="10%">ผู้รับผิดชอบ</th>
                    <th width="10%">กำหนดเสร็จ</th>
                    <th width="10%">วันที่แจ้ง</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <div class="row justify-content-center mb-2">
          <div class="col-xl-3">
            <a href="/helpdesk" class="btn btn-sm btn-danger btn-block">
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


  $(document).on("change", ".status-select, .service-select", function() {
    let status = ($(".status-select").val() ? $(".status-select").val() : "");
    let service = ($(".service-select").val() ? $(".service-select").val() : "");

    if (status || service) {
      $(".manage-data").DataTable().destroy();
      filter_datatable(status, service);
    } else {
      $(".manage-data").DataTable().destroy();
      filter_datatable();
    }
  });

  function filter_datatable(status, service) {
    $(".manage-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/helpdesk/manage-data",
        type: "POST",
        data: {
          status: status,
          service: service,
        }
      },
      columnDefs: [{
        targets: [0, 1],
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

  $(".status-select").select2({
    placeholder: "-- สถานะ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/helpdesk/status-select",
      method: "POST",
      dataType: "json",
      delay: 100,
      processResults: function(data) {
        return {
          results: data
        };
      },
      cache: true
    }
  });

  $(".service-select").select2({
    placeholder: "-- บริการ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/helpdesk/service-select",
      method: "POST",
      dataType: "json",
      delay: 100,
      processResults: function(data) {
        return {
          results: data
        };
      },
      cache: true
    }
  });
</script>