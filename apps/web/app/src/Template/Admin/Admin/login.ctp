<!DOCTYPE HTML>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <meta name="viewport"
    content="<?php include WWW_ROOT . 'admin/common/include/viewport.inc' ?>">
  <link rel="stylesheet" href="./common/css/normalize.css">
  <link rel="stylesheet" href="./common/css/font.css">
  <link rel="stylesheet" href="./common/css/common.css">
  <!--[if lt IE 9]>
<script src='./common/js/html5shiv.js'></script>
<![endif]-->
</head>

<body>

  <div id="container">
    <header>
      <div class="status">
      </div>
    </header>

    <div id="content">
      <div class="title_area">
        <h1>HOMEPAGE MANAGER</h1>
      </div>
      <!-- div class="error">error messeages error messeages error messeages error messeages error messeages</div -->
      <?= $this->element('error_message'); ?>
      <div class="content_inr">
        <div class="box" style="max-width:600px;margin-left:auto;margin-right:auto;">
          <h3>管理者ログイン</h3>
          <?= $this->Form->create('Admin', array('id' => 'AdminIndexForm'));?>
          <div class="table_area form_area">
            <table class="vertical_table">
              <tr>
                <td>ユーザーID</td>
                <td><input name="username" type="text" id="id" /></td>
              </tr>
              <tr>
                <td>パスワード</td>
                <td><input name="password" type="password" id="pw" /></td>
              </tr>
            </table>
            <div class="btn_area">
              <a href="javascript:void(0);" class="btn_confirm">ログイン</a>
              <a href="javascript:void(0);" class="btn_back">リセット</a>
            </div>
          </div>
          <?= $this->Form->end();?>
        </div>
      </div>
      <?php include WWW_ROOT . 'admin/common/include/footer.inc' ?>
    </div>
  </div>

  <script src="./common/js/jquery.js"></script>
  <script src="./common/js/base.js"></script>
  <script>
    $(function() {
      $('a.btn_confirm').on('click', function() {
        $('#AdminIndexForm').submit();
      });
      $('a.btn_back').on('click', function() {
        document.getElementById("AdminIndexForm").reset();
        //$('#AdminIndexForm').reset();
      });
    })
  </script>

</body>

</html>




</table>

<?php $this->start('afterContent');?>

<?php $this->end();?>