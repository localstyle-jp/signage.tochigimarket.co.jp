<div id="side">
  <h1><a href="/admin/">HOMEPAGE<br>MANAGER</a><br/><span class="version">ver5.0</span></h1>
  <nav>
    <ul class="menu scrollbar">
    <li>
      <span class="parent_link">お知らせ</span>
      <ul class="submenu">
        <li><a href="/admin/infos/edit/0">新規登録</a></li>
        <li><a href="/admin/infos/">お知らせ一覧</a></li>
    </ul>
    </li>
     <li>
     	<span class="parent_link">メニュー１</span>
     	<ul class="submenu">
        <li><a href="#">メニュー1-1</a></li>
     		<li><a href="#">メニュー1-2</a></li>
		</ul>
    </li>
    <li>
      <span class="parent_link">メニュー2</span>
      <ul class="submenu">
        <li><a href="#">メニュー2-1</a></li>
        <li><a href="#">メニュー2-2</a></li>
    </ul>
    </li>
    <li>
      <span class="parent_link">管理者メニュー</span>
      <ul class="submenu">
        <a href="<?= $this->Url->build(array('admin' => true,'controller' => 'mst_literal', 'action' => 'index')); ?>" class="btn_send btn_search" style="width:130px;text-align:center;">定数管理</a>
    </ul>
    </li>

    </ul>
  </nav>
</div>
