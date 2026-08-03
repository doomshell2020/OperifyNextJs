
<div class="content-wrapper">
<section class="content-header">
   <h1>
      Ho Sold Items
   </h1>
   <ol class="breadcrumb">
     <li>HO Solditems</li>
   
   </ol>
</section>
<!-- content header -->
<!-- Main content -->
<section class="content">
   <div class="row">
      <div class="col-xs-12">
         <div class="box">
            <div class="box-header">
               <?php echo $this->Flash->render(); ?>
           
               <script>          
                  $(document).ready(function () { 
                  $("#Mysubscriptions").bind("submit", function (event) {
                  $('.lds-facebook').show();
                  $.ajax({
                    async:true,
                    data:$("#Mysubscriptions").serialize(),
                    dataType:"html",
                    type:"POST",
                    url:"<?php echo ADMIN_URL ;?>Solditems/searchho",
                    success:function (data) {
                    $('.lds-facebook').hide();   
                        $("#example2").html(data); },
                        });
                        return false;
                      });
                    });
               </script>
               
               <?php  echo $this->Form->create('Mysubscription',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'Mysubscriptions','class'=>'form-horizontal')); ?>
               <div class="form-group"  style="display:flex; align-items: flex-end;">

               <div class="col-sm-10">
                 <div class="row" style=align-item:end;display:flex;>

                 <div class="col-sm-3">
                     <label for="inputEmail3" class="control-label">Requisition No.</label>	
                     <?php echo $this->Form->input('req_no',array('class'=>'form-control','label' =>false,'placeholder'=>'Enter Item Name','autocomplete'=>'off')); ?>  
                  </div>
                  <div class="col-sm-3">
                    <label for="inputEmail3" class="control-label" style="text-align: left !important;">Branch Name</label>
                    <select name="branch_name" class="form-control" style="width:200px" >
                           <option value="">Select Branch Name</option>
                           <?php foreach ($branches as $key=> $value) { 
                                 
                              $test= explode("_",$value['branch_name']); ?>
                              
                              <option value="<?php echo $value['branch_name']; 
                                             ?>"><?php echo ucfirst($test[1]); ?></option> <?php  } ?>
                  </select>  
                     </div>
         <div class="col-sm-3" style=" display: flex; align-items: end;">     
         <input type="submit" style="background-color:#00c0ef; color:#fff;width:100px !important; margin-top: 20px;" id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">      
             <a  href="<?php echo SITE_URL; ?>admin/Solditems/exporthosolditems"> 
              <i class="fa fa-file-excel-o " style="font-size: 28px; color:red; margin-left: 10px;"></i>
             </a>
            </div>
            </div>
               </div>
                  </div>
               </div>
               </div>
               <!-- </div>box-header -->
               <div class="box-body" id="example2" >
                  <table class="table table-bordered table-striped" width="100%">
                     <thead>
                        <tr>
                           <th width="5%">Requisition No.</th>
                           <th width="25%">School</th>
                           <th width="10%">Branch Name</th>
                           <th width="20%">Description</th>
                           <th width="20%">Remark</th>
                           <th width="5%">View</th>
                           <th width="5%">Status</th>
                           <th width="10%">Rq.Date</th>

                        </tr>
                     </thead>
                     <tbody>
                        <?php
                        $page = $this->request->params['paging'][$this->request->params['controller']]['page'];
                        $limit = $this->request->params['paging'][$this->request->params['controller']]['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                           if(isset($hosold) && !empty($hosold)){ 
                             foreach($hosold as $intusr){ //pr($intusr);
                                $branch_name = explode("_",$intusr['branch_name']);
                               ?>
                        <tr>
                           <td><?php echo $intusr['id'];?></td>
                           <td><?php echo "Canvas International Pre School (<b>".ucfirst($branch_name[1])."</b>) <br> Unit Of Ingenious Edu Scholars Private Limited";  ?></td>
                           <td><?php echo $intusr['branch_name'];?></td>
                           <td><?php echo $intusr['description'];?></td>
                           <td><?php echo $intusr['remark'];?></td>
                           <td>  <a title="Bill PDF"
                                       href="<?php echo SITE_URL; ?>admin/solditems/soldhobillgenerate/<?php echo $intusr['id']; ?>"
                                       style="padding:5px; background:#870606; display:flex; align-items:center; color:#fff; width:max-content; border-radius:3px; font-weight:normal; margin-right:4px;"
                                       target="_blank">
                                       <i class="far fa-file-pdf"
                                             style="font-size:16px; margin-right:4px;"></i> <span
                                             style="line-height:1;">Bill</span>
                                    </a></td>
                           <td><?php echo $intusr['status'];?></td>
                           <td><?php echo date('d-m-Y ', strtotime($intusr['approved_date']));?></td>

                        </tr>
                        <?php $counter++; }  }?>
                        
                     </tbody>

                  </table>
                  <?php echo $this->element('admin/pagination'); ?>
               </div>


               <!-- /.box-body -->
            </div>
            <!-- /.box -->
         </div>
         <!-- /.col -->  
      </div>
      <!-- /.row -->      
</section>
<!-- /.content -->  
</div>     
<!-- /.   content-wrapper -->  
