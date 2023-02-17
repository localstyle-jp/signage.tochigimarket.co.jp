<?php use App\Model\Entity\Material;

?>
<tr id="block_no_<?= $rownum; ?>" class="first-dir item_block">
  <!-- カテゴリ / 素材名 -->
  <td class="show_sp">
    <div>#<?= ($rownum + 1); ?>
      <?= h($material['material']['name']) ?>
    </div>
  </td>

  <td class="show_pc">
    <div class="sort_handle"></div>

  </td>

  <!-- カテゴリ / 素材名 -->
  <td class="show_pc">
    <div>
      <?= h($material['material']['name']) ?>
    </div>
  </td>

  <!-- 種別 -->
  <td data-label="種別">
    <?= Material::$type_list[$material['material']['type']]; ?>
    <?php if (Material::$type_list[$material['material']['type']] == 'mp4') : ?>
    <span
      class='badge <?= $material['material']['status_mp4'] == 'converting' ? 'badge-danger' : 'badge-success';?>'>
      <?= $material['material']['status_mp4'] == 'converting' ? '配信不可' : '配信可';?>
    </span>
    <?php endif; ?>
  </td>

  <!-- 表示秒数 -->
  <td data-label="表示秒数（0秒は永続します。15秒以上を指定）">
    <div class="input-group">
      <?= $this->Form->input("content_materials.{$rownum}.view_second", ['type' => 'text', 'maxlength' => 5, 'value' => $material['view_second'] ?? 0, 'error' => false, 'class' => 'form-control text-right', 'style' => 'width:100px;']); ?>
      <span class="input-group-text">秒</span>
      <?= $this->Form->error("content_materials.{$rownum}.view_second"); ?>
    </div>
  </td>

  <!-- 内容 -->
  <td class="text-center">
    <?= $this->Form->input("content_materials.{$rownum}.id", ['type' => 'hidden', 'value' => $material['id']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.position", ['type' => 'hidden', 'value' => $material['position']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.material_id", ['type' => 'hidden', 'value' => $material['material_id']]); ?>
    <?= $this->Form->input("content_materials.{$rownum}.is_delete", ['type' => 'hidden', 'value' => 0, 'id' => 'idIsDelete_' . $rownum]); ?>

    <?php if($material['material']['type'] == Material::TYPE_IMAGE):?>
    <div>
      <img
        src="<?= h($material['material']['attaches']['image']['s']) ?>"
        class="w-100">
    </div>

    <?php elseif($material['material']['type'] == Material::TYPE_MOVIE):?>
    <div>
      <iframe width="300"
        src="https://www.youtube.com/embed/<?= $material['material']['movie_tag']; ?>"
        title="YouTube video player" frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
        allowfullscreen></iframe>

    </div>

    <?php elseif ($material['material']['type'] == Material::TYPE_MOVIE_MP4): ?>
    <video playsinline controls muted width="300px;"
      id="mate_mp4_<?= $material['id'] ?>">
      <source
        src="<?= $material['material']['attaches']['file']['src']; ?>#t=1,2">
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
  <td data-label="字幕">
    <!-- <-?= $this->Form->input("content_materials.{$rownum}.rolling_caption",['type' => 'text','maxlength' => 100, 'value' => $material['rolling_caption'], 'readonly' => false]); ?> -->
    <?= $this->Form->input("content_materials.{$rownum}.rolling_caption", ['type' => 'textarea',
        'style' => 'height:auto;',
        'maxlength' => 1000,
        'readonly' => false,
        'value' => $material['rolling_caption'],
        'class' => 'form-control'
    ]); ?>

    <?php if ($material['material']['type'] == Material::TYPE_IMAGE): ?>
    <div class="input-group">
      <div class="input-group-prepend">
        <span class="input-group-text">BGM</span>
      </div>
      <?= $this->Form->input("content_materials.{$rownum}.sound", ['type' => 'select', 'options' => $sound_list, 'empty' => ['' => 'BGMなし'], 'class' => 'form-control']); ?>
    </div>
    <?php endif; ?>
  </td>

  <td>
    <?php if (!$locked): ?>

    <div class="btn-group">
      <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
        aria-expanded="false" onclick="clickItemConfig(this);"
        data-no="<?= h($rownum); ?>"><i
          class="fas fa-cog"></i></button>
      <div class="dropdown-menu" style="width: 200px;">
        <button type="button" class="dropdown-item btn btn-light up"
          onclick="clickSort(<?= h($rownum); ?>, 'first');"><i
            class="fas fa-angle-double-up"></i> 最上段に移動</button>
        <button type="button" class="dropdown-item btn btn-light up"
          onclick="clickSort(<?= h($rownum); ?>, 'up');"><i
            class="fas fa-angle-up"></i> １つ上に移動</button>
        <div class="dropdown-divider up down"></div>
        <button type="button" class="dropdown-item btn btn-light down"
          onclick="clickSort(<?= h($rownum); ?>, 'down');"><i
            class="fas fa-angle-down"></i> １つ下に移動</button>
        <button type="button" class="dropdown-item btn btn-light down"
          onclick="clickSort(<?= h($rownum); ?>, 'last');"><i
            class="fas fa-angle-double-down"></i> 最下段に移動</button>
        <div class="dropdown-divider"></div>
        <div class="text-center">
          <button type="button" class="btn btn-sm btn-secondary delete_row"
            data-row="<?= h($rownum); ?>"><i
              class="far fa-trash-alt"></i> 削除</button>
        </div>
      </div>
    </div>
    <?php endif; ?>
  </td>
</tr>