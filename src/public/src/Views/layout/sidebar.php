<?php
$dashboard_menu = (isset($menu) && ($menu === "dashboard") ? "show" : "");
$dashboard_machine = ($page === "dashboard-machine" ? 'class="active"' : "");
$dashboard_counter = ($page === "dashboard-counter" ? 'class="active"' : "");
$dashboard_energy = ($page === "dashboard-energy" ? 'class="active"' : "");

$service_menu = (isset($menu) && ($menu === "service") ? "show" : "");

$user_menu = (isset($menu) && ($menu === "user") ? "show" : "");
$user_profile = ($page === "user-profile" ? 'class="active"' : "");
$user_change = ($page === "user-change" ? 'class="active"' : "");

$setting_menu = (isset($menu) && ($menu === "setting") ? "show" : "");
$setting_system = ($page === "setting-system" ? 'class="active"' : "");
$setting_line = ($page === "setting-line-token" ? 'class="active"' : "");
$setting_user = ($page === "setting-user" ? 'class="active"' : "");
$setting_service = ($page === "setting-service" ? 'class="active"' : "");
$setting_auth = ($page === "setting-auth" ? 'class="active"' : "");
?>
<nav id="sidebar">
  <ul class="list-unstyled">
    <li>
      <a href="/home">หน้าหลัก</a>
    </li>
    <li>
      <a href="#dashboard-menu" data-toggle="collapse" class="dropdown-toggle">รายงาน</a>
      <ul class="collapse list-unstyled <?php echo $dashboard_menu ?>" id="dashboard-menu">
        <li <?php echo $dashboard_machine ?>>
          <a href="/dashboard-machine">
            <i class="fa fa-chart-line pr-2"></i>
            รายงาน MACHINE
          </a>
        </li>
        <li <?php echo $dashboard_counter ?>>
          <a href="/dashboard-counter">
            <i class="fa fa-chart-line pr-2"></i>
            รายงาน COUNTER
          </a>
        </li>
        <li <?php echo $dashboard_energy ?>>
          <a href="/dashboard-energy">
            <i class="fa fa-chart-line pr-2"></i>
            รายงาน ENERGY
          </a>
        </li>
        <?php foreach ($services as $key => $service) : ?>
          <?php
          $auth_check = (isset($user_auth[$key]) ? intval($user_auth[$key]) : "");
          if ($auth_check === 1) :
          ?>
            <li <?php echo ($page === "dashboard-{$service['link']}" ? 'class="active"' : "") ?>>
              <a href="/dashboard-<?php echo $service['link'] ?>">
                <i class="fa fa-chart-line pr-2"></i>
                <?php echo $service['name'] ?>
              </a>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
        <li <?php echo $dashboard_energy ?>>
          <a href="/dashboard-machine-2003">
            <i class="fa fa-chart-line pr-2"></i>
            PU 2003
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="#user-menu" data-toggle="collapse" class="dropdown-toggle">ข้อมูลส่วนตัว</a>
      <ul class="collapse list-unstyled <?php echo $user_menu ?>" id="user-menu">
        <li <?php echo $user_profile ?>>
          <a href="/user/profile">
            <i class="fa fa-address-book pr-2"></i>
            รายละเอียด
          </a>
        </li>
        <li <?php echo $user_change ?>>
          <a href="/user/change">
            <i class="fa fa-key pr-2"></i>
            เปลี่ยนรหัสผ่าน
          </a>
        </li>
      </ul>
    </li>
    <li>
      <a href="#service-menu" data-toggle="collapse" class="dropdown-toggle">
        บริการ
      </a>
      <ul class="collapse list-unstyled <?php echo $service_menu ?>" id="service-menu">
        <li>
          <a href="https://rtls.lailab.online" target="_blank">
            <i class="fa fa-bars pr-2"></i>
            ระบบติดตามวัตถุดิบผลิต
          </a>
        </li>
        <?php foreach ($services as $key => $service) : ?>
          <?php
          $auth_check = (isset($user_auth[$key]) ? intval($user_auth[$key]) : "");
          if ($auth_check === 1) :
          ?>
            <li <?php echo ($page === "service-{$service['link']}" ? 'class="active"' : "") ?>>
              <a href="/<?php echo $service['link']  ?>">
                <i class="fa fa-bars pr-2"></i>
                <?php echo $service['name'] ?><span class="badge badge-danger ml-2"></span>
              </a>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </li>
    <?php if (in_array(intval($user['department']), [45, 88])) : ?>
      <li>
        <a href="#setting-menu" data-toggle="collapse" class="dropdown-toggle">ตั้งค่าระบบ</a>
        <ul class="collapse list-unstyled <?php echo $setting_menu ?>" id="setting-menu">
          <li <?php echo $setting_system ?>>
            <a href="/system">
              <i class="fa fa-gear pr-2"></i>
              ระบบ
            </a>
          </li>
          <li <?php echo $setting_line ?>>
            <a href="/line-token">
              <i class="fa fa-gear pr-2"></i>
              LINE Token
            </a>
          </li>
          <li <?php echo $setting_service ?>>
            <a href="/service">
              <i class="fa fa-gear pr-2"></i>
              เมนูบริการ
            </a>
          </li>
          <li <?php echo $setting_auth ?>>
            <a href="/auth">
              <i class="fa fa-gear pr-2"></i>
              จัดการสิทธิ์
            </a>
          </li>
        </ul>
      </li>
    <?php endif; ?>
  </ul>
</nav>