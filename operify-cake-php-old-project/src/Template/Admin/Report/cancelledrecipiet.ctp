<!-- Content Wrapper. Contains page content -->
<style>
.checkbox input[type="checkbox"] {
	opacity: 1;
}
</style>
<style>
#load2 {
	width: 100%;
	height: 100%;
	position: fixed;
	z-index: 9999;
	background-color: white !important;
	background: url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
}
</style>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>Search Fee Receipt</h1>
		<ol class="breadcrumb">
			<li><a href="<?php echo ADMIN_URL;?>cancelledrecipiet"><i class="fa fa-home"></i>Home</a></li>
			<li><a href="<?php echo ADMIN_URL;?>report/cancelledrecipiet">Manage Receipt </a></li>
		</ol>
	</section>
	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box">
					<div class="box-header">
						<div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-body">
										<div class="loader">
											<div class="es-spinner"> <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i> </div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<script inline="1">
              $(document).ready(function() {
                $("#YourbuttonId").click(function() {
                  if($('input[type=checkbox]:checked').length == 0) {
                    alert('Please select atleast one checkbox');
                  }
                });
              });
              //<![CDATA[
              $(document).ready(function() {
                $("#feesexl").bind("submit", function(event) {
                  $.ajax({
                    async: true,
                    data: $("#feesexl").serialize(),
                    dataType: "html",
                    type: "POST",
                    beforeSend: function() {
                      // setting a timeout
                      $('#load2').css("display", "block");
                    },
                    url: "<?php echo ADMIN_URL ;?>report/searchcancelled",
                    success: function(data) {
                      //  alert(data); 
                      //	$("#updt").show();   
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
						<?php echo $this->Form->create('Fees',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'feesexl','class'=>'form-horizontal')); ?>
            <div class="form-group">
              <input type="hidden" name="acedmicyear" value="<? echo $acedmic; ?>">
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <label for="inputEmail3" class="control-label">Select Class</label>
                <?php echo $this->Form->input('class_id',array('class'=>'form-control','id'=>'subjt','type'=>'select','empty'=>'Select Class','options'=>$classes,'label' =>false)); ?>
              </div>
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <label for="inputEmail3" class="control-label">Scholar No.</label>
                <?php echo $this->Form->input('sr_no',array('class'=>'form-control','placeholder'=>'Enter Scholar No.','label' =>false)); ?>
              </div>
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <script>
                $(document).ready(function() {
                  $('#fdatefrom').datepicker({
                    dateFormat: 'yy-mm-dd',
                    yearRange: '2018:2025',
                    //   minDate: new Date(2018, 10 - 1, 01),
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
                    dateFormat: 'yy-mm-dd'
                  });
                  //$('#fendfrom').datepicker('setDate', 'today');
                });
                </script>
                <label for="inputEmail3" class="control-label">Date From</label>
                <?php echo $this->Form->input('datefrom',array('class'=>'form-control','id'=>'fdatefrom','readonly','placeholder'=>'Date From','label' =>false)); ?>
              </div>
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <label for="inputEmail3" class="control-label">Date To</label>
                <?php echo $this->Form->input('dateto',array('class'=>'form-control','id'=>'fendfrom','readonly','placeholder'=>'Date To','label' =>false)); ?>
              </div>
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <label for="inputEmail3" class="control-label">Receipt No.</label>
                <?php echo $this->Form->input('recipetno',array('class'=>'form-control','placeholder'=>'Enter Receipt No.','label' =>false)); ?>
              </div>
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <label for="inputEmail3" class="control-label">Cheque No. </label>
                <?php echo $this->Form->input('cheque_no',array('class'=>'form-control','placeholder'=>'Enter Cheque No.','label' =>false)); ?>
              </div>
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6">
                <label for="inputEmail3" class="control-label">Netbanking Reference No. </label>
                <?php echo $this->Form->input('ref_no',array('class'=>'form-control','placeholder'=>'Enter Reference No.','label' =>false)); ?>
              </div>
              <div class="col-lg-3 col-md-4 col-sm-6 col-xs-6 sr_recieptdv1">
                <label for="inputEmail3" class="control-label">Select Status</label>
                <div style="display:flex; align-items: center;">
                  <label class="form-control"><input type="radio" name="status" checked="checked" value="Both"> Both</label> &nbsp;&nbsp;
                  <label class="form-control"><input type="radio" name="status" value="N"> Can.</label> &nbsp;&nbsp;
                  <label class="form-control"><input type="radio" name="status" value="Y"> Dep.</label>
                </div>
              </div>
              <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 sr_recieptdv2 mt-2" style="margin-top:5px">
                <label for="inputEmail3" class="control-label"></label>
                <input type="submit" id="stud_sub" style="background-color:#00c0ef;color:#fff;width:100px !important; margin-left: 0px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">
                <label for="inputEmail3" class="control-label"></label>
                <button type="reset" style="background-color:#333;color:#fff;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>
              </div>
              <?php echo $this->Form->end(); ?>
                <!-- /.box-header -->
                <?php echo $this->Flash->render(); ?>
            </div>
            <script>
              $(document).ready(function() {
                $('#example1').DataTable({
                  "paging": true,
                });
              });
            </script>
          </div>  
          <div class="body">
            <div class="table-responsive">
              <div id="load2" style="display:none;"></div>
              <div id="updt"> </div>
            </div>
          </div>
        </div>      
      </div>      
    </div>      
  </section>
</div>     
