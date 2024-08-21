<?php
$menu = "service";
$page = "service-helpdesk";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Helpdesk;

$HELPDESK = new Helpdesk();
$approver = $HELPDESK->helpdesk_authorize($user['user_id'], 1);
$worker = $HELPDESK->helpdesk_authorize($user['user_id'], 2);
$worker_authorize = $HELPDESK->worker_authorize([$user['user_id']]);
$count = $HELPDESK->helpdesk_card();
?>

<div class="row mb-2">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">แจ้งปัญหาการใช้งาน</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <?php if (intval($worker) > 0 || intval($approver) > 0) : ?>
            <div class="col-xl-3 mb-2">
              <a href="/helpdesk/manage" class="btn btn-sm btn-info btn-block">
                <i class="fa fa-file-lines pr-2"></i>จัดการระบบ
              </a>
            </div>
          <?php endif; ?>
          <div class="col-xl-3 mb-2">
            <a href="/helpdesk/create" class="btn btn-sm btn-primary btn-block">
              <i class="fa fa-plus pr-2"></i>เพิ่ม
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php if (intval($approver) > 0 && intval($count['helpdesk_approve']) > 0) : ?>
  <div class="row mb-2">
    <div class="col-xl-12">
      <div class="card shadow">
        <div class="card-header">
          <div class="h5">รายการรออนุมัติ / ตรวจสอบ</div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover approve-data">
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
    </div>
  </div>
<?php endif; ?>

<?php if (intval($worker) > 0 && intval($count['helpdesk_assign']) > 0) : ?>
  <div class="row mb-2">
    <div class="col-xl-12">
      <div class="card shadow">
        <div class="card-header">
          <div class="h5">รายการรอรับเรื่อง</div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover assign-data">
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
    </div>
  </div>
<?php endif; ?>

<?php if (intval($worker) > 0 || intval($worker_authorize) > 0) : ?>
  <div class="row mb-2">
    <div class="col-xl-12">
      <div class="card shadow">
        <div class="card-header">
          <div class="h5">รายการกำลังดำเนินการ</div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-hover work-data">
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
    </div>
  </div>
<?php endif; ?>

<div class="row mb-2">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <div class="h5">รายการขอใช้บริการ</div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-hover request-data">
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
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  filter_datatable();

  function filter_datatable() {
    $(".request-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/helpdesk/request-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1, 6],
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

    $(".approve-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/helpdesk/approve-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1, 6],
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

    $(".assign-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/helpdesk/assign-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1, 6],
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

    $(".work-data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/helpdesk/work-data",
        type: "POST",
      },
      columnDefs: [{
        targets: [0, 1, 6],
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