<div class="title_area">
      <h1>サイト管理</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><a href="<?= $this->Url->build(array('action' => 'index')); ?>">サイト一覧</a></li>
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

            <tr>
              <td>No</td>
              <td><?= ($data["id"])? sprintf('No. %04d', $data["id"]) : "新規" ?></td>
            </tr>

            <tr>
              <td>名前</td>
              <td>
                <?= $this->Form->input('site_name', ['type' => 'text', 'maxlength' => '40']); ?>
              </td>
            </tr>

            <tr>
              <td>ディレクトリ名</td>
              <td>
                <?= $this->Form->input('is_root', ['type' => 'checkbox', 'value' => 1, 'label' => 'ルート']); ?>
                <?= $this->Form->input('slug', array('type' => 'text', 'maxlength' => '40'));?>    
              </td>
            </tr>

            <tr>
              <td>公開/非公開</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '非公開', 'publish' => '公開')));?>
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
