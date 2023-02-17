<div class="title_area">
  <h1>CMS利用者管理</h1>
  <div class="pankuzu">
    <ul>
      <?= $this->element('pankuzu_home'); ?>
      <li><span>ユーザー一覧 </span></li>
    </ul>
  </div>
</div>

<?php
//データの位置まで走査
$count = array('total' => 0,
    'enable' => 0,
    'disable' => 0);
      $count['total'] = $data_query->count();
      ?>

<?= $this->element('error_message'); ?>

<div class="content_inr">

  <div class="box">
    <h3>検索条件</h3>
    <div class="table_area form_area">
      <?= $this->Form->create(false, array('type' => 'get', 'id' => 'fm_search', 'url' => array('action' => 'index'))); ?>
      <table class="vertical_table " style="table-layout: fixed;">
        <colgroup>
          <col style="width:120px;">
          <col style="width:200px;">
          <col style="width:120px;">
          <col style="width:200px;">
          <col style="width:120px;">
          <col>
        </colgroup>
        <tr>
          <td class="head">所属</td>
          <td>
            <?= $this->Form->input('sch_company_id', ['type' => 'select',
                'options' => $company_list,
                'onChange' => 'changeQuery();',
                'value' => $sch_company_id,
                'empty' => ['' => '全て']
            ]); ?>
          </td>

          <td class="head">権限</td>
          <td>
            <?= $this->Form->input('sch_role', ['type' => 'select',
                'options' => $role_list,
                'value' => $sch_role,
                'empty' => ['' => '全て'],
                'onChange' => 'changeQuery();',
            ]); ?>
          </td>

          <td class="head">状態</td>
          <td>
            <?= $this->Form->input('sch_status', ['type' => 'select',
                'options' => $status_list,
                'value' => $sch_status,
                'empty' => ['' => '全て'],
                'onChange' => 'changeQuery();',
            ]); ?>
          </td>
        </tr>
      </table>
      <?= $this->Form->end(); ?>
    </div>
  </div>

  <div class="box">
    <h3 class="box__caption--count"><span>登録一覧</span><span
        class="count"><?php echo $numrows; ?>件の登録</span></h3>
    <div class="btn_area" style="margin-top:10px;">
      <a href="<?= $this->Url->build(array('action' => 'edit', '?' => [])); ?>"
        class="btn_confirm btn_post">新規登録</a>
      <a href="<?= $this->Url->build(array('action' => 'import', '?' => [])); ?>"
        class="btn_confirm btn_post">CSVファイル一括登録</a>
    </div>

    <?= $this->element('pagination')?>

    <div class="table_area">
      <table class="table__list">
        <colgroup>
          <col style="width: 250px;">
          <col style="width: 250px">
          <col style="width: 150px">
          <col style="width: 76px;">
          <col>
        </colgroup>

        <tr>
          <th>氏名</th>
          <th>所属</th>
          <th>権限</th>
          <th>状態</th>
          <th>ユーザーID</th>

        </tr>

        <?php
foreach ($data_query->toArray() as $key => $data):
    $no = sprintf('%02d', $data->id);
    $id = $data->id;
    $scripturl = '';
    if ($data['status'] === 'publish') {
        $count['enable']++;
        $status = true;
    } else {
        $count['disable']++;
        $status = false;
    }

    ?>
        <a name="m_<?= $id ?>"></a>
        <tr
          class="<?= $status ? 'visible' : 'unvisible' ?>"
          id="content-<?= $data->id ?>">

          <td>
            <?= $this->Html->link($data->name, ['action' => 'edit', $data->id, '?' => $query, 'escape' => false])?>
          </td>

          <td>
            <?= (array_key_exists((int)$data->company_id, $company_list) ? $company_list[$data->company_id] : ''); ?>
          </td>

          <td>
            <?= (array_key_exists((int)$data->role, $role_list) ? $role_list[$data->role] : ''); ?>
          </td>

          <td style="text-align: center;">
            <div
              class="<?= $status ? 'visi' : 'unvisi' ?>">
              <?= $this->Html->link(($status ? '有効' : '無効'), array('action' => 'enable', $data->id, '?' => $query))?>
            </div>
          </td>

          <td>
            <?= $data->username; ?>
          </td>

        </tr>

        <?php endforeach; ?>

      </table>

    </div>

    <div class="btn_area" style="margin-top:10px;"><a
        href="<?= $this->Url->build(array('action' => 'edit', '?' => [])); ?>"
        class="btn_confirm btn_post">新規登録</a></div>

    <?= $this->element('pagination')?>
  </div>
</div>
<?php $this->start('beforeBodyClose');?>
<link rel="stylesheet" href="/admin/common/css/cms.css">
<script>
  function changeQuery() {
    $("#fm_search").submit();

  }
  $(function() {



  })
</script>
<?php $this->end();?>