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

   .preview {
      margin-right: 15px;
   }

   #contractUL {
      position: relative;
   }

   #contractUL ul {
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

   #contractUL ul li {
      padding: 5px 8px;
      border: 1px solid lightgray;
      margin-left: 0px !important;
   }

   #contractUL ul li a {
      color: black;
   }
</style>
<style>
   #load2 {
      width: 100%;
      height: 100%;
      position: fixed;
      z-index: 9999;
      background-color: white !important;
      background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
   }
</style>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Contracts Manager
      </h1>
      <ol class="breadcrumb">
         <li><a href="<?php echo SITE_URL; ?>admin/contracts"><i class="fa fa-home"></i>Home</a></li>
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
                  <?php echo $this->Form->create('', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription', 'class' => 'form-horizontal', 'style' => 'margin-bottom:0px;')); ?>
                  <div class="form-group" style="margin-bottom:0px;">
                     <div class="row">
                        <div class="col">
                           <label for="inputEmail3" class=" control-label" style="text-align: left !important">Contract
                              Name</label>
                           <input type="hidden" name="contract_id" id="contrselectid">
                           <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Contract Name')); ?>
                           <div id="contractUL" style="display:none;">
                              <ul></ul>
                           </div>
                           <div id="contractUL1" style="display:none;">
                              <ul>
                                 <li
                                    style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                    No Record Found</li>
                              </ul>
                           </div>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">Supplier Name</label>
                           <input type="hidden" required="required" name="vendor_id" id="retail_ids">
                           <?php echo $this->Form->input('supplier_id', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Supplier Name')); ?>
                           <div id="testUL" style="display:none;">
                              <ul></ul>
                           </div>
                           <div id="testUL1" style="display:none;">
                              <ul>
                                 <li
                                    style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                    No Record Found</li>
                              </ul>
                           </div>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">Cost</label>
                           <?php echo $this->Form->input('cost', array('class' => 'form-control ', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Cost')); ?>
                        </div>
                        <div class="col">
                           <script>
                              $(document).ready(function() {
                                 $('#fdatefrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                 });
                                 $('#fendfrom').datepicker({
                                    dateFormat: 'dd-mm-yy',
                                    yearRange: '2018:2030',
                                    changeMonth: true,
                                    changeYear: true,
                                 });
                              });
                           </script>
                           <label for="inputEmail3" class="control-label">Start Date</label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Start Date', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <label for="inputEmail3" class="control-label">End Date</label>
                           <?php echo $this->Form->input('dateto', array('class' => 'form-control', 'id' => 'fendfrom', 'readonly', 'placeholder' => 'End Date', 'label' => false)); ?>
                        </div>
                        <div class="col">
                           <input type="submit" style="background-color:#00c0ef; color:#fff;margin-top:23px;" id=""
                              class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/contracts/index" class="excelbtn btn"
                              style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px;">Reset</a>
                        </div>
                        <?php
                        $role_permissions = $this->Permission->permissioncheck();
                        $fileurl = "admin/contracts/add";
                        if (in_array($fileurl, $role_permissions)) { ?>

                           <div class="col">
                              <a href="<?php echo SITE_URL; ?>admin/contracts/add"
                                 class="btn btn-success pull-right m-top10" style="margin-top: 23px;margin-bottom:10px;">
                                 <i class="fa fa-plus" aria-hidden="true"></i>
                                 Add</a>
                           </div>
                        <?php } ?>
                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

               </div>




               <!-- box-header -->
               <div id="load2" style="display:none;"></div>
               <div class="box-body" style="padding-top:0px;" id="example23">

                  <table class="table table-bordered table-striped" width="100%">
                     <thead>
                        <tr>
                           <th width="3%">S.No.</th>
                           <th width="15%">Title</th>
                           <th width="12%">Supplier Name</th>
                           <th width="8%">Cost</th>
                           <th width="8%">Issue Date</th>
                           <th width="8%">Start Date</th>
                           <th width="8%">End Date</th>
                           <th width="30%">Description</th>
                           <th width="8%">Action</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $page = $this->request->params['paging']['Contracts']['page'];
                        $limit = $this->request->params['paging']['Contracts']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        if (isset($users) && !empty($users)) {
                           foreach ($users as $intusr) {
                              $var = $this->Comman->findvendornames($intusr['supplier_id']);
                              $designsheetid = $this->Comman->checkdesignsheet($intusr['id']);
                              // pr($designsheetid);die;
                        ?>
                              <tr>
                                 <td>
                                    <?php echo $counter; ?>.
                                 </td>
                                 <td>
                                    <a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $intusr['id']; ?>"
                                       class="viewdetails">
                                       <?php echo $intusr['title'] . '(' . $intusr['workorder'] . ')'; ?>
                                    </a>

                                    <!-- <a style="color: red;" target="_blank"
                                    href="<?php // echo SITE_URL; 
                                          ?>admin/contracts/viewcontractdetail/<?php // echo $intusr['id']; 
                                                                                 ?>">view</a> -->
                                 </td>
                                 <td>
                                    <?php echo $var['name']; ?>
                                 </td>
                                 <td style="text-align:right;">
                                    <?php// echo sprintf('%.2f', $intusr['cost']); ?>
                                     <?php echo number_format($intusr['cost']); ?>
                                 </td>
                                 <td>
                                    <?php echo date('d-m-Y', strtotime($intusr['issuedate'])); ?>
                                 </td>
                                 <td>
                                    <?php echo date('d-m-Y', strtotime($intusr['contract_start_date'])); ?>
                                 </td>
                                 <td>
                                    <?php echo date('d-m-Y', strtotime($intusr['contract_end_date'])); ?>
                                 </td>
                                 <!-- <td>
                                 <?php echo $intusr['description']; ?>
                              </td> -->
                                 <td>
                                    <?php
                                    $description = h($intusr['description']);
                                    $wordArray = explode(' ', strip_tags($description));
                                    $wordCount = count($wordArray);

                                    $firstPart = implode(' ', array_slice($wordArray, 0, 20));
                                    $remainingPart = implode(' ', array_slice($wordArray, 20));
                                    ?>

                                    <span>
                                       <?php echo $firstPart; ?>
                                    </span>

                                    <?php if ($wordCount > 20): ?>
                                       <span id="more-<?php echo $intusr['id']; ?>" style="display: none;">
                                          <?php echo ' ' . $remainingPart; ?>
                                       </span>

                                       <a href="javascript:void(0);" onclick="toggleMessage(<?php echo $intusr['id']; ?>, this)">
                                          View more
                                       </a>
                                    <?php endif; ?>
                                 </td>

                                 <script>
                                    function toggleMessage(id, linkElement) {
                                       const moreText = document.getElementById(`more-${id}`);

                                       if (moreText.style.display === "none") {
                                          moreText.style.display = "inline";
                                          linkElement.textContent = "View less";
                                       } else {
                                          moreText.style.display = "none";
                                          linkElement.textContent = "View more";
                                       }
                                    }
                                 </script>




                                 <td> <strong>
                                       <?php

                                       if ($designsheetid == '') {
                                          $role_permissions = $this->Permission->permissioncheck();
                                          $fileurl = "admin/contracts/edit";
                                          if (in_array($fileurl, $role_permissions)) {
                                             echo $this->Html->link('', [
                                                'action' => 'edit',
                                                $intusr->id,
                                             ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                                          }
                                          $fileurl = "admin/contracts/delete";
                                          if (in_array($fileurl, $role_permissions)) {
                                             echo $this->Html->link('', [
                                                'action' => 'delete',
                                                $intusr->id
                                             ], [
                                                'class' => 'fas fa-trash-alt',
                                                'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                                "onClick" => "javascript: return confirm('Are you sure do you want to delete this Contract')"
                                             ]);
                                          }
                                       }
                                       ?>

                                       <a title="Download Contract PDF" class="fa fa-download fa-lg text-green"
                                          href="<?php echo ADMIN_URL; ?>production/viewcontractdetailspdf/<?php echo $intusr['id']; ?>"
                                          download="NewName.pdf"></a>

                                    </strong>
                                 </td>
                              </tr>
                           <?php $counter++;
                           }
                        } else { ?>
                        <?php } ?>
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
<script>
   $('.viewdetails').click(function(e) {
      e.preventDefault();
      $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
   });
</script>

<div class="modal fade" id="editsorts">
   <div class="modal-dialog" style="max-width:900px !important;">
      <div class="modal-content">
         <div class="modal-body"></div>
      </div>
   </div>
</div>



<script>
   function cllbckretail(id, cid, sid) {
      $('.secrh-retail').val(id);
      $('#retail_ids').val(cid);
      $('#testUL').hide();
      $('#testUL1').hide();
   }

   $(document).ready(function() {
      $('.secrh-retail').bind('keyup', function() {
         var pos = $(this).val();
         $('#testUL').show();
         $('#retail_ids').val('');
         var count = pos.length;
         if (count > 0) {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>vendors/getname',
               data: {
                  'fetch': pos,
               },

               success: function(data) {
                  if (data) {
                     console.log(data);
                     $('#testUL ul').html(data);
                  } else {
                     $('#testUL').hide();
                     $('#testUL1').show();
                  }
               },
            });
         } else {
            $('#testUL').hide();
            $('#testUL1').hide();
         }
      });
   });
</script>
<script>
   $(document).ready(function() {
      $("#mysubscription").bind("submit", function(event) {
         $.ajax({
            async: true,
            data: $("#mysubscription").serialize(),
            dataType: "html",
            type: "GET",
            url: "<?php echo ADMIN_URL; ?>contracts/searchitem",

            // success: function (data) {
            //    $("#example23").html(data);
            // },
            beforeSend: function(xhr) {
               xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
               $('#load2').css("display", "block"); // Show loader
            },
            success: function(data) {
               $('.lds-facebook').hide();
               $("#example23").html(data);
            },
            complete: function() {
               $('#load2').css("display", "none"); // Hide loader
            },
            error: function() {
               alert("An error occurred. Please try again.");
               $('#load2').css("display", "none"); // Hide loader on error
            }
         });
         return false;
      });

      $(document).on('click', '.pagination a', function(e) {
         var target = $(this).attr('href');
         // alert(target);
         console.log(target);
         var res = target.replace("/contracts/searchitem", "/contracts");
         console.log(res);
         window.location = res;
         return false;
      });
   });
</script>
<script>
   function cllbckretail2(id, cid) {
      $('.secrhcontract').val(id);
      $('#contrselectid').val(cid);
      $('#contractUL').hide();
      $('#contractUL1').hide();
   }
   $(function() {
      $('.secrhcontract').bind('keyup', function() {
         var pos = $(this).val();
         var check = 2;
         $('#contractUL').show();
         $('#contrselectid').val('');
         var count = pos.length;
         if (count > 0) {
            $.ajax({
               type: 'POST',
               url: '<?php echo ADMIN_URL; ?>production/getcontract',
               data: {
                  'fetch': pos,
                  'check': check
               },
               success: function(data) {
                  if (data) {
                     $('#contractUL ul').html(data);
                     $('#contractUL1').hide();
                  } else {
                     $('#contractUL').hide();
                     $('#contractUL1').show();
                  }
               },
            });
         } else {
            $('#contractUL').hide();
            $('#contractUL1').hide();
         }
      });
   });
</script>