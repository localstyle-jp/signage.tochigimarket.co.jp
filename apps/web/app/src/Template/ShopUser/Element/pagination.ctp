<?php if ($this->Paginator->hasPrev() || $this->Paginator->hasNext()):?>
<nav>
    <ul class="pagination justify-content-center">
        <?= $this->Paginator->first('<<');?>
        <?php if ($this->Paginator->hasPrev()):?><?= $this->Paginator->prev('<')?><?php endif;?>
        <?= $this->Paginator->numbers();?>
        <?php if ($this->Paginator->hasNext()):?><?= $this->Paginator->next('>')?><?php endif;?>
        <?= $this->Paginator->last('>>');?>
    </ul>
</nav>
<?php endif;?>