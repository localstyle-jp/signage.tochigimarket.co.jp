<div class="title_area">
      <h1>お知らせカテゴリ</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><span>お知らせカテゴリ </span></li>
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
            <th style="width:4em;">掲載</th>
            <th style="width:4em;">No</th>
            <th style="text-align:left;">カテゴリ名</th>
            <th style="width:12em;">順序の変更</th>
            </tr>

<?php
foreach ($query->toArray() as $key => $data):
$no = sprintf("%02d", $data->id);
$id = $data->id;
$scripturl = '';
if ($data['status'] === 'publish') {
    $status = true;
} else {
    $status = false;
}

?>
            <a name="m_<?= $id ?>"></a>
            <tr class="<?= $status ? "visible" : "unvisible" ?>" id="content-<?= $data->id ?>">
              <td><div class="<?= $status ? "visi" : "unvisi" ?>"><?= $this->Html->link(($status? "掲載中" : "下書き" ), array('action' => 'enable', $data->id) )?></div>
              </td>

              <td title="表示順：<?= $data->position?>">
                <?= $no?>
              </td>

              <td>
                <?= $this->Html->link($data->name, $this->Url->build(array('action' => 'edit', $data->id)))?>
              </td>

              <td>
                <ul class="ctrlis">
                <?php if($key > 0): ?>
                  <li class="cttop"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'top') )?></li>
                  <li class="ctup"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'up') )?></li>
                <?php else: ?>
                  <li class="non">&nbsp;</li>
                  <li class="non">&nbsp;</li>
                <?php endif; ?>

                <?php if($key < $count['total']-1): ?>
                  <li class="ctdown"><?= $this->Html->link('top', array('action' => 'position', $data->id, 'down') )?></li>
                  <li class="ctend"><?= $this->Html->link('bottom', array('action' => 'position', $data->id, 'bottom') )?></li>
                <?php else: ?>
                  <li class="non">&nbsp;</li>
                  <li class="non">&nbsp;</li>
                <?php endif; ?>
                </ul>
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
$(function () {



})
</script>
<?php $this->end();?>
