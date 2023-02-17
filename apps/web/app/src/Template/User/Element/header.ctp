<header>
  <span class="ml-5" style="display: inline-block;">
    <h1 class="h2" style="line-height: 3.5rem"><a href="/" class="text-white" style="text-decoration: none;">管理システム</a>
    </h1>
  </span>
  <div class="status">
    <span
      class="link_logout btn text-white">サーバー空き容量:<?= $this->Html->free_space(); ?></span>
    <!-- <a href="/" target="_blank"><i class="glyphs-pc"></i><span class="link_logout btn text-white"><i class="far fa-window-restore"></i> サイト表示</span></a> -->
    <a href="<?= $this->Url->build(['_name' => 'logout']); ?>"
      class="logout"><i class="glyphs-logout"></i><span class="link_logout btn text-white"><i
          class="fas fa-sign-out-alt"></i> ログアウト</span></a>
  </div>
</header>