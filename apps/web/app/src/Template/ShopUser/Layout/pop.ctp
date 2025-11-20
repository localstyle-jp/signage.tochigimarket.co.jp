<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SIGNAGE MANAGER V5</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="/shop_user/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/shop_user//dist/css/adminlte.min.css">
  <link rel="stylesheet" href="/shop_user//dist/css/cms_theme.css">
  <link rel="stylesheet" href="/user/common/css/jquery-ui-1.12.1/smoothness/jquery-ui.min.css">
  <link rel="stylesheet" href="/user/common/css/colorbox.css">
  <link rel="stylesheet" href="/shop_user/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
</head>

<body class="hold-transition sidebar-mini">
  <div class="wrapper">


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <?= $this->fetch('content'); ?>
    </div>
    <!-- /.content-wrapper -->


    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- To the right -->
      <div class="float-right d-none d-sm-inline">

      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; LOCALSTYLE Inc. all rights reserved.
    </footer>
  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED SCRIPTS -->

  <!-- jQuery -->
  <script src="/shop_user/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="/shop_user/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="/shop_user/dist/js/adminlte.min.js"></script>

  <script src="/user/common/js/jquery-ui-1.12.1.min.js"></script>
  <script type="text/javascript">
    $.widget.bridge('uibutton', $.ui.button);
    $.widget.bridge('uitooltip', $.ui.tooltip);
  </script>
  <script src="/user/common/js/jquery.colorbox-min.js"></script>
  <script src="/user/common/js/colorbox.js"></script>
  <script src="/user/common/js/cms.js"></script>

  <?php echo $this->fetch('beforeBodyClose');?>

</body>

</html>