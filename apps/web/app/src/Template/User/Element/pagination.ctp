<?php if ($this->Paginator->hasPrev() || $this->Paginator->hasNext()):?>
<div class="text-center mb-2">
    <div class="btn-toolbar" style="display: inline-block;">
        <div class="btn-group mr-2">
            <?= $this->Paginator->first('<<');?>
            <?php if ($this->Paginator->hasPrev()):?><?= $this->Paginator->prev('<')?><?php endif;?>
        </div>
        <div class="btn-group mr-2">
            <?= $this->Paginator->numbers();?>
        </div>

        <div class="btn-group">
            <?php if ($this->Paginator->hasNext()):?><?= $this->Paginator->next('>')?><?php endif;?>
            <?= $this->Paginator->last('>>');?>
        </div>
    </div>
</div>
<?php endif;?>