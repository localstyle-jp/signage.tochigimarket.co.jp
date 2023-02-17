<?php

use App\Model\Entity\Material;

?>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">表示端末</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a
              href="<?= $this->Url->build(['_name' => 'shopAdmin']); ?>">Home</a>
          </li>
          <li class="breadcrumb-item"><span>表示端末</span></li>
          <li class="breadcrumb-item active">
            <span><?= ($data['id'] > 0) ? '編集' : '新規登録'; ?></span>
          </li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">

  <?= $this->element('error_message'); ?>

  <div class="container-fluid">

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-gray-dark">
            <h2 class="card-title">
              <?= ($data['id'] > 0) ? '編集' : '新規登録'; ?>
            </h2>
          </div>

          <div class="card-body">
            <?= $this->Form->create($entity, array('type' => 'file', 'context' => ['validator' => 'default'], 'templates' => $form_templates)); ?>
            <?= $this->Form->input('id', array('type' => 'hidden', 'value' => $entity->id, 'id' => 'idId')); ?>
            <?= $this->Form->input('position', array('type' => 'hidden')); ?>

            <div class="table_edit_area">
              <!-- 名前 -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">名前</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40, 'class' => 'form-control', 'readonly' => true)); ?>
                </div>
              </div>

              <?php if (false): ?>
              <!-- URL -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">URL<span
                    class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?= '/view/' . $site_config->slug . '/'; ?><?= $this->Form->input('url', array('type' => 'text', 'maxlength' => 100, 'class' => 'form-control')); ?>
                  <span>※URLのみを入力してください</span>
                </div>
              </div>
              <?php else: ?>
              <?= $this->Form->input('url', array('type' => 'hidden')); ?>
              <?php endif; ?>

              <!-- 表示コンテンツ -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">表示コンテンツ</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('content_id', ['type' => 'select', 'options' => $content_list, 'empty' => ['0' => '選択してください'], 'class' => 'form-control']); ?>
                </div>
              </div>

              <!-- 解像度 -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">解像度</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('resolution', ['type' => 'select', 'options' => $resolution_list, 'onchange' => 'changeResolution();', 'class' => 'form-control']); ?>
                  <span id="resolution_wh" class="">
                    <?= $this->Form->input('width', ['type' => 'text', 'style' => 'width:100px; display:inline-block', 'class' => 'form-control']); ?>
                    <span> x </span>
                    <?= $this->Form->input('height', ['type' => 'text', 'style' => 'width:100px; display:inline-block', 'class' => 'form-control']); ?>
                  </span>
                  <span>※モニター横位置での解像度を設定してください</span>
                </div>
              </div>

              <!-- モニター位置 -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">モニター位置</label>
                <div class="col-12 col-md-9 control_value">
                  <?php if (false): ?>
                  <?= $this->Form->hidden('is_vertical', ['value' => '0']); ?>
                  <div class="icheck-primary d-inline ml-2">
                    <?= $this->Form->input('is_vertical', ['type' => 'checkbox', 'hiddenField' => false, 'value' => 1, 'label' => ['class' => 'form-check-label', 'text' => '縦表示'], 'class' => '']); ?>
                  </div>
                  <?php else: ?>
                  <?= $this->Form->hidden('is_vertical'); ?>
                  <?php if ($entity->is_vertical == 1): ?>
                  縦表示
                  <?php else: ?>
                  横表示
                  <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>

              <!-- メモ -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">メモ</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('memo', ['type' => 'textarea', 'style' => 'height:80px;', 'class' => 'form-control']); ?>
                </div>
              </div>

              <!-- 表示する字幕の設定 -->
              <div class="form-group row" id="caption_flg_wrapper">
                <label for="" class="col-12 col-md-3 col-form-label control_title">表示する字幕の設定</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('caption_flg', array('type' => 'radio', 'hiddenField' => false, 'options' => array('machine' => '共通の字幕', 'content' => '素材ごとに異なる字幕')));?>
                </div>
              </div>

              <!-- 字幕 -->
              <div class="form-group row" id="rolling_caption_wrapper">
                <label for="" class="col-12 col-md-3 col-form-label control_title">字幕</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('rolling_caption', ['type' => 'textarea', 'style' => 'height:80px;', 'maxlength' => 1000, 'class' => 'form-control']); ?>
                </div>
              </div>

              <?php if (false): ?>
              <div class="form-group row">
                <?php $_column = 'file'; ?>
                <label for="" class="col-12 col-md-3 col-form-label control_title">状態</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効'))); ?>
                </div>
              </div>
              <?php endif; ?>

            </div>

            <div class="btn_area center">
              <?php if (!empty($data['id']) && $data['id'] > 0) { ?>
              <a href="#" class="btn btn-danger btn_post submitButton"><i class="fas fa-check"></i> 変更する</a>
              <?php if (empty($is_import_data)) : ?>
              <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content')) ?>')"
                class="btn btn_post btn_delete">
                <i class="far fa-trash-alt"></i> 削除する</a>
              <?php else : ?>
              <a href="#" class="btn btn_post btn_delete disabled" role="button" aria-disabled="true">
                <i class="far fa-trash-alt"></i> 削除する</a><span class="attent">紐づいてるデータがあるので削除出来ません。</span>
              <?php endif; ?>
              <?php } else { ?>
              <a href="#" class="btn btn-danger btn_post submitButton"><i class="fas fa-check"></i> 登録する</a>
              <?php } ?>
            </div>
            <?= $this->Form->end(); ?>
          </div>

        </div>
      </div>
      <!-- /.col-md-6 -->
    </div>
    <!-- /.row -->



  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php $this->start('beforeBodyClose'); ?>
<link rel="stylesheet" href="/user/common/css/cms.css">

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

<?php $this->end(); ?>