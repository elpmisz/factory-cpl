<?php
$menu = "service";
$page = "service-preventive";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Preventive;

$PREVENTIVE = new Preventive();
$row = $PREVENTIVE->preventive_view([$uuid]);
$worker = $PREVENTIVE->worker_view([$uuid]);
$machine = $PREVENTIVE->machine_view([$uuid]);
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
            <form action="/preventive/view" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">ID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm id" name="id" value="<?php echo $row['id'] ?>" readonly>
                </div>
              </div>
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">UUID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm uuid" name="uuid" value="<?php echo $row['uuid'] ?>" readonly>
                </div>
              </div>
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">TYPE</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm type-id" name="type_id" value="<?php echo $row['type_id'] ?>" readonly>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ผู้ใช้บริการ</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['username'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ประเภทเครื่องจักร</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['type_name'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">วันที่นัดหมาย</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm date-select" name="date" value="<?php echo $row['appointment'] ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ผู้ดำเนินการ</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm user-select" name="worker_id[]" multiple required>
                    <?php
                    foreach ($worker as $wk) {
                      if (!empty($wk['user_id'])) {
                        echo "<option value='{$wk['user_id']}' selected>{$wk['username']}</option>\n";
                      }
                    }
                    ?>
                  </select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">รายละเอียด</label>
                <div class="col-xl-6">
                  <textarea class="form-control form-control-sm" name="text" rows="5" required><?php echo $row['text'] ?></textarea>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <div class="row mb-2 machine-div">
                <div class="col-xl-12">
                  <h5 class="text-danger">เลือกรายการ เพื่อลบ!! ***กรณีไม่ต้องการลบ กรุณาอย่าเลือก***</h5>
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm machine-table">
                      <tr>
                        <th width="5%"><input type="checkbox" id="check_all"></th>
                        <th width="10%">รหัสอุปกรณ์</th>
                        <th width="10%">เลขที่ทรัพย์สิน</th>
                        <th width="20%">ชื่อ</th>
                        <th width="20%">ประเภท</th>
                        <th width="10%">ฝ่าย/แผนก</th>
                        <th width="15%">สถานที่</th>
                      </tr>
                      <?php
                      foreach ($machine as $mc) :
                        if (!empty($mc['id'])) :
                      ?>
                          <tr>
                            <td class="text-center">
                              <input type="checkbox" name="machine__id[]" value="<?php echo $mc['id'] ?>">
                            </td>
                            <td class="text-center"><?php echo $mc['code'] ?></td>
                            <td class="text-center"><?php echo $mc['asset_code'] ?></td>
                            <td class="text-left"><?php echo $mc['name'] ?></td>
                            <td class="text-center"><?php echo $mc['type_name'] ?></td>
                            <td class="text-center"><?php echo $mc['department_name'] ?></td>
                            <td class="text-center"><?php echo $mc['location_name'] ?></td>
                          </tr>
                      <?php
                        endif;
                      endforeach;
                      ?>
                      <tr class="tr-machine">
                        <td class="text-center">
                          <button type="button" class="btn btn-sm btn-success machine-increase">+</button>
                          <button type="button" class="btn btn-sm btn-danger machine-decrease">-</button>
                        </td>
                        <td class="text-center"><span class="machine-code"></span></td>
                        <td class="text-center"><span class="machine-assetcode"></span></td>
                        <td>
                          <select class="form-control form-control-sm machine-select " name="machine[]"></select>
                        </td>
                        <td class="text-center"><span class="machine-type"></span></td>
                        <td class="text-center"><span class="machine-department"></span></td>
                        <td class="text-center"><span class="machine-location"></span></td>
                      </tr>
                    </table>
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

  $(document).on("change", ".machine-select", function() {
    let machine = ($(this).val() ? $(this).val() : "");
    let row = $(this).closest("tr");

    axios.post("/preventive/asset-detail", {
        machine: machine
      })
      .then((res) => {
        let result = res.data;
        row.find(".machine-assetcode").text(result.asset_code);
        row.find(".machine-code").text(result.code);
        row.find(".machine-type").text(result.type_name);
        row.find(".machine-department").text(result.department_name);
        row.find(".machine-location").text(result.location_name);
      }).catch((error) => {
        console.log(error);
      });
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

  let id = ($(".id").val() ? $(".id").val() : "");
  let type = ($(".type-id").val() ? $(".type-id").val() : "");

  $(".machine-select").select2({
    placeholder: "-- ทรัพย์สิน --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/preventive/machine-select",
      method: "POST",
      data: function(params) {
        return {
          keyword: params.term,
          id: id,
          type: type,
        }
      },
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

  $(".machine-decrease").hide();
  $(document).on("click", ".machine-increase", function() {
    $(".machine-select").select2('destroy');
    let row = $(".tr-machine:last");
    let clone = row.clone();
    clone.find("input, select").val("");
    clone.find("span").empty();
    clone.find(".machine-increase").hide();
    clone.find(".machine-decrease").show();
    clone.find(".machine-decrease").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();

    $(".machine-select").select2({
      placeholder: "-- ทรัพย์สิน --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/preventive/machine-select",
        method: "POST",
        data: function(params) {
          return {
            keyword: params.term,
            id: id,
            type: type,
          }
        },
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
  });
</script>