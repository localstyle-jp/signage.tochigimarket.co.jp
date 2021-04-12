<?php use App\Model\Entity\Material; ?>
<tr id="block_no_<?= $rownum; ?>" class="first-dir">
  <td>
    <div class="sort_handle"></div>
    
  </td>

  <!-- 素材名 -->
  <td><?= h($material['material']['name']) ?></td>

  <!-- 種別 -->
  <td>
    <?= Material::$type_list[$material['material']['type']]; ?>
  </td>

  <!-- 表示秒数 -->
  <td>
  <?php if ($material['material']['type'] == Material::TYPE_MOVIE): ?>
    <?= $this->Form->input("content_materials.{$rownum}.view_second",['type' => 'text','maxlength' => 5, 'value' => $material['view_second'], 'readonly' => true]); ?>秒
  <?php else: ?>
    <?= $this->Form->input("content_materials.{$rownum}.view_second",['type' => 'text','maxlength' => 5, 'value' => $material['view_second']]); ?>秒
  <?php endif; ?>
  </td>

  <!-- 内容 -->
  <td>
    <?= $this->Form->input("content_materials.{$rownum}.id", ['type' => 'hidden', 'value' => $material['id']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.position", ['type' => 'hidden', 'value' => $material['position']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.material_id", ['type' => 'hidden', 'value' => $material['material_id']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.is_delete", ['type' => 'hidden', 'value' => 0, 'id' => 'idIsDelete_' . $rownum]); ?>

    <?php if($material['material']['type'] == Material::TYPE_IMAGE):?>
      <div>
        <img src="<?= h($material['material']['attaches']['image']['s']) ?>">
      </div>
    
    <?php elseif($material['material']['type'] == Material::TYPE_MOVIE):?>
      <div>
        <iframe width="300" src="https://www.youtube.com/embed/<?= $material['material']['movie_tag']; ?>" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        
      </div>

    <?php elseif ($material['material']['type'] == Material::TYPE_MOVIE_MP4): ?>
      <video controls width="300px;">
        <source src="<?= $material['material']['attaches']['file']['src']; ?>">
      </video>
    
    <?php elseif($material['material']['type'] == Material::TYPE_URL):?>
      <div>
        <a href="<?= h($material['material']['url']); ?>"><?= h($material['material']['url']); ?></a>
      </div>
    
    <?php elseif($material['material']['type'] == Material::TYPE_PAGE):?>
      <div>
        <img src="<?= h($material['material']['attaches']['image']['s']) ?>">
      </div>
      <div>
        <?= mb_substr(strip_tags($material['material']['content']), 0, 10) ?>...
      </div>
    <?php endif;?>
    
  </td>
  

  <td>
      <div class='btn_area' style=''>
        <a href="javascript:void(0);" class="btn btn-secondary btn-sm delete_row" data-row="<?= h($rownum);?>">削除</a>
    </div>
  </td>
</tr>