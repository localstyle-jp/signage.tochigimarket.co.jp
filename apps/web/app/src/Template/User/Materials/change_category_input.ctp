<td class="border-0" colspan="11" id="category_input">
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
</td>