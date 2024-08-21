<?php
$menu = "service";
$page = "service-asset";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row mb-2">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ข้อมูลทรัพย์สิน</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/asset/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">รูปทรัพย์สิน</label>
                <div class="col-xl-6">
                  <table class="table table-borderless">
                    <tr class="tr-file">
                      <td class="text-center" width="5%">
                        <button type="button" class="btn btn-sm btn-success increase-file">+</button>
                        <button type="button" class="btn btn-sm btn-danger decrease-file">-</button>
                      </td>
                      <td>
                        <input type="file" class="form-control-file" name="file[]" accept=".jpeg, .png, .jpg">
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ชื่อ</label>
                <div class="col-xl-6">
                  <input type="text" class="form-control form-control-sm" name="name" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">เลขที่ทรัพย์สิน</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="asset_code" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ประเภท</label>
                <div class="col-sm-4">
                  <select class="form-control form-control-sm type-select" name="type_id" required></select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-xl-6">
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ฝ่าย/แผนก</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm department-select" name="department_id"></select>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">สถานที่</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm location-select" name="location_id"></select>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">หมายเลขอุปกรณ์</label>
                    <div class="col-xl-8">
                      <input type="text" class="form-control form-control-sm" name="serial_number" required>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">รหัสอุปกรณ์</label>
                    <div class="col-xl-8">
                      <input type="text" class="form-control form-control-sm" name="code" required>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">กำลังไฟ (kW)</label>
                    <div class="col-xl-8">
                      <input type="number" class="form-control form-control-sm" name="kw" step="0.01">
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-xl-6">
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ยี่ห้อ</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm brand-select" name="brand_id" required></select>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">รุ่น</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm model-select" name="model_id" disabled></select>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">วันที่ซื้อ</label>
                    <div class="col-xl-8">
                      <input type="text" class="form-control form-control-sm date-select" name="purchase_date">
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">วันที่หมดประกัน</label>
                    <div class="col-xl-8">
                      <input type="text" class="form-control form-control-sm date-select" name="expire_date">
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-2 specific-field-div"></div>

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">หมายเหตุ</label>
                <div class="col-xl-6">
                  <textarea class="form-control form-control-sm" name="text" rows="4"></textarea>
                </div>
              </div>

              <div class="row justify-content-center mb-2">
                <div class="col-xl-3 mb-2">
                  <button type="submit" class="btn btn-sm btn-success btn-block">
                    <i class="fas fa-check pr-2"></i>ยืนยัน
                  </button>
                </div>
                <div class="col-xl-3 mb-2">
                  <a href="/asset" class="btn btn-sm btn-danger btn-block">
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
  $(".decrease-file").hide();
  $(document).on("click", ".increase-file", function() {
    let row = $(".tr-file:last");
    let clone = row.clone();
    clone.find("input").val("");
    clone.find(".increase-file").hide();
    clone.find(".decrease-file").show();
    clone.find(".decrease-file").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();
  });

  $(document).on("change", "input[name='file[]']", function() {
    let file = $(this).val();
    let size = ($(this)[0].files[0].size / (1024 * 1024)).toFixed(2);
    let extension = file.split(".").pop().toLowerCase();
    let allow = ["png", "jpeg", "jpg"];
    if (size > 1) {
      Swal.fire({
        icon: "error",
        title: "ไฟล์รูปไม่เกิน 5 Mb!",
      })
      $(this).val("");
    }

    if ($.inArray(extension, allow) === -1) {
      Swal.fire({
        icon: "error",
        title: "เฉพาะไฟล์นามสกุล JPG และ PNG เท่านั้น",
      })
      $(this).val("");
    }
  });

  $(document).on("change", ".type-select", function() {
    let type = $(this).val();

    axios.post("/asset/type-item", {
        type: type
      })
      .then((res) => {
        let result = res.data;
        if (result.length > 0) {
          $(".specific-field-div").show();
          let div = '';
          result.forEach((v, k) => {
            let type = parseInt(v.type);
            div += '<div class="col-xl-6">';
            div += '<div class="row mb-2">';
            div += '<label class="col-xl-4 col-form-label">' + v.name + '</label>';
            div += '<div class="col-xl-8">';
            div += '<input type="hidden" name="item_id[]" value="' + v.id + '" readonly>';
            div += '<input type="hidden" name="item_type[]" value="' + v.type + '" readonly>';
            if (type === 1) {
              div += '<input type="text" class="form-control form-control-sm" name="item_value[]" ' + v.required_name + '>';
            }
            if (type === 2) {
              div += '<input type="number" class="form-control form-control-sm" step="0.01" name="item_value[]" ' + v.required_name + '>';
            }
            if (type === 3) {
              let text = v.text;
              let option = text.split(",");
              div += '<select class="form-control form-control-sm option-select" name="item_value[]" ' + v.required_name + '>';
              div += '<option value="">-- เลือก --</option>';
              option.forEach((value, index) => {
                div += '<option value="' + index + '">' + value + '</option>';
              });
              div += '</select>';
            }
            if (type === 4) {
              div += '<input type="text" class="form-control form-control-sm date-select" name="item_value[]" ' + v.required_name + '>';
            }
            div += '<div class="invalid-feedback">กรุณากรอกข้อมูล!</div>';
            div += '</div>';
            div += '</div>';
            div += '</div>';
          });
          $(".specific-field-div").empty().html(div);
        } else {
          $(".specific-field-div").hide();
          $("input[name='item_id[]'],input[name='item_type[]'],input[name='item_value[]']").val("");
          $("input[name='item_value[]']").prop("required", false);
        }

        $(".option-select").select2({
          placeholder: "-- SELECT --",
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
    placeholder: "-- ฝ่าย --",
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

  $(".brand-select").select2({
    placeholder: "-- ยี่ห้อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/brand-select",
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

  $(document).on("change", ".brand-select", function() {
    let brand = $(this).val();
    $(".model-select").empty();
    if (brand) {
      $(".model-select").prop("disabled", false);
      $(".model-select").prop("required", true);
      $(".model-select").select2({
        placeholder: "-- รุ่น --",
        width: "100%",
        allowClear: true,
        ajax: {
          url: "/asset/model-select",
          method: 'POST',
          dataType: 'json',
          delay: 100,
          data: function(params) {
            return {
              keyword: params.term,
              brand: brand
            }
          },
          processResults: function(data) {
            return {
              results: data
            };
          },
          cache: true
        }
      });
    } else {
      $(".model-select").prop("disabled", true);
      $(".model-select").prop("required", false);
    }
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
    $(this).val(picker.startDate.format("DD/MM/YYYY"));
  });

  $(".date-select").on("keydown paste", function(e) {
    e.preventDefault();
  });
</script>