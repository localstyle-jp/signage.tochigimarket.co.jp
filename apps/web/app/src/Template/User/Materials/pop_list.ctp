<?php use App\Model\Entity\Material; ?>
<div class="title_area">
  <h1>素材リスト</h1>
</div>

    <?= $this->element('error_message'); ?>
    
    <div class="content_inr">

      <div class="box">
          <h3>検索条件</h3>
          <div class="table_area form_area">
<!-- <-?= $this->Form->create(false, array('type' => 'get', 'name' => 'fm_search', 'id' => 'fm_search', 'class' => '')); ?> -->
              <table class=" table border-0">
<?php $search_column_list = ['sch_name', 'sch_category_id', 'sch_modified_year', 'sch_modified_month', 'sch_created_year', 'sch_created_month', 'sch_type']; ?>
                <!-- 素材名 -->
                <?= $this->Form->create(false, array('type' => 'get', 'id' => 'fm_search_name', 'class' => '')); ?>
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center; vertical-align: middle;">素材名</td>
                    <td class="border-0">
<?php $columns = ['sch_name'] ?>
                      <?php foreach ($search_column_list as $c) : ?>
                      <?php if (in_array($c, $columns)) {continue;} ?>
                      <?= $this->Form->input($c, ['type' => 'hidden', 'value' => $query[$c]]); ?>
                      <?php endforeach; ?>
                      <!-- <-?= $this->Form->input('sch_category_id', ['type' => 'hidden', 'value' => $query['sch_category_id']]); ?>
                      <-?= $this->Form->input('sch_type', ['type' => 'hidden', 'value' => $query['sch_type']]); ?>
                      <-?= $this->Form->input('sch_modified_year', ['type' => 'hidden', 'value' => $query['sch_modified_year']]); ?>
                      <-?= $this->Form->input('sch_modified_month', ['type' => 'hidden', 'value' => $query['sch_modified_month']]); ?>
                      <-?= $this->Form->input('sch_created_year', ['type' => 'hidden', 'value' => $query['sch_created_year']]); ?>
                      <-?= $this->Form->input('sch_created_month', ['type' => 'hidden', 'value' => $query['sch_created_month']]); ?> -->
                      <?= $this->Form->input('sch_name', ['type' => 'text',
                                                             'value' => $query['sch_name'],
                                                             'class' => 'w-100',
                                                             'onChange' => 'change_category("fm_search_name");',
                                                             'placeholder' => 'あいまい検索'
                                                           ]); ?>
                    </td>
                <?= $this->Form->end(); ?>
                <!-- 素材タイプ -->
                <?= $this->Form->create(false, array('type' => 'get', 'id' => 'fm_search_type', 'class' => '')); ?>
                    <td class="border-0 head" style="width: 120px;text-align: center; vertical-align: middle;">タイプ</td>
                    <td class="border-0">
<?php $columns = ['sch_type'] ?>
                      <?php foreach ($search_column_list as $c) : ?>
                      <?php if (in_array($c, $columns)) {continue;} ?>
                      <?= $this->Form->input($c, ['type' => 'hidden', 'value' => $query[$c]]); ?>
                      <?php endforeach; ?>
                      <!-- <-?= $this->Form->input('sch_category_id', ['type' => 'hidden', 'value' => $query['sch_category_id']]); ?>
                      <-?= $this->Form->input('sch_name', ['type' => 'hidden', 'value' => $query['sch_name']]); ?>
                      <-?= $this->Form->input('sch_modified_year', ['type' => 'hidden', 'value' => $query['sch_modified_year']]); ?>
                      <-?= $this->Form->input('sch_modified_month', ['type' => 'hidden', 'value' => $query['sch_modified_month']]); ?>
                      <-?= $this->Form->input('sch_created_year', ['type' => 'hidden', 'value' => $query['sch_created_year']]); ?>
                      <-?= $this->Form->input('sch_created_month', ['type' => 'hidden', 'value' => $query['sch_created_month']]); ?> -->
                      <?= $this->Form->input('sch_type', ['type' => 'select',
                                                          'options' => $type_list,
                                                          'empty' => ['0' => '全て'],
                                                          'onChange' => 'change_category("fm_search_type");',
                                                          'value' => $query['sch_type']
                                                        ]); ?>
                    </td>
                <?= $this->Form->end(); ?>
                  </tr>
                  <!-- 素材カテゴリ -->
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center; vertical-align: middle;">素材カテゴリ</td>
                    <td class="border-0" colspan="3">
                    <?php foreach ($category_list as $clist): ?>
                      <div class="breadcrumb-item" style="display: inline-block;">
                        <?= $this->Form->create(false, ['type' => 'get', 'id' => 'fm_search_' . $clist['category']->id, 'style' => 'display:inline-block;']); ?>
<?php $columns = ['sch_category_id'] ?>
                          <?php foreach ($search_column_list as $c) : ?>
                          <?php if (in_array($c, $columns)) {continue;} ?>
                          <?= $this->Form->input($c, ['type' => 'hidden', 'value' => $query[$c]]); ?>
                          <?php endforeach; ?>
                          <!-- <-?= $this->Form->input('sch_type', ['type' => 'hidden', 'value' => $query['sch_type']]); ?>
                          <-?= $this->Form->input('sch_name', ['type' => 'hidden', 'value' => $query['sch_name']]); ?>
                          <-?= $this->Form->input('sch_modified_year', ['type' => 'hidden', 'value' => $query['sch_modified_year']]); ?>
                          <-?= $this->Form->input('sch_modified_month', ['type' => 'hidden', 'value' => $query['sch_modified_month']]); ?>
                          <-?= $this->Form->input('sch_created_year', ['type' => 'hidden', 'value' => $query['sch_created_year']]); ?>
                          <-?= $this->Form->input('sch_created_month', ['type' => 'hidden', 'value' => $query['sch_created_month']]); ?> -->
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
                  <!-- 登録日時 -->
                  <?= $this->Form->create(false, array('type' => 'get', 'id' => 'fm_search_created', 'class' => '')); ?>
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center; vertical-align: middle;">登録日時</td>
                    <td class="border-0" colspan="3">
<?php $columns = ['sch_created_year','sch_created_month'] ?>
                      <?php foreach ($search_column_list as $c) : ?>
                      <?php if (in_array($c, $columns)) {continue;} ?>
                      <?= $this->Form->input($c, ['type' => 'hidden', 'value' => $query[$c]]); ?>
                      <?php endforeach; ?>
                      <!-- <-?= $this->Form->input('sch_category_id', ['type' => 'hidden', 'value' => $query['sch_category_id']]); ?>
                      <-?= $this->Form->input('sch_type', ['type' => 'hidden', 'value' => $query['sch_type']]); ?>
                      <-?= $this->Form->input('sch_name', ['type' => 'hidden', 'value' => $query['sch_name']]); ?>
                      <-?= $this->Form->input('sch_modified_year', ['type' => 'hidden', 'value' => $query['sch_modified_year']]); ?>
                      <-?= $this->Form->input('sch_modified_month', ['type' => 'hidden', 'value' => $query['sch_modified_month']]); ?> -->
                      <?= $this->Form->input('sch_created_year', ['type' => 'select',
                                                             'value' => $query['sch_created_year'],
                                                             'onChange' => 'change_category("fm_search_created");',
                                                             'options' => $year_list,
                                                             'empty' => ['0' => '--'],
                                                           ]); ?>
                      年

                      <?php if ($query['sch_created_year']) : ?>
                      <?= $this->Form->input('sch_created_month', ['type' => 'select',
                                                             'value' => $query['sch_created_month'],
                                                             'onChange' => 'change_category("fm_search_created");',
                                                             'options' => $month_list,
                                                             'empty' => ['0' => '--'],
                                                           ]); ?>
                      月
                      <?php endif; ?>
                      
                    </td>
                  </tr>
                  <?= $this->Form->end(); ?>
                  <!-- 更新日時 -->
                  <?= $this->Form->create(false, array('type' => 'get', 'id' => 'fm_search_modified', 'class' => '')); ?>
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center; vertical-align: middle;">更新日時</td>
                    <td class="border-0" colspan="3">
<?php $columns = ['sch_modified_year','sch_modified_month'] ?>
                      <?php foreach ($search_column_list as $c) : ?>
                      <?php if (in_array($c, $columns)) {continue;} ?>
                      <?= $this->Form->input($c, ['type' => 'hidden', 'value' => $query[$c]]); ?>
                      <?php endforeach; ?>
                      <!-- <-?= $this->Form->input('sch_category_id', ['type' => 'hidden', 'value' => $query['sch_category_id']]); ?>
                      <-?= $this->Form->input('sch_type', ['type' => 'hidden', 'value' => $query['sch_type']]); ?>
                      <-?= $this->Form->input('sch_name', ['type' => 'hidden', 'value' => $query['sch_name']]); ?>
                      <-?= $this->Form->input('sch_created_year', ['type' => 'hidden', 'value' => $query['sch_created_year']]); ?>
                      <-?= $this->Form->input('sch_created_month', ['type' => 'hidden', 'value' => $query['sch_created_month']]); ?> -->
                      <?= $this->Form->input('sch_modified_year', ['type' => 'select',
                                                             'value' => $query['sch_modified_year'],
                                                             'onChange' => 'change_category("fm_search_modified");',
                                                             'options' => $year_list,
                                                             'empty' => ['0' => '--'],
                                                           ]); ?>
                      年

                      <?php if ($query['sch_modified_year']) : ?>
                      <?= $this->Form->input('sch_modified_month', ['type' => 'select',
                                                             'value' => $query['sch_modified_month'],
                                                             'onChange' => 'change_category("fm_search_modified");',
                                                             'options' => $month_list,
                                                             'empty' => ['0' => '--'],
                                                           ]); ?>
                      月
                      <?php endif; ?>
                      
                    </td>
                  </tr>
                  <?= $this->Form->end(); ?>
              </table>

              <div class="btn_area">
                <a class="btn btn-secondary" href="<?= $this->Url->build(['action' => 'pop-list']); ?>"><i class="fas fa-eraser"></i> クリア</a>
                <!-- <button class="btn btn-primary" onclick="document.fm_search.submit();"><i class="fas fa-search"></i> 検索開始</button> -->
              </div>
<!-- <-?= $this->Form->end(); ?> -->
          </div>
      </div>

      <div class="box">
        <h3 class="box__caption--count"><span>登録一覧</span><span class="count"><?= $this->Paginator->param('count'); ?>件の登録</span></h3>      

        <div class="table_area">
          <?= $this->element('pagination')?>

          <table class="table__list table-hover" style="table-layout: fixed;">
          <colgroup>
            <col style="width: 70px;">
            <col style="width: 60px;">
            <col>
            <col style="width: 70px;">
            <col style="width: 100px;">
            <col style="width: 100px;">
          </colgroup>

            <tr>
              <th >選択</th>
              <th >ID</th>
              <th style="text-align:left;">カテゴリ / 素材名</th>
              <th >タイプ</th>
              <th >登録日時</th>
              <th >更新日時</th>
            </tr>

<?php
foreach ($data_query->toArray() as $key => $data):

$id = $data->id;

?>
            <a name="m_<?= $id ?>"></a>
            <tr class="visible" id="content-<?= $data->id ?>">

              <td class="text-center">
                <div class="btn_area">
                  <a href="#" class="btn btn-danger btn-sm" onClick="parent.pop_box.select('<?= $data->id;?>');">選択</a>
                </div>
              </td>

              <td style="padding: 0;padding-left: 10px;">
                <?= $data->id; ?>
              </td>

              <td style="padding: 0;padding-left: 10px;">
                【<?= h($data->material_category->name); ?>】<br>
                <?= h($data->name); ?>
              </td>

              <td>
                <?= Material::$type_list[$data->type]; ?>
              </td>

              <td style="padding: 0;padding-left: 10px;">
                <?= $data->created; ?>
              </td>

              <td style="padding: 0;padding-left: 10px;">
                <?= $data->modified; ?>
              </td>

            </tr>

<?php endforeach; ?>

          </table>

        </div>


    </div>
</div>
<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/admin/common/css/cms.css">
<script>
function change_category(elm) {
  $("#" + elm).submit();
    
}
$(function () {



})
</script>
<?php $this->end();?>
