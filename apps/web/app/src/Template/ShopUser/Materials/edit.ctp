<?php use App\Model\Entity\Material; ?>

<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0">素材</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= $this->Url->build(['_name' => 'shopAdmin']); ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?= $this->Url->build(['action' => 'index']); ?>">素材</a></li>
          <li class="breadcrumb-item active"><span><?= ($data['id'] > 0)? '編集': '新規登録'; ?></span></li>
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
            <h2 class="card-title"><?= ($data["id"] > 0)? '編集': '新規登録'; ?></h2>
          </div>

          <div class="card-body">
<?= $this->Form->create($entity, array('type' => 'file', 'context' => ['validator' => 'default'], 'class' => 'form-horizontal'));?>
<?= $this->Form->input('id', array('type' => 'hidden', 'value' => $entity->id, 'id' => 'idId'));?>
<?= $this->Form->input('position', array('type' => 'hidden'));?>
            
            <div class="table_edit_area">
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">素材名<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40, 'class' => 'form-control'));?>
                  <span>※40文字以内で入力してください</span>
                </div>
              </div>

              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">タイプ<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?php if (!empty($entity['attaches']['image']['0']) || !empty($entity['url'])) : ?>
                    <?= $this->Form->input('type', array('type' => 'hidden', 'id' => 'typeSelect', 'class' => 'form-control'));?>
                    <div><?= $type_list[$entity->type] ?></div>
                  <?php else : ?>
                    <?= $this->Form->select('type', $type_list,['id' => 'typeSelect','onChange' => 'select()', 'class' => 'form-control']);?>
                    <span>※素材タイプを選択してください</span>
                  <?php endif; ?>
                </div>
              </div>
              
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">素材カテゴリ<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?php if (empty($category_list)) : ?>
                    <?= $this->Form->input('category_id', ['type' => 'hidden', 'value' => '', 'class' => 'form-control']);?>
                    <?= $this->Form->error('category_id');?>
                    <span>※素材カテゴリを設定してください</span>
                  <?php else : ?>
                    <?= $this->Form->select('category_id', $category_list, ['class' => 'form-control']);?>
                  <?php endif; ?>
                </div>
              </div>

              <div class="form-group row changeArea imageArea contentArea">
                <label for="" class="col-12 col-md-3 col-form-label control_title">画像<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?php if (!empty($entity['attaches']['image']['0'])) :?>
                  <div>
                      <img src="<?= $this->Url->build($entity['attaches']['image']['0'])?>" style="width: 300px;">
                      <?= $this->Form->input("attaches.image.0", ['type' => 'hidden']); ?>
                      <?= $this->Form->input("_old_image", ['type' => 'hidden', 'value' => $entity->image]); ?>
                  </div>
                  <?php else:?>
                  <div>
                  <?= $this->Form->input('image', array('type' => 'file','accept' => 'image/jpeg,image/png,image/gif', 'id' => 'idMainImage', 'class' => 'attaches form-control'));?>
                      <span>※jpeg , jpg , gif , png ファイルのみ</span>
                      <br><span class="changeArea imageArea">推奨サイズ 1920 x 1080</span><span class="changeArea contentArea">推奨サイズ 960 x 540</span>
                  </div>
                  <?php endif;?>
                </div>
              </div>

              <div class="form-group row changeArea soundArea">
                <?php $_column = 'file'; ?>
                <label for="" class="col-12 col-md-3 col-form-label control_title">音楽<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <ul>
                    <?php if (!empty($entity['attaches'][$_column]['0'])) :?>
                    <li>
                    <?= $entity['file_name']; ?>.<?= $entity['file_extension']; ?>
                    </li>

                    <li class="<?= h($entity['attaches'][$_column]['extention']); ?>">
                    <?= $this->Form->input("file_name", ['type' => 'hidden', 'maxlength' => '50', 'style' => 'width:300px;', 'placeholder' => '添付ファイル']); ?>
                    <?= $this->Form->input("file_size", ['type' => 'hidden', 'value' => h($entity['file_size'])]); ?>
                    </li>
                    <?= $this->Form->input("_old_{$_column}", array('type' => 'hidden', 'value' => h($entity[$_column]))); ?>

                    <?php else : ?>

                    <li>
                    <?= $this->Form->input("file", array('type' => 'file', 'class' => 'attaches', 'class' => 'form-control'));?>
                    <div class="remark">※MP3(.mp3)ファイルのみ</div>
                    <!-- <div>※ファイルサイズxxxMB以内</div> -->
                    </li>

                    <?php endif; ?>

                    </ul>
                </div>
              </div>

              <div class="form-group row changeArea movieArea">
                <?php $_column = 'file'; ?>
                <label for="" class="col-12 col-md-3 col-form-label control_title">動画<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('movie_tag', array('type' => 'text', 'maxlength' => 40, 'style' => 'width:200px;', 'class' => 'form-control'));?>
                  <span class="btn_area">
                  <a href="javascript:void(0);" class="btn btn-info btn-sm" id="btnYoutubeInfo">情報取得</a>
                  </span>
                  <br><span class="text-danger">※Youtubeの動画コードを入力してください</span><br>
                  <div id="player"></div>
                  <div>
                  <?= $this->Form->input('view_second', ['type' => 'text', 'readonly' => false, 'style' => 'width: 60px;', 'id' => 'idViewSecond', 'class' => 'text-right form-control']); ?>秒
                  
                  </div>
                </div>
              </div>

              <div class="form-group row changeArea mp4Area webmArea">
                <?php $_column = 'file'; ?>
                <label for="" class="col-12 col-md-3 col-form-label control_title">ファイル<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <ul>
                    <?php if (!empty($entity['attaches'][$_column]['0'])) :?>

                    <video width="300px;" id="mate_mp4_<?= $entity['id'] ?>">
                        <source src="<?= $entity['attaches']['file']['src']; ?>">
                    </video>

                    

                    <li class="<?= h($entity['attaches'][$_column]['extention']); ?>">
                        <?= $this->Form->input("file_name", ['type' => 'hidden', 'maxlength' => '50', 'style' => 'width:300px;', 'placeholder' => '添付ファイル']); ?>
                        <?= $this->Form->input("file_size", ['type' => 'hidden', 'value' => h($entity['file_size'])]); ?>
                    </li>
                    <?= $this->Form->input("_old_{$_column}", array('type' => 'hidden', 'value' => h($entity[$_column]))); ?>

                    <?php else : ?>

                    <li>
                        <?= $this->Form->input("file", array('type' => 'file', 'class' => 'attaches form-control'));?>
                        <div class="remark">※mp4 , mov ファイルのみ</div>
                        <!-- <div>※ファイルサイズxxxMB以内</div> -->
                    </li>

                    <?php endif; ?>

                    <!-- <li>
                        <-?= $this->Form->input('view_second', ['type' => 'text', 'readonly' => false, 'style' => 'width: 60px;', 'id' => 'idViewSecond', 'class' => 'text-right']); ?>秒
                    </li> -->
                  </ul>
                </div>
              </div>

              <div class="form-group row changeArea urlArea">
                <?php $_column = 'file'; ?>
                <label for="" class="col-12 col-md-3 col-form-label control_title">URL<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('url', array('type' => 'text', 'maxlength' => 255, 'class' => 'form-control'));?>
                  <span>※URLのみを入力してください</span>
                </div>
              </div>

              <div class="form-group row changeArea contentArea">
                <?php $_column = 'file'; ?>
                <label for="" class="col-12 col-md-3 col-form-label control_title">コンテンツ<span class="attent">※必須</span></label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->input('content', ['type' => 'textarea',
                                                        'class' => 'editor'
                                                    ]); ?>
                </div>
              </div>

              <!-- 所有者 -->
              <div class="form-group row">
                <label for="" class="col-12 col-md-3 col-form-label control_title">所有者</label>
                <div class="col-12 col-md-9 control_value">
                  <?= $this->Form->hidden('user_id'); ?>
                  <?php if ($data['id']): ?>
                    <?php if (empty($entity->user_id)): ?>
                      管理者
                    <?php else: ?>
                      <?= $entity->user->name; ?>
                    <?php endif; ?>
                  <?php else: ?>
                    <?= $this->Session->read('data.name'); ?>
                  <?php endif; ?>
                </div>
              </div>
              
              <?php if (false): ?>
              <div class="form-group row">
                <?php $_column = 'file'; ?>
                <label for="" class="col-12 col-md-3 col-form-label control_title">状態</label>
                <div class="col-12 col-md-9 control_value">
                <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
                </div>
              </div>
              <?php endif; ?>

            </div>
          <?php if (empty($data['id']) || ($data['id'] && $entity->user_id == $this->Session->read('userid'))): ?>
            <div class="btn_area center">
              <?php if (!empty($data['id']) && $data['id'] > 0){ ?>
                  <a href="#" class="btn btn-danger btn_post submitButton"><i class="fas fa-check"></i> 変更する</a>
                <?php if (empty($is_import_data)): ?>
                  <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content'))?>')" class="btn btn_post btn_delete">
                    <i class="far fa-trash-alt"></i> 削除する</a>
                <?php else: ?>
                  <a href="#" class="btn btn_post btn_delete disabled" role="button" aria-disabled="true">
                    <i class="far fa-trash-alt"></i> 削除する</a><span class="attent">紐づいてるデータがあるので削除出来ません。</span>
                <?php endif; ?>
              <?php }else{ ?>
                  <a href="#" class="btn btn-danger btn_post submitButton"><i class="fas fa-check"></i> 登録する</a>
              <?php } ?>
            </div>
          <?php endif; ?>
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

<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/user/common/css/cms.css">


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
    $('.webmArea input').prop('disabled', false);
    $('.soundArea input').prop('disabled', true);
  } else if (type == '8') {
    $('.soundArea').show();
    $('.soundArea input').prop('disabled', false);
    $('.webmArea input').prop('disabled', true);
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
  
  document.getElementById('mate_mp4_<?= $entity['id'] ?>').currentTime = 1.0;

  $('#mate_mp4_<?= $entity['id'] ?>')
  .mouseover( function() {
      $(this).get(0).setAttribute("controls", "controls");
  }).mouseout( function() {
      $(this).get(0).removeAttribute("controls");
  });
})

</script>

<?php $this->end();?>
