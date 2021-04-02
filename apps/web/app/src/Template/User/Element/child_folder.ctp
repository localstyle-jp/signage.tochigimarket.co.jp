<ul style="margin-bottom: 5px;" id="child_folder_<?= ($k + 1); ?>">
  <li>
    <?= $this->Form->input("child_folders.{$k}.id", ['type' => 'hidden', 'value' => $child->id]); ?>
    <?= ($k + 1); ?>.<?= $this->Form->input("child_folders.{$k}.name", ['type' => 'text', 'value' => $child->name, 'style' => 'width:300px;']); ?>
  </li>
  
  <li>
    <?= $this->Form->input("child_folders.{$k}.status", ['type' => 'select', 'options' => ['publish' => '有効', 'draft' => '無効'], 'value' => $child->status]); ?>
  </li>

  <li>
    <span class="btn_area">
        <a href="#" class="btn_confirm small_btn btn_list_delete size_min" data-id="<?= $child->id; ?>" data-row="<?= ($k + 1); ?>">削除</a>
    </span>    
  </li>
</ul>