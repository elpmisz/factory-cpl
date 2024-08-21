<?php
$menu = "service";
$page = "service-preventive";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">สิทธิ์การจัดการ</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/preventive/authorize/create" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">รายชื่อ</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm user-select" name="user" required></select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">สิทธิ์</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="type" value="1" required>
                        <span class="text-success">ผู้อนุมัติ / ผู้ตรวจสอบ</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="type" value="2" required>
                        <span class="text-primary">ผู้จัดการระบบ</span>
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
                  <a href="/preventive/authorize" class="btn btn-sm btn-danger btn-block">
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
  $(".user-select").select2({
    placeholder: "-- รายชื่อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/preventive/authorize/user-select",
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