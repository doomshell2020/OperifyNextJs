<style>
    .modal-header .close {
    margin-top: -21px;
}
</style>
<body class="hold-transition skin-blue sidebar-mini">
    <div class="content-wrapper">
      <section class="content">
              <div class="box">
         <?php echo $this->Flash->render(); ?>
       <div class="box-header" style="display: flex; align-items: center;">
          <h3 class="box-title">Seo List</h3>
           <a href="<?php echo ADMIN_URL; ?>seo/add" style="margin-left: auto;"><strong class=" btn btn-info card-title pull-right">Add Seo</strong></a>
        </div>
          <div class="calls" style="margin-left: 40%; display: none;">
                Loading Please Wait....
                  <img src="<?php echo SITE_URL; ?>images/tenor.gif" height="100px" width="100px">
                 </div>
        <div class="box-body" id="seosearch">
          <table id="example1" class="table table-bordered table-striped">
            <thead> 
              <tr>
                <th class="align-top" style="width:5%">S.No.</th>
                <th class="align-top" style="width:10%">Page Name</th>
                <th class="align-top" style="width:20%">Page Location</th>
                <th class="align-top" style="width:25%">Title</th>
                <th class="align-top" style="width:10%">Description</th>
                <th class="align-top" style="width:10%">Keywords</th>
                <th class="align-top" style="139px"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody >

            <?php   //pr($this->request->params); die;
            $i=($this->request->params['paging']['Seo']['page']-1) * $this->request->params['paging']['Seo']['perPage']; 
            if(isset($seo) &&     !empty($seo)){ 
              foreach ($seo as $value){ $i++; //pr($value); ?>
                <tr>
                  <td><?php echo  $i; ?></td>
                  <td><?php echo $value['page']; ?></td>
                  <td><a target="_blank" href="<?php echo $value['location']; ?>"><?php echo $value['location']; ?></a></td>
                  <td><?php echo $value['title']; ?></td>
                  
                  <td>
                   <a href="<?php  echo ADMIN_URL ?>seo/viewdocument/<?php echo $value['id']; ?>" data-toggle="modal" class="documentcls badge badge-primary" title="View Description">view</a>
                  </td>
                    <td>
                   <a href="<?php  echo ADMIN_URL ?>seo/viewkeywords/<?php echo $value['id']; ?>" data-toggle="modal" class="documentclsss badge badge-primary" title="View Keywords">view</a>
                  </td>
                  <td>
                     <?php if($value['status']=='Y'){  ?>
                   <a href="<?php  echo ADMIN_URL ?>seo/status/<?php echo $value['id']; ?>/N" class="btn btn-success">Active</a>

                    <?php  }else { ?>
                     <a href="<?php  echo ADMIN_URL ?>seo/status/<?php echo $value['id']; ?>/Y" class="btn btn-success">Inactive</a>
                    <?php }  ?>   

                    <?php  echo $this->Html->link(__(''), ['action' => 'edit', $value->id,],array('class'=>'fa fa-pencil','title'=>'Edit','style'=>'font-size:24px; margin-left: 15px;')) ?>

                    <?php
                    echo $this->Html->link('', [
                      'action' => 'delete',
                      $value->id
                    ],['title'=>'Delete','class'=> 'fa fa-trash','style'=>'font-size:19px; color:#FF0000; margin-left: 15px;',"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Seo')"]); ?>

                    <?php  ?>
                  </td>
                </tr>
              <?php } } else{ ?>
                <tr>
                  <td colspan="12">No Data Available</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
       
        </div>
      </div>
    </section>
  </div>
</body>



  <div class="modal fade" id="mymodel">
<div class="modal-dialog" style="max-width: 500px !important;">
  <div class="modal-content">

    <!-- Modal Header -->
    <div class="modal-header">
      <h4 class="modal-title">Description</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <!-- Modal body -->
    <div class="modal-body">
    </div>
  </div>
</div>
</div>

<script>

$('.documentcls').click(function(e){ 
  e.preventDefault();
  $('#mymodel').modal('show').find('.modal-body').load($(this).attr('href'));
});

</script>



<div class="modal fade" id="mymodelll">
<div class="modal-dialog" style="max-width: 500px !important;">
  <div class="modal-content">

    <!-- Modal Header -->
    <div class="modal-header">
      <h4 class="modal-title">Keyword's</h4>
      <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>

    <!-- Modal body -->
    <div class="modal-body">
    </div>
  </div>
</div>
</div>

<script>

$('.documentclsss').click(function(e){ 
  e.preventDefault();
  $('#mymodelll').modal('show').find('.modal-body').load($(this).attr('href'));
});

</script>