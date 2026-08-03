<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script type="text/javascript"
   src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet"
   href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css" />
<style>
   #loader {
   display: none;
   position: absolute;
   top: 5%;
   left: 45%;
   width: 200px;
   height: 200px;
   padding:30px 15px 0px;
   border: 3px solid #ababab;
   box-shadow:1px 1px 10px #ababab;
   border-radius:20px;
   background-color: white;
   z-index: 1002;
   text-align:center;
   overflow: auto;
   }
</style>
<?php $date=date('m/Y');?>
<div class="content-wrapper">
   <!-- Content Header (Page header) -->
   <section class="content-header">
      <h1>
         <i class="fa fa-money"></i> Monthly Summary Collection Report
      </h1>
   </section>
   <section class="content">
      <div id="loader" >
         <img src="<?php echo SITE_URL; ?>img/loading-gif-loader-v4.gif" class="img-responsive" />
      </div>
      <div class="box">
         <div class="box-header">
            <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
               <div class="modal-dialog">
                  <div class="modal-content">
                     <div class="modal-body">
                        <div class="loader">
                           <div class="es-spinner">
                              <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <?php echo $this->Form->create('Fees',array('inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'feesexl','class'=>'form-horizontal')); ?>
            <div class="form-group">
               <div class="col-sm-5 col-xs-6" id="datepicker-group1" >
                  <label for="inputEmail3" class="control-label">Date From</label>
                  <?php echo $this->Form->input('datefrom',array('class'=>'form-control fendfrom','readonly','id'=>'fendfrom','placeholder'=>'Date To','label' =>false,'required','value' => $date,'data-date-end-date' => '0m')); ?>  
               </div>
               <div class="col-sm-5 col-xs-6" id="#datepicker-group" >
                  <label for="inputEmail3" class="control-label">Date To</label>
                  <?php echo $this->Form->input('dateto',array('class'=>'form-control fendto','readonly','id'=>'fendto','placeholder'=>'Date To','label' =>false,'required','value' => $date,'data-date-end-date' => '0m')); ?>  
               </div>
               <div class="col-sm-2 col-xs-12 text-xs-center" style="margin-top:26px;">  
                  <input type="submit" id="YourbuttonId" style="background-color:#00c0ef;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">  
               </div>
            </div>
            <?php $this->Form->end(); ?>
         </div>
      </div>
      <div class="box" id="updt">
      </div>
   </section>
</div>
<script>
   var startDate = new Date();
   var fechaFin = new Date();
   var FromEndDate = new Date();
   var ToEndDate = new Date();
   
   $('.fendfrom').datepicker({
       autoclose: true,
       minViewMode: 1,
       format: 'mm/yyyy'
   }).on('changeDate', function(selected){
           startDate = new Date(selected.date.valueOf());
           startDate.setDate(startDate.getDate(new Date(selected.date.valueOf())));
           // $('.fendto').datepicker('setStartDate', startDate);
       }); 
   
   $('.fendto').datepicker({
       autoclose: true,
       minViewMode: 1,
       format: 'mm/yyyy'
   }).on('changeDate', function(selected){
           FromEndDate = new Date(selected.date.valueOf());
           FromEndDate.setDate(FromEndDate.getDate(new Date(selected.date.valueOf())));
           // $('.fendfrom').datepicker('setEndDate', FromEndDate);
       });
   
</script>
<script>
   $(document).ready(function(){
     $("#feesexl").bind("submit", function (event) {
       $.ajax({
         async:true,
         data:$("#feesexl").serialize(),
         dataType:"html", 
         type:"POST", 
        url:"<?php echo ADMIN_URL ;?>report/monthlysummarydetail",
         beforeSend: function(){
       // Show image container
       $("#loader").show();
      },
         success:function (data) {
   		//  alert(data); 
   		  
   	//	$("#updt").show();   
           $("#updt").html(data); }, 
           complete:function(data){
       // Hide image container
       $("#loader").hide();
      },
     });
       return false;
     });
   });
</script>