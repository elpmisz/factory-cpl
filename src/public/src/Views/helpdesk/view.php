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
            <form action="/helpdesk/update" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
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
              <div class="row mb-2" style="display: none;">
                <label class="col-xl-2 offset-xl-2 col-form-label">SERVICE</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="service_id" value="<?php echo $row['service_id'] ?>" readonly>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">เลขที่เอกสาร</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['ticket'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ผู้ใช้บริการ</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['username'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ข้อมูลติดต่อ</label>
                <div class="col-xl-4">
                  <input type="text" class="form-control form-control-sm" name="contact" value="<?php echo $row['contact'] ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">หัวข้อบริการ</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['service_name'] ?>
                </div>
              </div>
              <?php if (!empty($row['asset_id']) || intval($row['asset_id']) > 0) : ?>
                <div class="row mb-2 asset-div">
                  <label class="col-xl-2 offset-xl-2 col-form-label">ทรัพย์สิน</label>
                  <div class="col-xl-6">
                    <select class="form-control form-control-sm asset-select" name="asset">
                      <?php echo "<option value='{$row['asset_id']}'>{$row['asset_name']}</option>"; ?>
                    </select>
                    <div class="invalid-feedback">
                      กรุณากรอกข้อมูล!
                    </div>
                  </div>
                </div>

                <div class="row mb-2 asset-detail-div">
                  <div class="col-xl-6">
                    <div class="row">
                      <label class="col-xl-3 offset-xl-1 col-form-label">เลขที่ทรัพย์สิน</label>
                      <div class="col-xl-6">
                        <input type="text" class="form-control form-control-sm asset-assetcode" value="<?php echo $row['asset_assetcode'] ?>" readonly>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-xl-3 offset-xl-1 col-form-label">ฝ่าย/แผนก</label>
                      <div class="col-xl-6">
                        <input type="text" class="form-control form-control-sm asset-department" value="<?php echo $row['asset_department'] ?>" readonly>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-xl-3 offset-xl-1 col-form-label">สถานที่</label>
                      <div class="col-xl-6">
                        <input type="text" class="form-control form-control-sm asset-location" value="<?php echo $row['asset_location'] ?>" readonly>
                      </div>
                    </div>
                  </div>

                  <div class="col-xl-6">
                    <div class="row">
                      <label class="col-xl-3 offset-xl-1 col-form-label">รหัสอุปกรณ์</label>
                      <div class="col-xl-6">
                        <input type="text" class="form-control form-control-sm asset-code" value="<?php echo $row['asset_code'] ?>" readonly>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-xl-3 offset-xl-1 col-form-label">ยี่ห้อ</label>
                      <div class="col-xl-6">
                        <input type="text" class="form-control form-control-sm asset-brand" value="<?php echo $row['asset_brand'] ?>" readonly>
                      </div>
                    </div>
                    <div class="row">
                      <label class="col-xl-3 offset-xl-1 col-form-label">รุ่น</label>
                      <div class="col-xl-6">
                        <input type="text" class="form-control form-control-sm asset-model" value="<?php echo $row['asset_model'] ?>" readonly>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <div class="row mb-2 specific-field-div">
                <div class="col-xl-12">
                  <?php
                  if (COUNT($items) > 0) :
                    foreach ($items as $item) :
                  ?>
                      <div class="row mb-2">
                        <label class="col-xl-2 offset-xl-2 col-form-label">
                          <?php echo $item['item_name'] ?>
                        </label>
                        <div class="col-xl-4">
                          <input type="hidden" class="form-control form-control-sm" name="item_id[]" value="<?php echo $item['item_id'] ?>" required>
                          <input type="hidden" class="form-control form-control-sm" name="item_type[]" value="<?php echo $item['item_type'] ?>" required>
                          <?php if (intval($item['item_type']) === 1) : ?>
                            <input type="text" class="form-control form-control-sm" name="item_value[]" value="<?php echo $item['item_value'] ?>" required>
                          <?php endif; ?>
                          <?php if (intval($item['item_type']) === 2) : ?>
                            <input type="number" class="form-control form-control-sm" name="item_value[]" value="<?php echo $item['item_value'] ?>" step="0.01" required>
                          <?php endif; ?>
                          <?php if (intval($item['item_type']) === 3) : ?>
                            <select class="form-control form-control-sm option-select" name="item_value[]" required>
                              <option value="">-- เลือก --</option>
                              <?php
                              $choice = explode(",", $item['item_text']);
                              foreach ($choice as $key => $value) {
                                echo "<option value='{$key}' " . ($key === intval($item['item_value']) ? "selected" : "") . ">{$value}</option>";
                              }
                              ?>
                            </select>
                          <?php endif; ?>
                          <?php if (intval($item['item_type']) === 4) : ?>
                            <input type="text" class="form-control form-control-sm date-select" name="item_value[]" value="<?php echo $item['item_value'] ?>" required>
                          <?php endif; ?>
                        </div>
                        <div class="invalid-feedback">
                          กรุณากรอกข้อมูล!
                        </div>
                      </div>
                  <?php
                    endforeach;
                  endif;
                  ?>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">ปัญหาที่พบ</label>
                <div class="col-xl-6">
                  <textarea class="form-control form-control-sm" name="text" rows="5" required><?php echo $row['text'] ?></textarea>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 offset-xl-2 col-form-label">เอกสารแนบ</label>
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
                          <td>
                            <a href="javascript:void(0)" class="file-delete" id="<?php echo $file['file_id'] ?>">
                              <span class="badge badge-danger font-weight-light">ลบ!</span>
                            </a>
                          </td>
                        </tr>
                    <?php
                      endif;
                    endforeach;
                    ?>
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
        axios.post("/helpdesk/file-delete", {
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