<?php use App\Model\Entity\Material; ?>
<div class="title_area">
  <h1>素材リスト</h1>
</div>

    <?= $this->element('error_message'); ?>
    
    <div class="content_inr">

      <div class="box">
          <h3>検索条件</h3>
          <div class="table_area form_area">
<?= $this->Form->create(false, array('type' => 'get', 'name' => 'fm_search', 'id' => 'fm_search', 'class' => '')); ?>
              <table class=" table border-0">
                <!-- 素材カテゴリ -->
                  <tr>
                    <td class="border-0" style="white-space: nowrap; text-align: center; vertical-align: middle;">素材カテゴリ</td>
                    <td class="border-0" colspan="11" id="category_input">
                    <?php foreach ($category_list as $n => $clist): ?>
                      <div class="breadcrumb-item" style="display: inline-block;">
                        <?= $this->Form->input('sch_category_id' . $n, ['type' => 'select',
                                                                      'options' => $clist['list'],
                                                                      'onChange' => "change_category_input($n);",
                                                                      'value' => $clist['category']->id,
                                                                      'empty' => $clist['empty']
                                                                    ]); ?>
                      </div>
                    <?php endforeach; ?>
                    </td>
                  </tr>
                
                  <tr>
                    <!-- 素材名 -->
                    <td class="border-0" style="white-space: nowrap; text-align: center; vertical-align: middle;">素材名</td>
                    <td class="border-0">
                      <?= $this->Form->input('sch_name', ['type' => 'text',
                                                             'value' => $query['sch_name'],
                                                             'class' => 'w-100',
                                                             'placeholder' => 'あいまい検索',
                                                             'max-length' => 40
                                                           ]); ?>
                    </td>
                    <!-- 素材タイプ -->
                    <td class="border-0 head" style="white-space: nowrap; text-align: center; vertical-align: middle;">タイプ</td>
                    <td class="border-0">
                      <?= $this->Form->input('sch_type', ['type' => 'select',
                                                          'options' => $type_list,
                                                          'empty' => ['0' => '全て'],
                                                          'value' => $query['sch_type']
                                                        ]); ?>
                    </td>
                  </tr>
                  <!-- 登録日時 -->
                  <tr>
                    <td class="border-0" style="white-space: nowrap; text-align: center; vertical-align: middle;">登録日時</td>
                    <td class="border-0" colspan="3">
                      <?= $this->Form->input('sch_created_start', array(
                        'type' => 'text', 
                        'class' => 'datepicker',
                        'value' => $query['sch_created_start'], 
                        'style' => 'width: 120px;'));?> 
                      　～　 
                      <?= $this->Form->input('sch_created_end', array(
                        'type' => 'text', 
                        'class' => 'datepicker',
                        'value' => $query['sch_created_end'],  
                        'style' => 'width: 120px;'));?>
                    </td>
                  </tr>
                  <!-- 更新日時 -->
                  <tr>
                    <td class="border-0" style="white-space: nowrap; text-align: center; vertical-align: middle;">更新日時</td>
                    <td class="border-0" colspan="3">
                      <?= $this->Form->input('sch_modified_start', array(
                        'type' => 'text', 
                        'class' => 'datepicker',
                        'value' => $query['sch_modified_start'],  
                        'style' => 'width: 120px;'));?> 
                      　～　 
                      <?= $this->Form->input('sch_modified_end', array(
                        'type' => 'text', 
                        'class' => 'datepicker',
                        'value' => $query['sch_modified_end'],  
                        'style' => 'width: 120px;'));?>                      
                    </td>
                  </tr>
                  
              </table>

              <div class="btn_area">
                <a class="btn btn-secondary" href="<?= $this->Url->build(['action' => 'pop-list']); ?>"><i class="fas fa-eraser"></i> クリア</a>
                <button class="btn btn-primary" onclick="document.fm_search.submit();"><i class="fas fa-search"></i> 検索開始</button>
              </div>
<?= $this->Form->end(); ?>
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
                <?php if (Material::$type_list[$data->type] == 'mp4') : ?>
                <span class='badge <?= $data->status_mp4 == 'converting' ? 'badge-danger' : 'badge-success' ;?>'>
                  <?= $data->status_mp4 == 'converting' ? '配信不可' : '配信可' ;?>
                </span>
                <?php endif; ?>
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
<script src="/user/common/js/jquery.ui.datepicker-ja.js"></script>
<?= $this->Html->script('/user/common/js/materials/index') ?>
<script>
// function change_category(elm) {
//   $("#" + elm).submit();
    
// }

function change_category_input(layer) {
    var category_id = $('[name="sch_category_id'+layer+'"] option:selected').val();
    var url = '/materials/change-category-input';
    var params = {
        'category_id': category_id
    };

    // if (max_layer >= 9) {
    //     $("#xxxxxxxx").html('<p id="add-txt">階層表示は10個までです。</p>');
    //     return;
    // }
    ajax_get(url, 'POST', params, function(a) {
        $("#category_input").replaceWith($(a));
    }, false);
}
$(function () {



})
</script>
<?php $this->end();?>
