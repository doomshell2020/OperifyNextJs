<div class="paginator col-sm-12" align="right">
    <ul class="pagination">
        <?php
        // Extract current query parameters
        $queryParams = $this->request->query;
        $queryParams['_method'] = 'POST'; // Add _method parameter

        if ($paging['prev']):
            // Add the page parameter to the query parameters for pagination
            $queryParams['page'] = 1;
            ?>
            <li><?= $this->Html->link('<< First', ['action' => $this->request->params['action'], '?' => $queryParams]) ?></li>
            <?php
            $queryParams['page'] = $paging['prev'];
            ?>
            <li><?= $this->Html->link('< Previous', ['action' => $this->request->params['action'], '?' => $queryParams]) ?></li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $paging['pages']; $i++): 
            $queryParams['page'] = $i;
            ?>
            <li><?= $this->Html->link($i, ['action' => $this->request->params['action'], '?' => $queryParams]) ?></li>
        <?php endfor; ?>

        <?php if ($paging['next']):
            $queryParams['page'] = $paging['next'];
            ?>
            <li><?= $this->Html->link('Next >', ['action' => $this->request->params['action'], '?' => $queryParams]) ?></li>
            <?php
            $queryParams['page'] = $paging['pages'];
            ?>
            <li><?= $this->Html->link('Last >>', ['action' => $this->request->params['action'], '?' => $queryParams]) ?></li>
        <?php endif; ?>
    </ul>
    <div class="col-sm-6" style="margin-left:-29px;">
        <p align="right">
            <?= "Page {$paging['page']} of {$paging['pages']}, showing {$limit} records out of {$paging['total']} total" ?>
        </p>
    </div>
</div>



<style type="text/css">
  .pagination {
    margin: 10px 0 2px;
    padding: 0;
    list-style: none;
  }

  .pagination li {
    display: inline;
    margin: 0 2px;
  }

  .pagination li a {
    display: inline-block;
    padding: 5px 8px;
    color: white;
    background-color: black;
    text-decoration: none;
  }

  .pagination li a:hover {
    background-color: #333;
  }

  .pagination li.active a {
    background-color: white;
    color: black;
  }

  p {
    margin: 10px 0 2px;
  }

  .paginator {
    /* background-color: black; */
    padding: 10px;
  }
</style>