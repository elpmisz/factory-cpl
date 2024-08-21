<?php
$menu = "service";
$page = "service-asset";
include_once(__DIR__ . "/../layout/header.php");
$param = (isset($params) ? explode("/", $params) : die(header("Location: /error")));
$uuid = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

use App\Classes\Asset;

$ASSET = new Asset();

$row = $ASSET->asset_view([$uuid]);
$asset_id = $row['id'];
$items = $ASSET->item_view([$asset_id]);
$files = $ASSET->file_view([$asset_id]);
$helpdesk = $ASSET->helpdesk_view([$asset_id]);
$preventive = $ASSET->preventive_view([$asset_id]);
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
            <form action="/asset/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">

              <?php if (COUNT($files) !== 0) : ?>
                <div class="row mb-2 justify-content-center">
                  <div class="col-xl-4">
                    <div id="control" class="carousel slide" data-ride="carousel">
                      <div class="carousel-inner">
                        <?php foreach ($files as $key => $file) : ?>
                          <div class="carousel-item <?php echo ($key === 0 ? "active" : "") ?>">
                            <img src="/src/Publics/asset/<?php echo $file['name'] ?>" class="d-block w-100 asset-image" alt="asset-image">
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <button class="carousel-control-prev" type="button" data-target="#control" data-slide="prev">
                      </button>
                      <button class="carousel-control-next" type="button" data-target="#control" data-slide="next">
                      </button>
                    </div>
                  </div>
                </div>
                <div class="row mb-2">
                  <label class="col-xl-2 offset-xl-2 col-form-label"></label>
                  <div class="col-xl-8">
                    <table class="table table-sm table-borderless">
                      <?php
                      foreach ($files as $key => $file) :
                        $key++;
                      ?>
                        <tr>
                          <td width="10%">
                            <a href="javascript:void(0)" class="badge badge-danger font-weight-light file-delete" id="<?php echo $file['id'] ?>">ลบ</a>
                          </td>
                          <td width="90%">
                            <a href="/src/Publics/asset/<?php echo $file['name'] ?>" target="_blank">
                              <?php echo "{$row['name']}_{$key}" ?>
                            </a>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </table>
                  </div>
                </div>
              <?php endif; ?>
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
                <label class="col-xl-2 offset-xl-2 col-form-label">ชื่อ</label>
                <div class="col-xl-6">
                  <input type="text" class="form-control form-control-sm" name="name" value="<?php echo $row['name'] ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">เลขที่ทรัพย์สิน</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="asset_code" value="<?php echo $row['asset_code'] ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">TYPE ID</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm type_id" value="<?php echo $row['type_id'] ?>" readonly>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ประเภท</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['type_name'] ?>
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-xl-6">
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">ฝ่าย/แผนก</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm department-select" name="department_id">
                        <?php
                        if (!empty($row['department_id'])) {
                          echo "<option value='{$row['department_id']}'>{$row['department_name']}</option>";
                        }
                        ?>
                      </select>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">สถานที่</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm location-select" name="location_id">
                        <?php
                        if (!empty($row['location_id'])) {
                          echo "<option value='{$row['location_id']}'>{$row['location_name']}</option>";
                        }
                        ?>
                      </select>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">หมายเลขอุปกรณ์</label>
                    <div class="col-xl-8">
                      <input type="text" class="form-control form-control-sm" name="serial_number" value="<?php echo $row['serial_number'] ?>" required>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">รหัสอุปกรณ์</label>
                    <div class="col-xl-8">
                      <input type="text" class="form-control form-control-sm" name="code" value="<?php echo $row['code'] ?>" required>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">กำลังไฟ (kW)</label>
                    <div class="col-xl-8">
                      <input type="number" class="form-control form-control-sm" name="kw" value="<?php echo $row['kw'] ?>" step="0.01">
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
                      <select class="form-control form-control-sm brand-select" name="brand_id" required>
                        <?php
                        if (!empty($row['brand_id'])) {
                          echo "<option value='{$row['brand_id']}'>{$row['brand_name']}</option>";
                        }
                        ?>
                      </select>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">รุ่น</label>
                    <div class="col-xl-8">
                      <select class="form-control form-control-sm model-select" name="model_id">
                        <?php
                        if (!empty($row['model_id'])) {
                          echo "<option value='{$row['model_id']}'>{$row['model_name']}</option>";
                        }
                        ?>
                      </select>
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">วันที่ซื้อ</label>
                    <div class="col-xl-8">
                      <input type="text" class="form-control form-control-sm date-select" name="purchase_date" value="<?php echo $row['purchase'] ?>">
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label">วันที่หมดประกัน</label>
                    <div class="col-xl-8">
                      <input type="text" class="form-control form-control-sm date-select" name="expire_date" value="<?php echo $row['expire'] ?>">
                      <div class="invalid-feedback">
                        กรุณากรอกข้อมูล!
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <?php foreach ($items as $key => $item) : ?>
                  <div class="col-xl-6">
                    <div class="row">
                      <label class="col-xl-4 col-form-label"><?php echo $item['type_item_name'] ?></label>
                      <div class="col-xl-8">
                        <input type="hidden" class="form-control form-control-sm" name="item__id[]" value="<?php echo $item['id'] ?>" readonly>
                        <input type="hidden" class="form-control form-control-sm" name="item__type[]" value="<?php echo $item['type_item_id'] ?>" readonly>
                        <?php if (intval($item['type_item_type']) === 1) : ?>
                          <input type="text" class="form-control form-control-sm" name="item__value[]" value="<?php echo $item['item_value'] ?>" <?php echo $item['item_required'] ?>>
                        <?php endif; ?>
                        <?php if (intval($item['type_item_type']) === 2) : ?>
                          <input type="number" class="form-control form-control-sm" name="item__value[]" step="0.01" value="<?php echo $item['item_value'] ?>" <?php echo $item['item_required'] ?>>
                        <?php endif; ?>
                        <?php
                        if (intval($item['type_item_type']) === 3) :
                          $selects = explode(",", $item['type_item_text']);
                        ?>
                          <select class="form-control form-control-sm option-select" name="item__value[]" <?php echo $item['item_required'] ?>>
                            <option value="">-- เลือก --</option>
                            <?php
                            foreach ($selects as $k => $v) {
                              echo "<option value='{$k}' " . ($k === intval($item['item_value']) ? "selected" : "") . ">{$v}</option>";
                            }
                            ?>
                          </select>
                        <?php endif; ?>
                        <?php if (intval($item['type_item_type']) === 4) : ?>
                          <input type="text" class="form-control form-control-sm date-select" name="item__value[]" value="<?php echo $item['item_value'] ?>" <?php echo $item['item_required'] ?>>
                        <?php endif; ?>
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">หมายเหตุ</label>
                <div class="col-xl-6">
                  <textarea class="form-control form-control-sm" name="text" rows="4"><?php echo $row['text'] ?></textarea>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 text-xl-right">สถานะ</label>
                <div class="col-xl-8">
                  <div class="row pb-2">
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="status" value="1" <?php echo ($row['status'] === 1 ? "checked" : "") ?> required>
                        <span class="text-success">ใช้งาน</span>
                      </label>
                    </div>
                    <div class="col-xl-3">
                      <label class="form-check-label px-3">
                        <input class="form-check-input" type="radio" name="status" value="2" <?php echo ($row['status'] === 2 ? "checked" : "") ?> required>
                        <span class="text-danger">ระงับการใช้งาน</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <?php if (COUNT($helpdesk) > 0) : ?>
                <hr>
                <div class="h5 text-primary">ประวัติการแจ้งปัญหาการใช้งาน</div>
                <div class="row mb-2">
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                      <thead>
                        <tr>
                          <th width="10%">#</th>
                          <th width="10%">เลขที่บริการ</th>
                          <th width="20%">หัวข้อบริการ</th>
                          <th width="40%">ปัญหาที่พบ</th>
                          <th width="10%">วันที่</th>
                        </tr>
                      </thead>
                      <?php
                      foreach ($helpdesk as $hd) :
                      ?>
                        <tr>
                          <td class="text-center">
                            <a href="/helpdesk/complete/<?php echo $hd['uuid'] ?>" class="badge badge-primary font-weight-light" target="_blank">รายละเอียด</a>
                          </td>
                          <td class="text-center"><?php echo $hd['ticket'] ?></td>
                          <td class="text-center"><?php echo $hd['service_name'] ?></td>
                          <td><?php echo str_replace("\r\n", "<br>", $hd['text']) ?></td>
                          <td><?php echo $hd['created'] ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </table>
                  </div>
                </div>
              <?php endif; ?>

              <?php if (COUNT($preventive) > 0) : ?>
                <hr>
                <div class="h5 text-primary">ประวัติการบำรุงรักษาเครื่องจักร</div>
                <div class="row mb-2">
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                      <thead>
                        <tr>
                          <th width="10%">#</th>
                          <th width="10%">เลขที่บริการ</th>
                          <th width="20%">การดำเนินการ</th>
                          <th width="40%">หมายเหตุ</th>
                          <th width="10%">วันที่</th>
                        </tr>
                      </thead>
                      <?php
                      foreach ($preventive as $pm) :
                      ?>
                        <tr>
                          <td class="text-center">
                            <a href="/preventive/complete/<?php echo $pm['uuid'] ?>" class="badge badge-primary font-weight-light" target="_blank">รายละเอียด</a>
                          </td>
                          <td class="text-center"><?php echo $pm['ticket'] ?></td>
                          <td><?php echo str_replace("\r\n", "<br>", $pm['process']) ?></td>
                          <td><?php echo str_replace("\r\n", "<br>", $pm['text']) ?></td>
                          <td><?php echo $pm['created'] ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </table>
                  </div>
                </div>
              <?php endif; ?>

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

  $(".option-select").select2({
    placeholder: "-- เลือก --",
    allowClear: true,
    width: "100%",
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

  $(document).on("click", ".file-delete", function(e) {
    let id = $(this).prop("id");
    e.preventDefault();
    Swal.fire({
      title: "ยืนยันที่จะลบ?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "ยืนยัน",
      cancelButtonText: "ปิด",
    }).then((result) => {
      if (result.value) {
        axios.post("/asset/file-delete", {
          id: id
        }).then((res) => {
          let result = res.data;
          if (result === 200) {
            location.reload()
          } else {
            location.reload()
          }
        }).catch((error) => {
          console.log(error);
        });
      } else {
        return false;
      }
    })
  });
</script>