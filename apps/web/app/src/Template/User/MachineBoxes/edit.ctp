
<?php $this->start('beforeHeaderClose'); ?>

<?php $this->end(); ?>

<div class="title_area">
  <h1>表示端末</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><a href="<?= $this->Url->build(['action' => 'index', '?' => []]); ?>">表示端末</a></li>
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
<?= $this->Form->input('id', array('type' => 'hidden', 'value' => $entity->id, 'id' => 'idId'));?>
<?= $this->Form->input('position', array('type' => 'hidden'));?>
          <table class="vertical_table table__meta">

            <tr>
              <td>名前<span class="attent">※必須</span></td>
              <td>
                <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40,));?>
                <br><span>※40文字以内で入力してください</span>
              </td>
            </tr>

            <tr>
              <td>URL<span class="attent">※必須</span></td>
              <td>
                <?= '/view/' . $site_config->slug . '/'; ?><?= $this->Form->input('url', array('type' => 'text', 'maxlength' => 100, 'style' => 'width:200px;'));?>
                <br><span>※URLのみを入力してください</span>
              </td>
            </tr>

            <tr>
              <td>表示コンテンツ</td>
              <td>
                <?= $this->Form->input('content_id', ['type' => 'select', 'options' => $content_list, 'empty' => ['0' => '選択してください']]); ?>

              </td>
            </tr>

            <tr>
              <td>解像度</td>
              <td>
                <?= $this->Form->input('resolution', ['type' => 'select', 'options' => $resolution_list, 'onchange' => 'changeResolution();']); ?>
                <span id="resolution_wh">
                  <?= $this->Form->input('width', ['type' => 'text', 'style' => 'width: 100px;']); ?> x 
                  <?= $this->Form->input('height', ['type' => 'text', 'style' => 'width: 100px;']); ?>
                </span>
                <div>
                  ※モニター横位置での解像度を設定してください
                </div>
              </td>
            </tr>

            <tr>
              <td>モニター位置</td>
              <td>
                <?= $this->Form->input('is_vertical', ['type' => 'checkbox', 'hiddenField' => true, 'value' => 1, 'label' => '縦表示']); ?>
              </td>
            </tr>

            <tr>
              <td>メモ</td>
              <td>
                <?= $this->Form->input('memo', ['type' => 'textarea', 'style' => 'height:80px;']); ?>
              </td>
            </tr>
<?php if(false) : ?>
            <tr>
              <td>状態</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
              </td>
            </tr>
            <?php endif; ?>

            <tr id="caption_flg_wrapper">
              <td>表示する字幕の設定</td>
              <td>
              <?= $this->Form->input('caption_flg', array('type' => 'radio', 'options' => array('machine' => '共通の字幕', 'content' => '素材ごとに異なる字幕')));?>
              </td>
            </tr>

            <tr id="rolling_caption_wrapper">
              <td>字幕</td>
              <td>
                <?= $this->Form->input('rolling_caption', ['type' => 'textarea', 'style' => 'height:80px;', 'maxlength' => 1000]); ?>
              </td>
            </tr>

        </table>

      <?php if ($data['id']): ?>
        <table class="vertical_table table__meta">
          <tr>
            <td>プレビューURL</td>
            <td>
              <?php $url = $this->Url->build('/', true) . 'view/' . $site_config->slug . '/' . trim($entity->url, '/') . '/'; ?>
              <?= $this->Form->input('_full_path', ['type' => 'text', 'class' => 'w-100', 'readonly' => true, 'value' => $url]); ?>
            </td>
          </tr>

          <!-- <tr>
            <td>ブラウザの再読み込み</td>
            <td>
              <-?= $this->Form->input('reload_flag', ['type' => 'checkbox', 'value' => '1', 'hiddenField' => true, 'label' => '表示端末側のブラウザを再読み込みさせます（10秒以内に実行されます）']); ?>
            </td>
          </tr> -->
        </table>

        <table class="vertical_table table__meta">
          <tr>
            <td colspan='2'>ビューアアプリ設定用情報</td>
          </tr>
          <tr>
            <td>URL</td>
            <td>
              <?= $this->Url->build('/', true); ?>
            </td>
          </tr>

          <tr>
            <td>ID</td>
            <td>
              <?= $entity->id; ?>
            </td>
          </tr>

          <!-- <tr>
            <td>ブラウザの再読み込み</td>
            <td>
              <-?= $this->Form->input('reload_flag_device', ['type' => 'checkbox', 'value' => '1', 'hiddenField' => true, 'label' => '表示端末側のブラウザを再読み込みさせます（10秒以内に実行されます）']); ?>
            </td>
          </tr> -->

        </table>
      <?php endif; ?>

        <div class="btn_area">
        <?php if ($data['id']): ?>
          <div class="text-danger"><strong>※「変更する」ボタンを押すと最新版のコンテンツに更新されます。</strong></div>
        <?php endif; ?>
        <?php if (!empty($data['id']) && $data['id'] > 0){ ?>
            <a href="#" class="btn btn-primary w-20 rounded-pill submitButton"><i class="fas fa-check"></i> 変更する</a>
          <?php if (empty($is_import_data)): ?>
            <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content'))?>')" class="btn btn-danger">
              <i class="far fa-trash-alt"></i> 削除する</a>
          <?php else: ?>
            <a href="#" class="btn btn-danger disabled" role="button" aria-disabled="true">
              <i class="far fa-trash-alt"></i> 削除する</a><span class="attent">紐づいてるデータがあるので削除出来ません。</span>
          <?php endif; ?>
        <?php }else{ ?>
            <a href="#" class="btn btn-danger w-20 rounded-pill submitButton"><i class="fas fa-check"></i> 登録する</a>
        <?php } ?>
        </div>

        <div id="deleteArea" style="display: hide;"></div>

        <?= $this->Form->end();?>

        </div> 
      </div>
    </div>


<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/user/common/css/cms.css">
<script src="/user/common/js/cms.js"></script>

<?= $this->Html->script('/user/common/js/system/pop_box'); ?>
<script>

function changeResolution() {
  var type = $("#resolution").val();

  if (type > 0) {
    $("#resolution_wh").hide();
  } else {
    $("#resolution_wh").show();
  }
}

$(function() {
  changeResolution();
})

function changeTargetType() {
  var type = $('#caption_flg_wrapper [type="radio"]:checked').val();
  if (type === 'content') {
    $("#rolling_caption_wrapper").hide();
  } else if (type === 'machine') {
    $("#rolling_caption_wrapper").show();
  }
}
$(function() {
  changeTargetType();
  $('#caption_flg_wrapper [type="radio"]').on('change', function() {
    changeTargetType();
  });
});

</script>

<?= $this->Html->script('/user/common/js/info/edit'); ?>
<?php $this->end();?>
