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
            <form action="/helpdesk/check" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">
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

              <?php if (intval($row['asset']) === 1) : ?>
                <hr>
                <div class="h5 text-primary">ตรวจสภาพเครื่องก่อนดำเนินการซ่อม</div>
                <div class="row">
                  <div class="col-xl-6">
                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">ระดับน้ำมัน</label>
                      <div class="col-xl-8">
                        <div class="row">
                          <div class="col-xl-6">
                            <div class="form-check pt-2">
                              <input class="form-check-input" type="radio" value="1" name="oil" id="oil-pass" <?php echo ($row['oil'] === 1 ? "checked" : "") ?> required>
                              <label class="form-check-label" for="oil-pass">
                                <span class="text-success">ปกติ</span>
                              </label>
                            </div>
                          </div>
                          <div class="col-xl-6">
                            <div class="form-check pt-2">
                              <input class="form-check-input" type="radio" value="2" name="oil" id="oil-nopass" <?php echo ($row['oil'] === 2 ? "checked" : "") ?> required>
                              <label class="form-check-label" for="oil-nopass">
                                <span class="text-danger">ต่ำ</span>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">สายไฟ</label>
                      <div class="col-xl-8">
                        <div class="row">
                          <div class="col-xl-6">
                            <div class="form-check pt-2">
                              <input class="form-check-input" type="radio" value="1" name="cable" id="cable-pass" <?php echo ($row['cable'] === 1 ? "checked" : "") ?> required>
                              <label class="form-check-label" for="cable-pass">
                                <span class="text-success">ปกติ</span>
                              </label>
                            </div>
                          </div>
                          <div class="col-xl-6">
                            <div class="form-check pt-2">
                              <input class="form-check-input" type="radio" value="2" name="cable" id="cable-nopass" <?php echo ($row['cable'] === 2 ? "checked" : "") ?> required>
                              <label class="form-check-label" for="cable-nopass">
                                <span class="text-danger">ชำรุด</span>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">อุปกรณ์</label>
                      <div class="col-xl-8">
                        <div class="row">
                          <div class="col-xl-6">
                            <div class="form-check pt-2">
                              <input class="form-check-input" type="radio" value="1" name="tool" id="tool-pass" <?php echo ($row['tool'] === 1 ? "checked" : "") ?> required>
                              <label class="form-check-label" for="tool-pass">
                                <span class="text-success">ปกติ</span>
                              </label>
                            </div>
                          </div>
                          <div class="col-xl-6">
                            <div class="form-check pt-2">
                              <input class="form-check-input" type="radio" value="2" name="tool" id="tool-nopass" <?php echo ($row['tool'] === 2 ? "checked" : "") ?> required>
                              <label class="form-check-label" for="tool-nopass">
                                <span class="text-danger">ชำรุด</span>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-xl-6">
                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">การทำงาน</label>
                      <div class="col-xl-8">
                        <div class="row">
                          <div class="col-xl-6">
                            <div class="form-check pt-2">
                              <input class="form-check-input" type="radio" value="1" name="work" id="work-pass" <?php echo ($row['work'] === 1 ? "checked" : "") ?> required>
                              <label class="form-check-label" for="work-pass">
                                <span class="text-success">ปกติ</span>
                              </label>
                            </div>
                          </div>
                          <div class="col-xl-6">
                            <div class="form-check pt-2">
                              <input class="form-check-input" type="radio" value="2" name="work" id="work-nopass" <?php echo ($row['work'] === 2 ? "checked" : "") ?> required>
                              <label class="form-check-label" for="work-nopass">
                                <span class="text-danger">ผิดปกติ</span>
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <label class="col-xl-4 col-form-label">การแก้ไข</label>
                    </div>
                    <div class="row mb-2">
                      <div class="col-xl-4">
                        <div class="form-check pt-2">
                          <input class="form-check-input" type="radio" value="1" name="fix" id="fix-change" <?php echo ($row['fix'] === 1 ? "checked" : "") ?> required>
                          <label class="form-check-label" for="fix-change">
                            <span class="text-success">เปลี่ยนอะไหล่สิ้นเปลือง</span>
                          </label>
                        </div>
                      </div>
                      <div class="col-xl-4">
                        <div class="form-check pt-2">
                          <input class="form-check-input" type="radio" value="2" name="fix" id="fix-pass" <?php echo ($row['fix'] === 2 ? "checked" : "") ?> required>
                          <label class="form-check-label" for="fix-pass">
                            <span class="text-primary">ซ่อมภายใน</span>
                          </label>
                        </div>
                      </div>
                      <div class="col-xl-4">
                        <div class="form-check pt-2">
                          <input class="form-check-input" type="radio" value="3" name="fix" id="fix-nopass" <?php echo ($row['fix'] === 3 ? "checked" : "") ?> required>
                          <label class="form-check-label" for="fix-nopass">
                            <span class="text-danger">ซ่อมภายนอก</span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-xl-6">
                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">ปัญหาที่เกิดขึ้นคือ</label>
                      <div class="col-xl-8 text-underline">
                        <?php echo str_replace("\n", "<br>", $row['what']) ?>
                      </div>
                    </div>
                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">ทำไมจึงเกิดปัญหา</label>
                      <div class="col-xl-8 text-underline">
                        <?php echo str_replace("\n", "<br>", $row['why']) ?>
                      </div>
                    </div>
                  </div>

                  <div class="col-xl-6">
                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">ถ้าปล่อยทิ้งไว้จะเกิดอะไร</label>
                      <div class="col-xl-8 text-underline">
                        <?php echo str_replace("\n", "<br>", $row['when']) ?>
                      </div>
                    </div>
                  </div>

                  <div class="col-xl-6">
                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">ใบจ่ายของเลขที่</label>
                      <div class="col-xl-8 text-underline">
                        <?php echo $row['pay'] ?>
                      </div>
                    </div>
                  </div>

                  <div class="col-xl-6">
                    <div class="row mb-2">
                      <label class="col-xl-4 col-form-label">ใบ PR เลขที่</label>
                      <div class="col-xl-8 text-underline">
                        <?php echo $row['pr'] ?>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

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
                        <td class="text-right"><?php echo (intval($process['cost']) === 0 ? "-" : $process['cost']) ?></td>
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

              <?php if (count($spares) > 0) : ?>
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
                          <?php foreach ($spares as $key => $spare) : $key++; ?>
                            <tr>
                              <td class="text-center"><?php echo $key ?></td>
                              <td><?php echo $spare['itemcode'] ?></td>
                              <td class="text-center"><?php echo $spare['quantity'] ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <hr>
              <div class="h5 text-danger">กรุณาเลือกผลการตรวจสอบ</div>
              <div class="row mb-2">
                <label class="col-xl-2 col-form-label">ผลการตรวจสอบ</label>
                <div class="col-xl-8">
                  <div class="row">
                    <div class="col-xl-3">
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="radio" value="8" name="status" id="pass" required>
                        <label class="form-check-label" for="pass">
                          <span class="text-success">ผ่าน</span>
                        </label>
                      </div>
                    </div>
                    <div class="col-xl-3">
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="radio" value="6" name="status" id="nopass" required>
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
  $(document).on("click", "input[name='oil'], input[name='cable'], input[name='tool'], input[name='work'], input[name='fix']", function(e) {
    e.preventDefault();
  });

  $(document).on("click", "input[name='status']", function() {
    let status = ($(this).val() ? parseInt($(this).val()) : "");
    if (status === 6) {
      $(".remark-input").prop("required", true);
    } else {
      $(".remark-input").prop("required", false);
    }
  });
</script>