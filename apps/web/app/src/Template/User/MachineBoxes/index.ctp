<div class="title_area">
      <h1>表示端末</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><span>表示端末</span></li>
        </ul>
      </div>
    </div>

<?php
//データの位置まで走査
$count = array('total' => 0,
               'enable' => 0,
               'disable' => 0);
$count['total'] = $data_query->count();
?>
  
    <?= $this->element('error_message'); ?>
    
    <div class="content_inr">

      <div class="box">
          <h3>検索条件</h3>
          <div class="table_area form_area">
<?= $this->Form->create(false, array('type' => 'get', 'name' => 'fm_search', 'id' => 'fm_search', 'url' => array('action' => 'index'), 'class' => '')); ?>
              <table class=" table border-0">
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center;vertical-align: middle;">表示コンテンツ</td>
                    <td class="border-0" colspan="3">
                      <?= $this->Form->input('sch_content', ['type' => 'select',
                                                             'value' => $query['sch_content'],
                                                             'options' => $content_list,
                                                             'empty' => ['0' => '全て']
                                                           ]); ?>
                    </td>
                  </tr>
              </table>

              <div class="btn_area">
                <a class="btn btn-secondary" href="<?= $this->Url->build(['action' => 'index']); ?>"><i class="fas fa-eraser"></i> クリア</a>
                <button class="btn btn-primary" onclick="document.fm_search.submit();"><i class="fas fa-search"></i> 検索開始</button>
              </div>
<?= $this->Form->end(); ?>
          </div>
      </div>

      <div class="box">
        <h3 class="box__caption--count"><span>登録一覧</span><span class="count"><?php echo $count['total']; ?>件の登録</span></h3>

        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit', '?' => [])); ?>" class="btn btn-primary w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a></div>

        

        <div class="table_area">
          <table class="table__list table-hover" style="table-layout: fixed;">
          <colgroup>
          <?php if (false): ?>
            <col style="width: 74px;">
            <?php endif; ?>
            <col style="width: 75px;">
            <col>
            <col style="width: 250px;">
            <col style="width: 150px;">
            <col style="width: 300px;">
          <?php if (!$is_search): ?>
            <col style="width: 150px;">
          <?php endif; ?>

          </colgroup>

            <tr>
            <?php if (false): ?>
              <th >状態</th>
              <?php endif; ?>
              <th >ID</th>
              <th style="text-align:left;">名前</th>
              <th colspan="2">表示コンテンツ [シリアルNo.]</th>
              <th>操作</th>
            <?php if (!$is_search): ?>
              <th>並び順</th>
            <?php endif; ?>
            </tr>

<?php
foreach ($data_query->toArray() as $key => $data):
$no = sprintf("%02d", $data->id);
$id = $data->id;
$scripturl = '';
if ($data['status'] === 'publish') {
    $count['enable']++;
    $status = true;
} else {
    $count['disable']++;
    $status = false;
}

$preview_url = "/" . $this->Common->session_read('data.username') . "/{$data->id}?preview=on";
?>
            <a name="m_<?= $id ?>"></a>
            <tr class="<?= $status ? "visible" : "unvisible" ?>" id="content-<?= $data->id ?>">

            <?php if (false): ?>
              <td>
                <?= $this->element('status_button', ['status' => $status, 'id' => $data->id]); ?>
              </td>
              <?php endif; ?>

              <td>
                <?= h($data->id); ?>
              </td>

              <td>
                <?= $this->Html->link($data->name, ['action' => 'edit', $data->id, '?' => []], ['class' => 'btn-block text-left'])?>
              </td>

              <td>
              <?php if ($data->machine_content_id): ?>
                <?= $data->machine_content->name; ?> [<?= $data->machine_content->serial_no; ?>]
                
                <?php else: ?>
                  （未設定）
                <?php endif; ?>
              </td>

              <td>
              <?php if ($data->machine_content_id): ?>
                <span class="btn_area">
                  <a href="<?= $this->Url->build(['controller' => 'contents', 'action' => 'edit', $data->content_id, '?' => ['mode' => 'machine']]); ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> コンテンツの編集</a>

                
                </span>
              <?php endif; ?>
              </td>


              <td>
                <?php $url = '/view/' . $site_config->slug . '/' . trim($data->url, '/') . '/'; ?>
                <div class="btn_area" style="text-align:left;">
                  <a href="<?= $this->Url->build(['action' => 'edit', $data->id]); ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 編集</a>
                  <a href="<?= $url; ?>" class="btn btn-info btn-sm" target="_blank"><i class="fas fa-search"></i> プレビュー</a>
                <?php if ($data->content->serial_no != $data->machine_content->serial_no): ?>
                  <a href="javascript:void(0);" class="btn btn-warning btn-sm blinking" style="color:#212529;" id="btnUpdateContent" data-id="<?= $data->id; ?>"><i class="fas fa-sync-alt"></i> 最新版にする</a>
                  <?= $this->Form->create(false, ['type' => 'get', 'url' => ['action' => 'update-content', $data->id], 'id' => 'fm_update_'.$data->id]); ?>
                  <?= $this->Form->end(); ?>
                <?php else: ?>
                  <a href="#" class="btn btn-success btn-sm disabled" aria-disabled="true"><i class="fas fa-sync-alt"></i> 最新版です</a>
                <?php endif; ?>
                </div>
              </td>

            <?php if (!$is_search): ?>
              <td>
                <ul class="ctrlis">
                <?php if(!$this->Paginator->hasPrev() && $key == 0): ?>
                  <li class="non">&nbsp;</li>
                  <li class="non">&nbsp;</li>
                <?php else: ?>
                  <li class="cttop"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'top') )?></li>
                  <li class="ctup"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'up') )?></li>
                <?php endif; ?>

                <?php if(!$this->Paginator->hasNext() && $key == count($datas)-1): ?>
                  <li class="non">&nbsp;</li>
                  <li class="non">&nbsp;</li>
                <?php else: ?>
                  <li class="ctdown"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'down') )?></li>
                  <li class="ctend"><?= $this->Html->link('bottom', array('action' => 'position', $data->id, 'bottom') )?></li>
                <?php endif; ?>
                </ul>
              </td>
            <?php endif; ?>

            </tr>

<?php endforeach; ?>

          </table>

        </div>

        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit', '?' => [])); ?>" class="btn btn-primary w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a></div>


    </div>
</div>
<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/user/common/css/cms.css">
<script src="/user/common/js/jquery.ui.datepicker-ja.js"></script>
<script src="/user/common/js/cms.js"></script>
<script>
function change_category() {
  $("#fm_search").submit();
  
}
$(function () {

$("#btnUpdateContent").on('click', function() {
  var id = $(this).data('id');

  alert_dlg('現在のコンテンツを最新版にします。<br><span class="text-danger">自動的に表示端末のブラウザの再読み込みを実行します。</span><br>元に戻すことは出来ません。よろしいですか？', 
    {
      buttons:[
        {
          text:'いいえ',
          click: function(){
            $(this).dialog("close");
          }
        },
        {
          text:'はい',
          click: function(){
            $("#fm_update_" + id).submit();
            $(this).dialog("close");
          }
        }
    ]
    });

  return false;
});


})
</script>
<?php $this->end();?>
