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
                  <tr>
                    <td class="border-0" style="width: 120px;text-align: center;">素材名</td>
                    <td class="border-0">
                      <?= $this->Form->input('sch_name', ['type' => 'text',
                                                             'value' => $query['sch_name'],
                                                             'class' => 'w-100',
                                                             'placeholder' => 'あいまい検索'
                                                           ]); ?>
                    </td>

                    <td class="border-0 head" style="width: 120px;text-align: center;">タイプ</td>
                    <td class="border-0">
                      <?= $this->Form->input('sch_type', ['type' => 'select',
                                                          'options' => $type_list,
                                                          'empty' => ['0' => '全て'],
                                                          'value' => $query['sch_type']
                                                        ]); ?>
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
            <col style="width: 70px;">
            <col>
            <col style="width: 100px;">
          </colgroup>

            <tr>
              <th >選択</th>
              <th >ID</th>
              <th style="text-align:left;">素材名</th>
              <th >タイプ</th>
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
                <?= $data->name; ?>
              </td>

              <td>
                <?= Material::$type_list[$data->type]; ?>
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
function change_category() {
  $("#fm_search").submit();
    
}
$(function () {



})
</script>
<?php $this->end();?>
