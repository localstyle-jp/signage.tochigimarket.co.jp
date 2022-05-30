<?php $enabled_class = 'btn btn-light border w-25px'; ?>
<?php $disabled_class = 'bg-light border-0 w-25px'; ?>
<div class="btn-toolbar">
    <span class="mr-1 p-1">並び順</span>
    <div class="btn-group btn-group-sm mr-1">
    <?php if(!$this->Paginator->hasPrev() && $key == 0): ?>
        <span class="<?= $disabled_class; ?>">　　</span>
        <span class="<?= $disabled_class; ?>">　　</span>
    <?php else: ?>
        <a href="<?= $this->Url->build(['action' => 'position', $id, 'top']); ?>" data-toggle="tooltip"  title="最初に移動" class="<?= $enabled_class; ?>">
            <i class="fas fa-angle-double-left"></i>
        </a>

        <a href="<?= $this->Url->build(['action' => 'position', $id, 'up']); ?>" data-toggle="tooltip"  title="１つ前に移動" class="<?= $enabled_class; ?>">
            <i class="fas fa-angle-left"></i>
        </a>
    <?php endif; ?>
  </div>
  
  <div class="btn-group btn-group-sm">

    <?php if(!$this->Paginator->hasNext() && $key == $count): ?>
        <span class="<?= $disabled_class; ?>">　　</span>
        <span class="<?= $disabled_class; ?>">　　</span>
    <?php else: ?>
        <a href="<?= $this->Url->build(['action' => 'position', $id, 'down']); ?>" data-toggle="tooltip"  title="１つ後に移動" class="<?= $enabled_class; ?>">
            <i class="fas fa-angle-right"></i>
        </a>

        <a href="<?= $this->Url->build(['action' => 'position', $id, 'bottom']); ?>" data-toggle="tooltip"  title="最後に移動" class="<?= $enabled_class; ?>">
            <i class="fas fa-angle-double-right"></i>
        </a>
    <?php endif; ?>
    </div>
</div>
