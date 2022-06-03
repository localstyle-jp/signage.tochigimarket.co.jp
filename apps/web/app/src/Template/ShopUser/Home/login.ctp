<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HOMEPAGE MANAGER | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/shop_user/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="/shop_user/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/shop_user/dist/css/adminlte.min.css">
  <link rel="stylesheet" href="/shop_user//dist/css/cms_theme.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>HOMEPAGE MANAGER</b>
    <div>CATERS</div>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg"></p>
      <?= $this->element('error_message'); ?>

<?= $this->Form->create(null, array('id' => 'AdminIndexForm'));?>
        <div class="input-group mb-3">
          <input type="text" name="username" class="form-control" placeholder="ユーザー名">
          <div class="input-group-append">
            <div class="input-group-text">
            <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="パスワード">
          <div class="input-group-append">
            <div class="input-group-text">
            <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

    <?php if (false): ?>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
    <?php endif; ?>
        
        <div class="row">
            <div class="col-12">
            <button type="submit" class="btn btn-dark btn-block">ログイン</button>
            </div>
        </div>

<?= $this->Form->end();?>

    <?php if (false): ?>
      <div class="social-auth-links text-center mb-3">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
        </a>
      </div>
    <?php endif; ?>
      <!-- /.social-auth-links -->

    <?php if (false): ?>
      <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p>
      <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p>
    <?php endif; ?>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<div class="login-box">

</div>

<!-- jQuery -->
<script src="/shop_user/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/shop_user/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/shop_user/dist/js/adminlte.min.js"></script>
</body>
</html>
