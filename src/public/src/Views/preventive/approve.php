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
            <form action="/preventive/approve" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 col-form-label">ID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm id" name="id" value="<?php echo $row['id'] ?>" readonly>
                </div>
              </div>
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 col-form-label">UUID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm uuid" name="uuid" value="<?php echo $row['uuid'] ?>" readonly>
                </div>
              </div>
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 col-form-label">TYPE</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm type-id" name="type_id" value="<?php echo $row['type_id'] ?>" readonly>
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-xl-6">
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">เลขที่เอกสาร</label>
                    <div class="col-xl-6 text-underline">
                      <?php echo $row['ticket'] ?>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ผู้ใช้บริการ</label>
                    <div class="col-xl-6 text-underline">
                      <?php echo $row['username'] ?>
                    </div>
                  </div>
                </div>

                <div class="col-xl-6">
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">วันที่</label>
                    <div class="col-xl-6 text-underline">
                      <?php echo $row['created'] ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ประเภทเครื่องจักร</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['type_name'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ผู้ดำเนินการ</label>
                <div class="col-xl-4 text-underline">
                  <?php
                  foreach ($worker as $wk) {
                    if (!empty($wk['user_id'])) {
                      echo "{$wk['username']}<br>";
                    }
                  }
                  ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">วันที่นัดหมาย</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['appointment'] ?>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">รายละเอียด</label>
                <div class="col-xl-6 text-underline">
                  <?php echo str_replace("\n", "<br>", $row['text']) ?>
                </div>
              </div>

              <hr>
              <div class="row mb-2">
                <div class="col-xl-12">
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm machine-table">
                      <tr>
                        <th width="5%">#</th>
                        <th width="10%">รหัสอุปกรณ์</th>
                        <th width="10%">เลขที่ทรัพย์สิน</th>
                        <th width="20%">ชื่อ</th>
                        <th width="20%">ประเภท</th>
                        <th width="10%">ฝ่าย/แผนก</th>
                        <th width="15%">สถานที่</th>
                      </tr>
                      <?php
                      foreach ($machine as $key => $mc) :
                        if (!empty($mc['id'])) :
                          $key++;
                      ?>
                          <tr>
                            <td class="text-center"><?php echo $key ?></td>
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
                    </table>
                  </div>
                </div>
              </div>

              <hr>
              <div class="h5 text-danger">กรุณาเลือกผลการอนุมัติ</div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ผลการอนุมัติ</label>
                <div class="col-xl-8">
                  <div class="row">
                    <div class="col-xl-3">
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="radio" value="2" name="status" id="pass" required>
                        <label class="form-check-label" for="pass">
                          <span class="text-success">ผ่าน</span>
                        </label>
                      </div>
                    </div>
                    <div class="col-xl-3">
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="radio" value="6" name="status" id="nopass" required>
                        <label class="form-check-label" for="nopass">
                          <span class="text-danger">ไม่ผ่าน</span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-2 reason-div">
                <label class="col-xl-2 col-form-label">เหตุผล</label>
                <div class="col-xl-6">
                  <textarea class="form-control form-control-sm remark-input" name="remark" rows="5"></textarea>
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
  $(document).on("click", "input[name='status']", function() {
    let status = ($(this).val() ? parseInt($(this).val()) : "");
    if (status === 6) {
      $(".remark-input").prop("required", true);
    } else {
      $(".remark-input").prop("required", false);
    }
  });
</script>