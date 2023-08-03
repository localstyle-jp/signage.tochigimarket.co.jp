<?php $this->start('beforeHeaderClose'); ?>

<?php $this->end(); ?>

<div class="title_area">
  <h1>素材</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><a
          href="<?= $this->Url->build(['action' => 'index', '?' => []]); ?>">素材</a>
      </li>
      <li>
        <span><?= ($data['id'] > 0) ? '編集' : '新規登録'; ?></span>
      </li>
    </ul>
  </div>
</div>

<?= $this->element('error_message'); ?>
<div class="content_inr">
  <div class="box">
    <h3>
      <?= ($data['id'] > 0) ? '編集' : '新規登録'; ?>
    </h3>
    <div class="table_area form_area">
      <?= $this->Form->create($entity, array('type' => 'file', 'context' => ['validator' => 'default']));?>
      <?= $this->Form->input('id', array('type' => 'hidden', 'value' => $entity->id, 'id' => 'idId'));?>
      <?= $this->Form->input('position', array('type' => 'hidden'));?>
      <table class="vertical_table table__meta">

        <tr>
          <td>素材名<span class="attent">※必須</span></td>
          <td>
            <?= $this->Form->input('name', array('type' => 'text', 'maxlength' => 40, ));?>
            <br><span>※40文字以内で入力してください</span>
          </td>
        </tr>


        <tr>
          <td>タイプ<span class="attent">※必須</span></td>
          <td>
            <?php if (!empty($entity['attaches']['image']['0']) || !empty($entity['url'])) : ?>
            <?= $this->Form->input('type', array('type' => 'hidden', 'id' => 'typeSelect'));?>
            <div><?= $type_list[$entity->type] ?></div>
            <?php else : ?>
            <?= $this->Form->select('type', $type_list, ['id' => 'typeSelect', 'onChange' => 'select()']);?>
            <br><span>※素材タイプを選択してください</span>
            <?php endif; ?>
          </td>
        </tr>

        <?php if(VIEW_MCAETGORY): ?>
        <tr>
          <td>素材カテゴリ<span class="attent">※必須</span></td>
          <td>
            <?php if (empty($category_list)) : ?>
            <?= $this->Form->input('category_id', ['type' => 'hidden', 'value' => '']);?>
            <?= $this->Form->error('category_id');?>
            <span>※素材カテゴリを設定してください</span>
            <?php else : ?>
            <?= $this->Form->select('category_id', $category_list);?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endif; ?>

        <tr>
          <td>所有者<span class="attent">※必須</span></td>
          <td>
            <?= $this->Form->select('user_id', $user_list, ['empty' => [0 => '管理者(共有)']]);?>
          </td>
        </tr>

        <tr class="changeArea imageArea contentArea">
          <td>画像<span class="attent">※必須</span></td>
          <?php if (!empty($entity['attaches']['image']['0'])) :?>
          <td>
            <img
              src="<?= $this->Url->build($entity['attaches']['image']['0'])?>"
              style="width: 300px;">
            <?= $this->Form->input('attaches.image.0', ['type' => 'hidden']); ?>
            <?= $this->Form->input('_old_image', ['type' => 'hidden', 'value' => $entity->image]); ?>
          </td>
          <?php else:?>

          <td>
            <?= $this->Form->input('image', array('type' => 'file', 'accept' => 'image/jpeg,image/png,image/gif', 'id' => 'idMainImage', 'class' => 'attaches'));?>
            <br><span>※jpeg , jpg , gif , png ファイルのみ</span>
            <br><span class="changeArea imageArea">推奨サイズ 1920 x 1080</span><span class="changeArea contentArea">推奨サイズ
              960 x 540</span>
          </td>
          <?php endif;?>
        </tr>

        <tr class="changeArea soundArea">
          <?php $_column = 'file'; ?>
          <td>音楽<span class="attent">※必須</span></td>
          <td>
            <ul>
              <?php if (!empty($entity['attaches'][$_column]['0'])) :?>
              <li>
                <?= $entity['file_name']; ?>.<?= $entity['file_extension']; ?>
              </li>

              <li
                class="<?= h($entity['attaches'][$_column]['extention']); ?>">
                <?= $this->Form->input('file_name', ['type' => 'hidden', 'maxlength' => '50', 'style' => 'width:300px;', 'placeholder' => '添付ファイル']); ?>
                <?= $this->Form->input('file_size', ['type' => 'hidden', 'value' => h($entity['file_size'])]); ?>
              </li>
              <?= $this->Form->input("_old_{$_column}", array('type' => 'hidden', 'value' => h($entity[$_column]))); ?>

              <?php else : ?>

              <li>
                <?= $this->Form->input('file', array('type' => 'file', 'class' => 'attaches'));?>
                <div class="remark">※MP3(.mp3)ファイルのみ</div>
                <!-- <div>※ファイルサイズxxxMB以内</div> -->
              </li>

              <?php endif; ?>

            </ul>
          </td>
        </tr>


        <tr class="changeArea movieArea">
          <td>動画<span class="attent">※必須</span></td>
          <td>
            <?= $this->Form->input('movie_tag', array('type' => 'text', 'maxlength' => 40, 'style' => 'width:200px;'));?>
            <span class="btn_area">
              <a href="javascript:void(0);" class="btn btn-info btn-sm" id="btnYoutubeInfo">情報取得</a>
            </span>
            <br><span class="text-danger">※Youtubeの動画コードを入力してください</span><br>
            <div id="player"></div>
            <div>
              <?= $this->Form->input('view_second', ['type' => 'text', 'readonly' => false, 'style' => 'width: 60px;', 'id' => 'idViewSecond', 'class' => 'text-right']); ?>秒

            </div>
          </td>
        </tr>

        <tr class="changeArea mp4Area webmArea">
          <?php $_column = 'file'; ?>
          <td>ファイル<span class="attent">※必須</span></td>
          <td>
            <ul>
              <?php if (!empty($entity['attaches'][$_column]['0'])) :?>

              <video width="300px;"
                id="mate_mp4_<?= $entity['id'] ?>">
                <source
                  src="<?= $entity['attaches']['file']['src']; ?>">
              </video>

              <script type="text/javascript">
                document.getElementById(
                    'mate_mp4_<?= $entity['id'] ?>')
                  .currentTime = 1.0;

                $('#mate_mp4_<?= $entity['id'] ?>')
                  .mouseover(function() {
                    $(this).get(0).setAttribute("controls", "controls");
                  }).mouseout(function() {
                    $(this).get(0).removeAttribute("controls");
                  });
              </script>

              <li
                class="<?= h($entity['attaches'][$_column]['extention']); ?>">
                <?= $this->Form->input('file_name', ['type' => 'hidden', 'maxlength' => '50', 'style' => 'width:300px;', 'placeholder' => '添付ファイル']); ?>
                <?= $this->Form->input('file_size', ['type' => 'hidden', 'value' => h($entity['file_size'])]); ?>
              </li>
              <?= $this->Form->input("_old_{$_column}", array('type' => 'hidden', 'value' => h($entity[$_column]))); ?>

              <?php else : ?>

              <li>
                <?= $this->Form->input('file', array('type' => 'file', 'class' => 'attaches'));?>
                <div class="remark">※mp4 , mov ファイルのみ</div>
                <!-- <div>※ファイルサイズxxxMB以内</div> -->
              </li>

              <?php endif; ?>

              <!-- <li>
                    <-?= $this->Form->input('view_second', ['type' => 'text', 'readonly' => false, 'style' => 'width: 60px;', 'id' => 'idViewSecond', 'class' => 'text-right']); ?>秒
                  </li> -->
            </ul>

          </td>
        </tr>


        <tr class="changeArea urlArea">
          <td>URL<span class="attent">※必須</span></td>
          <td>
            <?= $this->Form->input('url', array('type' => 'text', 'maxlength' => 255, ));?>
            <br><span>※URLのみを入力してください</span>
          </td>
        </tr>

        <tr class="changeArea contentArea">
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

        <?php if (false) : ?>
        <tr>
          <td>状態</td>
          <td>
            <?= $this->Form->input('status', array('type' => 'select', 'options' => array('draft' => '無効', 'publish' => '有効')));?>
          </td>
        </tr>
        <?php endif; ?>

      </table>

      <div class="btn_area">
        <?php if (!empty($data['id']) && $data['id'] > 0) { ?>
        <a href="#" class="btn btn-primary w-20 rounded-pill submitButton"><i class="fas fa-check"></i> 変更する</a>
        <?php if (empty($is_import_data)): ?>
        <a href="javascript:kakunin('データを完全に削除します。よろしいですか？','<?= $this->Url->build(array('action' => 'delete', $data['id'], 'content'))?>')"
          class="btn btn-danger">
          <i class="far fa-trash-alt"></i> 削除する</a>
        <?php else: ?>
        <a href="#" class="btn btn-danger disabled" role="button" aria-disabled="true">
          <i class="far fa-trash-alt"></i> 削除する</a><span class="attent">紐づいてるデータがあるので削除出来ません。</span>
        <?php endif; ?>
        <?php } else { ?>
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

  $(function() {

    select();

  })

  function select() {
    var type = $('#typeSelect').val();

    $('.changeArea').hide();

    if (type == '1') {
      $('.imageArea').show();
    } else if (type == '2') {
      $('.movieArea').show();
    } else if (type == '3') {
      $('.urlArea').show();
    } else if (type == '4') {
      $('.contentArea').show();
    } else if (type == '5') {
      $('.mp4Area').show();
    } else if (type == '6') {
      $('.mp4Area').show();
      $('.imageArea').show();
    } else if (type == '7') {
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
        playerVars: {
          'autoplay': 0,
          'controls': 0,
          'loop': 0,
          'playlist': movie,
          'rel': 0,
          'showinfo': 0,
          'color': 'white'
        },
        events: {
          'onReady': function(event) {
            event.target.mute();
            $("#idViewSecond").val(event.target.getDuration());
          },
          'onStateChange': function(event) {

          },
          'onError': function(event) {

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