<?php
$menu = "service";
$page = "service-preventive";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">บำรุงรักษาเครื่องจักร</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/preventive/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ผู้ใช้บริการ</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $user['fullname'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ประเภทเครื่องจักร</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm asset-type-select" name="type_id" required></select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">วันที่นัดหมาย</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm date-select" name="date" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ผู้ดำเนินการ</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm user-select" name="worker_id[]" multiple required></select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">รายละเอียด</label>
                <div class="col-xl-6">
                  <textarea class="form-control form-control-sm" name="text" rows="5" required></textarea>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <div class="row mb-2 machine-div">
                <div class="col-xl-12">
                  <h5 class="text-danger">กรุณาเลือกเครื่องจักร</h5>
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm machine-table"></table>
                  </div>
                </div>
              </div>

              <div class="row justify-content-center mb-2">
                <div class="col-xl-3 mb-2">
                  <button type="submit" class="btn btn-sm btn-success btn-block">
                    <i class="fas fa-check pr-2"></i>ยืนยัน
                  </button>
                </div>
                <div class="col-xl-3 mb-2">
                  <a href="/preventive" class="btn btn-sm btn-danger btn-block">
                    <i class="fa fa-arrow-left pr-2"></i>กลับ
                  </a>
                </div>
              </div>

            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  $(document).on("change", "#check_all", function() {
    $("input:checkbox").prop("checked", $(this).prop("checked"));
  });

  $(".machine-div").hide();
  $(document).on("change", ".asset-type-select", function() {
    let assetType = ($(this).val() ? $(this).val() : "");
    $(".user-select").empty();

    axios.post("/preventive/asset-worker", {
        type: assetType
      })
      .then((res) => {
        let result = res.data;
        if (result.length > 0) {
          result.forEach((user) => {
            if (user.id) {
              let selected = new Option(user.username, user.id, true, true);
              $(".user-select").append(selected).trigger("change");
            }
          });
        }
      }).catch((error) => {
        console.log(error);
      });

    axios.post("/preventive/asset-machine", {
        type: assetType
      })
      .then((res) => {
        let result = res.data;

        if (result.length > 0) {
          let div = '';
          div += '<tr>';
          div += '<th width="5%"><input type="checkbox" id="check_all"></th>';
          div += '<th width="10%">รหัสอุปกรณ์</th>';
          div += '<th width="10%">เลขที่ทรัพย์สิน</th>';
          div += '<th width="20%">ชื่อ</th>';
          div += '<th width="20%">ประเภท</th>';
          div += '<th width="10%">ฝ่าย/แผนก</th>';
          div += '<th width="15%">สถานที่</th>';
          div += '</tr>';
          result.forEach((m) => {
            div += '<tr>';
            div += '<td class="text-center"><input type="checkbox" name="machine[]" value="' + m.id + '"></td>';
            div += '<td class="text-center">' + m.code + '</td>';
            div += '<td class="text-center">' + m.asset_code + '</td>';
            div += '<td class="text-left">' + m.asset_name + '</td>';
            div += '<td class="text-left">' + m.type_name + '</td>';
            div += '<td class="text-left">' + m.department_name + '</td>';
            div += '<td class="text-left">' + m.location_name + '</td>';
            div += '</tr>';
          });
          $(".machine-div").show();
          $(".machine-table").empty().html(div);
        } else {
          $(".machine-div").hide();
          $(".machine-table").empty().html();
        }
      }).catch((error) => {
        console.log(error);
      });
  });

  $(".asset-type-select").select2({
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

  $(".user-select").select2({
    placeholder: "-- รายชื่อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/helpdesk/authorize/user-select",
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

  $(".date-select").daterangepicker({
    autoUpdateInput: false,
    showDropdowns: true,
    locale: {
      "format": "DD/MM/YYYY",
      "applyLabel": "ยืนยัน",
      "cancelLabel": "ยกเลิก",
      "daysOfWeek": [
        "อา", "จ", "อ", "พ", "พฤ", "ศ", "ส"
      ],
      "monthNames": [
        "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
        "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
      ]
    },
    "applyButtonClasses": "btn-success",
    "cancelClass": "btn-danger"
  });

  $(".date-select").on("apply.daterangepicker", function(ev, picker) {
    $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
  });

  $(".date-select").on("keydown paste", function(e) {
    e.preventDefault();
  });
</script>