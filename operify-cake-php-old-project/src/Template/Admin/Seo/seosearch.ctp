    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">  
<style>
    .modal-header .close {
    margin-top: -21px;
}
</style>

  
<table id="example1" class="table table-bordered table-striped">
            <thead> 
             <tr>
                <th class="align-top" style="width:5%">S.No.</th>
                <th class="align-top" style="width:10%">Page Name</th>
                <th class="align-top" style="width:20%">Page Location</th>
                <th class="align-top" style="width:25%">Title</th>
                <th class="align-top" style="width:10%">Description</th>
                <th class="align-top" style="width:10%">Keywords</th>
                <th class="align-top" style="width:10%;"><?= __('Actions') ?></th>
              </tr>
            </thead>
            <tbody >

            <?php   //pr($this->request->params); die;
            $i=($this->request->params['paging']['Coupancode']['page']-1) * $this->request->params['paging']['Coupancode']['perPage']; 
            if(isset($searchresult) &&     !empty($searchresult)){ 
              foreach ($searchresult as $value){ $i++; //pr($value); ?>
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
                   <a href="<?php  echo ADMIN_URL ?>seo/status/<?php echo $value['id']; ?>/N"><img src="<?php echo SITE_URL; ?>images/adminimages/active.png" class="" title="Active"></a>

                    <?php  }else { ?>
                     <a href="<?php  echo ADMIN_URL ?>seo/status/<?php echo $value['id']; ?>/Y"><img src="<?php echo SITE_URL; ?>images/adminimages/inactive.png" class="" title="In Active" height="22px" width="22px" ></a>
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
          <?php echo $this->element('admin/pagination'); ?>        


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