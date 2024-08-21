<?php
$menu = "service";
$page = "service-asset";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ยี่ห้อ/รุ่น</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/asset/brand/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ชื่อ</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="name" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ประเภท</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="type" value="1" required>
                        <span class="text-success">ยี่ห้อ</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="type" value="2" required>
                        <span class="text-danger">รุ่น</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-2 reference-div">
                <label class="col-xl-2 offset-xl-2 col-form-label">ยี่ห้อ</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm brand-select" name="reference"></select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
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
                  <a href="/asset/brand" class="btn btn-sm btn-danger btn-block">
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
  $(".reference-div").hide();
  $(document).on("click", "input[name='type']", function() {
    let type = parseInt($(this).val());
    if (type === 2) {
      $(".reference-div").show();
      $(".brand-select").prop("required", true);
    } else {
      $(".reference-div").hide();
      $(".brand-select").prop("required", false);
      $(".brand-select").empty();
    }
  });

  $(".brand-select").select2({
    placeholder: "-- ยี่ห้อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/brand/brand-select",
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