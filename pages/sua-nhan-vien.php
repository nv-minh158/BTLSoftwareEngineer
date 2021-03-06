<?php

// create session
session_start();

if (isset($_SESSION['username']) && isset($_SESSION['level'])) {
  // include file
  include('../layouts/header.php');
  include('../layouts/topbar.php');
  include('../layouts/sidebar.php');

  // show data
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $showData = "SELECT nv.id as id, phong_ban_id, chuc_vu_id,  ma_nv, hinh_anh, ten_nv,  gioi_tinh, nv.ngay_tao as ngay_tao, ngay_sinh,  so_cmnd,  tam_tru,   ten_phong_ban, ten_chuc_vu FROM nhanvien nv, phong_ban pb, chuc_vu cv WHERE nv.phong_ban_id = pb.id AND nv.chuc_vu_id = cv.id AND nv.id = $id";
    $result = mysqli_query($conn, $showData);
    $row = mysqli_fetch_array($result);

    // set option active
    $pb_id = $row['phong_ban_id'];
    $ten_pb = $row['ten_phong_ban'];

    $cv_id = $row['chuc_vu_id'];
    $ten_cv = $row['ten_chuc_vu'];



    // set value option another


    $pb = "SELECT id, ten_phong_ban FROM phong_ban WHERE id <> $pb_id";
    $resultPB = mysqli_query($conn, $pb);
    $arrPB = array();
    while ($rowPB = mysqli_fetch_array($resultPB)) {
      $arrPB[] = $rowPB;
    }

    $cv = "SELECT id, ten_chuc_vu FROM chuc_vu WHERE id <> $cv_id";
    $resultCV = mysqli_query($conn, $cv);
    $arrCV = array();
    while ($rowCV = mysqli_fetch_array($resultCV)) {
      $arrCV[] = $rowCV;
    }
  }


  // chuc nang them nhan vien
  if (isset($_POST['save'])) {
    // tao bien bat loi
    $error = array();
    $success = array();
    $showMess = false;

    // lay du lieu ve
    $tenNhanVien = $_POST['tenNhanVien'];
    $CMND = $_POST['CMND'];
    $gioiTinh = $_POST['gioiTinh'];
    $ngaySinh = $_POST['ngaySinh'];
    $tamTru = $_POST['tamTru'];
    $phongBan = $_POST['phongBan'];
    $chucVu = $_POST['chucVu'];
    $id_user = $row_acc['id'];
    $ngaySua = date("Y-m-d H:i:s");

    // cau hinh o chon anh
    $hinhAnh = $_FILES['hinhAnh']['name'];
    $target_dir = "../uploads/staffs/";
    $target_file = $target_dir . basename($hinhAnh);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // validate
    if (empty($tenNhanVien))
      $error['tenNhanVien'] = 'error';
    if (empty($CMND))
      $error['CMND'] = 'error';
    if ($gioiTinh == 'chon')
      $error['gioiTinh'] = 'error';
    if (empty($tamTru))
      $error['tamTru'] = 'error';
    if ($phongBan == 'chon')
      $error['phongBan'] = 'error';
    if ($chucVu == 'chon')
      $error['chucVu'] = 'error';

    // validate file
    if ($hinhAnh) {
      if ($_FILES['hinhAnh']['size'] > 50000000)
        $error['kichThuocAnh'] = 'error';
      if ($imageFileType != 'jpg' && $imageFileType != 'jpeg' && $imageFileType != 'png' && $imageFileType != 'gif')
        $error['kieuAnh'] = 'error';
    }

    if (!$error) {
      if ($hinhAnh) {
        $imageName = time() . "." . $imageFileType;
        $moveFile = $target_dir . $imageName;

        // remove old image
        $oldImage = $row['hinh_anh'];

        // insert data
        $update = " UPDATE nhanvien SET 
                    hinh_anh = '$imageName',
                    ten_nv = '$tenNhanVien',
                    gioi_tinh = '$gioiTinh',
                    ngay_sinh = '$ngaySinh',
                    so_cmnd = '$CMND',
                    tam_tru = '$tamTru',
                    phong_ban_id = '$phongBan',
                    chuc_vu_id = '$chucVu',
                    nguoi_sua_id = '$id_user',
                    ngay_sua = '$ngaySua'
                    WHERE id = $id";
        $result = mysqli_query($conn, $update);
        if ($result) {
          $showMess = true;

          // remove old image
          if ($oldImage != "demo-3x4.jpg") {
            unlink($target_dir . $oldImage);
          }

          // move image
          move_uploaded_file($_FILES["hinhAnh"]["tmp_name"], $moveFile);

          $success['success'] = 'L??u th??ng tin th??nh c??ng';
          echo '<script>setTimeout("window.location=\'sua-nhan-vien.php?p=staff&a=list-staff&id=' . $id . '\'",1000);</script>';
        }
      } else {
        $showMess = true;
        // update data
        $update = " UPDATE nhanvien SET 
                    hinh_anh = '$imageName',
                    ten_nv = '$tenNhanVien',
                    gioi_tinh = '$gioiTinh',
                    ngay_sinh = '$ngaySinh',
                    so_cmnd = '$CMND',
                    tam_tru = '$tamTru',
                    phong_ban_id = '$phongBan',
                    chuc_vu_id = '$chucVu',
                    nguoi_sua_id = '$id_user',
                    ngay_sua = '$ngaySua'
                    WHERE id = $id";
        $result = mysqli_query($conn, $update);
        if ($result) {
          $success['success'] = 'L??u th??ng tin th??nh c??ng';
          echo '<script>setTimeout("window.location=\'sua-nhan-vien.php?p=staff&a=list-staff&id=' . $id . '\'",1000);</script>';
        }
      }
    }
  }

?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Ch???nh s???a nh??n vi??n
      </h1>
      <ol class="breadcrumb">
        <li><a href="index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> T???ng quan</a></li>
        <li><a href="danh-sach-nhan-vien.php?p=staff&a=list-staff">Nh??n vi??n</a></li>
        <li class="active">Ch???nh s???a th??ng tin nh??n vi??n</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Ch???nh s???a th??ng tin nh??n vi??n</h3> &emsp;
              <small>Nh???ng ?? nh???p c?? d???u <span style="color: red;">*</span> l?? b???t bu???c</small>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <?php
              // show error
              if ($row_acc['quyen'] != 1) {
                echo "<div class='alert alert-warning alert-dismissible'>";
                echo "<h4><i class='icon fa fa-ban'></i> Th??ng b??o!</h4>";
                echo "B???n <b> kh??ng c?? quy???n </b> th???c hi???n ch???c n??ng n??y.";
                echo "</div>";
              }
              ?>

              <?php
              // show success
              if (isset($success)) {
                if ($showMess == true) {
                  echo "<div class='alert alert-success alert-dismissible'>";
                  echo "<h4><i class='icon fa fa-check'></i> Th??nh c??ng!</h4>";
                  foreach ($success as $suc) {
                    echo $suc . "<br/>";
                  }
                  echo "</div>";
                }
              }
              ?>
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>M?? nh??n vi??n: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" name="maNhanVien" value="<?php echo $row['ma_nv']; ?>" readonly>
                    </div>
                    <div class="form-group">
                      <label>T??n nh??n vi??n <span style="color: red;">*</span>: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Nh???p t??n nh??n vi??n" name="tenNhanVien" value="<?php echo $row['ten_nv']; ?>">
                      <small style="color: red;"><?php if (isset($error['tenNhanVien'])) {
                                                    echo "T??n nh??n vi??n kh??ng ???????c ????? tr???ng";
                                                  } ?></small>
                    </div>
                    <div class="form-group">
                      <label>S??? CMND <span style="color: red;">*</span>: </label>
                      <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Nh???p s??? CMND" name="CMND" value="<?php echo $row['so_cmnd']; ?>">
                      <small style="color: red;"><?php if (isset($error['CMND'])) {
                                                    echo "Vui l??ng nh???p s??? CMND";
                                                  } ?></small>
                    </div>
                    <!-- /.col -->
                    <div class="col-md-6">
                      <div class="form-group">
                        <label>???nh 3x4 (N???u c??): </label>
                        <input type="file" class="form-control" id="exampleInputEmail1" name="hinhAnh">
                        <small style="color: red;"><?php if (isset($error['kichThuocAnh'])) {
                                                      echo "K??ch th?????c ???nh qu?? l???n";
                                                    } ?></small>
                        <small style="color: red;"><?php if (isset($error['kieuAnh'])) {
                                                      echo "Ch??? nh???n file ???nh d???ng: jpg, jpeg, png, gif";
                                                    } ?></small>
                      </div>
                      <div class="form-group">
                        <label>Gi???i t??nh <span style="color: red;">*</span>: </label>
                        <select class="form-control" name="gioiTinh">
                          <?php
                          if ($row['gioi_tinh'] == 1) {
                            echo "<option value='1' selected>Nam</option>";
                            echo "<option value='0'>N???</option>";
                          } else {
                            echo "<option value='1'>Nam</option>";
                            echo "<option value='0' selected>N???</option>";
                          }
                          ?>
                        </select>
                        <small style="color: red;"><?php if (isset($error['gioiTinh'])) {
                                                      echo "Vui l??ng ch???n gi???i t??nh";
                                                    } ?></small>
                      </div>
                      <div class="form-group">
                        <label>Ng??y sinh: </label>
                        <input type="date" class="form-control" id="exampleInputEmail1" name="ngaySinh" value="<?php echo $row['ngay_sinh']; ?>">
                      </div>
                      <div class="form-group">
                        <label>?????a ch???: </label>
                        <textarea class="form-control" name="tamTru"><?php echo $row['tam_tru']; ?></textarea>
                      </div>
                      <div class="form-group">
                        <label>Ph??ng ban <span style="color: red;">*</span>: </label>
                        <select class="form-control" name="phongBan">
                          <option value="<?php echo $pb_id; ?>"><?php echo $ten_pb; ?></option>
                          <?php
                          foreach ($arrPB as $pb) {
                            echo "<option value='" . $pb['id'] . "'>" . $pb['ten_phong_ban'] . "</option>";
                          }
                          ?>
                        </select>
                        <small style="color: red;"><?php if (isset($error['phongBan'])) {
                                                      echo "Vui l??ng ch???n ph??ng ban";
                                                    } ?></small>
                      </div>
                      <div class="form-group">
                        <label>Ch???c v??? <span style="color: red;">*</span>: </label>
                        <select class="form-control" name="chucVu">
                          <option value="<?php echo $cv_id; ?>"><?php echo $ten_cv; ?></option>
                          <?php
                          foreach ($arrCV as $cv) {
                            echo "<option value='" . $cv['id'] . "'>" . $cv['ten_chuc_vu'] . "</option>";
                          }
                          ?>
                        </select>
                        <small style="color: red;"><?php if (isset($error['chucVu'])) {
                                                      echo "Vui l??ng ch???n ch???c v???";
                                                    } ?></small>
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
                  <!-- /.row -->
                  <?php
                  if ($_SESSION['level'] == 1)
                    echo "<button type='submit' class='btn btn-warning' name='save'><i class='fa fa-save'></i> L??u l???i th??ng tin</button>";
                  ?>
              </form>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>

<?php
  // include
  include('../layouts/footer.php');
} else {
  // go to pages login
  header('Location: dang-nhap.php');
}

?>