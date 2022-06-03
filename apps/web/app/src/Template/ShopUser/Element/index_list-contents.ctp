<?php use App\Model\Entity\Material; ?>
<div class="table_list_area">
  <table id="index_list" class="table table-sm table-hover table-bordered dataTable dtr-inline" style="table-layout: fixed;">
    <colgroup class="show_pc">
      <col style="width: 75px;">
      <col>
      <col style="width: 100px;">
      <col style="width: 130px;">
      <col style="width: 100px;">
    </colgroup>

    <thead class="bg-gray">

      <tr>
        <th>#</th>
        <th style="text-align:left;">コンテンツ名</th>
        <th>Serial No.</th>
        <th>更新日時</th>
        <th>操作</th>
      </tr>
    </thead>

    <tbody>
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
                #<?= h($data->id); ?>
                <?php if (empty($data->user_id)): ?><br class="show_pc">
                <span class="badge badge-warning">共有</span>
                <?php endif; ?>
              </td>

              <td data-label="コンテンツ名">
                <div class="val_block">
                  <?= $this->Html->link(h($data->name), ['action' => 'edit', $data->id, '?' => $query], ['class' => 'btn btn-light w-100 text-left'])?>
                </div>
              </td>

              <td data-label="Serial No.">
                <div class="val_block">
                  <?= h($data->serial_no);?>
                </div>
              </td>

              <td data-label="更新日時">
                <div class="val_block">
                  <?= $data->modified->format('Y/m/d H:i'); ?>
                </div>
              </td>


              <td data-label="">
                <div class="btn_area center">
                  <a href="<?= $this->Url->build(['action' => 'edit', $data->id]); ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> 編集</a>
                </div>
              </td>


            </tr>

<?php endforeach; ?>
    </tbody>
  </table>

</div>