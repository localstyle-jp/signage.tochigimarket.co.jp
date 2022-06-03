    <?php use App\Model\Entity\Material; ?>

    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">素材</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= $this->Url->build(['_name' => 'shopAdmin']); ?>">Home</a></li>
              <li class="breadcrumb-item active"><span>素材</span></li>
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
            <div class="card collapsed-card">
              <div class="card-header bg-gray-dark">
                <h2 class="card-title">検索条件</h2>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse"><i class="fas fa-plus"></i></button>
                </div>
              </div>

              <div class="card-body">
<?= $this->Form->create(false, array('type' => 'get', 'name' => 'fm_search', 'id' => 'fm_search', 'url' => array('action' => 'index'), 'class' => '', 'templates' => $search_templates)); ?>
                <div class="table__search">
                    <ul class="search__row">
                        <li>
                            <div class="search__title">素材カテゴリ</div>
                            <div class="search__column">
                                <?php foreach ($category_list as $n => $clist): ?>
                                <div class="breadcrumb-item" style="display: inline-block;">
                                    <?= $this->Form->input('sch_category_id' . $n, ['type' => 'select',
                                                                                'options' => $clist['list'],
                                                                                'onChange' => "change_category_input($n);",
                                                                                'value' => $clist['category']->id,
                                                                                'empty' => $clist['empty']
                                                                                ]); ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </li>
                    </ul>

                    <ul class="search__row">
                      <li>
                        <div class="search__title">素材所有者</div>
                        <div class="search__column">
                          <div class="">
                          <?= $this->Form->input('sch_user_type', ['type' => 'radio',
                                                                  'options' => $user_type_list,
                                                                  'value' => $query['sch_user_type']
                                                        ]); ?>
                          </div>
                        </div>
                      </li>
                    </ul>
                    <ul class="search__row">
                        <li>
                          <div class="search__title">素材名</div>
                          <div class="search__column">
                            <?= $this->Form->input('sch_name', ['type' => 'text',
                                                              'value' => $query['sch_name'],
                                                              'class' => 'w-100',
                                                              'placeholder' => 'あいまい検索',
                                                              'max-length' => 40
                                                            ]); ?>
                          </div>
                        </li>

                        <li>
                          <div class="search__title">タイプ</div>
                          <div class="search__column">
                            <?= $this->Form->input('sch_type', ['type' => 'select',
                                                             'value' => $query['sch_type'],
                                                             'options' => $type_list,
                                                             'empty' => ['0' => '全て']
                                                           ]); ?>
                          </div>
                        </li>
                    </ul>

                    <ul class="search__row">
                      <li>
                        <div class="search__title">登録日時</div>
                        <div class="search__column">
                          <?= $this->Form->input('sch_created_start', array(
                          'type' => 'text', 
                          'class' => 'datepicker',
                          'value' => $query['sch_created_start'], 
                          'style' => 'width: 120px;'));?> 
                        ～ 
                        <?= $this->Form->input('sch_created_end', array(
                          'type' => 'text', 
                          'class' => 'datepicker',
                          'value' => $query['sch_created_end'],  
                          'style' => 'width: 120px;'));?>
                        </div>
                      </li>

                      <li>
                        <div class="search__title">更新日時</div>
                        <div class="search__column">
                          <?= $this->Form->input('sch_modified_start', array(
                          'type' => 'text', 
                          'class' => 'datepicker',
                          'value' => $query['sch_modified_start'],  
                          'style' => 'width: 120px;'));?> 
                        ～ 
                        <?= $this->Form->input('sch_modified_end', array(
                          'type' => 'text', 
                          'class' => 'datepicker',
                          'value' => $query['sch_modified_end'],  
                          'style' => 'width: 120px;'));?>
                        </div>
                      </li>
                    </ul>
                </div>

                <div class="btn_area center">
                  <a class="btn btn-secondary" href="<?= $this->Url->build(['action' => 'index']); ?>"><i class="fas fa-eraser"></i> クリア</a>
                  <button class="btn btn-primary" onclick="document.fm_search.submit();"><i class="fas fa-search"></i> 検索開始</button>
                </div>
<?= $this->Form->end(); ?>
              </div>

            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->


<?php
//データの位置まで走査
$count = array('total' => 0,
               'enable' => 0,
               'disable' => 0);
// $count['total'] = $data_query->count();
$count['total'] = $numrows;
?>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-gray-dark">
                <h2 class="card-title">登録一覧　<span class="count"><?php echo $count['total']; ?>件の登録</span></h2>

              </div>

              <div class=" card-body">
                <div class="btn_area center">
                  <a href="<?= $this->Url->build(array('action' => 'edit', '?' => ['sch_type' => $query['sch_type'], 'sch_category_id' => $query['sch_category_id']])); ?>" class="btn btn_post w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a>
                </div>
                
                <div style="" class="mb-2 mt-2 text-center"><?= $this->element('pagination');?></div>

                <?= $this->element('index_list', ['count' => $count]); ?>

                <div style="" class="mb-2 mt-2 text-center"><?= $this->element('pagination');?></div>

                <div class="btn_area center">
                  <a href="<?= $this->Url->build(array('action' => 'edit', '?' => ['sch_type' => $query['sch_type'], 'sch_category_id' => $query['sch_category_id']])); ?>" class="btn btn_post w-20 rounded-pill"><i class="far fa-plus-square"></i> 新規登録</a>
                </div>
              </div>

            </div>
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->

      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

<?php $this->start('beforeBodyClose'); ?>
<!-- dataTables -->
<!-- <script src="/shop_user/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/shop_user/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/shop_user/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/shop_user/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script> -->
<script>
  $(function () {

    // $('.table_list_area table').DataTable({
    //   "paging": true,
    //   "lengthChange": false,
    //   "searching": false,
    //   "ordering": false,
    //   "info": true,
    //   "autoWidth": false,
    //   "responsive": true,
    // });
  });
</script>

<?php $this->end(); ?>