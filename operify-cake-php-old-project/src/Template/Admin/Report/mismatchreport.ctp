 <!-- Content Wrapper. Contains page content -->

 <div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
     <h1>Daily Mismatch Report</h1>
     <ol class="breadcrumb">
       <li><a href="<?php echo ADMIN_URL; ?>students/admitstudentcollectiondetail"><i class="fa fa-thumbs-up"></i>Office
           Summary Detail Report</a></li>



     </ol>
   </section>

   <!-- Main content -->
   <section class="content">
     <div class="row">
       <div class="col-xs-12">

         <?php echo $this->Flash->render(); ?>
         <div class="box">

           <div class="box-header">



             <style>
             #load2 {
               width: 100%;
               height: 100%;
               position: fixed;
               z-index: 9999;
               background-color: white !important;
               background: url("<?echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
             }
             </style>
             <script inline="1">
             //<![CDATA[
             $(document).ready(function() {
               $("#TaskAdminCustomerFormss").bind("submit", function(event) {
                 $.ajax({
                   async: true,
                   data: $("#TaskAdminCustomerFormss").serialize(),
                   dataType: "html",
                   beforeSend: function() {
                     // setting a timeout
                     $('#load2').css("display", "block");
                   },
                   type: "POST",
                   url: "<?php echo ADMIN_URL; ?>report/mismatchReportSearch",
                   success: function(data) {
                     $("#updt").show();
                     $("#updt").html(data);
                   },
                   complete: function() {
                     $('#load2').css("display", "none");
                   },
                 });
                 return false;
               });
             });
             //]]>
             </script>

             <?php echo $this->Form->create('Task', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'TaskAdminCustomerFormss', 'class' => 'form-horizontal')); ?>

             <div class="form-group">


               <div class="col-md-4 col-sm-6 col-xs-12">
                 <script>
                 //alert();
                 $(function() {
                   $("#datepicker").datepicker({
                     dateFormat: 'yy-mm-dd',
                     maxDate: '0'
                   });
                 });
                 //$('#fdatefrom').datepicker('setDate', 'today');
                 </script>
                 <label for="inputEmail3" class="control-label">Date From</label>
                 <?php echo $this->Form->input('date', array('class' => 'form-control', 'id' => 'datepicker', 'readonly', 'placeholder' => 'Date From', 'label' => false)); ?>
               </div>

               <div class="col-md-4 col-sm-6 col-xs-12 text-xs-center" style="margin-top: 29px;">

                 <input type="submit" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;"
                   class="btn btn4 btn_pdf myscl-btn date" value="Submit">
                 <button type="reset" style="background-color:#f4f4f4;width:100px !important; margin-left: 10px;"
                   class="btn btn4 btn_pdf myscl-btn date">Reset</button>

               </div>

             </div>




             <?php
echo $this->Form->end();
?>


             <!-- /.box-header -->
             <?php echo $this->Flash->render(); ?>

           </div>
           <div id="load2" style="display:none;"></div>

           <div id="updt">





             <div style="clear: both;"></div>

<div class="table-responsive">
             <table id="example1" class="table table-bordered table-striped">
               <thead>



                 <tr>
                   <th>#</th>

                   <th>Date</th>
                   <th>SR. No.</th>
                   <th>Students Name</th>
                   <th>Class
                   <th>
                   <th>Section</th>



                 </tr>
               </thead>
               <tbody id="example22">


                 <tr>
                   <td colspan="5" style="text-align:center;color:red;"><b>Please Select Above Criteria !!!!</b></td>
                 </tr>






               </tbody>

             </table>
             </div>
           </div>