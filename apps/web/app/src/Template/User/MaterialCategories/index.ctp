<?php use App\Model\Entity\Material; ?>

<div class="title_area">
      <h1>素材カテゴリ</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><span>素材カテゴリ</span></li>
        </ul>
      </div>
    </div>

<?php
//データの位置まで走査
$count = array('total' => 0,
               'enable' => 0,
               'disable' => 0);
// $count['total'] = $data_query->count();
$count['total'] = $numrows;
?>
  
    <?= $this->element('error_message'); ?>
    
    <div class="content_inr">
<?php if (false) : ?>
      <div class="box">
          <h3>検索条件</h3>
          <div class="table_area form_area">
<?= $this->Form->create(false, array('type' => 'get', 'name' => 'fm_search', 'id' => 'fm_search', 'url' => array('action' => 'index'), 'class' => '')); ?>
              <table class=" table border-0">
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center;vertical-align: middle;">タイプ</td>
                    <td class="border-0" colspan="3">
                      <?= $this->Form->input('sch_type', ['type' => 'select',
                                                             'value' => $query['sch_type'],
                                                             'options' => $type_list,
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
<?php endif; ?>


      <div class="box">
        <h3 class="box__caption--count"><span>登録一覧</span><span class="count"><?php echo $count['total']; ?>件の登録</span></h3>

        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit', '?' => ['parent_id' => $query['parent_id']])); ?>" class="btn btn-primary w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a></div>

        <div class="btn_area" style="margin-top:10px; text-align:right; margin-right:30px;">
          <a href="<?= $this->Url->build(array('controller' => 'materials', 'action' => 'index')); ?>" class="btn btn-primary">素材一覧</a>
        </div>

        <div class="table_area">
          <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="<?= $this->Url->build(['action' => 'index', '?' => []]); ?>">ルート</a></li>
            <?php $parent_categories = $parent_category; ?>
            <?php while ($pcat = array_pop($parent_categories)): ?>
              <?php if ($query['parent_id'] == $pcat->id): ?>
                <li class="breadcrumb-item active"><?= $pcat->name; ?></li>
              <?php else: ?>
                <li class="breadcrumb-item"><a href="<?= $this->Url->build(['action' => 'index', '?' => ['parent_id' => $pcat->id]]); ?>"><?= $pcat->name; ?></a></li>
              <?php endif; ?>
            <?php endwhile; ?>
            </ul>
          </nav>

          <table class="table__list table-hover" style="table-layout: fixed;">
          <colgroup>
          <?php if (false): ?>
            <col style="width: 74px;">
            <?php endif; ?>
            <col style="width: 75px;">
            <col>
            <col style="width: 250px;">
            <col style="width: 150px;">

          </colgroup>

            <tr>
            <?php if (false): ?>
              <th >状態</th>
              <?php endif; ?>
              <th >ID</th>
              <th style="text-align:left;">カテゴリ名</th>
              <th>操作</th>
              <th>並び順</th>
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
                <?= $this->Html->link($data->name, ['action' => 'edit', $data->id, '?' => ['parent_id' => $data->parent_category_id]], ['class' => 'btn-block text-left'])?>
              </td>

              <td>
                <div class="btn_area" style="text-align:left;">
                  <a href="<?= $this->Url->build(['action' => 'index', '?' => ['parent_id' => $data->id]]); ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 下層カテゴリ</a>
                </div>
              </td>

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

            </tr>

<?php endforeach; ?>

          </table>

        </div>
        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit', '?' => ['parent_id' => $query['parent_id']])); ?>" class="btn btn-primary w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a></div>


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
