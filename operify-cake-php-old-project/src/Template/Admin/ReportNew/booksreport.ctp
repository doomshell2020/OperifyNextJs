 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

   <section class="content-header">
     <h1>
       <i class="fa fa-book"></i>
       Books Report
     </h1>
     <ol class="breadcrumb">
       <li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
       <li><a href="<?php echo ADMIN_URL;?>ReportNew/booksreport">Manage Books Report</a></li>
     </ol>
   </section>

   <!-- Main content -->
   <section class="content">

     <!-- start -->
     <div class="row">
       <div class="col-xs-12">

         <div class="box">

           <div class="box-header">
             <h3 class="box-title">
               Summary Book Report
             </h3>
           </div>
           <!-- /.box-header -->
           <div class="box-body">
             <div class="row">

               <div class="col-sm-2 mks">
                 <div
                   style="font-size:16px; text-align:center; border:1px solid #ccc; padding:15px 0px; border-radius:5px;">
                   <img src="<?php echo SITE_URL;?>/images/total_book.png" class="" alt="User Image">
                   <p style="margin-bottom:0px; margin-top:5px;"><b style="color:#3c8dbc; font-size: ">Total
                       Books:-</b><?php echo number_format($total_books); ?></p>
                 </div>
               </div>
               <div class="col-sm-2 mks">
                 <div
                   style="font-size:15px; text-align:center; border:1px solid #ccc; padding:15px 0px; border-radius:5px;">
                   <img src="<?php echo SITE_URL;?>/images/english_book.png" class="" alt="User Image">
                   <p style="margin-bottom:0px; margin-top:5px;"><b style="color:#3c8dbc; font-size: ">English
                       Books:-</b><?php echo number_format($eng_book); ?></p>
                 </div>
               </div>
               <div class="col-sm-2 mks">
                 <div
                   style="font-size:16px; text-align:center; border:1px solid #ccc; padding:15px 0px; border-radius:5px;">
                   <img src="<?php echo SITE_URL;?>/images/hindi_book.png" class="" alt="User Image">
                   <p style="margin-bottom:0px; margin-top:5px;"><b style="color:#3c8dbc; font-size: ">Hindi
                       Books:-</b><?php echo number_format($hin_book); ?></p>
                 </div>
               </div>
               <div class="col-sm-2 mks">
                 <div
                   style="font-size:16px; text-align:center; border:1px solid #ccc; padding:15px 0px; border-radius:5px;">
                   <img src="<?php echo SITE_URL;?>/images/Junior_book.png" class="" alt="User Image">
                   <p style="margin-bottom:0px; margin-top:5px;"><b style="color:#3c8dbc; font-size: ">Junior
                       Books:-</b><?php echo number_format($junior_book); ?></p>
                 </div>
               </div>
               <div class="col-sm-2 mks">
                 <div
                   style="font-size:16px; text-align:center; border:1px solid #ccc; padding:15px 0px; border-radius:5px;">
                   <img src="<?php echo SITE_URL;?>/images/Senior_book.png" class="" alt="User Image">
                   <p style="margin-bottom:0px; margin-top:5px;"><b style="color:#3c8dbc; font-size: ">Senior
                       Books:-</b><?php echo number_format($senior_book); ?></p>
                 </div>
               </div>

               <div class="col-sm-2 mks">
                 <div
                   style="font-size:16px; text-align:center; border:1px solid #ccc; padding:15px 0px; border-radius:5px;">
                   <img src="<?php echo SITE_URL;?>images/Senior_bluebook.png" class="" alt="User Image">
                   <p style="margin-bottom:0px; margin-top:5px;"><b style="color:#3c8dbc; font-size: ">Issued
                       Books:-</b><?php echo number_format($issued_book); ?></p>
                 </div>
               </div>

               <div class="col-sm-2 mks">
                 <div
                   style="font-size:14px; text-align:center; border:1px solid #ccc; padding:15px 0px; border-radius:5px;">
                   <img src="<?php echo SITE_URL;?>images/Senior_bluebook.png" class="" alt="User Image">
                   <p style="margin-bottom:0px; margin-top:5px;"><b style="color:#3c8dbc; font-size: ">Periodicals
                       Books:-</b><?php echo number_format($periodical); ?></p>
                 </div>
               </div>
             </div>


           </div>

           <style>
           #loader {
             display: none;
             position: absolute;

             left: 45%;
             width: 200px;
             height: 200px;
             padding: 30px 15px 0px;
             border: 3px solid #ababab;
             box-shadow: 1px 1px 10px #ababab;
             border-radius: 20px;
             background-color: white;
             z-index: 1002;
             text-align: center;
             overflow: auto;
           }
           </style>

           <div class="box-header">
             <h3 class="box-title">

               <i class="fa fa-search"></i> Search Book
             </h3>
           </div>

           <div class="box-body">
             <div id="loader">
               <img src="<?php echo SITE_URL; ?>img/loading-gif-loader-v4.gif" class="img-responsive" />
             </div>

             <div class="manag-stu">

               <?php echo $this->Form->create(null, array('class'=>'form-horizontal', 'id'=>'TaskAdminCustomerForm')); ?>

               <div class="form-group">

                 <div class="col-sm-3">
                   <label>Acc. No.</label>
                   <?php echo $this->Form->input('asn_no',array('class'=>'form-control f', 'label' =>false)); ?>
                 </div>

                 <div class="col-sm-3">
                   <label>ISBN No.</label>
                   <?php echo $this->Form->input('isbn_no',array('class'=>'form-control f', 'label' =>false)); ?>
                 </div>

                 <div class="col-sm-3">
                   <label>Book Name</label>
                   <?php echo $this->Form->input('b_name',array('class'=>'form-control f','id'=>'tags','label' =>false)); ?>
                 </div>

                 <div class="col-sm-3">
                   <label>Language</label>

                   <?php echo $this->Form->input('langu',array('class'=>'form-control f','type'=>'select','options'=>$lahu,'empty'=>'Select Language','label' =>false)); ?>
                 </div>

               </div>

               <script>
               $(document).ready(function() {
                 $('#tags').on('keyup', function() {


                   var b_name = $('#tags').val();
                   //alert(b_name);
                   //alert(h_type);
                   $.ajax({

                     type: 'POST',

                     url: '<?php echo ADMIN_URL;?>Issuebooks/autobookfinder',

                     data: {
                       'b_name': b_name
                     },

                     dataType: "json",

                     success: function(data) {
                       //alert(data);
                       $("#tags").autocomplete({

                         source: data,

                         change: function(event, ui) {
                           val = $(this).val();
                           exists = $.inArray(val, data);
                           if (exists < 0) {
                             $(this).val("");
                             return false;
                           }
                         }

                       });
                     },

                   });

                 });



               });
               </script>



               <div class="form-group">

                 <div class="col-sm-3">
                   <label>Book Category</label>
                   <?php
 									echo $this->Form->input('b_category', array('class'=>'form-control f','type'=>'select', 'empty'=>'Select Book Category',
 										'options'=>$b_category, 'label' =>false)
 									);
 									?>
                 </div>

                 <div class="col-sm-3">
                   <label>Author</label>
                   <?php echo $this->Form->input('author',array('class'=>'form-control f','label' =>false)); ?>
                 </div>



                 <div class="col-sm-3">
                   <label>Book Type</label>

                   <?php $df=array('0'=>'Normal','1'=>'Periodical');
                  echo $this->Form->input('type', array('class'=>'form-control f','type'=>'select',
                    'options'=>$df, 'label' =>false)
                  );
                  ?>
                 </div>

                 <div class="col-sm-3">
                   <label>Status</label>
                   <?php
 									echo $this->Form->input('status', array('class'=>'form-control f','type'=>'select', 'empty'=>'Select Book Status',
 										'options'=>$b_status, 'label' =>false)
 									);
 									?>
                 </div>

               </div>
               <div class="form-group billdate">
                 <div class="col-sm-3">
                   <label for="inputEmail3" class="control-label">Sub Title</label>
                   <?php echo $this->Form->input('subtitle',array('class'=>'form-control f','id'=>'subt','placeholder'=>'Sub Title','label' =>false)); ?>
                 </div>
                 <div class="col-sm-3">
                   <label for="inputEmail3" class="control-label">Advance Category</label>
                   <?php
 									echo $this->Form->input('adv_cat', array('class'=>'form-control f','type'=>'select', 'empty'=>'Select Category',
 										'options'=>array('FICTION'=>'FICTION','NON FICTION'=>'NON FICTION','ENGLISH'=>'ENGLISH','HINDI'=>'HINDI'), 'label' =>false)
 									);
 									?>

                 </div>
                 <div class="col-sm-2">
                   <script>
                   $(document).ready(function() {
                     $('#fdatefrom').datepicker({
                       dateFormat: 'yy-mm-dd',
                       yearRange: '2000:2030',
                       maxDate: 0,
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


                     //$('#fdatefrom').datepicker('setDate', 'today');

                     $('#fendfrom').datepicker({
                       maxDate: 0,
                       changeMonth: true,
                       changeYear: true,
                       dateFormat: 'yy-mm-dd'
                     });
                     // $('#fendfrom').datepicker('setDate', 'today');
                   });
                   </script>

                   <label for="inputEmail3" class="control-label">Bill Date From</label>
                   <?php echo $this->Form->input('datefrom',array('class'=>'form-control f','id'=>'fdatefrom','readonly','placeholder'=>'Date From','label' =>false)); ?>
                 </div>
                 <div class="col-sm-2">
                   <label for="inputEmail3" class="control-label">Bill Date To</label>
                   <?php echo $this->Form->input('dateto',array('class'=>'form-control f','id'=>'fendfrom','readonly','placeholder'=>'Date To','label' =>false)); ?>
                 </div>
               </div>

               <div class="form-group">

                 <div class="col-sm-12">

                   <button type="submit" class="btn btn-success">Search</button>&nbsp;
                   <button type="reset" class="btn btn-primary">Reset</button>

                 </div>

               </div>

               <?php echo $this->Form->end(); ?>

             </div>

           </div>

         </div>
       </div>
     </div>

     <!-- end -->
     <div class="row">
       <div class="col-xs-12">

         <div class="box" style="display: none;" id="appear-box">

           <div class="box-header">

             <h3 class="box-title"><i class="fa fa-eye"></i> View Books</h3>

             <a id='ck' style="position: absolute; top: 10px; right: 16px;" class="btn btn-info btn-sm pull-right"
               href="<?php echo ADMIN_URL ;?>ReportNew/excelExportBooks">
               <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel
             </a>

           </div>
           <!-- /.box-header -->

           <div class="box-body table-responsive">

             <table id="example1" class="table table-bordered table-striped">

               <thead>

                 <tr>
                   <th>#</th>
                   <th>Acc. No.</th>
                   <th>ISBN No.</th>
                   <th>Bill No.</th>
                   <th>Bill Date</th>
                   <th>Book Name</th>
                   <th>Category</th>
                   <th>Cupboard</th>

                   <th> Shelf</th>
                   <th>Language</th>
                   <th>Author</th>
                   <th>Status</th>
                 </tr>

               </thead>

               <tbody id="srch-rslt">
                 <!-- search result will be loaded here using AJAX -->
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


 <!-- custom search script: start -->

 <script>
$(document).ready(function() {

  $("#TaskAdminCustomerForm").bind("submit", function(event) {
    event.preventDefault();
    var hasInput = false;
    $('.f').each(function() {
      if ($(this).val() !== "") {
        hasInput = true;
      }
    });
    if (!hasInput) {
      alert("Fill at least one option");
      return false;
    } else {
      $.ajax({

        async: true,

        type: "POST",

        url: "<?php echo ADMIN_URL ;?>ReportNew/searchBook",

        data: $("#TaskAdminCustomerForm").serialize(),

        dataType: "html",
        beforeSend: function() {
          // Show image container
          $("#loader").show();
        },
        success: function(data) {
          // alert(data);
          $("#srch-rslt").html(data);
          $("#appear-box").show();
        },
        complete: function(data) {
          // Hide image container
          $("#loader").hide();
        },

      });
    }
    return false;

  });

});
 </script>

 <!-- custom search script: end -->