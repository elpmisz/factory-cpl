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
            <form action="/preventive/check" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
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
                            <td class="text-center"><?php echo $key ?></td>
                            <td class="text-center"><?php echo $mc['code'] ?></td>
                            <td class="text-left"><?php echo $mc['name'] ?></td>
                            <td>
                              <?php echo str_replace("\n", "<br>", $mc['process']) ?>
                            </td>
                            <td>
                              <?php echo str_replace("\n", "<br>", $mc['text']) ?>
                            </td>
                            <td class="text-center">
                              <?php if (!empty($mc['file'])) : ?>
                                <a href="/src/Publics/preventive/<?php echo $mc['file'] ?>" class="badge badge-success font-weight-light" target="_blank">
                                  ดาวน์โหลด
                                </a>
                              <?php else : ?>
                                -
                              <?php endif; ?>
                            </td>
                            <td class="text-center">
                              <a href="javascript:void(0)" class="badge badge-primary font-weight-light checklist-form" id="<?php echo $mc['id'] ?>">
                                รายการตรวจสอบ
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
              </div>

              <hr>
              <div class="h5 text-danger">กรุณาเลือกผลการตรวจสอบ</div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ผลการตรวจสอบ</label>
                <div class="col-xl-8">
                  <div class="row">
                    <div class="col-xl-3">
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="radio" value="5" name="status" id="pass" required>
                        <label class="form-check-label" for="pass">
                          <span class="text-success">ผ่าน</span>
                        </label>
                      </div>
                    </div>
                    <div class="col-xl-3">
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="radio" value="4" name="status" id="nopass" required>
                        <label class="form-check-label" for="nopass">
                          <span class="text-danger">แก้ไขข้อมูล</span>
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
  $(document).on("click", ".checked", function() {
    return false;
  });

  $(document).on("click", "input[name='status']", function() {
    let status = ($(this).val() ? parseInt($(this).val()) : "");
    if (status === 4) {
      $(".remark-input").prop("required", true);
    } else {
      $(".remark-input").prop("required", false);
    }
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
            div += '<div class="row mb-2">';
            div += '<label class="col-xl-8">' + v.checklist_name;
            div += '<input type="hidden" class="form-control form-control-sm" name="checklist_id[]" value="' + checklist_id + '" readonly>';
            div += '<input type="hidden" class="form-control form-control-sm" name="checklist_checklist[]" value="' + checklist_checklist + '" readonly></label>';
            div += '<div class="col-xl-4">';
            div += '<div class="row pb-2">';
            div += '<div class="col-xl-6">';
            div += '<label class="form-check-label px-3">';
            div += '<input class="form-check-input checked" type="radio" name="checklist_result[' + k + ']" value="1" ' + (parseInt(checklist_result) === 1 ? "checked" : "") + ' required>';
            div += '<span class="text-success">ผ่าน</span>';
            div += '</label></div>';
            div += '<div class="col-xl-6">';
            div += '<label class="form-check-label px-3">';
            div += '<input class="form-check-input checked" type="radio" name="checklist_result[' + k + ']" value="2" ' + (parseInt(checklist_result) === 2 ? "checked" : "") + ' required>';
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
</script>