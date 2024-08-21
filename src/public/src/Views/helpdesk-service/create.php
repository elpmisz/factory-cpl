<?php
$menu = "service";
$page = "service-helpdesk";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">หัวข้อบริการ</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/helpdesk/service/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-lable">บริการ</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="name" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-lable">ระยะเวลา (SLA)</label>
                <div class="col-xl-2">
                  <input type="number" class="form-control form-control-sm text-center" name="date" min="1" max="100" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
                <label class="col-xl-2 col-form-label">วัน</label>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">แจ้งเตือน</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm line-token-select" name="line_token_id"></select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <h5>ข้อมูลเพิ่มเติม</h5>
              <div class="row mb-2">
                <div class="table-responsive">
                  <table class="table table-sm table-bordered">
                    <thead>
                      <tr>
                        <th width="5%">#</th>
                        <th width="20%">ชื่อ</th>
                        <th width="20%">ประเภท</th>
                        <th width="30%">ตัวเลือก</th>
                        <th width="15%">ความจำเป็น</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr class="tr-input">
                        <td class="text-center">
                          <button type="button" class="btn btn-sm btn-success increase-btn">+</button>
                          <button type="button" class="btn btn-sm btn-danger decrease-btn">-</button>
                        </td>
                        <td>
                          <input type="text" class="form-control form-control-sm" name="item_name[]">
                        </td>
                        <td>
                          <select class="form-control form-control-sm input-type-select" name="item_type[]"></select>
                          <div class="invalid-feedback">
                            กรุณากรอกข้อมูล!
                          </div>
                        </td>
                        <td>
                          <input type="text" class="form-control form-control-sm" name="item_text[]" placeholder="ตัวอย่าง. ใช่,ไม่ใช่">
                          <div class="invalid-feedback">
                            กรุณากรอกข้อมูล!
                          </div>
                        </td>
                        <td>
                          <select class="form-control form-control-sm input-require-select" name="item_required[]"></select>
                          <div class="invalid-feedback">
                            กรุณากรอกข้อมูล!
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-lable">เลือกทรัพย์สิน</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="asset" value="1" required>
                        <span class="text-success">ใช่</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="asset" value="2" required>
                        <span class="text-danger">ไม่ใช่</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-lable">การอนุมัติ</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="approve" value="1" required>
                        <span class="text-success">ใช่</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="approve" value="2" required>
                        <span class="text-danger">ไม่ใช่</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-lable">การตรวจสอบ</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="check" value="1" required>
                        <span class="text-success">ใช่</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="check" value="2" required>
                        <span class="text-danger">ไม่ใช่</span>
                      </label>
                    </div>
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
                  <a href="/helpdesk/service" class="btn btn-sm btn-danger btn-block">
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
  $(".decrease-btn").hide();
  $(document).on("click", ".increase-btn", function() {
    $(".input-type-select, .input-require-select").select2("destroy");
    let row = $(".tr-input:last");
    let clone = row.clone();
    clone.find("input, select").val("");
    clone.find(".increase-btn").hide();
    clone.find(".decrease-btn").show();
    clone.find(".decrease-btn").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();

    $(".input-type-select").select2({
      placeholder: "-- ประเภท --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/asset/type/input-type-select",
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

    $(".input-require-select").select2({
      placeholder: "-- ความจำเป็น --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/asset/type/input-require-select",
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
  });

  $(document).on("blur", "input[name='item_name[]']", function() {
    let text = $(this).val();
    let row = $(this).closest("tr");
    if (text) {
      row.find(".input-type-select, .input-require-select").prop("required", true);
    } else {
      row.find(".input-type-select, .input-require-select").prop("required", false);
    }
  });

  $(document).on("change", ".input-type-select", function() {
    let type = parseInt($(this).val());
    let row = $(this).closest("tr");
    if (type === 3) {
      row.find("input[name='item_text[]']").prop("required", true);
    } else {
      row.find("input[name='item_text[]']").prop("required", false);
    }
  });

  $(".line-token-select").select2({
    placeholder: "-- LINE Token --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/helpdesk/service/line-token-select",
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

  $(".input-type-select").select2({
    placeholder: "-- ประเภท --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/type/input-type-select",
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

  $(".input-require-select").select2({
    placeholder: "-- ความจำเป็น --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/type/input-require-select",
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