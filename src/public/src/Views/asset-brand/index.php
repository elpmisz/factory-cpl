<?php
$menu = "service";
$page = "service-asset";
include_once(__DIR__ . "/../layout/header.php");
?>

<div class="row">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header">
        <h4 class="text-center">ยี่ห้อ/รุ่น</h4>
      </div>
      <div class="card-body">

        <div class="row justify-content-end mb-2">
          <div class="col-xl-3 mb-2">
            <select class="form-control form-control-sm brand-select" name="reference"></select>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/asset/brand/export" class="btn btn-sm btn-success btn-block">
              <i class="fa fa-download pr-2"></i>นำข้อมูลออก
            </a>
          </div>
          <div class="col-xl-3 mb-2">
            <a href="/asset/brand/create" class="btn btn-sm btn-primary btn-block">
              <i class="fa fa-plus pr-2"></i>เพิ่ม
            </a>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col-xl-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover data">
                <thead>
                  <tr>
                    <th width="10%">#</th>
                    <th width="50%">ชื่อ</th>
                    <th width="40%">ยี่ห้อ</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>

        <div class="row justify-content-center mb-2">
          <div class="col-xl-3">
            <a href="/asset" class="btn btn-sm btn-danger btn-block">
              <i class="fa fa-arrow-left pr-2"></i>กลับ
            </a>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<?php include_once(__DIR__ . "/../layout/footer.php"); ?>
<script>
  filter_datatable();

  $(document).on("change", ".brand-select", function() {
    let brand = ($(this).val() ? $(this).val() : "");
    if (brand) {
      $(".data").DataTable().destroy();
      filter_datatable(brand);
    } else {
      $(".data").DataTable().destroy();
      filter_datatable();
    }
  });

  function filter_datatable(brand) {
    $(".data").DataTable({
      serverSide: true,
      searching: true,
      scrollX: true,
      order: [],
      ajax: {
        url: "/asset/brand/data",
        type: "POST",
        data: {
          brand: brand
        }
      },
      columnDefs: [{
        targets: [0],
        className: "text-center",
      }],
      "oLanguage": {
        "sLengthMenu": "แสดง _MENU_ ลำดับ ต่อหน้า",
        "sZeroRecords": "ไม่พบข้อมูลที่ค้นหา",
        "sInfo": "แสดง _START_ ถึง _END_ ของ _TOTAL_ ลำดับ",
        "sInfoEmpty": "แสดง 0 ถึง 0 ของ 0 ลำดับ",
        "sInfoFiltered": "",
        "sSearch": "ค้นหา :",
        "oPaginate": {
          "sFirst": "หน้าแรก",
          "sLast": "หน้าสุดท้าย",
          "sNext": "ถัดไป",
          "sPrevious": "ก่อนหน้า"
        }
      },
    });
  };

  $(document).on("click", ".btn-delete", function(e) {
    let uuid = $(this).prop("id");
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
        axios.post("/asset/brand/delete", {
          uuid: uuid
        }).then((res) => {
          let result = parseInt(res.data);
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

  $(".brand-select").select2({
    placeholder: "-- ยี่ห้อ --",
    allowClear: true,
    width: "100%",
    ajax: {
      url: "/asset/brand/brand-select",
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
</script>