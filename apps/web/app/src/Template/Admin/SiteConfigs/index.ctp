<div class="title_area">
      <h1>サイト管理</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><span>サイト一覧 </span></li>
        </ul>
      </div>
    </div>

<?php
//データの位置まで走査
$count = array('total' => 0,
               'enable' => 0,
               'disable' => 0);
$count['total'] = $query->count();
?>
    <div class="content_inr">

      <div class="box">
        <h3>登録一覧</h3>
        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit')); ?>" class="btn_confirm">新規登録</a></div>
        <span class="result"><?php echo $count['total']; ?>件の登録</span>
        <div class="table_area">
          <table>
            <tr>
            <th style="width:4em;">アカウント状態</th>
            <th style="width:4em;">No</th>
            <th style="text-align:left;width: 300px;">サイト名</th>
            <th style="text-align:left;">ディレクトリ名</th>
            </tr>

<?php
foreach ($query->toArray() as $key => $data):
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

$preview_url = $this->Url->build(array('admin' => false, 'action' => 'detail', $data->id, '?' => array('preview' => 'on')));
?>
            <a name="m_<?= $id ?>"></a>
            <tr class="<?= $status ? "visible" : "unvisible" ?>" id="content-<?= $data->id ?>">
              <td><div class="<?= $status ? "visi" : "unvisi" ?>"><?= $this->Html->link(($status? "掲載中" : "下書き" ), array('action' => 'enable', $data->id) )?></div>
              </td>

              <td title="表示順：<?= $data->position; ?>">
                <?= $no; ?>
              </td>

              <td>
                <?= $this->Html->link($data->site_name, $this->Url->build(array('action' => 'edit', $data->id)))?>
              </td>

              <td>
                <?= $this->Html->link($data->slug, $this->url->build(['action' => 'edit', $data->id])); ?>
              </td>

            </tr>

<?php endforeach; ?>

          </table>

    <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit')); ?>" class="btn_confirm">新規登録</a></div>

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
