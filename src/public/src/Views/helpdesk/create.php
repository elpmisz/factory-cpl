<?php
$menu = "service";
$page = "service-helpdesk";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">แจ้งปัญหาการใช้งาน</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/helpdesk/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">USER</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="user_id" value="<?php echo $user['user_id'] ?>" readonly>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ผู้ใช้บริการ</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $user['fullname'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ข้อมูลติดต่อ</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="contact" value="<?php echo $user['contact'] ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">หัวข้อบริการ</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm service-select" name="service_id" required></select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2 asset-div">
                <label class="col-xl-2 offset-xl-2 col-form-label">ทรัพย์สิน</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm asset-select" name="asset"></select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <div class="row asset-detail-div">
                <div class="col-xl-6">
                  <div class="row">
                    <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่ทรัพย์สิน</label>
                    <div class="col-xl-6">
                      <input type="text" class="form-control form-control-sm asset-assetcode" readonly>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-xl-3 offset-xl-1 col-form-label">ฝ่าย/แผนก</label>
                    <div class="col-xl-6">
                      <input type="text" class="form-control form-control-sm asset-department" readonly>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-xl-3 offset-xl-1 col-form-label">สถานที่</label>
                    <div class="col-xl-6">
                      <input type="text" class="form-control form-control-sm asset-location" readonly>
                    </div>
                  </div>
                </div>

                <div class="col-xl-6">
                  <div class="row">
                    <label class="col-xl-3 offset-xl-1 col-form-label">รหัสอุปกรณ์</label>
                    <div class="col-xl-6">
                      <input type="text" class="form-control form-control-sm asset-code" readonly>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-xl-3 offset-xl-1 col-form-label">ยี่ห้อ</label>
                    <div class="col-xl-6">
                      <input type="text" class="form-control form-control-sm asset-brand" readonly>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-xl-3 offset-xl-1 col-form-label">รุ่น</label>
                    <div class="col-xl-6">
                      <input type="text" class="form-control form-control-sm asset-model" readonly>
                    </div>
                  </div>
                </div>
              </div>

              <div class="specific-field-div"></div>

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ปัญหาที่พบ</label>
                <div class="col-xl-6">
                  <textarea class="form-control form-control-sm" name="text" rows="5" required></textarea>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">เอกสารแนบ</label>
                <div class="col-xl-6">
                  <table class="table-sm">
                    <tr class="tr-file">
                      <td>
                        <a href="javascript:void(0)" class="btn btn-success btn-sm file-increase">+</a>
                        <a href="javascript:void(0)" class="btn btn-danger btn-sm file-decrease">-</a>
                      </td>
                      <td>
                        <input type="file" class="form-control" name="file[]" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                      </td>
                    </tr>
                  </table>
                </div>
              </div>

              <div class="row justify-content-center mb-2">
                <div class="col-xl-3 mb-2">
                  <button type="submit" class="btn btn-sm btn-success btn-block">
                    <i class="fas fa-check pr-2"></i>ยืนยัน
                  </button>
                </div>
                <div class="col-xl-3 mb-2">
                  <a href="/helpdesk" class="btn btn-sm btn-danger btn-block">
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
  $(".asset-div, .asset-detail-div").hide();
  $(document).on("change", ".service-select", function() {
    $(".asset-select").empty();
    let service = ($(this).val() ? $(this).val() : "");

    axios.post("/helpdesk/asset-check", {
        service: service
      })
      .then((res) => {
        let result = res.data;
        let asset = parseInt(result);
        if (asset === 1) {
          $(".asset-div, .asset-detail-div").show();
          $(".asset-select").prop("required", true);
        } else {
          $(".asset-div, .asset-detail-div").hide();
          $(".asset-select").prop("required", false);
        }
      }).catch((error) => {
        console.log(error);
      });

    axios.post("/helpdesk/service-item", {
        service: service
      })
      .then((res) => {
        let result = res.data;
        if (result.length > 0) {
          $(".specific-field-div").show();
          let div = '';
          result.forEach((v, k) => {
            let type = parseInt(v.type);
            div += '<div class="row mb-2">';
            div += '<label class="col-xl-2 offset-xl-2 col-form-label">' + v.name + '</label>';
            div += '<div class="col-xl-4">';
            div += '<input type="hidden" name="item_id[]" value="' + v.id + '" readonly>';
            div += '<input type="hidden" name="item_type[]" value="' + v.type + '" readonly>';
            if (type === 1) {
              div += '<input type="text" class="form-control form-control-sm item-value" name="item_value[]" ' + v.required_name + '>';
            }
            if (type === 2) {
              div += '<input type="number" class="form-control form-control-sm item-value" step="0.01" name="item_value[]" ' + v.required_name + '>';
            }
            if (type === 3) {
              let text = v.text;
              let option = text.split(",");
              div += '<select class="form-control form-control-sm item-value option-select" name="item_value[]" ' + v.required_name + '>';
              div += '<option value="">-- เลือก --</option>';
              option.forEach((value, index) => {
                div += '<option value="' + index + '">' + value + '</option>';
              });
              div += '</select>';
            }
            if (type === 4) {
              div += '<input type="text" class="form-control form-control-sm item-value date-select" name="item_value[]" ' + v.required_name + '>';
            }
            div += '<div class="invalid-feedback">กรุณากรอกข้อมูล!</div>';
            div += '</div>';
            div += '</div>';
          });
          $(".specific-field-div").empty().html(div);
        } else {
          $(".specific-field-div").hide();
          $(".item-value").val("");
          $(".item-value").prop("required", false);
        }

        $(".option-select").select2({
          placeholder: "-- เลือก --",
          width: "100%",
          allowClear: true,
        });

        $(".date-select").daterangepicker({
          autoUpdateInput: false,
          singleDatePicker: true,
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
          $(this).val(picker.startDate.format('DD/MM/YYYY'));
        });

        $(".date-select").on("keydown paste", function(e) {
          e.preventDefault();
        });
      }).catch((error) => {
        console.log(error);
      });
  });

  $(document).on("change", ".asset-select", function() {
    let asset = ($(this).val() ? $(this).val() : "");

    axios.post("/helpdesk/asset-detail", {
        asset: asset
      })
      .then((res) => {
        let result = res.data;
        $(".asset-assetcode").val(result.asset_code);
        $(".asset-department").val(result.department_name);
        $(".asset-location").val(result.location_name);
        $(".asset-code").val(result.code);
        $(".asset-brand").val(result.brand_name);
        $(".asset-model").val(result.model_name);
      }).catch((error) => {
        console.log(error);
      });
  });

  $(".file-decrease").hide();
  $(document).on("click", ".file-increase", function() {
    let row = $(".tr-file:last");
    let clone = row.clone();
    clone.find("input").val("");
    clone.find(".file-increase").hide();
    clone.find(".file-decrease").show();
    clone.find(".file-decrease").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();
  });

  $(document).on("change", "input[name='file[]']", function() {
    let file = $(this).val();
    let size = ($(this)[0].files[0].size / (1024 * 1024)).toFixed(2);
    let extension = file.split(".").pop().toLowerCase();
    let allow = ["png", "jpeg", "jpg", "pdf", "doc", "docx", "xls", "xlsx"];
    if (size > 1) {
      Swal.fire({
        icon: "error",
        title: "ไฟล์เอกสารไม่เกิน 5 Mb!",
      })
      $(this).val("");
    }

    if ($.inArray(extension, allow) === -1) {
      Swal.fire({
        icon: "error",
        title: "เฉพาะไฟล์นามสกุล JPG, PNG, WORD และ EXCEL เท่านั้น",
      })
      $(this).val("");
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

  $(".asset-select").select2({
    placeholder: "-- ทรัพย์สิน --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/helpdesk/asset-select",
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