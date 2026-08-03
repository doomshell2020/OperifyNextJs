<!-- Content Wrapper. Contains page content -->
<style>
  .checkbox input[type="checkbox"]{
  opacity:1;
  }
</style>
<div class="content-wrapper">
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h3>
        Gender Report
    </h3>
    <ol class="breadcrumb">
        <li><a href="<?php echo ADMIN_URL;?>report/studentgender"><i class="fa fa-home"></i>Home</a></li>
        <li><a href="<?php echo ADMIN_URL;?>report/studentgender">Gender Report </a></li>
    </ol>
    <!------------------------------------------------- For Loader CSS------------------------------------>
    <style>
        #load{
        display: none;
        width:100%;
        height:100%;
        position:fixed;
        z-index:9999;
        background-color: white !important;
        background:url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
        }
    </style>
    <div id="load"></div>
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
                            <div class="es-spinner">
                              <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
                            </div>
                        </div>
                      </div>
                  </div>
                </div>
            </div>
            <script inline="1">
                $(document).ready(function(){
                $("#YourbuttonId").click(function(){
                    if($('input[type=checkbox]:checked').length == 0)
                    {
                        alert('Please select atleast one checkbox');
                    }
                });
                });
                
                //<![CDATA[
                $(document).ready(function () {
                  $("#feesexl").bind("submit", function (event) {
                    $.ajax({
                      async:true,
                      data:$("#feesexl").serialize(),
                      dataType:"html",
                      type:"POST",
                      beforeSend: function() {
                        // setting a timeout
                        $("#load").show();
                
                    },
                    url:"<?php echo ADMIN_URL ;?>report/searchstudentgender",
                      success:function (data) {
                
                        $("#updt").html(data);  $("#load").hide(); },
                  });
                    return false;
                });});
                //]]>
            </script>

            <?php echo $this->Form->create('Fees',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'feesexl','class'=>'form-horizontal')); ?>
            <div class="form-group margin_btmcol">
                <input type="hidden" name="acedmicyear" value="<? echo $acedmic; ?>">
                <div class="col-md-3 col-sm-6 col-xs-12">
                  <label>Acedamic Year<span style="color:red;">*</span></label>
                  <?php echo $this->Form->input('acedmicyear', array('type' => 'select', 'options' => $acd, 'empty' => '--Select Academic Year--', 'class' => 'form-control', 'value' => $rolepresentyear, 'label' => false)); ?>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12" >
                  <label for="inputEmail3" class="control-label">Select Class</label>
                  <select class="form-control" name="class_id[]" required="required" multiple="multiple">
                      <?php  foreach($class_id2 as $esr=>$es) { ?>
                      <option  value="<?php echo $esr; ?>" selected="selected"><?php echo $es; ?></option>
                      <?php } ?>
                  </select>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12" >
                  <label for="inputEmail3" class="control-label">Select Section</label>
                  <select class="form-control" name="section_id[]" required="required" multiple="multiple">
                      <?php  foreach($section_id2 as $esr=>$es) { ?>
                      <option  value="<?php echo $esr; ?>" selected="selected"><?php echo $es; ?></option>
                      <?php } ?>
                  </select>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12" >
                  <label for="inputEmail3" class="control-label">Select Gender</label>
                  <div style="display:flex; align-items:center">
                    <label style="margin-right:15px;"><input type="radio" name="gender" checked="checked" value="Both"> Both</label>
                    <label style="margin-right:15px;"><input type="radio" name="gender" value="Male"> Male</label>
                    <label style="margin-right:15px;"><input type="radio" name="gender" value="Female"> Female</label>
                  </div>
                </div>
            </div>
            <div class="form-group text-xs-center">
                <input type="submit" style="background-color:#00c0ef; color:#fff;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date" value="Submit">
                <button type="reset" style="background-color:#333; color:#fff;width:100px !important; margin-left: 10px;" class="btn btn4 btn_pdf myscl-btn date">Reset</button>
            </div>
            <?php echo $this->Form->end(); ?>
            <!-- /.box-header -->
            <?php echo $this->Flash->render(); ?>
          </div>

          <div class="box-body">
            <div id="updt">
              <div class="table-responsive">
                <table id="" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Class Name</th>
                      <? if($gender=='Both'){ ?>
                      <th>Male</th>
                      <th>Female</th>
                      <? }else if($gender=='Male'){ ?>
                      <th>Male</th>
                      <? }else if($gender=='Female'){ ?>
                      <th>Female</th>
                      <? } ?>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $page = $this->request->params['paging']['Services']['page'];
                      $limit = $this->request->params['paging']['Services']['perPage'];
                      $counter = ($page * $limit) - $limit + 1;
                        $session = $this->request->session();
                        $session->delete('gender');
                        $session->delete('class_id2');
                        $session->delete('section_id2');
                      ?>
                    <tr>
                      <td colspan="8" style="text-align:center;color:red;font-weight:bold;">Select Criteria For Data</td>
                    <tr>
                  </tbody>
                </table>
              <div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>