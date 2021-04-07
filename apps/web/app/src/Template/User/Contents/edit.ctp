
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
              <td>状態</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
              </td>
            </tr>

        </table>

        <table id="blockTable" class="vertical_table block_area table__edit" style="table-layout: fixed;">
          <colgroup>
            <col style="width: 70px;">
            <col style="width: 150px;">
            <col>
            <col style="width: 90px;">
          </colgroup>
          <tbody id="blockArea" class="list_table">
          <?php if (!empty($entity->content_materials)): ?>
          <?php foreach ($entity->content_materials as $k => $material): ?>
            <?= $this->element('content_material', ['material' => $material, 'rownum' => $k, 'entity' => $entity]); ?>
          <?php endforeach; ?>  
          <?php endif; ?>
          </tbody>
        </table>

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
var material_row = <?= !empty($entity->content_materials)?count($entity->content_materials)+1:0 ?>;

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
})
</script>
<?php $this->end();?>
