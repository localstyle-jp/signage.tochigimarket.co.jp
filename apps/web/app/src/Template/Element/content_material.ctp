<?php use App\Model\Entity\Material;

?>
<tr id="block_no_<?= $rownum; ?>" class="first-dir">
  <td>
    <div class="sort_handle"></div>

  </td>

  <!-- カテゴリ / 素材名 -->
  <td>

    <?php if(VIEW_MCAETGORY): ?>
    <div>
      【<?= h($category_list[$material['material']['category_id']]) ?>】
    </div>
    <?php endif; ?>

    <div>
      <?= h($material['material']['name']) ?>
    </div>
  </td>

  <!-- 種別 -->
  <td>
    <?= Material::$type_list[$material['material']['type']]; ?>
    <?php if (Material::$type_list[$material['material']['type']] == 'mp4') : ?>
    <span
      class='badge <?= $material['material']['status_mp4'] == 'converting' ? 'badge-danger' : 'badge-success';?>'>
      <?= $material['material']['status_mp4'] == 'converting' ? '配信不可' : '配信可';?>
    </span>
    <?php endif; ?>
  </td>

  <!-- 表示秒数 -->
  <td>
    <?php if ($material['material']['type'] == Material::TYPE_MOVIE): ?>
    <?= $this->Form->input("content_materials.{$rownum}.view_second", ['type' => 'text', 'maxlength' => 5, 'value' => $material['view_second'] ?? 0, 'readonly' => false, 'error' => false]); ?>秒
    <?= $this->Form->error("content_materials.{$rownum}.view_second"); ?>
    <?php else: ?>
    <?= $this->Form->input("content_materials.{$rownum}.view_second", ['type' => 'text', 'maxlength' => 5, 'value' => $material['view_second'] ?? 0, 'error' => false]); ?>秒
    <?= $this->Form->error("content_materials.{$rownum}.view_second"); ?>
    <?php endif; ?>

      <?php if ($vmode == 'simple'): ?>
          <?= $this->Form->input("content_materials.{$rownum}.rolling_caption", ['type' => 'hidden','value' => $material['rolling_caption']]); ?>
          <?= $this->Form->input("content_materials.{$rownum}.id", ['type' => 'hidden', 'value' => $material['id']]); ?>
          <?= $this->Form->input("content_materials.{$rownum}.position", ['type' => 'hidden', 'value' => $material['position']]); ?>
          <?= $this->Form->input("content_materials.{$rownum}.material_id", ['type' => 'hidden', 'value' => $material['material_id']]); ?>
          <?= $this->Form->input("content_materials.{$rownum}.is_delete", ['type' => 'hidden', 'value' => 0, 'id' => 'idIsDelete_' . $rownum]); ?>
      <?php endif; ?>

  </td>

  <?php if ($vmode != 'simple'): ?>
  <!-- 内容 -->
  <td>
    <?= $this->Form->input("content_materials.{$rownum}.id", ['type' => 'hidden', 'value' => $material['id']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.position", ['type' => 'hidden', 'value' => $material['position']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.material_id", ['type' => 'hidden', 'value' => $material['material_id']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.is_delete", ['type' => 'hidden', 'value' => 0, 'id' => 'idIsDelete_' . $rownum]); ?>

    <?php if($material['material']['type'] == Material::TYPE_IMAGE):?>
    <div>
      <img
        src="<?= h($material['material']['attaches']['image']['s']) ?>">
    </div>

    <?php elseif($material['material']['type'] == Material::TYPE_MOVIE):?>
    <div>
      <iframe width="150"
        src="https://www.youtube.com/embed/<?= $material['material']['movie_tag']; ?>"
        title="YouTube video player" frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen></iframe>

    </div>

    <?php elseif ($material['material']['type'] == Material::TYPE_MOVIE_MP4): ?>
    <video width="120px;"
      id="mate_mp4_<?= $material['id'] ?>"
      playsinline>
      <source
        src="<?= $material['material']['attaches']['file']['src']; ?>">
    </video>

    <script type="text/javascript">
      document.getElementById(
          'mate_mp4_<?= $material['id'] ?>').currentTime =
        1.0;

      $('#mate_mp4_<?= $material['id'] ?>')
        .mouseover(function() {
          $(this).get(0).setAttribute("controls", "controls");
        }).mouseout(function() {
          $(this).get(0).removeAttribute("controls");
        });
    </script>

    <?php elseif($material['material']['type'] == Material::TYPE_URL):?>
    <div>
      <a
        href="<?= h($material['material']['url']); ?>"><?= h($material['material']['url']); ?></a>
    </div>

    <?php elseif($material['material']['type'] == Material::TYPE_PAGE):?>
    <div>
      <img
        src="<?= h($material['material']['attaches']['image']['s']) ?>">
    </div>
    <div>
      <?= mb_substr(strip_tags($material['material']['content']), 0, 10) ?>...
    </div>
    <?php endif;?>

  </td>

  <!-- 字幕 -->
  <td>
    <!-- <-?= $this->Form->input("content_materials.{$rownum}.rolling_caption",['type' => 'text','maxlength' => 100, 'value' => $material['rolling_caption'], 'readonly' => false]); ?> -->
    <?= $this->Form->input("content_materials.{$rownum}.rolling_caption", ['type' => 'textarea', 'style' => 'height:auto;', 'maxlength' => 1000, 'readonly' => false, 'value' => $material['rolling_caption']]); ?>

    <?php if ($material['material']['type'] == Material::TYPE_IMAGE): ?>
    <?= $this->Form->input("content_materials.{$rownum}.sound", ['type' => 'select', 'options' => $sound_list, 'empty' => ['' => 'BGMなし']]); ?>
    <?php endif; ?>
  </td>
  <?php endif; ?>

  <td>
    <div class='btn_area' style=''>
      <a href="javascript:void(0);" class="btn btn-secondary btn-sm delete_row"
        data-row="<?= h($rownum);?>">削除</a>
    </div>
  </td>
</tr>