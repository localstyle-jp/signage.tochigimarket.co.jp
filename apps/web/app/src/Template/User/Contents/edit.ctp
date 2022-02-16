
<?php $this->start('beforeHeaderClose'); ?>

<?php $this->end(); ?>

<div class="title_area">
  <h1>コンテンツ</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><a href="<?= $this->Url->build(['action' => 'index', '?' => []]); ?>">コンテンツ</a></li>
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
<!-- 仮追加 -->
<?= $this->Form->input('site_config_id', array('type' => 'hidden'));?>

          <table class="vertical_table table__meta">

            <tr>
              <td>コンテンツ名<span class="attent">※必須</span></td>
              <td>
                <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40,));?>
                <br><span>※40文字以内で入力してください</span>
              </td>
            </tr>

            <tr>
              <td>シリアルNo</td>
              <td>
                <?= $this->Form->input("serial_no", ['type' => 'text', 'readonly' => true]); ?>
              </td>
            </tr>

          <?php if ($data['id']): ?>
            <tr>
              <td>作成日時</td>
              <td>
                <?= $entity->created->format("Y/m/d H:i"); ?>
              </td>
            </tr>

            <tr>
              <td>更新日時</td>
              <td>
                <?= $entity->modified->format('Y/m/d H:i'); ?>
              </td>
            </tr>
          <?php endif; ?>
          <?php if (false) : ?>
            <tr>
              <td>状態</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
              </td>
            </tr>
            <?php endif; ?>

        </table>

        <table id="blockTable" class="vertical_table block_area table__edit" style="table-layout: fixed;">
          <colgroup>
            <col style="width: 70px;">
            <col style="width: 150px;">
            <col style="width: 100px;">
            <col style="width: 160px;">
            <col>
            <col style="width: 400px;">
            <col style="width: 90px;">
          </colgroup>

          <thead>
            <th></th>
            <th>カテゴリ / 素材名</th>
            <th>種別</th>
            <th>表示秒数<br>(※15秒以上で指定、0秒の場合は永続表示)</th>
            <th>内容</th>
            <th>字幕</th>
            <th></th>
          </thead>

          <tbody id="blockArea" class="list_table">
          <?php if (!empty($data['content_materials'])): ?>
          <?php foreach ($data['content_materials'] as $k => $material): ?>
            <?= $this->element('content_material', ['material' => $material, 'rownum' => $k]); ?>
          <?php endforeach; ?>  
          <?php endif; ?>
          </tbody>
        </table>

        <div>
          <div class="btn_area" style="margin-bottom: 20px;">
            <a href="javascript:void(0);" class="btn btn-primary" id="btnMaterial"><i class="far fa-plus-square"></i>素材を選択</a>
          </div>

          <div class="btn_area">
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
var pop_box = new PopBox();
var material_row = <?= !empty($entity->content_materials)?count($entity->content_materials):0 ?>;

function addMaterial(id) {
  var url = '/user/contents/addMaterial';
  var params = {
    'material_id' : id,
    'rownum' : material_row
  };

  $.post(url, params, function(a) {
    $("#blockArea").append(a);

    material_row++;
  });
  
}

$(function(){
    // 素材
  $("#btnMaterial").on('click', function() {
    pop_box.select = function(material_id) {
      addMaterial(material_id);
      pop_box.close();
    };

    pop_box.open({
          element: "#btnMaterial",
          href: "/user/materials/pop_list",
          open: true,
          onComplete: function(){
          },
          onClosed: function() {
              pop_box.remove();
          },
          opacity: 0.5,
          iframe: true,
          width: '900px',
          height: '750px'
        });

        return false;
  });

  // 行削除
  $('body #blockArea').on('click', '.delete_row', function() {
    var row = $(this).data('row');
    var val = $("#idIsDelete_" + row).val();

    if (val == 0) {
      val = 1;
      $('#block_no_' + row + ' input, #block_no_' + row + ' textarea, #block_no_' + row + ' select').attr('readonly', true);
      $("#block_no_" + row).addClass('bg-secondary');
      $(this).text('元に戻す');
      $(this).removeClass('btn-secondary');
      $(this).addClass('btn-danger');
    } else {
      val = 0;
      $('#block_no_' + row + ' input, #block_no_' + row + ' textarea, #block_no_' + row + ' select').attr('readonly', false);
      $("#block_no_" + row).removeClass('bg-secondary');
      $(this).text('削除');
      $(this).removeClass('btn-danger');
      $(this).addClass('btn-secondary');
    }

    $("#idIsDelete_" + row).val(val);

    return false;

  });

  // 並び替え
  $(".list_table").sortable({
    items:'tr.first-dir',
    placeholder: 'ui-state-highlight',
    opacity: 0.7,
    handle:'td div.sort_handle',
    update: function(e, obj) {

    }
  });

})
</script>
<?php $this->end();?>
