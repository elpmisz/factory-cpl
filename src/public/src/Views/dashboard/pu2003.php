<?php
$menu = "dashboard";
$page = "dashboard-2003";
include_once(__DIR__ . "/../layout/header.php");

use App\Classes\Helpdesk;

$HELPDESK = new Helpdesk();
$card = $HELPDESK->helpdesk_card();
?>
<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-body">
        <iframe title="factory-preventive" width="100%" height="900" src="https://cplmon-aws.smartsensedesign.net/d/7a8aae1f-3c88-4c89-8aac-cb22359e4270/oee-keynote-pu2003?orgId=1&refresh=5s&from=1720139659376&to=1720161259376" frameborder="0" allowFullScreen="true"></iframe>
      </div>
    </div>
  </div>
</div>

<?php include_once(__DIR__ . "/../layout/footer.php"); ?>