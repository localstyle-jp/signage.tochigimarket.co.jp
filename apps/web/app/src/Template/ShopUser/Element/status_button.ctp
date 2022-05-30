<a role="button" type="button" class="btn w-100 text-light btn-sm btn-<?= ($status ? 'success' : 'danger'); ?> text-decoration-none" href="<?= $this->Url->build(['action' => 'enable', $id]); ?>">
  <?= ($status ? '有効' : '無効'); ?>
</a>