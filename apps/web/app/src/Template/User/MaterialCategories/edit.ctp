
<?php $this->start('beforeHeaderClose'); ?>

<?php $this->end(); ?>

<div class="title_area">
  <h1>素材のカテゴリ</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><a href="<?= $this->Url->build(['action' => 'index', '?' => []]); ?>">素材のカテゴリ</a></li>
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
              <td>上層カテゴリ</td>
              <td>
                <?php if (empty($parent_category)): ?>
                  （なし）
                  <?= $this->Form->input('parent_category_id', ['type' => 'hidden', 'value' => 0]); ?>
                <?php else: ?>
                  <?= $parent_category->name; ?>
                  <?= $this->Form->input('parent_category_id', ['type' => 'hidden', 'value' => $query['parent_id']]); ?>
                <?php endif; ?>
              </td>
            </tr>
            <tr>
              <td>カテゴリ名<span class="attent">※必須</span></td>
              <td>
                <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40,));?>
                <br><span>※40文字以内で入力してください</span>
              </td>
            </tr>
            

            <?php if (false): ?>
            <tr>
              <td>識別子</td>
              <td>
                <?= $this->Form->input('identifier', ['type' => 'text']); ?>
                <br><span>※30文字以内で入力してください</span>
              </td>
            </tr>
          <?php endif; ?>

            <tr>
              <td>有効/無効</td>
              <td>
                  <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
              </td>
            </tr>

        </table>

        <div class="btn_area">
        <?php if (!empty($data['id']) && $data['id'] > 0){ ?>
            <a href="#" class="btn btn-primary w-20 rounded-pill submitButton"><i class="fas fa-check"></i> 変更する</a>
            <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content'))?>')" class="btn btn-danger">
              <i class="far fa-trash-alt"></i> 削除する</a>
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

<script>
var player;

var tag = document.createElement('script');
tag.src = "https://www.youtube.com/iframe_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

$(function(){

    select();

})

function select(){
  var type = $('#typeSelect').val();

  $('.changeArea').hide();

  if(type == '1'){
    $('.imageArea').show();
  }else if(type == '2') {
    $('.movieArea').show();
  }else if(type == '3'){
    $('.urlArea').show();
  }else if(type == '4'){
    $('.contentArea').show();
  }else if (type == '5') {
    $('.mp4Area').show();
  }else if (type == '6') {
    $('.mp4Area').show();
    $('.imageArea').show();
  }else if (type == '7') {
    $('.webmArea').show();
  }
}

function movie_management() {
  var movie = $("#movie-tag").val();

    if (movie != "") {
      

      player = new YT.Player('player', {
        // height: '300px',
        videoId: movie,
        wmode: 'transparent',
        // width: '300px',
        playerVars:{
          'autoplay': 0,
          'controls': 0,
          'loop': 0,
          'playlist': movie,
          'rel': 0,
          'showinfo': 0,
          'color': 'white'
        },
        events: {
          'onReady': function (event) {
            event.target.mute();
            $("#idViewSecond").val(event.target.getDuration());
          },
          'onStateChange': function (event) {

          },
          'onError': function (event) {

          }
        }
      });
    }
}
$(function() {
  

  $("#btnYoutubeInfo").on('click', function() {
    movie_management();
  });

  $("#movie-tag").on('change', function() {
    $("#idViewSecond").val('');
  });
})

</script>
<?= $this->Html->script('/user/common/js/info/edit'); ?>
<?php $this->end();?>
