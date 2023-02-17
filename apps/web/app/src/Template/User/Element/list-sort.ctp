<ul class="ctrlis">
  <?php if(!$this->Paginator->hasPrev() && $key == 0): ?>
  <li class="non">&nbsp;</li>
  <li class="non">&nbsp;</li>
  <?php else: ?>
  <li class="cttop">
    <?= $this->Html->link('top', array('action' => 'position', $id, 'top'))?>
  </li>
  <li class="ctup">
    <?= $this->Html->link('top', array('action' => 'position', $id, 'up'))?>
  </li>
  <?php endif; ?>

  <?php if(!$this->Paginator->hasNext() && $key == $count): ?>
  <li class="non">&nbsp;</li>
  <li class="non">&nbsp;</li>
  <?php else: ?>
  <li class="ctdown">
    <?= $this->Html->link('top', array('action' => 'position', $id, 'down'))?>
  </li>
  <li class="ctend">
    <?= $this->Html->link('bottom', array('action' => 'position', $id, 'bottom'))?>
  </li>
  <?php endif; ?>
</ul>