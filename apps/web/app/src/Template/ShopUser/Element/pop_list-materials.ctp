<?php use App\Model\Entity\Material;

?>
<div class="table_list_area">
  <table id="index_list" class="table table-sm table-hover table-bordered dataTable dtr-inline"
    style="table-layout: fixed;">
    <colgroup class="show_pc">
      <col style="width: 70px;">
      <col style="width: 60px;">
      <col>
      <col style="width: 70px;">
      <col style="width: 100px;">
      <col style="width: 100px;">
    </colgroup>

    <thead class="bg-gray">

      <tr>
        <th>選択</th>
        <th>ID</th>

        <th style="text-align:left;">
          <?= VIEW_MCAETGORY ? 'カテゴリ / ' : '' ?>素材名
        </th>
        <th>タイプ</th>
      </tr>

    </thead>

    <tbody>
      <?php
foreach ($data_query->toArray() as $key => $data):

    $id = $data->id;

    ?>
      <a name="m_<?= $id ?>"></a>
      <tr class="visible" id="content-<?= $data->id ?>">

        <td>
          <div class="btn_area center">
            <a href="#" class="btn btn-danger w-50 btn-sm"
              onClick="parent.pop_box.select('<?= $data->id;?>');">選択</a>
          </div>
        </td>

        <td>
          <?= h($data->id); ?>
          <?php if (empty($data->user_id)): ?><br class="show_pc">
          <span class="badge bg-warning">共有</span>
          <?php endif; ?>
        </td>

        <td
          data-label="<?= VIEW_MCAETGORY ? 'カテゴリ／' : '' ?>素材名">
          <?php if(VIEW_MCAETGORY): ?>
          【<?= h($data->material_category->name ?? ''); ?>】
          <?php endif; ?>

          <div class="val_block">
            <?= $this->Html->link(h($data->name), ['action' => 'edit', $data->id, '?' => ['sch_type' => $query['sch_type'], 'sch_category_id' => $query['sch_category_id']]], ['class' => 'btn btn-light w-100 text-left'])?>
          </div>
        </td>

        <td data-label="タイプ">
          <div class="val_block">
            <?= Material::$type_list[$data->type]; ?>
            <?php if (Material::$type_list[$data->type] == 'mp4') : ?>
            <span
              class='badge <?= $data->status_mp4 == 'converting' ? 'badge-danger' : 'badge-success';?>'>
              <?= $data->status_mp4 == 'converting' ? '配信不可' : '配信可';?>
            </span>
            <?php endif; ?>
          </div>
        </td>



      </tr>

      <?php endforeach; ?>
    </tbody>
  </table>

</div>