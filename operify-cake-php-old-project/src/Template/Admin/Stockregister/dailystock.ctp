<style>
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

   #test1UL {
      position: relative;
   }

   #test1UL ul {
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

   #test1UL ul li {
      padding: 5px 8px;
      border: 1px solid lightgray;
   }

   #test1UL ul li a {
      color: black;
   }

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

   #load2 {
      width: 100%;
      height: 100%;
      position: fixed;
      z-index: 9999;
      background-color: white !important;
      background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
   }

   .dropdown-box {
      position: relative;
   }

   .dropdown-input {
      border: 1px solid #ced4da;
      padding: 6px 10px;
      cursor: pointer;
      background: #fff;
   }

   .dropdown-list {
      display: none;
      position: absolute;
      background: #fff;
      border: 1px solid;
      width: 100%;
      max-height: 220px;
      /* 🔥 MAIN FIX */
      overflow-y: auto;
      /* 🔥 MAIN FIX */
      z-index: 1000;
   }
</style>
<div class="content-wrapper">
   <section class="content-header">
      <h1>
         Stock Report
      </h1>
      <ol class="breadcrumb">
         <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
         <li><a href="#">Stock Report</a></li>
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
                           <label for="inputEmail3" class="control-label">Date<span style="color: red;">*</span></label>
                           <?php echo $this->Form->input('datefrom', array('class' => 'form-control ', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date', 'label' => false)); ?>
                        </div>


                        <div class="col">
                           <label class="control-label">Category</label>

                           <div class="dropdown-box" id="productDropdown">
                              <div class="form-control" onclick="toggleProduct(event)">
                                 Select Category
                              </div>

                              <div class="dropdown-list" id="productList">

                                 <!-- Select All -->
                                 <label>
                                    <input type="checkbox" name="product[1]" id="selectAllProducts">
                                    <strong>All Category</strong>
                                 </label>
                                 <hr style="margin:4px 0;">

                                 <?php foreach ($categortyname as $cat) { ?>
                                    <label>
                                       <input type="checkbox"
                                          class="product-checkbox"
                                          name="product[]"
                                          value="<?= $cat['keyField'] ?>">
                                       <?= h($cat['valueField']) ?>
                                    </label><br>
                                 <?php } ?>

                              </div>
                           </div>
                        </div>

                        <script>
                           function toggleProduct(e) {
                              e.stopPropagation(); // 🔥 important
                              let box = document.getElementById('productList');
                              box.style.display = box.style.display === 'block' ? 'none' : 'block';
                           }

                           // Select All logic
                           document.getElementById('selectAllProducts').addEventListener('change', function() {
                              let checked = this.checked;
                              document.querySelectorAll('.product-checkbox')
                                 .forEach(cb => cb.checked = checked);
                           });

                           // 🔥 Close dropdown when clicking outside
                           document.addEventListener('click', function(event) {
                              let dropdown = document.getElementById('productDropdown');
                              let list = document.getElementById('productList');

                              if (!dropdown.contains(event.target)) {
                                 list.style.display = 'none';
                              }
                           });
                        </script>


                        <div class="col d-flex align-items-end gap-1">
                           <input type="submit" style="background-color:#00c0ef; color:#fff;margin-top:0px;" id=""
                              class="btn btn4 btn_pdf myscl-btn date" value="Search">

                           <a href="<?php echo SITE_URL; ?>admin/stockregister/dailystock" class="excelbtn btn text-white"
                              >Reset</a>

                           <!-- <a href="<?php echo SITE_URL; ?>admin/stockregister/dailystockexcel"
                              class="excelbtn btn pull-right" style="padding:0;margin-top: 23px;"><i
                                 class="fa fa-file-excel-o" style="font-size:28px; margin-right:10px;"></i></a> -->


                           <a href="<?php echo SITE_URL; ?>admin/stockregister/weeklystockexcel"
                              class=""><i
                                 class="fa fa-file-excel-o" style="padding:0; font-size:28px !important;margin-top:1px;"></i></a>

                        </div>

                        <!-- <div class="col"> -->
                        <!-- <a href="<?php echo SITE_URL; ?>admin/production/dailysheetexcel"
                              class="excelbtn btn pull-right" style="padding:0;margin-top: 23px;"><i
                                 class="fa fa-file-excel-o" style="font-size:28px; margin-right:10px;"></i></a> -->
                        <!-- </div> -->



                     </div>
                  </div>
                  <?php echo $this->Form->end(); ?>

               </div>
               <!-- /.box-header -->
               <div id="load2" style="display:none;"></div>
               <div class="box-body" id="updt">

                  <table class="table table-bordered table-striped" width="100%">
                     <thead>
                     </thead>
                     <tbody>
                     </tbody>
                  </table>
                  <!-- <?php echo $this->element('admin/pagination'); ?> -->
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
   $(document).ready(function() {
      $("#mysubscription").bind("submit", function(event) {
         $.ajax({
            async: true,
            data: $("#mysubscription").serialize(),
            dataType: "html",
            type: "GET",
            url: "<?php echo ADMIN_URL; ?>stockregister/searchstock",



            beforeSend: function(xhr) {
               xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
               $('#load2').css("display", "block"); // Show loader
            },
            success: function(data) {
               $("#updt").html(data);
            },
            complete: function() {
               $('#load2').css("display", "none"); // Hide loader
            },
            error: function() {
               alert("An error occurred. Please try again.");
               $('#load2').css("display", "none"); // Hide loader on error
            }



            // success: function(data) {
            //    $("#updt").html(data);
            // },
         });
         return false;
      });

      $(document).on('click', '.pagination a', function(e) {
         var target = $(this).attr('href');
         var res = target.replace("/production/searchstock", "/production");
         window.location = res;
         return false;
      });
   });
</script>
<script>
   $(document).ready(function() {

      $('#fdatefrom').datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '2018:2030',
         changeMonth: true,
         changeYear: true
      });

      // Make field required
      $('#fdatefrom').prop('required', true);

      // Force validation on form submit
      $('#mysubscription').on('submit', function(e) {

         if ($('#fdatefrom').val() === '') {
            alert('Please select Date');
            $('#fdatefrom').focus();
            e.preventDefault();
            return false;
         }

      });

   });
</script>