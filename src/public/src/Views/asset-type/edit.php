<?php
$menu = "service";
$page = "service-asset";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\AssetType;
use App\Classes\Validation;

$TYPE = new AssetType();
$VALIDATION = new Validation();
$row = $TYPE->type_view([$uuid]);
$checklists = $TYPE->checklist_view([$uuid]);
$workers = $TYPE->worker_view([$uuid]);
$items = $TYPE->item_view([$uuid]);

$id = (!empty($row['id']) ? $row['id'] : "");
$uuid = (!empty($row['uuid']) ? $row['uuid'] : "");
$name = (!empty($row['name']) ? $row['name'] : "");
$weekly_yes = (intval($row['weekly']) === 1 ? "checked" : "");
$weekly_no = (intval($row['weekly']) === 2 ? "checked" : "");
$monthly_yes = (intval($row['monthly']) === 1 ? "checked" : "");
$monthly_no = (intval($row['monthly']) === 2 ? "checked" : "");
$active = (intval($row['status']) === 1 ? "checked" : "");
$inactive = (intval($row['status']) === 2 ? "checked" : "");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">TYPE</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="/asset/type/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">ID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="id" value="<?php echo $id ?>" readonly>
                </div>
              </div>
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">UUID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $uuid ?>" readonly>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ชื่อ</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="name" value="<?php echo $name ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">รายการตรวจสอบ</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm checklist-select" name="checklist[]" multiple>
                    <?php
                    foreach ($checklists as $checklist) {
                      if (!empty($checklist['id'])) {
                        echo "<option value='{$checklist['id']}' selected>{$checklist['name']}</option>\n";
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ผู้รับผิดชอบ</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm worker-select" name="worker[]" multiple>
                    <?php
                    foreach ($workers as $worker) {
                      if (!empty($worker['worker'])) {
                        echo "<option value='{$worker['worker']}' selected>{$worker['username']}</option>\n";
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>

              <h5 class="col-xl-4 offset-xl-2 mb-2">แผนการบำรุงรักษา</h5>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">รายสัปดาห์</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="weekly" value="1" <?php echo $weekly_yes ?> required>
                        <span class="text-success">ใช่</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="weekly" value="2" <?php echo $weekly_no ?> required>
                        <span class="text-danger">ไม่ใช่</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">รายเดือน</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="monthly" value="1" <?php echo $monthly_yes ?> required>
                        <span class="text-success">ใช่</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="monthly" value="2" <?php echo $monthly_no ?> required>
                        <span class="text-danger">ไม่ใช่</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ระบุเดือน</label>
                <div class="col-xl-6">
                  <select class="form-control form-control-sm month-select" name="month[]" multiple>
                    <?php
                    $months = explode(",", $row['month']);
                    foreach ($months as $month) {
                      if (!empty($month)) {
                        echo "<option value='{$month}' selected>" . $VALIDATION->month_th_name($month) . "</option>\n";
                      }
                    }
                    ?>
                  </select>
                </div>
              </div>

              <h5>ข้อมูลเพิ่มเติม</h5>
              <div class="row mb-2">
                <div class="table-responsive">
                  <table class="table table-sm table-bordered">
                    <thead>
                      <tr>
                        <th width="5%"><input type="checkbox" id="checked-all"></th>
                        <th width="20%">ชื่อ</th>
                        <th width="20%">ประเภท</th>
                        <th width="30%">ตัวเลือก</th>
                        <th width="15%">ความจำเป็น</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($items as $item) :
                        if (!empty($item['id'])) :
                      ?>
                          <tr>
                            <td class="text-center">
                              <input type="checkbox" name="item___id[]" value="<?php echo $item['id'] ?>">
                            </td>
                            <td>
                              <input type="hidden" class="form-control form-control-sm" name="item__id[]" value="<?php echo $item['id'] ?>">
                              <input type="text" class="form-control form-control-sm" name="item__name[]" value="<?php echo $item['name'] ?>">
                            </td>
                            <td>
                              <select class="form-control form-control-sm input-type-select" name="item__type[]">
                                <?php
                                if (!empty($item['type'])) {
                                  echo "<option value='{$item['type']}' selected>" . $VALIDATION->input_th_name($item['type']) . "</option>";
                                }
                                ?>
                              </select>
                            </td>
                            <td>
                              <input type="text" class="form-control form-control-sm" name="item__text[]" value="<?php echo $item['text'] ?>" placeholder="ตัวอย่าง. ใช่,ไม่ใช่">
                            </td>
                            <td>
                              <select class="form-control form-control-sm input-require-select" name="item__required[]">
                                <?php
                                if (!empty($item['required'])) {
                                  echo "<option value='{$item['required']}' selected>" . $VALIDATION->require_th_name($item['required']) . "</option>";
                                }
                                ?>
                              </select>
                            </td>
                          </tr>
                      <?php
                        endif;
                      endforeach;
                      ?>
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
                <label class="col-xl-2 offset-xl-2 col-form-label">สถานะ</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="status" value="1" <?php echo $active ?> กรุณากรอกข้อมูล>
                        <span class="text-success">ใช้งาน</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="status" value="2" <?php echo $inactive ?> กรุณากรอกข้อมูล>
                        <span class="text-danger">ระงับการใช้งาน</span>
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
                  <a href="/asset/type" class="btn btn-sm btn-danger btn-block">
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
  $(document).on("change", "#checked-all", function() {
    $("input:checkbox").prop("checked", $(this).prop("checked"));
  });

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

  $(".checklist-select").select2({
    placeholder: "-- หัวข้อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/type/checklist-select",
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

  $(".worker-select").select2({
    placeholder: "-- รายชื่อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/type/worker-select",
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

  $(".month-select").select2({
    placeholder: "-- เดือน --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/type/month-select",
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