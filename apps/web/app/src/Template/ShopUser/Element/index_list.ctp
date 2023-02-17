<?php use App\Model\Entity\Material;

?>
<div class="table_list_area">
  <table id="index_list" class="table table-sm table-hover table-bordered dataTable dtr-inline"
    style="table-layout: fixed;">
    <colgroup class="show_pc">
      <?php if (false): ?>
      <col style="width: 74px;">
      <?php endif; ?>
      <col style="width: 75px;">
      <col>
      <col style="width: 100px;">
      <col style="width: 100px;">
      <?php if (!$is_search): ?>
      <col style="width: 150px;">
      <?php endif; ?>
    </colgroup>

    <thead class="bg-gray">

      <tr>
        <?php if (false): ?>
        <th>状態</th>
        <?php endif; ?>
        <th class="sorting sorting_asc" tabindex="0" rowspan="1" colspan="1" area-controls="index_list"
          area-sort="ascending" area-label="ID"></th>
        <th style="text-align:left;">
          <?= VIEW_MCAETGORY ? 'カテゴリ / ' : '' ?>素材名
        </th>
        <th>タイプ</th>
        <th>操作</th>
        <?php if (!$is_search): ?>
        <th>並び順</th>
        <?php endif; ?>
      </tr>
    </thead>

    <tbody>
      <?php
foreach ($data_query->toArray() as $key => $data):
    $no = sprintf('%02d', $data->id);
    $id = $data->id;
    $scripturl = '';
    if ($data['status'] === 'publish') {
        $count['enable']++;
        $status = true;
    } else {
        $count['disable']++;
        $status = false;
    }

    $preview_url = '/' . $this->Common->session_read('data.username') . "/{$data->id}?preview=on";
    ?>
      <a name="m_<?= $id ?>"></a>
      <tr
        class="<?= $status ? 'visible' : 'unvisible' ?>"
        id="content-<?= $data->id ?>">

        <?php if (false): ?>
        <td>
          <?= $this->element('status_button', ['status' => $status, 'id' => $data->id]); ?>
        </td>
        <?php endif; ?>

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


        <td data-label="">
          <div class="btn_area center">
            <a href="<?= $this->Url->build(['action' => 'edit', $data->id]); ?>"
              class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 編集</a>
          </div>
        </td>

        <?php if (!$is_search): ?>
        <td data-label="並び順">
          <ul class="ctrlis">
            <?php if(!$this->Paginator->hasPrev() && $key == 0): ?>
            <li class="non">&nbsp;</li>
            <li class="non">&nbsp;</li>
            <?php else: ?>
            <li class="cttop">
              <?= $this->Html->link('top', array('action' => 'position', $data->id, 'top'))?>
            </li>
            <li class="ctup">
              <?= $this->Html->link('top', array('action' => 'position', $data->id, 'up'))?>
            </li>
            <?php endif; ?>

            <?php if(!$this->Paginator->hasNext() && $key == count($datas) - 1): ?>
            <li class="non">&nbsp;</li>
            <li class="non">&nbsp;</li>
            <?php else: ?>
            <li class="ctdown">
              <?= $this->Html->link('top', array('action' => 'position', $data->id, 'down'))?>
            </li>
            <li class="ctend">
              <?= $this->Html->link('bottom', array('action' => 'position', $data->id, 'bottom'))?>
            </li>
            <?php endif; ?>
          </ul>
        </td>
        <?php endif; ?>

      </tr>

      <?php endforeach; ?>
    </tbody>
  </table>

</div>