<?php
$menu = "service";
$page = "service-helpdesk";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Helpdesk;

$HELPDESK = new Helpdesk();
$row = $HELPDESK->helpdesk_view([$uuid]);
$items = $HELPDESK->items_view([$uuid]);
$files = $HELPDESK->files_view([$uuid]);
$processes = $HELPDESK->process_view([$uuid]);
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายละเอียด</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/helpdesk/assign" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">ID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="id" value="<?php echo $row['id'] ?>" readonly>
                </div>
              </div>
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">UUID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $row['uuid'] ?>" readonly>
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
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ข้อมูลติดต่อ</label>
                    <div class="col-xl-6 text-underline">
                      <?php echo $row['contact'] ?>
                    </div>
                  </div>
                </div>
              </div>


              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">หัวข้อบริการ</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['service_name'] ?>
                </div>
              </div>

              <?php if (!empty($row['asset_id']) || intval($row['asset_id']) > 0) : ?>
                <hr>
                <div class="row mb-2 asset-div">
                  <label class="col-xl-2 col-form-label">ทรัพย์สิน</label>
                  <div class="col-xl-6 text-underline">
                    <?php echo $row['asset_name'] ?>
                  </div>
                </div>

                <div class="row mb-2 asset-detail-div">
                  <div class="col-xl-6">
                    <div class="row">
                      <label class="col-xl-4 col-form-label">เลขที่ทรัพย์สิน</label>
                      <div class="col-xl-6 text-underline">
                        <?php echo $row['asset_assetcode'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-xl-4 col-form-label">ฝ่าย/แผนก</label>
                      <div class="col-xl-6 text-underline">
                        <?php echo $row['asset_department'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-xl-4 col-form-label">สถานที่</label>
                      <div class="col-xl-6 text-underline">
                        <?php echo $row['asset_location'] ?>
                      </div>
                    </div>
                  </div>

                  <div class="col-xl-6">
                    <div class="row">
                      <label class="col-xl-4 col-form-label">รหัสอุปกรณ์</label>
                      <div class="col-xl-6 text-underline">
                        <?php echo $row['asset_code'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-xl-4 col-form-label">ยี่ห้อ</label>
                      <div class="col-xl-6 text-underline">
                        <?php echo $row['asset_brand'] ?>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-xl-4 col-form-label">รุ่น</label>
                      <div class="col-xl-6 text-underline">
                        <?php echo $row['asset_model'] ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if (COUNT($items) > 0) : ?>
                <hr>
                <div class="row mb-2 specific-field-div">
                  <div class="col-xl-12">
                    <?php foreach ($items as $item) : ?>
                      <div class="row mb-2">
                        <label class="col-xl-2 col-form-label">
                          <?php echo $item['item_name'] ?>
                        </label>
                        <div class="col-xl-4 text-underline">
                          <?php echo $item['item_value'] ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

              <hr>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ปัญหาที่พบ</label>
                <div class="col-xl-6 text-underline">
                  <?php echo str_replace("\n", "<br>", $row['text']) ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">เอกสารแนบ</label>
                <div class="col-xl-6">
                  <table class="table-sm">
                    <?php
                    foreach ($files as $file) :
                      if (!empty($file['file_name'])) :
                    ?>
                        <tr>
                          <td>
                            <a href="/src/Publics/helpdesk/<?php echo $file['file_name'] ?>" class="text-primary" target="_blank">
                              <span class="badge badge-primary font-weight-light">ดาวน์โหลด!</span>
                            </a>
                          </td>
                        </tr>
                    <?php
                      endif;
                    endforeach;
                    ?>
                  </table>
                </div>
              </div>

              <hr>
              <div class="h5 text-danger">กรุณาเลือกผู้รับผิดชอบ</div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ผู้รับผิดชอบ</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm user-select" name="user" required>
                    <?php
                    if (!empty($user['user_id'])) {
                      echo "<option value='{$user['user_id']}'>{$user['fullname']}</option>";
                    }
                    ?>
                  </select>
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
</script>