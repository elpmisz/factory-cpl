<?php
$menu = "service";
$page = "service-asset";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Asset;

$ASSET = new Asset();
$card = $ASSET->asset_card();
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ข้อมูลทรัพย์สิน</h4>
      </div>
      <div class="card-body">

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

        <div class="row mb-2">
          <div class="col-xl-3 mb-2">
            <a href="/asset/department" class="btn btn-sm btn-info btn-block">
              <i class="fa fa-file-lines pr-2"></i>แผนก/ฝ่าย
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/asset/location" class="btn btn-sm btn-info btn-block">
              <i class="fa fa-file-lines pr-2"></i>สถานที่
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/asset/brand" class="btn btn-sm btn-info btn-block">
              <i class="fa fa-file-lines pr-2"></i>ยี่ห้อ
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/asset/checklist" class="btn btn-sm btn-info btn-block">
              <i class="fa fa-file-lines pr-2"></i>รายการตรวจสอบ
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/asset/type" class="btn btn-sm btn-info btn-block">
              <i class="fa fa-file-lines pr-2"></i>ประเภท
            </a>
          </div>

          <div class="col-xl-6"></div>

          <div class="col-xl-3 mb-2">
            <a href="javascript:void(0)" class="btn btn-sm btn-success btn-block machine-report">
              <i class="fa fa-download pr-2"></i>นำข้อมูลออก
            </a>
          </div>
        </div>

        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm type-select"></select>
          </div>
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm department-select"></select>
          </div>
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm location-select"></select>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/asset/create" class="btn btn-sm btn-primary btn-block">
              <i class="fa fa-plus pr-2"></i>เพิ่ม
            </a>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover data">
                <thead>
                  <tr>
                    <th width="10%">#</th>
                    <th width="20%">ชื่อ</th>
                    <th width="10%">เลขที่ทรัพย์สิน</th>
                    <th width="10%">รหัสอุปกรณ์</th>
                    <th width="10%">ประเภท</th>
                    <th width="10%">ฝ่าย/แผนก</th>
                    <th width="10%">สถานที่</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  filter_datatable();

  $(document).on("change", ".type-select, .department-select, .location-select", function() {
    let type = ($(".type-select").val() ? $(".type-select").val() : "");
    let department = ($(".department-select").val() ? $(".department-select").val() : "");
    let location = ($(".location-select").val() ? $(".location-select").val() : "");

    if (type || department || location) {
      $(".data").DataTable().destroy();
      filter_datatable(type, department, location);
    } else {
      $(".data").DataTable().destroy();
      filter_datatable();
    }
  });

  $(".type-select").select2({
    placeholder: "-- ประเภท --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/type-select",
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

  $(".department-select").select2({
    placeholder: "-- ฝ่าย/แผนก --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/department-select",
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

  $(".location-select").select2({
    placeholder: "-- สถานที่ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/location-select",
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

  function filter_datatable(type, department, location) {
    $(".data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/asset/data",
        type: "POST",
        data: {
          type: type,
          department: department,
          location: location,
        }
      },
      columnDefs: [{
        targets: [0],
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

  $(document).on("click", ".btn-delete", function(e) {
    let uuid = $(this).prop("id");
    e.preventDefault();
    Swal.fire({
      title: "ยืนยันที่จะลบ?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "ยืนยัน",
      cancelButtonText: "ปิด",
    }).then((result) => {
      if (result.value) {
        axios.post("/asset/asset-delete", {
          uuid: uuid
        }).then((res) => {
          let result = res.data;
          if (result === 200) {
            location.reload()
          } else {
            location.reload()
          }
        }).catch((error) => {
          console.log(error);
        });
      } else {
        return false;
      }
    })
  });
</script>