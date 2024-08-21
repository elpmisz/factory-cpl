<?php
$menu = "service";
$page = "service-asset";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายการตรวจสอบ</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/asset/checklist/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

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
                        <span class="text-success">หัวข้อ</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="type" value="2" required>
                        <span class="text-danger">รายการตรวจสอบ</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-2 reference-div">
                <label class="col-xl-2 offset-xl-2 col-form-label">หัวข้อ</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm checklist-select" name="reference"></select>
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
                  <a href="/asset/checklist" class="btn btn-sm btn-danger btn-block">
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
      $(".checklist-select").prop("required", true);
    } else {
      $(".reference-div").hide();
      $(".checklist-select").prop("required", false);
      $(".checklist-select").empty();
    }
  });

  $(".checklist-select").select2({
    placeholder: "-- หัวข้อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/checklist/checklist-select",
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