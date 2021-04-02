<?php $role_key = $this->Common->getUserRoleKey(); ?>
<?php $menu_list = $this->UserAdmin->getUserMenu($role_key); ?>

<div class="title_area">
  <h1>管理メニュー</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><span>管理メニュー</span></li>
    </ul>
  </div>
</div>

<?= $this->element('error_message'); ?>

<div class="content_inr">
  
  <?php foreach ($menu_list as $title => $menu): ?>
    <div class="box">
        <h3 style="margin-bottom:20px;"><?= $title; ?></h3>
        <?php foreach ($menu as $sub_title=> $m): ?>

        <?php if (!is_numeric($sub_title)): ?>
          <h4 style="padding: 10px;"><?= $sub_title; ?></h4>
        <?php endif; ?>

          <div class="btn_area" style="text-align:left;margin-left: 20px;margin-bottom: 10px !important;">
            <?php foreach ($m as $name => $link): ?>
              <?php if (is_array($link)): ?>
              <a href="<?= $link['link']; ?>" class="btn btn-primary btn" style="width:130px;text-align:center;"><i class="<?= $link['icon'];?>"></i> <?= $name; ?></a>
              <?php else: ?>
              <a href="<?= $link; ?>" class="btn btn-primary btn" style="width:130px;text-align:center;"><?= $name; ?></a>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>

        <?php endforeach; ?>

    </div>
  <?php endforeach; ?>

</div>


