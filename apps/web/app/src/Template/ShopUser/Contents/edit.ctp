<?php
use App\Model\Entity\Material;

?>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">コンテンツ</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a
              href="<?= $this->Url->build(['_name' => 'shopAdmin']); ?>">Home</a>
          </li>
          <li class="breadcrumb-item"><a
              href="<?= $this->Url->build(['action' => 'index']); ?>">コンテンツ</a>
          </li>
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
    <?= $this->Form->create($entity, array('type' => 'file', 'context' => ['validator' => 'default']));?>
    <?= $this->Form->input('id', array('type' => 'hidden', 'value' => $entity->id, 'id' => 'idId'));?>
    <?= $this->Form->input('position', array('type' => 'hidden'));?>
    <!-- 仮追加 -->
    <?= $this->Form->input('site_config_id', array('type' => 'hidden'));?>
    <?php
$locked = true;
if (empty($data['id']) || ($data['id'] && $entity->user_id == $this->Session->read('userid'))) {
    $locked = false;
}
?>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-gray-dark">
            <h2 class="card-title">
              <?= ($data['id'] > 0) ? '編集' : '新規登録'; ?>
            </h2>
          </div>

          <div class="card-body">

            <div class="table_edit_area">
              <!-- コンテンツ名 -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">コンテンツ名<span
                    class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40, 'class' => 'form-control'));?>
                  <span>※40文字以内で入力してください</span>
                </div>
              </div>

              <!-- シリアルNo -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">シリアルNo</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('serial_no', ['type' => 'text', 'readonly' => true, 'class' => 'form-control']); ?>
                </div>
              </div>

              <?php if ($data['id']): ?>
              <!-- 作成日時 -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">作成日時</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $entity->created->format('Y/m/d H:i'); ?>
                </div>
              </div>
              <!-- 更新日時 -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">作成日時</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $entity->modified->format('Y/m/d H:i'); ?>
                </div>
              </div>

              <?php endif; ?>

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

          </div>

        </div>
      </div>
      <!-- /.col-md-6 -->
    </div>
    <!-- /.row -->

    <!-- 使用素材一覧 -->
    <div class="row">
      <div class="col-12">
        <div class="card">

          <div class="card-header bg-gray-dark">
            <h2 class="card-title">コンテンツリスト</h2>
          </div>

          <div class=" card-body">
            <div class="table_list_area">
              <table class="table table-sm table-hover table-bordered dataTable dtr-inline"
                style="table-layout: fixed;">
                <colgroup class="show_pc">
                  <col style="width: 70px;">
                  <col style="width: 150px;">
                  <col style="width: 100px;">
                  <col style="width: 160px;">
                  <col style="min-width: 330px;">
                  <col style="width: 400px;">
                  <col style="width: 90px;">
                </colgroup>

                <thead class="bg-gray">
                  <th class="show_pc"></th>
                  <th>カテゴリ / 素材名</th>
                  <th>種別</th>
                  <th>表示秒数<br>(※15秒以上で指定、0秒の場合は永続表示)</th>
                  <th>内容</th>
                  <th>字幕/BGM（画像のみ）</th>
                  <th></th>
                </thead>

                <tbody id="blockArea" class="list_table">
                  <?php if (!empty($data['content_materials'])): ?>
                  <?php foreach ($data['content_materials'] as $k => $material): ?>
                  <?= $this->element('content_material', ['material' => $material, 'rownum' => $k, 'locked' => $locked]); ?>
                  <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>

          </div>

          <?php if (!$locked): ?>
          <div class="card-footer">
            <div class="btn_area center">
              <a href="javascript:void(0);" class="btn btn-primary" id="btnMaterial"><i
                  class="far fa-plus-square"></i>素材を選択</a>
            </div>
          </div>
          <?php endif; ?>
        </div>

        <?php if (!$locked): ?>
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
        <?php endif; ?>
      </div>
      <!-- /.col-md-6 -->
    </div>
    <!-- /.row -->
    <?= $this->Form->end(); ?>
  </div><!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php $this->start('beforeBodyClose'); ?>
<link rel="stylesheet" href="/user/common/css/cms.css">
<?= $this->Html->script('/user/common/js/system/pop_box'); ?>
<?= $this->Html->script('/user/common/js/info/edit'); ?>
<script>
  var pop_box = new PopBox();
  var
    material_row = <?= !empty($entity->content_materials) ? count($entity->content_materials) : 0 ?> ;

  function addMaterial(id) {
    var url = '/shop_user/contents/addMaterial';
    var params = {
      'material_id': id,
      'rownum': material_row
    };

    $.post(url, params, function(a) {
      $("#blockArea").append(a);

      material_row++;
    });

  }
  $(function() {
    // 素材
    $("#btnMaterial").on('click', function() {
      pop_box.select = function(material_id) {
        addMaterial(material_id);
        pop_box.close();
      };

      pop_box.open({
        element: "#btnMaterial",
        href: "/shop_user/materials/pop_list",
        open: true,
        onComplete: function() {},
        onClosed: function() {
          pop_box.remove();
        },
        opacity: 0.5,
        iframe: true,
        width: '900px',
        height: '750px',
        maxWidth: "100%",
        maxHeight: "90%"
      });

      return false;
    });

    // 行削除
    $('body #blockArea').on('click', '.delete_row', function() {
      var row = $(this).data('row');
      var val = $("#idIsDelete_" + row).val();

      if (val == 0) {
        val = 1;
        $('#block_no_' + row + ' input, #block_no_' + row + ' textarea, #block_no_' + row + ' select').attr(
          'readonly', true);
        $("#block_no_" + row).addClass('bg-secondary');
        $(this).text('元に戻す');
        $(this).removeClass('btn-secondary');
        $(this).addClass('btn-danger');
      } else {
        val = 0;
        $('#block_no_' + row + ' input, #block_no_' + row + ' textarea, #block_no_' + row + ' select').attr(
          'readonly', false);
        $("#block_no_" + row).removeClass('bg-secondary');
        $(this).text('削除');
        $(this).removeClass('btn-danger');
        $(this).addClass('btn-secondary');
      }

      $("#idIsDelete_" + row).val(val);

      return false;

    });

  })
</script>

<?php $this->end(); ?>