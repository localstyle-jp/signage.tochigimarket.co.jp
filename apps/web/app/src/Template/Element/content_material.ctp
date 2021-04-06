<tr id="block_no_" data-sub-block-move="1" class="first-dir">
  <td>
    <div class="sort_handle"></div>
    
  </td>
  <td><?= h($material['name']) ?></td>
  <td>
    <?= $this->Form->input("content_materials.{$rownum}.id", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("content_materials.{$rownum}.position", ['type' => 'hidden', 'value' => '']); ?>
    <?= $this->Form->input("content_materials.{$rownum}.material_id", ['type' => 'hidden', 'value' => $material['id']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.content_id", ['type' => 'hidden', 'value' => '']); ?>
    <div>
    素材名：<?= h($material['name']) ?>
    </div>
    <div>
    素材タイプ：<?= $type_list[h($material['type'])] ?>
    </div>

    <?php if($material['type'] == 1):?>
      <div>
        画像；<img src="<?= h($material['attaches']['image']['s']) ?>">
      </div>
    
    <?php elseif($material['type'] == 2):?>
      <div>
        動画
      </div>
    
    <?php elseif($material['type'] == 3):?>
      <div>
        URL:<a href="<?= h($material['url']) ?>"><?= h($material['url']) ?></a>
      </div>
    
    <?php elseif($material['type'] == 4):?>
      <div>
        画像；<img src="<?= h($material['attaches']['image']['s']) ?>">
      </div>
      <div>
        文章；<?= strip_tags($material['content']) ?>
      </div>
    <?php endif;?>
    
  </td>
  <td>
  表示秒数
  <?= $this->Form->input("content_materials.{$rownum}.view_second",['type' => 'text','maxlength' => 5]) ?>秒
  </td>

  <td>
      <div class='btn_area' style='float: right;'>
        <a href="javascript:void(0);" class="btn_confirm small_btn btn_list_delete size_min" data-row="<?= h($rownum);?>" style='text-align:center; width:auto;'>削除</a>
    </div>
  </td>
</tr>