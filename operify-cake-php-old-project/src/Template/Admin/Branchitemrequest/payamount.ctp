<a href="#" class= "cash_pay">Cash </a>
<a href="#" class= "cheque_pay">Cheque </a>

<h5>Total Payable Amount : 500<h5>
                    <div class="box-body">
                        <?php echo $this->Form->create($item, array(
                            'class'=>'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            
                          )); ?>
                        <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">
                       


                            <div class="col-md-3">

                                <label for="inputEmail3" class="col-md-12 control-label"
                                    style="text-align: left !important;">Pay Amount</label>

                                <div class="col-md-12">


                                    <?php echo $this->Form->input('pay_amount', array('class' => 'form-control category_id','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off')); ?>
                                </div>

                            </div>






                            <div class="col-md-3">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;"> Date</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('pay_date', array('class' => 'form-control category_id','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','id'=>'datepicker1','autocomplete'=>'off','readonly')); ?>
                            </div>

                            </div>


                            <div class="col-md-3">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Discount Amount</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('discount', array('class' => 'form-control','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off')); ?>
                            </div>

                            </div>


                            <div class="col-md-3">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Hosteler indent No:</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('indent_no', array('class' => 'form-control','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off')); ?>
                            </div>

                            </div>




                            <div class="col-md-3">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Description</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('pay_remark', array('class' => 'form-control', 'type' => 'textarea',   'label' => false,  'autofocus', 'autocomplete' => 'off','required')); ?>
                            </div>

                            </div>

                            <div class="col-md-3">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Manual Reciept No:</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('manual_receipt_no', array('class' => 'form-control', 'type' => 'text',   'label' => false,  'autofocus', 'autocomplete' => 'off','required')); ?>
                            </div>

                            </div>

                 
                        <div class="col-md-3">

                        <label for="inputEmail3" class="col-md-12 control-label"
                        style="text-align: left !important;"> Manual Reciept Date</label>

                        <div class="col-md-12">


                        <?php echo $this->Form->input('manual_receipt_date', array('class' => 'form-control category_id','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','id'=>'datepicker3','autocomplete'=>'off','readonly')); ?>
                        </div>

                        </div>

                         <div class = "cheque_data" style = "display:none"> 
                          
                        <div class="col-md-3">

                        <label for="inputEmail3" class="col-md-12 control-label"
                        style="text-align: left !important;"> Bank</label>

                        <div class="col-md-12">


                        <?php echo $this->Form->input('bank_name', array('class' => 'form-control category_id','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','autocomplete'=>'off')); ?>
                        </div>

                        </div>




                        <div class="col-md-3">

                        <label for="inputEmail3" class="col-md-12 control-label"
                        style="text-align: left !important;">Branch</label>

                        <div class="col-md-12">


                        <?php echo $this->Form->input('bankbranch_name', array('class' => 'form-control category_id','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','autocomplete'=>'off')); ?>
                        </div>

                        </div>




                        <div class="col-md-3">

                        <label for="inputEmail3" class="col-md-12 control-label"
                        style="text-align: left !important;">Cheque No</label>

                        <div class="col-md-12">


                        <?php echo $this->Form->input('chequeno', array('class' => 'form-control','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','autocomplete'=>'off')); ?>
                        </div>

                        </div>




                        <div class="col-md-3">

                        <label for="inputEmail3" class="col-md-12 control-label"
                        style="text-align: left !important;">Cheque Date</label>

                        <div class="col-md-12">


                        <?php echo $this->Form->input('pay_date', array('class' => 'form-control category_id','type'=>'text','value'=>'','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','id'=>'datepicker2','autocomplete'=>'off','readonly')); ?>
                        </div>

                        </div>
                        </div>


                        <div class="col-md-12">


                            <?php
                        if(isset($item['id'])){
                        echo $this->Form->submit(
                            'Update', 
                            array('class' => 'btn btn-info pull-right', 'title' => 'Update')
                        ); }else{ 
                            echo $this->Form->submit(
                            'Submit', 
                            array('class' => 'btn btn-info pull-right', 'title' => 'Add')
                            );
                        }
                        ?>

                        </div>
                        <?php echo $this->Form->end(); ?>
                     </div>

                        </div>
                    </div>

                 


                       



                </div>

                <script>
  $( function() {
    $( "#datepicker1" ).datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    });

    $( "#datepicker2" ).datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    });


    $( "#datepicker3" ).datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    });


  } );
</script>


<script>
     $("#bank-name").prop("disabled", true);
     $("#bankbranch-name").prop("disabled", true);
     $("#chequeno").prop("disabled", true);
     $("#datepicker2").prop("disabled", true);
     
$(".cheque_pay").click(function () {
        $(".cheque_data").css("display","block")
        $("#bank-name").prop("disabled", false);
     $("#bankbranch-name").prop("disabled", false);
     $("#chequeno").prop("disabled", false);
     $("#datepicker2").prop("disabled", false);
        // $("#").show();   
    });
    $(".cash_pay").click(function () {
        $(".cheque_data").css("display","none")
        $("#bank-name").prop("disabled", true);
     $("#bankbranch-name").prop("disabled", true);
     $("#chequeno").prop("disabled", true);
     $("#datepicker2").prop("disabled", true);
        // $("#").show();   
    });
    </script>