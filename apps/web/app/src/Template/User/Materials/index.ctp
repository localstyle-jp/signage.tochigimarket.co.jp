<?php use App\Model\Entity\Material; ?>

<div class="title_area">
      <h1>素材</h1>
      <div class="pankuzu">
        <ul>
          <?= $this->element('pankuzu_home'); ?>
          <li><span>素材</span></li>
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
<!-- <-?= $this->Form->create(false, array('type' => 'get', 'name' => 'fm_search', 'id' => 'fm_search', 'url' => array('action' => 'index'), 'class' => '')); ?> -->
              <table class=" table border-0">
                  <?= $this->Form->create(false, array('type' => 'get', 'name' => 'fm_search', 'id' => 'fm_search', 'url' => array('action' => 'index'), 'class' => '')); ?>
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center;vertical-align: middle;">タイプ</td>
                    <td class="border-0" colspan="3">
                      <?= $this->Form->input('sch_category_id', ['type' => 'hidden', 'value' => $query['sch_category_id']]); ?>
                      <?= $this->Form->input('sch_type', ['type' => 'select',
                                                             'value' => $query['sch_type'],
                                                             'onChange' => 'change_category("fm_search");',
                                                             'options' => $type_list,
                                                             'empty' => ['0' => '全て']
                                                           ]); ?>
                    </td>
                  </tr>
                  <?= $this->Form->end(); ?>
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center;vertical-align: middle;">素材カテゴリ</td>
                    <td class="border-0" colspan="3">
                    <?php foreach ($category_list as $clist): ?>
                      <div class="breadcrumb-item" style="display: inline-block;">
                        <?= $this->Form->create(false, ['type' => 'get', 'id' => 'fm_search_' . $clist['category']->id, 'style' => 'display:inline-block;']); ?>
                        <?= $this->Form->input('sch_type', ['type' => 'hidden', 'value' => $query['sch_type']]); ?>
                        <?= $this->Form->input('sch_category_id', ['type' => 'select',
                                                                    'options' => $clist['list'],
                                                                    'onChange' => 'change_category("fm_search_' . $clist['category']->id . '");',
                                                                    'value' => $clist['category']->id,
                                                                    'empty' => $clist['empty']
                                                                  ]); ?>
                        <?= $this->Form->end(); ?>
                      </div>
                    <?php endforeach; ?>
                    </td>
                  </tr>
              </table>

              <div class="btn_area">
                <a class="btn btn-secondary" href="<?= $this->Url->build(['action' => 'index']); ?>"><i class="fas fa-eraser"></i> クリア</a>
                <!-- <button class="btn btn-primary" onclick="document.fm_search.submit();"><i class="fas fa-search"></i> 検索開始</button> -->
              </div>
<!-- <-?= $this->Form->end(); ?> -->
          </div>
      </div>


      <div class="box">
        <h3 class="box__caption--count"><span>登録一覧</span><span class="count"><?php echo $count['total']; ?>件の登録</span></h3>

        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit', '?' => ['sch_type' => $query['sch_type'], 'sch_category_id' => $query['sch_category_id']])); ?>" class="btn btn-primary w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a></div>
        
        <div style="text-align: center; margin-top: 10px;"><?= $this->Paginator->numbers();?></div>

        <div class="table_area">
          <table class="table__list table-hover" style="table-layout: fixed;">
          <colgroup>
            <col style="width: 74px;">
            <col>
            <col style="width: 100px;">
            <col style="width: 100px;">
            <col style="width: 100px;">
            <col style="width: 150px;">
          <?php if (!$is_search): ?>
            <col style="width: 150px;">
          <?php endif; ?>

          </colgroup>

            <tr>
              <th >状態</th>
              <th style="text-align:left;">素材名</th>
              <th>タイプ</th>
              <th>登録日時</th>
              <th>更新日時</th>
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
                <?= h($data->material_category->name); ?>
                <?= $this->Html->link($data->name, ['action' => 'edit', $data->id, '?' => ['sch_type' => $query['sch_type'], 'sch_category_id' => $query['sch_category_id']]], ['class' => 'btn-block text-left'])?>
              </td>

              <td>
                <?= Material::$type_list[$data->type]; ?>
              </td>

              <td>
                <?= $data->created; ?>
              </td>

              <td>
                <?= $data->modified; ?>
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
        <div style="text-align: center; margin-top: 10px;"><?= $this->Paginator->numbers();?></div>
        <div class="btn_area" style="margin-top:10px;"><a href="<?= $this->Url->build(array('action' => 'edit', '?' => ['sch_type' => $query['sch_type'], 'sch_category_id' => $query['sch_category_id']])); ?>" class="btn btn-primary w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a></div>


    </div>
</div>
<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/admin/common/css/cms.css">
<script>
function change_category(elm) {
  $("#" + elm).submit();
  
}

function change_type() {
  $("#fm_search").submit();
  
}
$(function () {



})
</script>
<?php $this->end();?>
