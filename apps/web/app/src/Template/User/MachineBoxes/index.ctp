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
        <h3 class="box__caption--count"><span>登録一覧</span><span class="count"><?php echo $count['total']; ?>件の登録</span></h3>

        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit', '?' => [])); ?>" class="btn btn-primary w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a></div>

        

        <div class="table_area">
          <table class="table__list table-hover" style="table-layout: fixed;">
          <colgroup>
            <col style="width: 74px;">
            <col>
            <col style="width: 300px;">
            <col style="width: 200px;">
            <col style="width: 100px;">
          <?php if (!$is_search): ?>
            <col style="width: 150px;">
          <?php endif; ?>

          </colgroup>

            <tr>
              <th >状態</th>
              <th style="text-align:left;">名前</th>
              <th>表示コンテンツ</th>
              <th>URL</th>
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

              <td>
                <?= $this->element('status_button', ['status' => $status, 'id' => $data->id]); ?>
              </td>

              <td>
                <?= $this->Html->link($data->name, ['action' => 'edit', $data->id, '?' => []], ['class' => 'btn-block text-left'])?>
              </td>

              <td>
              <?php if ($data->content_id): ?>
                <?= $data->content->name; ?>
                <span class="btn_area">
                  <a href="<?= $this->Url->build(['controller' => 'contents', 'action' => 'edit', $data->content_id]); ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 編集</a>
                </span>
                <?php else: ?>
                  （未設定）
                <?php endif; ?>
              </td>

              <td>
                <?php $url = '/view/' . $site_config->slug . '/' . trim($data->url, '/') . '/'; ?>
                <a href="<?= $url; ?>" target="_blank">
                  <?= $url; ?>
                </a>
              </td>


              <td>
                <div class="btn_area" style="text-align:left;">
                  <a href="<?= $this->Url->build(['action' => 'edit', $data->id]); ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 編集</a>
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
<link rel="stylesheet" href="/admin/common/css/cms.css">
<script>
function change_category() {
  $("#fm_search").submit();
  
}
$(function () {



})
</script>
<?php $this->end();?>
