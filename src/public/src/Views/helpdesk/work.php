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
$spares = $HELPDESK->spares_view([$uuid]);
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
            <form action="/helpdesk/work" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
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
              <div class="h5 text-primary">รายละเอียดการดำเนินการ</div>
              <div class="row mb-2">
                <div class="table-responsive">
                  <table class="table table-sm table-bordered table-hover">
                    <thead>
                      <tr>
                        <th width="10%">#</th>
                        <th width="10%">รับเรื่อง</th>
                        <th width="10%">กำหนดเสร็จ</th>
                        <th width="30%">การดำเนินการ</th>
                        <th width="10%">ค่าใช้จ่าย</th>
                        <th width="20%">ผู้ดำเนินการ</th>
                        <th width="10%">เอกสารแนบ</th>
                        <th width="10%">วันที่</th>
                      </tr>
                    </thead>
                    <?php
                    foreach ($processes as $process) :
                    ?>
                      <tr>
                        <td class="text-center">
                          <span class="badge badge-<?php echo $process['status_color'] ?> font-weight-light">
                            <?php echo $process['status_name'] ?>
                          </span>
                        </td>
                        <td class="text-center"><?php echo $process['start'] ?></td>
                        <td class="text-center"><?php echo $process['end'] ?></td>
                        <td><?php echo str_replace("\r\n", "<br>", $process['text']) ?></td>
                        <td class="text-right"><?php echo $process['cost'] ?></td>
                        <td class="text-center"><?php echo $process['worker'] ?></td>
                        <td class="text-center">
                          <?php if (!empty($process['file'])) : ?>
                            <a href="/src/Publics/helpdesk/<?php echo $process['file'] ?>" class="text-primary" target="_blank">
                              <span class="badge badge-primary font-weight-light">ดาวน์โหลด!</span>
                            </a>
                          <?php endif; ?>
                        </td>
                        <td><?php echo $process['created'] ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </table>
                </div>
              </div>

              <hr>
              <div class="h5 text-danger">อุปกรณ์ที่เปลี่ยน</div>
              <div class="row mb-2">
                <div class="col-xl-10">
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm spare-table">
                      <thead>
                        <tr>
                          <th width="10%">#</th>
                          <th width="50%">อุปกรณ์ที่เปลี่ยน</th>
                          <th width="20%">จำนวน</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($spares as $spare) : ?>
                          <tr>
                            <td class="text-center">
                              <a href="javascript:void(0)" class="badge badge-danger font-weight-light spare-delete" id="<?php echo $spare['id'] ?>">ลบ</a>
                            </td>
                            <td><?php echo $spare['itemcode'] ?></td>
                            <td class="text-center"><?php echo $spare['quantity'] ?></td>
                          </tr>
                        <?php endforeach; ?>
                        <tr class="item-tr">
                          <td class="text-center">
                            <button type="button" class="btn btn-sm btn-success increase-item">+</button>
                            <button type="button" class="btn btn-sm btn-danger decrease-item">-</button>
                          </td>
                          <td class="text-left">
                            <select class="form-control form-control-sm spare-select" name="item_code[]"></select>
                            <div class="invalid-feedback">
                              กรุณากรอกข้อมูล!
                            </div>
                          </td>
                          <td>
                            <input type="number" class="form-control form-control-sm text-center spare-quantity" name="item_quantity[]" min="0">
                            <div class="invalid-feedback">
                              กรุณากรอกข้อมูล!
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <hr>
              <div class="h5 text-danger">การดำเนินการ</div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">รายละเอียด</label>
                <div class="col-xl-6">
                  <textarea class="form-control form-control-sm" name="remark" rows="4" required></textarea>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">กำหนดเสร็จ</label>
                <div class="col-xl-2">
                  <input type="text" class="form-control form-control-sm text-center date-select" name="date" value="<?php echo $row['finish'] ?>" required>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ค่าใช้จ่าย</label>
                <div class="col-xl-2">
                  <input type="number" class="form-control form-control-sm text-center" name="cost" min="1" max="100000">
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">เอกสารแนบ</label>
                <div class="col-xl-4">
                  <input type="file" class="form-control-file" name="file" accept=".jpeg, .png, .jpg, .xls, .xlsx, .doc, .docx, .pdf">
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ผู้ดำเนินการ</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm user-select" name="worker_id" required>
                    <?php echo '<option value="' . $user['user_id'] . '">' . $user['fullname'] . '</option>' ?>
                  </select>
                  <div class="invalid-feedback">
                    กรุณากรอกข้อมูล!
                  </div>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">สถานะ</label>
                <div class="col-xl-4">
                  <select class="form-control form-control-sm option-select" name="status" required>
                    <option value="">-- เลือก --</option>
                    <?php
                    $status = [
                      4 => 'อยู่ระหว่างดำเนินการ',
                      5 => 'รออุปกรณ์ / รออะไหล่',
                      7 => 'ดำเนินการเรียบร้อย'
                    ];
                    foreach ($status as $key => $value) {
                      echo "<option value='{$key}'>{$value}</option>\r\n";
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
  $(document).on("change", ".spare-select", function() {
    let item = ($(this).val() ? $(this).val() : "");
    let row = $(this).closest("tr");
    if (item) {
      row.find(".spare-quantity").prop("required", true);
    } else {
      row.find(".spare-quantity").prop("required", false);
    }
  });

  $(".decrease-item").hide();
  $(document).on("click", ".increase-item", function() {
    $(".spare-select").select2("destroy");
    let row = $(".item-tr:last");
    let clone = row.clone();
    clone.find("input, select").val("");
    clone.find(".increase-item").hide();
    clone.find(".decrease-item").show();
    clone.find(".decrease-item").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();

    $(".spare-select").select2({
      placeholder: "-- เลือก --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/helpdesk/spare-select",
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

  $(".spare-select").select2({
    placeholder: "-- เลือก --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/helpdesk/spare-select",
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

  $(document).on("click", ".spare-delete", function(e) {
    let id = $(this).prop("id");
    console.log(id)
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
        axios.post("/helpdesk/spare-delete", {
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

  $(document).on("change", "input[name='file']", function() {
    let file = ($(this).val() ? $(this).val() : "");
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
</script>