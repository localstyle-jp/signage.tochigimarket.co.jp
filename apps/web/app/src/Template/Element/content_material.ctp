<tr id="block_no_" data-sub-block-move="1" class="first-dir">
  <td>
    <div class="sort_handle"></div>
    
  </td>
  <td></td>
  <td>
    <?= $this->Form->input("content_materials.{$rownum}.id", ['type' => 'text', 'value' => '']); ?>
    <?= $this->Form->input("content_materials.{$rownum}.position", ['type' => 'text', 'value' => '']); ?>
    <?= $this->Form->input("content_materials.{$rownum}.material_id", ['type' => 'text', 'value' => '']); ?>

  </td>

  <td>
      <div class='btn_area' style='float: right;'>
        <a href="javascript:void(0);" class="btn_confirm small_btn btn_list_delete size_min" data-row="<?= h($rownum);?>" style='text-align:center; width:auto;'>削除</a>
    </div>
  </td>
</tr>