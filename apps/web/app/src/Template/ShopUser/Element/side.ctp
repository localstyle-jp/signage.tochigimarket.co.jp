<?php $role_key = $this->Common->getUserRoleKey(); ?>
<?php $menu_list = $this->Common->getAdminMenu(); ?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="<?= $this->Url->build(['_name' => 'shopAdmin']); ?>"
    class="brand-link" style="background-color:#FFF;height: 60px;">
    <p class="user__title"><img src="/user/common/images/theme/title_cms.png" alt="CATERS CMS" class="brand-image"></p>
    <!-- <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span> -->
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <?php if ($this->Session->read('data.face_image')): ?>
        <img
          src="<?= $this->Session->read('data.face_image'); ?>"
          class="img-circle elevation-2" alt="User Image">
        <?php else: ?>
        <img src="/shop_user/dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
        <?php endif; ?>
      </div>
      <div class="info">
        <a href="#"
          class="d-block"><?= $this->Session->read('data.name'); ?></a>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <!-- <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div> -->

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

        <?php foreach ($menu_list['side'] as $m): ?>
        <?php $role_only = false; ?>
        <?php if (!empty($m['role']) && !empty($m['role']['role_only'])) {
            $role_key = $m['role']['role_only'];
        } ?>

        <?php if (empty($m['role']) || empty($m['role']['role_type']) ||
          (!empty($m['role']) && !empty($m['role']['role_type']) && $this->Common->isUserRole($m['role']['role_type'], $role_only))): ?>
        <li class="nav-header">
          <?= $m['title']; ?>
        </li>

        <?php foreach($m['buttons'] as $sub): ?>
        <!-- <li class="nav-item menu-open">
            <a href="#" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Starter Pages
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="#" class="nav-link active">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Active Page</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Inactive Page</p>
                </a>
              </li>
            </ul>
          </li> -->
        <li class="nav-item">
          <?php
          $current_path = $this->Url->build();
            $side_menu_class = '';
            $pattern = '^' . $sub['link'] . '$|^' . $sub['link'] . '\/';
            $pattern = str_replace('?', "\?", $pattern);
            ?>
          <?php if (preg_match('{' . $pattern . '}', $current_path)): $side_menu_class = 'active'; ?>
          <?php endif; ?>
          <a href="<?= $sub['link']; ?>"
            class="nav-link <?= $side_menu_class; ?>">
            <?php if (!empty($sub['icon'])): ?>
            <i class="<?= $sub['icon'];?>"></i>
            <?php endif; ?>
            <p>
              <?= $sub['name']; ?>
              <!-- <span class="right badge badge-danger">New</span> -->
            </p>
            <i class="btn-icon-right fas fa-angle-right"></i>
          </a>
        </li>
        <?php endforeach; endif;  ?>
        <?php endforeach; ?>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>