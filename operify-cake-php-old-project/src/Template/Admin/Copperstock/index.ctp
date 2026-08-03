<style>
   input {
      font-size: 12px;
      padding: 4px;
      width: 100%;
   }

   .tableCover {
      height: 100%;
      max-height: 72vh;
      overflow: auto;
      position: relative;
   }

   .fix {
      position: sticky;
      top: 0px;
      /* z-index: /; */
      width: 100%;
      margin: 0px;
   }
</style>

<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Copper Stock Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="#">Copper Stock</a></li>
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

                  <?php echo $this->Form->create(
                     '',
                     array(
                        'type' => 'file',
                        'inputDefaults' => array('div' => false, 'label' => false),
                        'id' => 'mysubscription',
                        'class' => 'form-horizontal',
                        'validate',
                        'style' => 'margin-bottom:0px;'
                     )
                  ); ?>
                  <div class="form-group" style="margin-bottom:0px;">
                     <div class="row">
                        <div class="col-sm-3">
                           <script>
                              $(document).ready(function () {
                                 $('#fdatefrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                 });
                              });
                           </script>
                           <label for="inputEmail3" class="control-label">Date</label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Enter Date', 'required', 'label' => false)); ?>
                        </div>

                        <div style="display: flex; align-items: end;" class="col-sm-3">
                           <input type="submit"
                              style="background-color:#00c0ef; color:#fff;width:100px !important;margin-top:5px;margin-right:20px;"
                              id="" class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a style="font-size: 20px;" target="_blank"
                              href="<?php echo ADMIN_URL; ?>copperstock/viewpdf/<?php echo $value['id']; ?>"><i
                                 class="fa fa-file-pdf-o" style="font-size: 20px;"></i></a>&nbsp;
                        </div>
                        <?php echo $this->Form->end(); ?>
                     </div>
                  </div>
               </div>
            </div>

            <!-- /.box-header -->
            <div class="box-body" id="example145">
               <?php echo $this->Form->create(
                  '',
                  array(
                     'class' => 'form-horizontal',
                     'enctype' => 'multipart/form-data',
                     'id' => 'sevice_form',
                     'validate'
                  )
               );
               $date = date('d-M-Y'); ?>
               <div class="tableCover">
                  <table class="table table-bordered table-striped" width="100%">
                     <p style="text-align:right;">Date -
                        <?php echo $date; ?>
                     </p>
                     <thead class="fix">
                        <tr>
                           <th width="5%" >S No.</th>
                           <th width="35%" >Product Name</th>
                           <th width="20%" >Type</th>
                           <th width="20%" >TPPL</th>
                           <th width="20%" >KCPL</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;

                        foreach ($data as $key => $intusr) {
                           $id = $intusr['id'];
                           $copperstock = $this->Comman->findcopperstock($id);

                           $copperstocktype = isset($copperstock['type']) ? $copperstock['type'] : ('-');
                           $copperstocktppl = isset($copperstock['tppl']) ? $copperstock['tppl'] : ('-');
                           $copperstockkcpl = isset($copperstock['kcpl']) ? $copperstock['kcpl'] : ('-');
                           ?>

                           <tr>
                              <td>
                                 <?php echo $counter; ?>
                              </td>
                              <td>
                                 <?php echo $intusr['item_name']; ?>
                              </td>
                             

                              <td>
                                 <?php echo $this->Form->input('type[' . $id . ']', array('class' => '', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'value' => $copperstocktype, 'required', 'id' => 'issuequant-' . $key, 'oninput' => 'calculatetotalqty(this)', 'onkeypress' => 'return isNumberKey(event)')); ?>
                              </td>

                              <td>
                                 <?php echo $this->Form->input('tppl[' . $id . ']', array('class' => '', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'id' => 'issuewei-' . $key, 'value' => $copperstocktppl, 'oninput' => 'calculatetotalweight(this)', 'onkeypress' => 'return isNumberKey(event)')); ?>
                              </td>

                              <td>
                                 <?php echo $this->Form->input('kcpl[' . $id . ']', array('class' => '', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'value' => $copperstockkcpl, 'id' => 'tpplquant-' . $key, 'oninput' => 'calculatetotalqty(this)', 'onkeypress' => 'return isNumberKey(event)')); ?>
                              </td>


                              <?php $counter++;
                        } ?>
                           <div style="text-align:right;margin-bottom:5px;">
                              <input type="submit"
                                 style="background-color:#00c0ef; color:#fff;width:100px !important;margin-top:5px;"
                                 id="" class="btn btn4 btn_pdf myscl-btn date" value="Submit">
                              <?php echo $this->Form->end(); ?>
                           </div>
                     </tbody>
                  </table>
               </div>
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
<!-- content-wrapper -->
<script>
   $("#sevice_form").keypress(function (e) {
      if (e.which == 13) {
         return false;
      }
   });

   $(document).ready(function () {
      $("#mysubscription").bind("submit", function (event) {
         $.ajax({
            async: true,
            data: $("#mysubscription").serialize(),
            dataType: "html",
            type: "POST",
            url: "<?php echo ADMIN_URL; ?>copperstock/searchitem",
            success: function (data) {
               $("#example145").html(data);
            },
         });
         return false;
      });
   });
</script>

<script>
   function isNumberKey(evt) {
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57))
         return false;
      return true;
   }
</script>
