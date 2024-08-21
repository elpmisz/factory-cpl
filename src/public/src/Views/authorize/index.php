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
                    <th width="30%">ชื่อ</th>
                    <?php foreach ($services as $service) : ?>
                      <th><?php echo $service['name'] ?></th>
                    <?php endforeach; ?>
                  </tr>
                  <?php foreach ($users as $us) :  ?>
                    <tr>
                      <form action="/auth/update" method="POST">
                        <td class="text-center">
                          <button class="btn btn-success btn-sm" type="submit"><i class="fa fa-check"></i></button>
                          <input type="hidden" name="uuid" value="<?php echo $us['uuid'] ?>" readonly>
                        </td>
                        <td><?php echo $us['fullname'] ?></td>
                        <?php
                        $auths = explode(",", $us['auth']);
                        foreach ($services as $key => $value) :
                          $checked = (isset($auths[$key]) ? $auths[$key] : "");
                        ?>
                          <td class="text-center">
                            <input type="hidden" name="<?php echo "auth[{$key}]" ?>" value="0">
                            <input type="checkbox" name="<?php echo "auth[{$key}]" ?>" value="1" <?php echo (intval($checked) === 1 ? "checked" : "") ?>>
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