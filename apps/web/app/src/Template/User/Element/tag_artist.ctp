<li id="tag_artist_id_<?= $tag['id'];?>" class="badge badge-light border border-info" style="font-size:1.2rem;">
  <span class="tag_name"><?= $tag['name']; ?></span>
  <span><a href="javascript:void(0);" class="delete_tag" data-id="<?= $tag['id'];?>"><i class="fas fa-times"></i></a></span>
  <?= $this->Form->input("sch_artists.{$num}.id", ['type' => 'hidden', 'value' => $tag['id']]); ?>
  <?= $this->Form->input("sch_artists.{$num}.name", ['type' => 'hidden', 'value' => $tag['name']]); ?>
</li>