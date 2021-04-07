
<?php $this->start('beforeHeaderClose'); ?>

<?php $this->end(); ?>

<div class="title_area">
  <h1>素材</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><a href="<?= $this->Url->build(['action' => 'index', '?' => []]); ?>">素材</a></li>
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
              <td>素材名<span class="attent">※必須</span></td>
              <td>
                <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40,));?>
                <br><span>※40文字以内で入力してください</span>
              </td>
            </tr>
            

            <tr>
              <td>タイプ<span class="attent">※必須</span></td>
              <td>
                <?= $this->Form->select('type', $type_list,['id' => 'typeSelect','onChange' => 'select()']);?>
                <br><span>※素材タイプを選択してください</span>
              </td>
            </tr>

            <tr class="imageArea">
              <td>画像<span class="attent">※必須</span></td>
              <?php if (!empty($entity['attaches']['image']['0'])) :?>
              <td>
                <img src="<?= $this->Url->build($entity['attaches']['image']['0'])?>" style="width: 300px;">
                <?= $this->Form->input("attaches.image.0", ['type' => 'hidden']); ?>
              </td>
              <?php else:?>

              <td>
              <?= $this->Form->input('image', array('type' => 'file','accept' => 'image/jpeg,image/png,image/gif', 'id' => 'idMainImage', 'class' => 'attaches'));?>
                <br><span>※jpeg , jpg , gif , png ファイルのみ</span>
              </td>
              <?php endif;?>
            </tr>



            


            <tr class="movieArea">
              <td>動画<span class="attent">※必須</span></td>
              <td>
              <?= $this->Form->input('movie_tag', array('type' => 'text', 'maxlength' => 100));?>
                <br><span>※埋め込みタグ(src部分のみ)を入力してください</span>
              </td>
            </tr>
            

            <tr class="urlArea">
              <td>URL<span class="attent">※必須</span></td>
              <td>
                <?= $this->Form->input('url', array('type' => 'text', 'maxlength' => 255,));?>
                <br><span>※URLのみを入力してください</span>
              </td>
            </tr>

            <tr class="contentArea">
              <td>コンテンツ<span class="attent">※必須</span></td>
              <td id="blockTable">
                <div>
                <?= $this->Form->input('content', ['type' => 'textarea',
                                                    'class' => 'editor'
                                                  ]); ?>
                </div>
                <div>※全角文字はイタリック体が効きません。</div>
              </td>

            </tr>


            <tr>
              <td>状態</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
              </td>
            </tr>

        </table>

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
<link rel="stylesheet" href="/user/common/css/redactor/redactor.min.css">
<script src="/user/common/js/cms.js"></script>
<script src="/user/common/js/redactor/redactor-custom-min.js"></script>
<script src="/user/common/js/redactor/ja.js"></script>
<script src="/user/common/js/redactor/alignment.js"></script>
<script src="/user/common/js/redactor/counter.js"></script>
<script src="/user/common/js/redactor/fontcolor.js"></script>
<script src="/user/common/js/redactor/fontsize.js"></script>
<?= $this->Html->script('/user/common/js/system/pop_box'); ?>
<script>
var pop_box = new PopBox();
$(function(){
  $('.imageArea').show();
  $('.contentArea').hide();
  $('.movieArea').hide();
  $('.urlArea').hide();
  if($('#typeSelect').val() != 1){
    select();
  }
})

function select(){
  var type = $('#typeSelect').val();
  if(type == '1'){
    $('.imageArea').show();
    $('.contentArea').hide();
    $('.movieArea').hide();
    $('.urlArea').hide();
  }else if(type == '2') {
    $('.imageArea').hide();
    $('.contentArea').hide();
    $('.movieArea').show();
    $('.urlArea').hide();
  }else if(type == '3'){
    $('.imageArea').hide();
    $('.contentArea').hide();
    $('.movieArea').hide();
    $('.urlArea').show();
  }else if(type == '4'){
    $('.imageArea').show();
    $('.contentArea').show();
    $('.movieArea').hide();
    $('.urlArea').hide();
  }
}


</script>

<?= $this->Html->script('/user/common/js/info/edit'); ?>
<?php $this->end();?>
