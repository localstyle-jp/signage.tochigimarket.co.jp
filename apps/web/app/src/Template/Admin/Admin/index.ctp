<?php $menu_list = $this->UserAdmin->getAdminMenu('main'); ?>

<div class="title_area">
  <h1>メインメニュー</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><span>メインメニュー</span></li>
    </ul>
  </div>
</div>

<?= $this->element('error_message'); ?>
<?php if(true): ?>
<div class="content_inr">

  <?php foreach ($menu_list as $title => $menu): ?>
    <div class="box">
        <h3 style="margin-bottom:20px;"><?= $title; ?></h3>
        <?php foreach ($menu as $m): ?>
        <div class="btn_area" style="text-align:left;margin-left: 20px;margin-bottom: 10px;">
          <?php foreach ($m as $name => $link): ?>
            <a href="<?= $link; ?>" class="btn_send btn_search" style="width:130px;text-align:center;"><?= $name; ?></a>
          <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
  <?php endforeach; ?>

</div>
<?php endif; ?>

