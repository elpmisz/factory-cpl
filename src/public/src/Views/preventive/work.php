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
$processes = $PREVENTIVE->process_view([$uuid]);
$checklist_count = $PREVENTIVE->checklist_check([$row['type_id']]);
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
            <form action="/preventive/work" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
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
                  <div class="h5 text-primary">รายการอุปกรณ์</div>
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm machine-table">
                      <tr>
                        <th width="5%">#</th>
                        <th width="10%">รหัสอุปกรณ์</th>
                        <th width="20%">ชื่อ</th>
                        <th width="20%">การดำเนินการ</th>
                        <th width="20%">หมายเหตุ</th>
                        <th width="10%">แนบเอกสาร</th>
                        <th width="10%">รายการตรวจสอบ</th>
                      </tr>
                      <?php
                      foreach ($machine as $key => $mc) :
                        if (!empty($mc['id'])) :
                          $key++;
                      ?>
                          <tr>
                            <td class="text-center">
                              <?php echo $key ?>
                              <input type="hidden" name="item_id[]" value="<?php echo $mc['id'] ?>" readonly>
                            </td>
                            <td class="text-center"><?php echo $mc['code'] ?></td>
                            <td class="text-left"><?php echo $mc['name'] ?></td>
                            <td>
                              <textarea class="form-control form-control-sm" name="item_process[]" rows="2"><?php echo $mc['process'] ?></textarea>
                            </td>
                            <td>
                              <textarea class="form-control form-control-sm" name="item_text[]" rows="2"><?php echo $mc['text'] ?></textarea>
                            </td>
                            <td class="text-center">
                              <?php if (!empty($mc['file'])) : ?>
                                <a href="/src/Publics/preventive/<?php echo $mc['file'] ?>" class="badge badge-success font-weight-light" target="_blank">
                                  ดาวน์โหลด
                                </a>
                                <a href="javascript:void(0)" class="badge badge-danger font-weight-light file-delete" id="<?php echo $mc['id'] ?>">
                                  ลบ
                                </a>
                              <?php else : ?>
                                <a href="javascript:void(0)" class="badge badge-primary font-weight-light file-form" id="<?php echo $mc['id'] ?>">
                                  แนบเอกสาร
                                </a>
                              <?php endif; ?>
                            </td>
                            <td class="text-center">
                              <?php if (!empty($checklist_count)) : ?>
                                <a href="javascript:void(0)" class="badge badge-primary font-weight-light checklist-form" id="<?php echo $mc['id'] ?>">
                                  รายการตรวจสอบ
                                </a>
                              <?php else : ?>
                                -
                              <?php endif; ?>
                            </td>
                          </tr>
                      <?php
                        endif;
                      endforeach;
                      ?>
                      <tr class="tr-machine">
                        <td class="text-center">
                          <button type="button" class="btn btn-sm btn-success machine-increase">+</button>
                          <button type="button" class="btn btn-sm btn-danger machine-decrease">-</button>
                        </td>
                        <td class="text-center"><span class="machine-code"></span></td>
                        <td>
                          <select class="form-control form-control-sm machine-select " name="machine[]"></select>
                        </td>
                        <td class="text-center" colspan="4"></td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>

              <?php if (COUNT($processes) > 0) : ?>
                <hr>
                <div class="h5 text-primary">รายละเอียดการดำเนินการ</div>
                <div class="row mb-2">
                  <div class="table-responsive">
                    <table class="table table-sm table-bordered table-hover">
                      <thead>
                        <tr>
                          <th width="10%">#</th>
                          <th width="40%">รายละเอียด</th>
                          <th width="20%">ผู้ดำเนินการ</th>
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
                          <td><?php echo str_replace("\r\n", "<br>", $process['text']) ?></td>
                          <td class="text-center"><?php echo $process['worker'] ?></td>
                          <td><?php echo $process['created'] ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </table>
                  </div>
                </div>
              <?php endif; ?>

              <div class="h5 text-danger">กรุณาเลือกการดำเนินการ</div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">สถานะ</label>
                <div class="col-xl-8">
                  <div class="row">
                    <div class="col-xl-3">
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="radio" value="2" name="status" id="pass" required>
                        <label class="form-check-label" for="pass">
                          <span class="text-success">ปรับปรุงข้อมูล</span>
                        </label>
                      </div>
                    </div>
                    <div class="col-xl-3">
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="radio" value="3" name="status" id="nopass" required>
                        <label class="form-check-label" for="nopass">
                          <span class="text-danger">ดำเนินการเรียบร้อย</span>
                        </label>
                      </div>
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

<div class="modal fade file-modal" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <form action="/preventive/file-add" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          <h4 class="text-center">แนบเอกสาร</h4>
          <div class="row" style="display: none;">
            <label class="col-xl-4 col-md-4 col-form-label text-xl-right">UUID</label>
            <div class="col-xl-6">
              <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $row['uuid'] ?>" readonly>
            </div>
          </div>
          <div class="row" style="display: none;">
            <label class="col-xl-4 col-md-4 col-form-label text-xl-right">ITEM</label>
            <div class="col-xl-6">
              <input type="text" class="form-control form-control-sm item_id" name="item" readonly>
            </div>
          </div>
          <div class="row">
            <label class="col-xl-4 col-md-4 col-form-label text-xl-right">เลือกเอกสาร</label>
            <div class="col-xl-7">
              <input type="file" class="form-control form-control-sm" name="file" accept=".jpeg, .png, .jpg, .pdf, .doc, .docx, .xls, .xlsx">
            </div>
          </div>

          <div class="row justify-content-center">
            <div class="col-sm-5 mb-2">
              <button type="submit" class="btn btn-success btn-sm btn-block">
                <i class="fa fa-check mr-2"></i>ยืนยัน
              </button>
            </div>
            <div class="col-sm-5 mb-2">
              <button type="button" class="btn btn-danger btn-sm btn-block" data-dismiss="modal">
                <i class="fa fa-times mr-2"></i>ปิด
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade checklist-modal" data-backdrop="static">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header justify-content-center">
        <h4>รายการตรวจสอบ</h4>
      </div>
      <div class="modal-body">
        <form action="/preventive/checklist-add" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
          <div class="row" style="display: none;">
            <label class="col-xl-4 col-md-4 col-form-label text-xl-right">UUID</label>
            <div class="col-xl-6">
              <input type="text" class="form-control form-control-sm" name="uuid" value="<?php echo $row['uuid'] ?>" readonly>
            </div>
          </div>
          <div class="row" style="display: none;">
            <label class="col-xl-4 col-md-4 col-form-label text-xl-right">ITEM</label>
            <div class="col-xl-6">
              <input type="text" class="form-control form-control-sm item_id" name="item" readonly>
            </div>
          </div>

          <div class="checklist-div"></div>

          <div class="row justify-content-center">
            <div class="col-sm-5 mb-2">
              <button type="submit" class="btn btn-success btn-sm btn-block">
                <i class="fa fa-check mr-2"></i>ยืนยัน
              </button>
            </div>
            <div class="col-sm-5 mb-2">
              <button type="button" class="btn btn-danger btn-sm btn-block" data-dismiss="modal">
                <i class="fa fa-times mr-2"></i>ปิด
              </button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  let id = ($(".id").val() ? $(".id").val() : "");
  let uuid = ($(".uuid").val() ? $(".uuid").val() : "");
  let type = ($(".type-id").val() ? $(".type-id").val() : "");

  $(document).on("click", ".file-form", function() {
    let item = ($(this).prop("id") ? $(this).prop("id") : "");
    $(".item_id").val(item);
    $(".file-modal").modal("show");
  });

  $(document).on("click", ".checklist-form", function() {
    let item = ($(this).prop("id") ? $(this).prop("id") : "");
    $(".item_id").val(item);
    $(".checklist-modal").modal("show");

    axios.post("/preventive/checklist-detail", {
        item: item
      })
      .then((res) => {
        let result = res.data;

        if (result.length > 0) {
          let div = '';
          result.forEach((v, k) => {
            let checklist_id = (v.id ? v.id : "");
            let checklist_item = v.item_id
            let checklist_checklist = v.checklist_id
            let checklist_result = (v.result ? v.result : "")
            div += '<div class="row">';
            div += '<label class="col-xl-8">' + v.checklist_name;
            div += '<input type="hidden" class="form-control form-control-sm" name="checklist_id[]" value="' + checklist_id + '" readonly>';
            div += '<input type="hidden" class="form-control form-control-sm" name="checklist_checklist[]" value="' + checklist_checklist + '" readonly></label>';
            div += '<div class="col-xl-4">';
            div += '<div class="row pb-2">';
            div += '<div class="col-xl-6">';
            div += '<label class="form-check-label px-3">';
            div += '<input class="form-check-input" type="radio" name="checklist_result[' + k + ']" value="1" ' + (parseInt(checklist_result) === 1 ? "checked" : "") + ' required>';
            div += '<span class="text-success">ผ่าน</span>';
            div += '</label></div>';
            div += '<div class="col-xl-6">';
            div += '<label class="form-check-label px-3">';
            div += '<input class="form-check-input" type="radio" name="checklist_result[' + k + ']" value="2" ' + (parseInt(checklist_result) === 2 ? "checked" : "") + ' required>';
            div += '<span class="text-danger">ไม่ผ่าน</span>';
            div += '</label></div>';
            div += '</div></div></div>';
          });

          $(".checklist-div").empty().html(div);
        } else {
          $(".checklist-div").empty().html();
        }
      }).catch((error) => {
        console.log(error);
      });
  });

  $(".machine-select").select2({
    placeholder: "-- ทรัพย์สิน --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/preventive/machine-select",
      method: "POST",
      data: function(params) {
        return {
          keyword: params.term,
          id: id,
          type: type,
        }
      },
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

  $(".machine-decrease").hide();
  $(document).on("click", ".machine-increase", function() {
    $(".machine-select").select2('destroy');
    let row = $(".tr-machine:last");
    let clone = row.clone();
    clone.find("input, select").val("");
    clone.find("span").empty();
    clone.find(".machine-increase").hide();
    clone.find(".machine-decrease").show();
    clone.find(".machine-decrease").on("click", function() {
      $(this).closest("tr").remove();
    });
    row.after(clone);
    clone.show();

    $(".machine-select").select2({
      placeholder: "-- ทรัพย์สิน --",
      allowClear: true,
      width: "100%",
      ajax: {
        url: "/preventive/machine-select",
        method: "POST",
        data: function(params) {
          return {
            keyword: params.term,
            id: id,
            type: type,
          }
        },
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

  $(document).on("click", ".file-delete", function(e) {
    let item = ($(this).prop("id") ? $(this).prop("id") : "");
    e.preventDefault();
    Swal.fire({
      title: "ยืนยันที่จะทำรายการ?",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "ยืนยัน",
      cancelButtonText: "ปิด",
    }).then((result) => {
      if (result.value) {
        axios.post("/preventive/file-delete", {
          item: item
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