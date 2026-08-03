<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
      <h1>Student Dropped (WTC) Summary Report</h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo ADMIN_URL;?>students/admitstudent"><i class="fa fa-thumbs-up"></i>Student Dropped Summary Report</a></li>
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
                  #load2{
                  width:100%;
                  height:100%;
                  position:fixed;
                  z-index:9999;
                  background-color: white  !important;
                  background:url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
                  }
                </style>
                <script inline="1">
                  //<![CDATA[
                  $(document).ready(function () {
                    $("#TaskAdminCustomerFormss").bind("submit", function (event) {
                      $.ajax({
                        async:true,
                        data:$("#TaskAdminCustomerFormss").serialize(),
                        dataType:"html", 
                        beforeSend: function() {
                          // setting a timeout
                          $('#load2').css("display", "block");
                      },
                        type:"POST", 
                      url:"<?php echo ADMIN_URL ;?>report/droppedsearch",
                        success:function (data) {
                      $("#updt").show();
                          $("#updt").html(data); }, complete: function() {
                            $('#load2').css("display", "none");
                      },
                    });
                      return false;
                  });});
                  //]]>
                </script>
                <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerFormss','class'=>'form-horizontal')); ?>
                <div class="form-group margin_btmcol" style="display:flex; align-items:flex-end; flex-wrap:wrap; margin-bottom:0px;">
                  <div class="col-md-4">
                      <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
                      <script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
                      <script type='text/javascript'>
                        $(document).ready(function(){
                            $('.txtDate').datepicker({
                        dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                            onSelect: function(date){
                            var selectedDate = new Date(date);
                            var endDate = new Date(selectedDate);
                            endDate.setDate(endDate.getDate());
                            $(".txtDate1").datepicker( "option", "minDate", endDate );
                            $(".txtDate1").val(date);
                        }
                        });
                        $('.txtDate').datepicker('setDate', 'today');
                        $('.txtDate1').datepicker({
                        dateFormat: 'yy-mm-dd',changeMonth: true,
                        changeYear: true});
                        $('.txtDate1').datepicker('setDate', 'today');
                        });
                      </script>
                      <label for="inputEmail3" class="control-label">Dropped Date From<span style="color:red;">*</span></label>
                      <?php echo $this->Form->input('from',array('readonly'=>'readonly','required'=>'required','class'=>'form-control txtDate','placeholder'=>'Admission From','id'=>'','label' =>false,'value'=>date('F Y'))); ?>
                  </div>

                  <div class="col-md-4">
                      <label for="inputEmail3" class="control-label">Dropped Date To<span style="color:red;">*</span></label>
                      <?php echo $this->Form->input('to',array('readonly'=>'readonly','required'=>'required','class'=>'form-control txtDate1','placeholder'=>'Admission From','id'=>'','label' =>false,'value'=>date('F Y'))); ?>
                  </div>

                  <div class="col-md-4" >
                      <label for="inputEmail3" class="control-label">Select Mode<span style="color:red;">*</span></label>
                      <select class="form-control" name="status_tc"  required="required" >
                        <option value="Y">W.T.C</option>
                        <option value="N">L.W.T.C</option>
                      </select>
                  </div>

                  <div class="col-md-4" >
                      <label for="inputEmail3" class="control-label">Select Academic Year<span style="color:red;">*</span></label>
                      <select class="form-control" name="acedmicyear[]"  required="required" multiple="multiple">
                        <option value="<?php echo $previous_year ;?>"><?php echo $previous_year ;?></option>
                        <option value="2021-22">2021-22</option>
                        <option value="<?php echo $academicyear ;?>"><?php echo $academicyear ;?></option>
                      </select>
                  </div>

                  <div class="col-md-4" >
                      <label for="inputEmail3" class="control-label">Select Class<span style="color:red;">*</span></label>
                      <select class="form-control" name="class_id[]" required="required" multiple="multiple">
                        <?php  foreach($classes as $esr=>$es) { ?>
                        <option  selected="selected" value="<?php echo $esr; ?>"><?php echo $es; ?></option>
                        <?php } ?>
                      </select>
                  </div>

                  <div class="col-md-4"  style="margin-top: 15px;">
                      <input type="submit" style="background-color:#00c0ef;color:#fff;width:100px !important; margin-left: 30px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">  
                      <button type="reset" style="background-color:#333;color:#fff;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button> 
                  </div>
                </div>
                <?php echo $this->Form->end(); ?>
                <!-- /.box-header -->
                <?php echo $this->Flash->render(); ?>
            </div>
            <div class="box-body">
                <div id="load2" style="display:none;"></div>
                <div id="updt">
                  <div style="clear: both;"></div>
                  <div class="table-responsive">
                      <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                              <th>#</th>
                              <th>Classes</th>
                              <th>2018-19</th>
                              <th>2019-20</th>
                              <th>2020-21</th>
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
            </div>
          </div>
        </div>
      </div>
  </section>
</div>