<style>
   #testUL {
      position: relative;
   }

   #testUL ul {
      position: absolute;
      z-index: 999;
      overflow: scroll;
      height: 100px;
      top: 100%;
      left: 0px;
      right: 0px;
      list-style-type: none;
      background-color: white;
      padding-left: 0px;
   }

   #testUL ul li {
      padding: 5px 8px;
      border: 1px solid lightgray;
   }

   #testUL ul li a {
      color: black;
   }

   .form-group .row .col-md {
      flex: 0 0 auto !important;

   }

   .preview {
      margin-right: 15px;
   }
</style>
<?php
if ($ids3) {

?>
   <a href="<?php echo ADMIN_URL; ?>purchaseorder/view/<?php echo $ids5 . "/" . $ids4 . "/" . $ids3; ?>" target="_blank" id="redicaution"></a>
   <script type="text/javascript">
      $('#redicaution')[0].click();
   </script>
<?php } ?>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Sales Orders

      </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="<?php echo SITE_URL; ?>admin/Sales/customerorder">Sales Orders</a></li>
      </ol>
   </section>
   <!-- content header -->
   <!-- Main content -->
   <section class="content">
      <div class="row">
         <div class="col-xs-12">
            <div class="box">
               <div class="box-header" style="padding-bottom:0px;">
                  <?php echo $this->Flash->render(); ?>
                  <script>
                     function cllbckretail0(id, cid, sid) {
                        $('.secrh-retail').val(id);
                        $('#retail_ids').val(cid);

                        $('#testUL').hide();
                     }

                     $(function() {
                        $('.secrh-retail').bind('keyup', function() {
                           var pos = $(this).val();
                           //alert(pos);
                           var check = 0;
                           //var catid=$('#subcategory').val();
                           //alert(pos);
                           $('#testUL').show();
                           $('#retail_ids').val('');
                           var count = pos.length;
                           if (count > 0) {
                              $.ajax({
                                 type: 'POST',
                                 url: '<?php echo ADMIN_URL; ?>vendors/getname',
                                 data: {
                                    'fetch': pos,
                                    'check': check
                                 },
                                 success: function(data) {
                                    console.log(data);
                                    $('#testUL ul').html(data);
                                 },
                              });
                           } else {
                              $('#testUL').hide();
                           }
                        });
                     });
                  </script>
                  <script inline="1">
                     $(document).ready(function() {
                        $("#vendorsdetails").bind("submit", function(event) {
                           $.ajax({
                              async: true,
                              data: $("#vendorsdetails").serialize(),
                              dataType: "html",
                              type: "POST",
                              url: "<?php echo ADMIN_URL; ?>Purchaseorder/searchitem",
                              beforeSend: function() {

                              },
                              success: function(data) {
                                 $("#updt").html(data);
                              },
                              complete: function(data) {},
                           });
                           return false;
                        });
                     });
                  </script>
                  <?php echo $this->Form->create('Purchaseorder', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'vendorsdetails', 'class' => 'form-horizontal')); ?>
                  <div class="form-group">
                     <div class="row">
                        <div class="col-md col-sm-4">
                           <label for="inputEmail3" class="control-label">PO ID</label>
                           <?php echo $this->Form->input('purchaseorder_id', array('class' => 'form-control', 'type' => 'text',  'placeholder' => 'Enter Purchase Order ID', 'label' => false)); ?>
                        </div>
                        <div class="col-md col-sm-4">
                           <label for="inputEmail3" class="control-label">Supplier</label>
                           <input type="hidden" required="required" name="vendor_id" id="retail_ids">
                           <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Supplier Name')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                        </div>
                        <div class="col-md col-sm-4">
                           <script>
                              $(document).ready(function() {
                                 $('#fdatefrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                    onSelect: function(date) {
                                       var selectedDate = new Date(date);
                                       var endDate = new Date(selectedDate);
                                       endDate.setDate(endDate.getDate());
                                       $("#fendfrom").datepicker("option", "minDate", endDate);
                                       $("#fendfrom").val(date);
                                    }
                                 });
                                 $('#fdatefrom').datepicker('setDate', 'today');
                                 $('#fendfrom').datepicker({
                                    dateFormat: 'dd-mm-yy'
                                 });
                                 $('#fendfrom').datepicker('setDate', 'today');
                              });
                           </script>
                           <label for="inputEmail3" class="control-label">Date From</label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
                        </div>
                        <div class="col-md col-sm-4">
                           <label for="inputEmail3" class="control-label">Date To</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'Date To', 'label' => false)); ?>
                        </div>
                        <div class="col-md col-sm-4">
                           <label for="inputEmail3" class="control-label">Product / Any product of folder</label>
                           <?php $stats = array('' => 'All');
                           echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'select', 'options' => $stats, 'placeholder' => 'Status', 'label' => false)); ?>
                        </div>
                        <div class="col-md col-sm-4">
                           <label for="inputEmail3" class="control-label">On Hand</label>
                           <?php $stats = array('any' => 'Any', 'POSITIVE_ONLY' => 'Positive', 'Negative' => 'Negative', 'Zero' => 'Zero', 'Non zero' => 'Non zero', 'UNDER_MINIMUM_BALANCE_ONLY' => 'Below Reorder Point');
                           echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'select', 'options' => $stats, 'placeholder' => 'Status', 'label' => false)); ?>
                        </div>
                        <div class="col-md col-sm-4">
                           <label for="inputEmail3" class="control-label">Available</label>
                           <?php $stats = array('any' => 'Any', 'POSITIVE_ONLY' => 'Positive', 'Negative' => 'Negative', 'Zero' => 'Zero', 'Non zero' => 'Non zero', 'UNDER_MINIMUM_BALANCE_ONLY' => 'Below Reorder Point');
                           echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'select', 'options' => $stats, 'placeholder' => 'Status', 'label' => false)); ?>
                        </div>
                        <div class="col-md col-sm-4">
                           <label for="inputEmail3" class="control-label">Selled products</label>
                           <?php $stats = array('All' => 'All', 'SOLD_ONLY' => 'Only sold', 'NOT_SOLD' => 'Only unsold');
                           echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'select', 'options' => $stats, 'placeholder' => 'Status', 'label' => false)); ?>
                        </div>
                        <div class="col-md col-sm-4">
                           <label for="inputEmail3" class="control-label">Warehouse</label>
                           <?php $stats = array('' => 'All', 'N' => 'Cancel', 'R' => 'Revised');
                           echo $this->Form->input('status', array('class' => 'form-control', 'type' => 'select', 'options' => $stats, 'placeholder' => 'Status', 'label' => false)); ?>
                        </div>
                        <div class="col-md col-sm-4">
                           <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-top: 23px; color:#fff;" id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">
                        </div>
                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>
                  <a href="<?php echo SITE_URL; ?>admin/Sales/customerorderadd" style="position: absolute; right: 0px; bottom: -15px;">
                     <button class="btn btn-success pull-right m-top10" style="margin-top: -50px;"><i class="fa fa-plus"></i>&nbsp;Order</button></a>
               </div>
               <!-- /.box-header -->
               <div class="box-body" id="updt" style="padding-top:0px;">
                  <table id="example14" class="table table-bordered table-striped ">
                     <thead>
                        <tr>
                           <th>No.</th>
                           <th>Date Created</th>
                           <th>Customer</th>
                           <th>My Company</th>
                           <th>Company Account</th>
                           <th>Total</th>
                           <th>Tax Sum</th>
                           <th>In Sales Invoices</th>
                           <th>Paid</th>
                           <th>Un Paid</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                           <td>1</td>
                        </tr>
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
<div class="modal fade" id="myModal" style="width:51% !important;overflow-y: auto !important;" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
   <div class="modal-dialog" style="width:100% !important;">
      <div class="modal-content personal">
         <div class="loader">
            <div class="es-spinner">
               <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   $(document).ready(function() {
      $(".globalModals").click(function(event) {
         // alert($(this).attr("href"));
         $('.modal-content').load($(this).attr("href")); //load content from href of link
      });
   });
</script>