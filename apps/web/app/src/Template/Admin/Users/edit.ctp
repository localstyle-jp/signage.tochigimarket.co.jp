<div class="title_area">
      <h1>ユーザー管理</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><a href="<?= $this->Url->build(array('action' => 'index')); ?>">ユーザー一覧</a></li>
          <li><span><?= ($data['id'] > 0)? '編集': '新規登録'; ?></span></li>
        </ul>
      </div>
    </div>

    <?= $this->element('error_message'); ?>
    <div class="content_inr">
      <div class="box">
        <h3><?= ($data["id"] > 0)? '編集': '新規登録'; ?></h3>
        <div class="table_area form_area">
<?= $this->Form->create($entity, array('type' => 'file', 'context' => ['validator' => 'default']));?>
<?php if ($data['id'] > 0): ?>
<?= $this->Form->input('id', array('type' => 'hidden'));?>
<?php endif; ?>
          <table class="vertical_table">
            <colgroup>
              <col style="width: 120px;">
              <col>
            </colgroup>

            <tr>
              <td>No</td>
              <td><?= ($data["id"])? sprintf('No. %04d', $data["id"]) : "新規" ?></td>
            </tr>

            <tr>
              <td>名前<span class="attent">*</span></td>
              <td>
                <?= $this->Form->input('name', ['type' => 'text']); ?>
              </td>
            </tr>

            <tr>
              <td>ユーザーID<span class="attent">*</span></td>
              <td>
                <?= $this->Form->input('username', ['type' => 'text']); ?>
              </td>
            </tr>

        <?php if (!$data['id']): ?>
            <tr>
              <td>パスワード<span class="attent">*</span></td>
              <td>
                <?= $this->Form->input('_password', ['type' => 'text']); ?>
                <div>※半角英字と数字をそれぞれ１文字以上入れてください。記号は「-_#.」が使えます。</div>
              </td>
            </tr>

        <?php else: ?>
            <tr>
              <td>パスワード変更時のみ</td>
              <td>
                <?= $this->Form->input('_password', ['type' => 'password']); ?>
                <?= $this->Form->error('password'); ?>
                <div>※半角英字と数字をそれぞれ１文字以上入れてください。記号は「-_#.」が使えます。</div>
              </td>
            </tr>
            <tr>
              <td>パスワード確認</td>
              <td>
                <?= $this->Form->input('password_confirm', ['type' => 'password']); ?>
              </td>
            </tr>
        <?php endif; ?>

            <tr>
              <td>権限</td>
              <td>
                <?= $this->Form->input("role", ['type' => 'select', 'options' => $role_list]); ?>
              </td>
            </tr>

            <tr>
              <td>アカウントの有効性</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
              </td>
            </tr>

        </table>

        <div class="btn_area">
        <?php if (!empty($data['id']) && $data['id'] > 0){ ?>
            <a href="#" class="btn_confirm submitButton">変更する</a>
            <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content'))?>')" class="btn_delete">削除する</a>
        <?php }else{ ?>
            <a href="#" class="btn_confirm submitButton">登録する</a>
        <?php } ?>
        </div>

        </div>
        <?= $this->Form->end();?>
      </div>
    </div>


<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/admin/common/css/cms.css">
<link rel="stylesheet" href="/admin/common/css/jquery-ui-1.9.2.custom/css/smoothness/jquery-ui-1.9.2.custom.min.css">
<script src="/admin/common/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="/admin/common/js/jquery.ui.datepicker-ja.js"></script>
<script src="/admin/common/js/cms.js"></script>


<script>
$(function () {
    // 本文


});
</script>
<?php $this->end();?>
