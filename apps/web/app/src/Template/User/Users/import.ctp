<div class="title_area">
  <h1>CMS利用者管理</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><a href="/user/users/">ユーザー一覧 </a></li>
      <li><span>CSVファイル一括登録 </span></li>
    </ul>
  </div>
</div>

<?= $this->element('error_message'); ?>

<div class="content_inr">
  <div class="box">
    <h3>インポート</h3>
    <div class="table_area form_area">
      <?= $this->Form->create(false, array('type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false)));?>
      <table class="vertical_table">

        <tr>
          <td>CSVファイル</td>
          <td>
            <?= $this->Form->input('info_file', array('type' => 'file'));?>
          </td>
        </tr>

        <?php $_ = $res['info']; ?>
        <?php if ($gamen_mode == 'import' && $_['valid']): ?>
        <tr>
          <td>インポート結果</td>
          <td>
            <?php if (empty($_['errors'])): ?>
            <div>正常にインポートできました</div>
            <?php else: ?>
            <?php foreach ($_['errors'] as $line => $error): ?>
            <div>
              <?= $line; ?>件目：　<?= $error; ?>
            </div>
            <?php if (isset($_['validate_errors'][$line])): ?>
            <?php foreach ($_['validate_errors'][$line] as $column => $errors): ?>
            <div>　　[列名]<?= $column; ?></div>
            <?php foreach ($errors as $error): ?>
            <div>　　[エラー]<?= $error; ?></div>
            <?php endforeach; ?>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php endforeach; ?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endif; ?>

      </table>

      <div class="btn_area">
        <a href="#" class="btn_confirm submitButton">インポート実行</a>
      </div>

    </div>
    <?= $this->Form->end();?>
  </div>

  <div class="box">
    <h3>CSV仕様</h3>

    <div class="table_area form_area">
      <table calss="vertical_table">

        <tr>
          <td class="head">１行目：ヘッダー</td>
          <td>
            "ID","氏名","所属","権限","状態","ユーザーID","パスワード"
          </td>
        </tr>

        <tr>
          <td class="head">ID</td>
          <td>
            新規は0、特定のデータを上書きする場合はそのデータのIDを入力
          </td>
        </tr>

        <tr>
          <td class="head">氏名</td>
          <td>
            自由入力<br>
            40文字以内
          </td>
        </tr>

        <tr>
          <td class="head">所属<span class="attent">*</span></td>
          <td>
            <a href="/user/companies/" target="_blank">こちらを参考</a>に所属IDを入力してください
          </td>
        </tr>

        <tr>
          <td class="head">権限<span class="attent">*</span></td>
          <td>
            admin=システム管理権限、cms=CM登録権限
          </td>
        </tr>


        <tr>
          <td class="head">状態<span class="attent">*</span></td>
          <td>
            0=停止中、1=利用中
          </td>
        </tr>

        <tr>
          <td class="head">ユーザーID<span class="attent">*</span></td>
          <td>
            半角英数3文字以上、30文字以内
          </td>
        </tr>

        <tr>
          <td class="head">パスワード<span class="attent">*</span></td>
          <td>
            半角英字と数字をそれぞれ１文字以上入れてください。記号は-_#.が使えます。
          </td>
        </tr>

      </table>
    </div>
  </div>

</div>


<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/admin/common/css/cms.css">
<script src="/admin/common/js/cms.js"></script>

<?php $this->end();?>