<?php $role_key = $this->Common->getUserRoleKey(); ?>
<?php $menu_list = $this->UserAdmin->getUserMenu($role_key); ?>

<div class="title_area">
  <h1>管理メニュー</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><span>管理メニュー</span></li>
    </ul>
  </div>
</div>

<?= $this->element('error_message'); ?>

<div class="content_inr">

  <?php foreach ($menu_list as $title => $menu): ?>
  <div class="box">
    <h3 style="margin-bottom:20px;"><?= $title; ?></h3>
    <?php foreach ($menu as $sub_title => $m): ?>

    <?php if (!is_numeric($sub_title)): ?>
    <h4 style="padding: 10px;"><?= $sub_title; ?></h4>
    <?php endif; ?>

    <div class="btn_area" style="text-align:left;margin-left: 20px;margin-bottom: 10px !important;">
      <?php foreach ($m as $name => $link): ?>
      <?php if (is_array($link)): ?>
      <a href="<?= $link['link']; ?>"
        class="btn btn-primary btn" style="width:130px;text-align:center;"><i
          class="<?= $link['icon'];?>"></i>
        <?= $name; ?></a>
      <?php else: ?>
      <a href="<?= $link; ?>" class="btn btn-primary btn"
        style="width:130px;text-align:center;"><?= $name; ?></a>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>

    <?php endforeach; ?>

  </div>
  <?php endforeach; ?>

  <div class="box">
    <h3>表示端末</h3>
    <div class="table_area">
      <?php if (!empty($machines)): ?>
      <div class="row">
        <?php foreach ($machines as $key => $data): ?>
        <?php $status = ($data->status == 'publish' ? true : false); ?>
        <div class="col-lg-3 mb-5">
          <div class="card" style="width: 250px;">
            <div class="card-header">
              <div class="btn_area text-center mt-2">
                <?php if ($data->content_id && $data->content): ?>
                <?php if (empty($data->machine_content) || $data->content->serial_no != $data->machine_content->serial_no): ?>
                <a href="javascript:void(0);" class="btn btn-warning btn-sm blinking w-100 btnUpdateContent"
                  style="color:#212529;"
                  data-id="<?= $data->id; ?>"><i
                    class="fas fa-sync-alt"></i> 最新版にする</a>
                <?= $this->Form->create(false, ['type' => 'get', 'url' => ['controller' => 'machine-boxes', 'action' => 'update-content', $data->id], 'id' => 'fm_update_' . $data->id]); ?>
                <?= $this->Form->end(); ?>
                <?php else: ?>
                <a href="#" class="btn btn-success btn-sm w-100" aria-disabled="true" id="btnLastVersion"><i
                    class="fas fa-sync-alt"></i> 最新版です</a>
                <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>

            <div class="card-body">
              <h5 class="card-title"><?= h($data->name); ?></h5>
              <div class="btn_area">
                <a href="<?= '/view/' . $data->site_config->slug . '/' . trim($data->url, '/') . '/'; ?>"
                  target="_blank" class="btn btn-info btn-sm"><i class="fas fa-search"></i> プレビュー</a>
                <a href="<?= $this->Url->build(['controller' => 'machine-boxes', 'action' => 'edit', $data->id]); ?>"
                  class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 編集</a>
              </div>



            </div>
            <div class="card-footer">
              <?= nl2br(h($data->memo)); ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

    </div>
  </div>

</div>
<?php $this->start('beforeBodyClose');?>
<script src="/user/common/js/jquery.ui.datepicker-ja.js"></script>
<script src="/user/common/js/cms.js"></script>
<script>
  function change_category() {
    $("#fm_search").submit();

  }
  $(function() {

    $(".btnUpdateContent").on('click', function() {
      var id = $(this).data('id');

      alert_dlg(
        '現在のコンテンツを最新版にします。<br><span class="text-danger">自動的に表示端末のブラウザの再読み込みを実行します。</span><br>元に戻すことは出来ません。よろしいですか？', {
          buttons: [{
              text: 'いいえ',
              click: function() {
                $(this).dialog("close");
              }
            },
            {
              text: 'はい',
              click: function() {
                $("#fm_update_" + id).submit();
                $(this).dialog("close");
              }
            }
          ]
        });

      return false;
    });

    $("#btnLastVersion").on('click', function() {
      alert_dlg('最新版です');
    });

  })
</script>
<?php $this->end();?>