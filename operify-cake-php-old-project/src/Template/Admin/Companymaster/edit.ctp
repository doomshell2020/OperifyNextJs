<div class="content-wrapper">
   <section class="content-header">
    <h1>
    Company Master Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/companymaster">Company Master Manager</a></li>
    </ol> 
  </section> <!-- content header -->

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">    
        <div class="box">
          <div class="box-header">
            <?php echo $this->Flash->render(); ?>
            <!-- <a href="<?php echo SITE_URL; ?>admin/sizemanager/add">
              <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus" aria-hidden="true"></i>
              Add Size</button></a> -->
              <?php echo $this->Form->create($company, array(
              'class'=>'form-horizontal',
              'enctype' => 'multipart/form-data',
              'controller' => 'CompanyController',
              'id' => 'sevice_form',
              'validate'
            )); ?>   
            <div class="form-group">             

              <div class="col-sm-8">
                <label for="inputEmail3" class="control-label">Company Name<strong style='color:red;'>*</strong></label>
                <?php echo $this->Form->input('cname', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Company Name', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>     
              <div class="col-sm-4">
                <label for="inputEmail3" class="control-label">GST No.<strong style='color:red;'>*</strong></label>
                <?php echo $this->Form->input('gst', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Gst No.', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>    
              <div class="col-sm-4">
                <label for="inputEmail3" class="control-label">Account No.<strong style='color:red;'>*</strong></label>
                <?php echo $this->Form->input('accountno', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Account No', 'autofocus', 'autocomplete' => 'off')); ?>
              </div>  
              
              
              <div class="col-sm-4">
                <label for="inputEmail3" class="control-label">Tin Date<strong style='color:red;'>*</strong></label>
                <?php echo $this->Form->input('tin_datenn', array('class' => 'form-control input1','label'=>false,'placeholder'=>'From Date','id'=>'datepicker1','autocomplete'=>'off','readonly','value'=>date('Y-m-d', strtotime($company['tin_date'])))); ?>
              </div> 

           
              <div class="col-sm-4">
                <label for="inputEmail3" class="control-label">IFSC Code<strong style='color:red;'>*</strong></label>
                <?php echo $this->Form->input('ifsc', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter IFSC Code', 'autofocus', 'autocomplete' => 'off')); ?>
              </div> 

              <div class="col-sm-12">
              <label for="inputEmail3" class="control-label">Address<strong style='color:red;'>*</strong></label>
                <?php echo $this->Form->textarea('address', array('class' => 'form-control','type'=>'text','required','label'=>false,'placeholder'=>'Enter Description','autofocus','autocomplete'=>'off')); ?>
              </div>
            
            </div>
            <div class="box-footer">
            

            <?php
            if(isset($company['id'])){
              echo $this->Form->submit(
                'Update', 
                array('class' => 'btn btn-info pull-right','id'=> 'formsubmitbtn', 'title' => 'Update')
              ); }else{ 
                echo $this->Form->submit(
                  'edit', 
                  array('class' => 'btn btn-info pull-right','id'=> 'formsubmitbtn', 'title' => 'Edit')
                );
              }
              ?>

            <?php echo $this->Form->end(); ?>
            </div>
            </div><!-- /.box-header -->
            </div>
            <script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script>
  $( function() {
    var dateFormat = 'dd-mm-yy',
    from = $( "#datepicker1" )
    .datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    })
    .on( "change", function() {
      to.datepicker( "option", "minDate", getDate( this ) );
    }),
    to = $( "#datepicker2" ).datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    })
    .on( "change", function() {
      from.datepicker( "option", "maxDate", getDate( this ) );
    });
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
      return date;
    }
  } );
</script>
<script>
    $(document).ready(function () {
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>