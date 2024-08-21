<?php
$menu = "dashboard";
$page = "dashboard-machine";

include_once(__DIR__ . "/../layout/header.php");

use App\Classes\DashboardMachine;

$DASHBOARD = new DashboardMachine();

$url = "https://plan.smartsensedesign.net/api/machine";
$json = file_get_contents($url);
$data = json_decode($json, true);
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">รายงาน MACHINE</h4>
      </div>
      <div class="card-body">

        <div class="row">
          <?php
          foreach ($data as $row) :
          ?>
            <div class="col-xl-6 mb-2">
              <div class="card shadow">
                <div class="card-header bg-<?php echo $row['status_color'] ?>">
                  <h5 class="text-white"><?php echo $row['machine'] . " : " . $row['status_name'] ?></h5>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-xl-6 mb-2">
                      <div class="card shadow">
                        <div class="card-body">
                          <h3 class="text-right"><?php echo $row['diff'] ?></h3>
                          <h5 class="text-right"><i class="fa fa-clock pr-2"></i>TIME</h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-xl-6 mb-2">
                      <div class="card shadow">
                        <div class="card-body">
                          <h3 class="text-right"><?php echo $row['job'] ?></h3>
                          <h5 class="text-right"><i class="fa fa-file-lines pr-2"></i>JOB</h5>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-xl-6 mb-2">
                      <div class="card shadow">
                        <div class="card-body">
                          <h3 class="text-right"><?php echo $row['target'] ?></h3>
                          <h5 class="text-right"><i class="fa fa-bullseye pr-2"></i>PLAN</h5>
                        </div>
                      </div>
                    </div>
                    <div class="col-xl-6 mb-2">
                      <div class="card shadow">
                        <div class="card-body">
                          <h3 class="text-right"><?php echo $row['output'] ?></h3>
                          <h5 class="text-right"><i class="fa fa-download pr-2"></i>ACTUAL</h5>
                        </div>
                      </div>
                    </div>
                  </div>

                  <?php if (intval($row['energy_started']) === 0) : ?>
                    <div class="row">
                      <div class="col-xl-6 mb-2">
                        <div class="card shadow">
                          <div class="card-body">
                            <h3 class="text-right"><?php echo $row['drive_diff'] ?></h3>
                            <h5 class="text-right"><i class="fa fa-plug pr-2"></i>ENERGY (DRIVE)</h5>
                          </div>
                        </div>
                      </div>
                      <div class="col-xl-6 mb-2">
                        <div class="card shadow">
                          <div class="card-body">
                            <h3 class="text-right"><?php echo $row['weld_diff'] ?></h3>
                            <h5 class="text-right"><i class="fa fa-plug pr-2"></i>ENERGY (WELD)</h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php else : ?>
                    <div class="row">
                      <div class="col-xl-12 mb-2">
                        <div class="card shadow">
                          <div class="card-body">
                            <h3 class="text-right"><?php echo $row['energy_diff'] ?></h3>
                            <h5 class="text-right"><i class="fa fa-plug pr-2"></i>ENERGY</h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>

                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      </div>
    </div>
  </div>
</div>


<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  window.setTimeout(function() {
    window.location.reload();
  }, 10000);
</script>