<div class="paginator col-sm-12" align="right">
  <div class="row">

    <div class="col-sm-6">
      <p align="left">
        <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?>
      </p>
    </div>

    <div class="col-sm-6" style="text-align:right !important;">
      <ul class="pagination" style="display: flex;
    justify-content: end;">
        <?= $this->Paginator->first('<< ' . __('First')) ?>
        <?= $this->Paginator->prev('< ' . __('Previous')) ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next(__('Next') . ' >') ?>
        <?= $this->Paginator->last(__('Last') . ' >>') ?>
      </ul>
    </div>


  </div>
</div>


<style type="text/css">
  .pagination {
    margin: 10px 0 2px;
  }

  p {
    margin: 10px 0 2px;
  }

  .pagination li a {
    background: black;
    color: white;
    padding: 8px;
    margin: 3px;
    margin-top: 17px;
  }
</style>