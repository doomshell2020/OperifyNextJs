<script>
   $(document).ready(function() {
      $(".globalModals").click(function(event) {
         // alert($(this).attr("href"));
         $('.modal-content').load($(this).attr("href")); //load content from href of link
      });
   });
</script>

                  <script >
                      $(document).ready(function() {
                        $("#mysubscription").bind("submit", function(event) {
                           $.ajax({
                              async: true,
                              data: $("#mysubscription").serialize(),
                              dataType: "html",
                              type: "POST",
                              url: "<?php echo ADMIN_URL; ?>indent/searchitem",

                              success: function(data) {
                                 $("#example14").html(data);
                              },
                              
                           });
                           return false;
                        });
                     });
                  </script>


<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Indent Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/dashboards/headbranch"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/indent">Indent Manager</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <?php echo $this->Flash->render(); ?>
               <!-- /.box-header -->
               
               <?php echo $this->Form->create('', array('inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription', 'class' => 'form-horizontal', 'style' => 'margin-bottom:0px;')); ?>

                  <div class="form-group" style="margin-bottom:0px;">
                     <div class="row">
                        <div class="col-sm-3">
                           <label for="inputEmail3" class="control-label">Indent Id</label>
                           <?php echo $this->Form->input('indent_id', array('class' => 'form-control ', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Indent Id')); ?>
                        </div>
                        <div class="col-sm-3">
                           <script>
                              $(document).ready(function() {
                                 $('#fdatefrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                 });
                                 $('#fendfrom').datepicker({
                                    dateFormat: 'dd-mm-yy'
                                 });
                              });
                           </script>
                           <label for="inputEmail3" class="control-label">Date From <strong style="color:red;">*</strong></label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
                        </div>
                        <div class="col-sm-3">
                           <label for="inputEmail3" class="control-label">Date To <strong style="color:red;">*</strong></label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Date To', 'label' => false)); ?>
                        </div>
                        <div style="display: flex;    align-items: end;    justify-content: space-between;" class="col-sm-3">
                           <label for="inputEmail3" class="control-label"></label>
                           <input type="submit" style="background-color:#00c0ef; color:#fff;width:100px !important;" id="" class="btn btn4 btn_pdf myscl-btn date" value="Search">
                        </div>
                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

               </div>


                  <table id="example14" class="table table-bordered table-striped">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Indent Id </th>
                           <th>Items</th>
                           <th>Requested Qty</th>
                           <th>Genrated Date</th>
                           <th>Indent / PO</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($users) && !empty($users)) {
                           $counter = 1;
                           foreach ($users as $intusr) {
                              //  pr($intusr); 
                              $var = $this->Comman->indentitemquantity($intusr['indent_id']);
                        ?>
                              <tr>
                                 <!-- <thead> -->
                                 <td><?php echo $counter; ?></td>
                                 <td><?php echo $intusr['indent_id']; ?></td>
                                 <td>
                                    <table class="table table-bordered" style="margin-bottom:0px;">
                                       <!-- <thead> -->
                                       <tr>
                                          <th>Name</th>

                                          <th>Qty</th>
                                          <th>PO In-Stock</th>

                                       </tr>
                                       <!-- </thead> -->
                                       <tbody>
                                          <?php
                                          $indentdetail = $this->Comman->indentdetail($intusr['indent_id']);
                                          // pr($indentdetail); die;

                                          $totl = 0;

                                          foreach ($indentdetail as $value) {
                                             //pr($value); 
                                             $unitname = $this->Comman->getunitnamepoview($value['additem']['unit_id']);

                                             $remain = $value['quantity'] - $value['return_qty'];
                                             if ($remain > 0) {
                                                $totl += $remain;
                                             }

                                          ?>
                                             <tr>
                                                <td>
                                                   <?php echo $value['additem']['item_name']; ?>
                                                </td>

                                                <td style="color:red;"><?php echo $value['quantity']; ?></td>
                                                <?php
                                                $totalrecivied = $this->Comman->totalstockregisteropeningrecivied($value['item_id']);
                                                $totaldispatched = $this->Comman->totalstockregisteropeningdispatched($value['item_id']);
                                                //  pr($totaldispatched); 
                                                // $remain=$totalrecivied[0]['quantity']-$totaldispatched[0]['sum']; 
                                                $remain = $totalrecivied[0]['quantity'];

                                                ?>
                                                <td style="color:green;"><?php echo $remain; ?></td>
                                                <!-- <td style="color:green;"><? php //echo $value['return_qty']; 
                                                                              ?></td> -->
                                             </tr>
                                          <?php } ?>
                                       </tbody>
                                    </table>
                                 </td>
                                 <td><?php echo $var[0]['quantity']; ?></td>
                                 <!-- <td><?php // if($totl==0){ echo "Completed"; }else{ echo "<strong style='color:red;'>Pending</strong>"; }
                                          ?></td> -->
                                 <td><?php echo date("d-m-Y", strtotime($intusr['added_time'])); ?></td>
                                 <td>
                                    <div style="display:flex; align-items:flex-start">
                                       <a target="_blank" title="Download Indent" href="<?php echo ADMIN_URL; ?>indent/view/<?php echo $intusr['indent_id']; ?>"><i class="fa fa-file-pdf-o" style="font-size: 20px !important;"></i>&nbsp;</a>

                                       <?php if ($totl != 0) { ?>&nbsp;
                                       <a target="_blank" title="PO" href="<?php echo ADMIN_URL; ?>purchaseorder/add/<?php echo $intusr['indent_id']; ?>"><img src="<?php echo SITE_URL; ?>/images/subMenu/po.png" style="width: 20px;">&nbsp;</a> <?php } ?>
                                    </div>
                                 </td>
                              </tr>
                           <?php $counter++;
                           }
                        } else { ?>
                        <?php } ?>
                     </tbody>
                  </table>
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
<div class="modal fade" id="globalModalbag" style="width:51% !important;" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width:100% !important;">
      <div class="modal-content personal">
         <div class="modal-body">
            <div class="col-sm-6 col-md-6 col-sm-offset-2 col-md-offset-2">
            </div>
            <div class="loader">
               <div class="es-spinner">
                  <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>