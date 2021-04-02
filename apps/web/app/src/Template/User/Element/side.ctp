<?php $role_key = $this->Common->getUserRoleKey(); ?>
<?php $menu_list = $this->UserAdmin->getUserMenu('side_' . $role_key); ?>
<div id="side">

  <nav>
    <ul class="menu scrollbar">

  <?php foreach ($menu_list as $name => $sub): ?>
    <li>
      <span class="parent_link"><?= $name; ?></span>
      <ul class="submenu">
      <?php foreach($sub as $sub_name => $link): ?>
        <li><a href="<?= $link; ?>"><?= $sub_name; ?></a></li>
      <?php endforeach; ?>
    </ul>
    </li>
  <?php endforeach; ?>
    </ul>
  </nav>
</div>
