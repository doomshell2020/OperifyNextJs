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

  .pagination li {
    list-style: none;
  }

  .pagination li a, .pagination li span {
    background: black;
    color: white;
    padding: 5px 10px;
    margin: 3px;
    margin-top: 17px;
    display: inline-block;
    white-space: nowrap;
    text-decoration: none;
  }

  .pagination li.active a, .pagination li.active span {
    background: white;
    color: black;
    border: 1px solid black;
  }
</style>