    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">管理メニュー</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><a
                  href="<?= $this->Url->build(['_name' => 'shopAdmin']); ?>">Home</a>
              </li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">

      <?= $this->element('error_message'); ?>

      <div class="container-fluid">

        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-gray-dark">
                <h2 class="card-title">コンテンツ</h2>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i
                      class="fas fa-minus"></i></button>
                </div>
              </div>

              <div class="card-body row">
                <div class="col-12 col-md-4 mb-2">
                  <a href="<?= $this->Url->build('/shop_user/materials/'); ?>"
                    class="btn btn-block btn-secondary btn-lg">
                    素材
                    <i class="btn-icon-right fas fa-angle-right"></i>
                  </a>
                </div>
                <!-- <div class="col-12 col-md-4 mb-2">
                  <button type="button" class="btn btn-block btn-secondary btn-lg">素材</button>
                </div>
                <div class="col-12 col-md-4 mb-2">
                  <button type="button" class="btn btn-block btn-secondary btn-lg">素材</button>
                </div> -->
              </div>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->

        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-gray-dark">
                <h2 class="card-title">表示端末</h2>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i
                      class="fas fa-minus"></i></button>
                </div>
              </div>

              <?php if (!empty($machines)): ?>
              <div class=" card-body row">
                <?php foreach ($machines as $key => $data): ?>
                <?php $status = ($data->status == 'publish' ? true : false); ?>
                <div class="col-12 col-md-4 mb-5">
                  <div class="card" style="">
                    <div class="card-header bg-secondary">
                      <!-- <div class="col-12"> -->
                      <h5 class="card-title"><?= h($data->name); ?>
                      </h5>
                      <div class="card-tools">
                        <a href="<?= $this->Url->build(['controller' => 'machine-boxes', 'action' => 'edit', $data->id]); ?>"
                          type="button" class="btn btn-tool" style="color:#FFF;"><i class="fas fa-cog"></i></a>
                      </div>
                      <!-- </div> -->

                    </div>

                    <div class="card-body">
                      <div class="row">
                        <div class="col-12">
                          <div class="btn_area text-center mt-2">
                            <?php if ($data->content_id && $data->content): ?>
                            <?php if (empty($data->machine_content) || $data->content->serial_no != $data->machine_content->serial_no): ?>
                            <a href="javascript:void(0);" class="btn btn-warning btn-sm blinking w-100 btnUpdateContent"
                              style="color:#212529;"
                              data-id="<?= $data->id; ?>"><i
                                class="fas fa-sync-alt"></i> 最新版にする</a>
                            <?= $this->Form->create(false, ['type' => 'get', 'url' => ['controller' => 'machine-boxes', 'action' => 'update-content', $data->id], 'id' => 'fm_update_' . $data->id]); ?>
                            <?= $this->Form->end(); ?>
                            <?php else: ?>
                            <a href="#" class="btn btn-success btn-sm w-100" aria-disabled="true" id="btnLastVersion"><i
                                class="fas fa-sync-alt"></i> 最新版です</a>
                            <?php endif; ?>
                            <?php endif; ?>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="btn_area center">
                            <a href="/shop_user/contents/edit/<?= $data->content_id; ?>?mode=machine"
                              class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> コンテンツ編集</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-footer">
                      <?= nl2br(h($data->memo)); ?>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->

      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->


    <?php $this->start('beforeBodyClose'); ?>
    <script>
      $(function() {
        $(".btnUpdateContent").on('click', function() {
          var id = $(this).data('id');

          alert_dlg(
            '現在のコンテンツを最新版にします。<br><span class="text-danger">自動的に表示端末のブラウザの再読み込みを実行します。</span><br>元に戻すことは出来ません。よろしいですか？', {
              buttons: [{
                  text: 'いいえ',
                  click: function() {
                    $(this).dialog("close");
                  }
                },
                {
                  text: 'はい',
                  click: function() {
                    $("#fm_update_" + id).submit();
                    $(this).dialog("close");
                  }
                }
              ]
            });

          return false;
        });

      });
    </script>
    <?php $this->end(); ?>