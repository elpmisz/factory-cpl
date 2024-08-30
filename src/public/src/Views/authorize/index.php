<?php
$menu = "setting";
$page = "setting-auth";
include_once(__DIR__ . "/../layout/header.php");

$users = $USER->user_read();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">จัดการสิทธิ์</h4>
      </div>
      <div class="card-body">

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th width="10%">#</th>
                    <th width="20%">ชื่อ</th>
                    <th width="20%">ฝ่าย</th>
                    <?php foreach ($services as $service) : ?>
                      <th><?php echo $service['name'] ?></th>
                    <?php endforeach; ?>
                  </tr>
                  <?php foreach ($users as $row) :  ?>
                    <tr>
                      <form action="/auth/update" method="POST">
                        <td class="text-center">
                          <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-check"></i></button>
                          <input type="hidden" name="user_id" value="<?php echo $row['user_id'] ?>" readonly>
                        </td>
                        <td><?php echo $row['fullname'] ?></td>
                        <td><?php echo $row['department_name'] ?></td>
                        <?php
                        $auth = explode(",", $row['service']);
                        foreach ($services as $key => $value) :
                          $checked = (isset($auth[$key]) ? $auth[$key] : "");
                        ?>
                          <td class="text-center">
                            <input type="hidden" name="<?php echo "service[{$key}]" ?>" value="0">
                            <input type="checkbox" name="<?php echo "service[{$key}]" ?>" value="1" <?php echo (intval($checked) === 1 ? "checked" : "") ?>>
                          </td>
                        <?php endforeach; ?>
                      </form>
                    </tr>
                  <?php endforeach; ?>
                </thead>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>