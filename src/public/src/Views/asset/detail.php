<?php

use App\Classes\Machine;

$page = "setting-asset";
$menu = "setting";

include_once(__DIR__ . "/../../../layout/header.php");
include_once(__DIR__ . "/../../vendor/autoload.php");

$MACHINE = new Machine();

$param = (isset($params) ? explode("/", $params) : header("Location: /error"));
$id = (isset($param[0]) ? $param[0] : die(header("Location: /error")));

$row = $MACHINE->view([$id]);
$items = $MACHINE->item_view([$id]);
$images = $MACHINE->image_view([$id]);
$url = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
?>

<div class="row mb-2">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ASSET</h4>
      </div>
      <div class="card-body">
        <div class="row justify-content-end mb-2">
          <div class="col-xl-12">
            <form action="javascript:void()" method="POST" class="needs-validation" novalidate enctype="multipart/form-data">


              <div class="row justify-content-end mb-2">
                <?php if (COUNT($images) !== 0) : ?>
                  <div class="col-xl-4 offset-xl-4">
                    <div id="control" class="carousel slide" data-ride="carousel">
                      <div class="carousel-inner">
                        <?php foreach ($images as $key => $image) : ?>
                          <div class="carousel-item <?php echo ($key === 0 ? "active" : "") ?>">
                            <img src="/factory/page/asset/public/uploads/<?php echo $image['name'] ?>" class="d-block w-100 machine-image" alt="machine-image">
                          </div>
                        <?php endforeach; ?>
                      </div>
                      <button class="carousel-control-prev" type="button" data-target="#control" data-slide="prev">
                      </button>
                      <button class="carousel-control-next" type="button" data-target="#control" data-slide="next">
                      </button>
                    </div>
                  </div>
                <?php endif; ?>
                <div class="col-xl-4">
                  <img src="https://chart.googleapis.com/chart?chs=200x200&cht=qr&chl={$url}&choe=UTF-8" title="qr machine">
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-4 col-form-label text-xl-right">NAME</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $row['name'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label text-xl-right">ASSET NO.</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['code'] ?>
                </div>
              </div>
              <div class="row mb-2">
                <label class="col-xl-4 col-form-label text-xl-right">TYPE</label>
                <div class="col-xl-4 text-underline">
                  <?php echo $row['type_name'] ?>
                </div>
              </div>

              <div class="row mb-2">
                <div class="col-xl-6">
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">DEPARTMENT</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['department_name'] ?>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">LOCATION</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['location_name'] ?>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">SERIAL NUMBER</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['serial_number'] ?>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">ASSET CODE</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['asset_code'] ?>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">kW</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['kw'] ?>
                    </div>
                  </div>
                </div>

                <div class="col-xl-6">
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">BRAND</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['brand_name'] ?>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">MODEL</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['model_name'] ?>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">PURCHASE</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['purchase'] ?>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <label class="col-xl-4 col-form-label text-xl-right">EXPIRE</label>
                    <div class="col-xl-8 text-underline">
                      <?php echo $row['expire'] ?>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-2">
                <label class="col-xl-4 col-form-label text-xl-right">REMARK</label>
                <div class="col-xl-6 text-underline">
                  <?php echo $row['text'] ?>
                </div>
              </div>

              <div class="row justify-content-center mb-2">
                <div class="col-xl-3 mb-2">
                  <a href="/factory/asset/" class="btn btn-sm btn-danger btn-block">
                    <i class="fa fa-arrow-left pr-2"></i>BACK TO HOME
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


<? include_once(__DIR__ . "/../../../layout/footer.php"); ?>