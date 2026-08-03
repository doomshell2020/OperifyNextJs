<style>
.input_fields_wrap .form-control {
    margin-bottom: 15px;
}
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Branch Request Items
            <?php 

// pr($item);die;
?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/Branchitemrequest"><i class="fa fa-home"></i>Home</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <!-- right column -->
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-info">



                    <!-- /.box-footer -->

                    <br><br>
                    <table class="table table-bordered table-striped">
                        <thead style="background:#333; color:#fff;">
                            <tr>
                                <th>S.No.</th>
                                <th>Category</th>
                                <th>Item Name</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Amount</th>
<!-- 
                                <th>Action</th> -->

                            </tr>
                        </thead>

                        <tbody>
                            <?php $page = $this->request->params['paging']['']['page'];
                  $limit = $this->request->params['paging']['']['perPage'];
                  $counter = ($page * $limit) - $limit + 1;
                  // pr($item); die;
                  if(isset($requestdetails) && !empty($requestdetails)){ 
                    foreach($requestdetails as $intusr){ //pr($intusr);
                      ?>
                            <tr>
                                <td><?php echo $counter;?></td>
                                <td> <?php echo $intusr['itemcategory']['category_name']  ?></td>
                                <td> <?php echo $intusr['additem']['item_name']; ?></td>
                                <td> <i class="fas fa-rupee-sign"></i><?php echo $intusr['additem']['sale_price']; ?></td>
                                <td> <?php echo $intusr['item_qty']; ?></td>
                                <td>  <?php echo $intusr['discount']; ?></td>
                                <td> <?php echo "0"; ?></td>
                                <td><i class="fas fa-rupee-sign"></i> <?php echo  $intusr['item_qty'] * $intusr['additem']['sale_price']; ?></td>
                              <?php  
                              $total_unit += $intusr['additem']['sale_price'];
                              $total_qty += $intusr['item_qty'];
                              $total_amount += $intusr['item_qty'] * $intusr['additem']['sale_price']; ?>
                                <!-- <td>
                                    &nbsp;<?php
                           /*  echo $this->Html->link('', [
                              'action' => 'delete',
                              $intusr->id
                            ],['class'=> 'glyphicon glyphicon-remove','style'=>'font-size: 21px;'	
                 ,"onClick"=>"javascript: return confirm('Are you sure do you want to delete this Item')"]); */ ?>
                                    </strong></td> -->


                            </tr>
                            <?php $counter++; } ?>
                            <tr> 

                            <td></td>
                                <td></td>
                                <td>Total</td>
                                <td><i class="fas fa-rupee-sign"></i> <?php echo $total_unit; ?></td>
                                <td></i><?php echo $total_qty; ?></td>
                                <td> </td>
                                <td></td>
                                <td><i class="fas fa-rupee-sign"></i> <?php echo  $total_amount; ?></td>

                            </tr>
                        
                        
                       <?php }else {   ?>

                            <tr>
                                <td colspan="4" style="text-align:center;">
                                    <h4> No Item Added </h4>
                                </td>
                            </tr>

                            <?php } ?>
                        </tbody>

                    </table>

                    <?php echo $this->Flash->render(); ?>
                    <div class="box-header with-border">
                        <!-- <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php //if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Item';} ?></h3> -->
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->

                    <div class="box-body">
                        <?php echo $this->Form->create($item, array(
                            'class'=>'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'validate'
                          )); ?>
                        <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">
                        <!-- <div class="col-md-3">

<label for="inputEmail3" class="col-md-12 control-label"
    style="text-align: left !important;">Customer Type</label>

<div class="col-md-12">

 <input type = "radio" name = "customer_type" value = "Customer" checked>Customer
 <input type = "radio" name = "customer_type" value = "Other">Other
</div>

</div>    -->


                            <div class="col-md-4">

                                <label for="inputEmail3" class="col-md-12 control-label"
                                    style="text-align: left !important;">Name</label>

                                <div class="col-md-12">
<?php $customer_data = explode("_",$approve_req['branch_name']);

?>

                                    <?php echo $this->Form->input('customer_name', array('class' => 'form-control category_id','type'=>'text','value'=>$customer_data['1'],'label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','readonly')); ?>
                                </div>

                            </div>

                            <div class="col-md-4">

<label for="inputEmail3" class="col-md-12 control-label"
    style="text-align: left !important;">Sale Date</label>

<div class="col-md-12">
<?php $current_Date = date('d-m-Y'); ?>

    <?php echo $this->Form->input('sale_date', array('class' => 'form-control category_id','type'=>'text','value'=>$current_Date,'label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off','id'=>'datepicker1','autocomplete'=>'off','readonly')); ?>
</div>

</div>

<div class="col-md-4">

<label for="inputEmail3" class="col-md-12 control-label"
    style="text-align: left !important;">Upload Description</label>

<div class="col-md-12">


<?php echo $this->Form->input('upload_description', array('class' => 'form-control', 'type' => 'file',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?> 
</div>

</div>

<div class="col-md-12">

<label for="inputEmail3" class="col-md-12 control-label"
    style="text-align: left !important;">Remark</label>

<div class="col-md-12">


<?php echo $this->Form->input('description', array('class' => 'form-control', 'type' => 'textarea',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
</div>

</div>

                 

<div class="col-md-12" style = "margin-top: 7px;">

<!-- <a href = "<?php //echo SITE_URL; ?>admin/branchitemrequest/payamount/<?php //echo $id; ?>" class = "btn btn-success payrequest">Pay</a> -->
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

                        </div>
                    </div>

                 


                        
                        <?php echo $this->Form->end(); ?>
                     </div>



                </div>

            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>





<script>
  $( function() {
    $( "#datepicker1" ).datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    });
    

  } );
</script>





<div class="modal fade" id="paysorts">
	<div class="modal-dialog">
		<div class="modal-content">
    <div class="modal-header">
          <h4 class="modal-title">Pay Amount</h4>
        </div>
			<div class="modal-body">
            <a href="#" class= "cash_pay">Cash </a>
<a href="#" class= "cheque_pay">Cheque </a>

<h5>Total Payable Amount : <?php echo $approve_req['totalamount']; ?><h5>
                    <div class="box-body">
                        <?php echo $this->Form->create($item, array(
                            'class'=>'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'controller'=>'branchitemrequest',
                            'action'=>'payamount'
                            
                          )); ?>
                        <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">
                       


                            <div class="col-md-3">

                                <label for="inputEmail3" class="col-md-12 control-label"
                                    style="text-align: left !important;">Pay Amount</label>

                                <div class="col-md-12">

                            <input type ="hidden" name = "id" value = "<?php echo $id; ?>" >
                                    <?php echo $this->Form->input('pay_amount', array('class' => 'form-control category_id','type'=>'text','value'=>'','label'=>false,'autofocus','autocomplete'=>'off','value'=>$approve_req['totalamount'],'readonly')); ?>
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


                            <?php echo $this->Form->input('pay_remark', array('class' => 'form-control', 'type' => 'textarea',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
                            </div>

                            </div>

                            <div class="col-md-3">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Manual Reciept No:</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('manual_receipt_no', array('class' => 'form-control', 'type' => 'text',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
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

			</div>
		</div>
	</div>
</div>


<!-- Relation Beetween Location and Sublocation  -->
<script>
$(document).ready(function() {
    $('.category_request').on('click', function(e) {

        e.preventDefault();
        var category_id = $('.category_id').val();
        var category_qty = $('.category_qty').val();

        $(".error").hide();

        var hasError = false;
        if (category_id == '') {
            $(".category_id").after(
                '<span class="error" style = "color:red;">Select Atleast one category</span>');
            hasError = true;
        }

        if (category_qty == '' || category_qty <= 0) {
            $(".category_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
            hasError = true;
        }
        if (hasError == true) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL;?>/admin/branchitemrequest/categoryrequest',
            data: {
                'category_id': category_id,
                'category_qty': category_qty
            },
            success: function(data) {
                location.reload();
            },

        });
    });



    $('.item_request').on('click', function(e) {
        e.preventDefault();

        var item_id = $('.item_id').val();
        var item_qty = $('.item_qty').val();

        $(".error").hide();

        var hasError = false;
        if (item_id == '') {
            $(".item_id").after(
                '<span class="error" style = "color:red;">Select Atleast one category</span>');
            hasError = true;
        }

        if (item_qty == '' || item_qty <= 0) {
            $(".item_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
            hasError = true;
        }
        if (hasError == true) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL;?>/admin/branchitemrequest/itemrequest',
            data: {
                'item_id': item_id,
                'item_qty': item_qty
            },
            success: function(data) {
                location.reload();
            },

        });
    });

});

$(document).ready(function() {
    $('#location-name').on('change', function() {
        var id = $('#location-name').val();
        // alert(id);
        $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL;?>/admin/additem/find_sublocation',
            data: {
                'id': id
            },
            success: function(data) {
                $('#sub-location').empty();
                $('#sub-location').html(data);
            },

        });
    });
});
</script>
<!-- end  -->

<script>
$(function() {
    $("#imagename").change(function() {
        // alert('hello');
        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.pdf|.jpg|.png)$/;
        if (regex.test($(this).val().toLowerCase())) {
            return true;

        } else {
            $('#imagename').val('');
            alert("Please upload pdf/jpg/png files.");
        }
    });
});
</script>


<script type="text/javascript">
$(document).ready(function() {
    $("#category_ids").on('change', function() {
        var id = $(this).val();
        $("#subcategory").find('option').remove();
        //$("#city").find('option').remove();
        if (id) {
            var dataString = id;
            $.ajax({
                type: "POST",
                url: '<?php echo SITE_URL;?>/admin/additem/getsubcategory',
                data: {
                    'dataString': id
                },
                cache: false,
                success: function(html) {
                    //alert(html);
                    $('<option>').val("").text("Select Sub Category").appendTo($(
                        "#subcategory"));
                    $.each(html, function(key, value) {
                        $('<option>').val(key).text(value).appendTo($(
                            "#subcategory"));
                    });
                }
            });
        }
    });
});
</script>

<script type="text/javascript">
$(document).ready(function() {
    $("#location").on('change', function() {
        var id = $(this).val();
        $("#sublocation").find('option').remove();
        //$("#city").find('option').remove();
        if (id) {
            var dataString = id;
            $.ajax({
                type: "POST",
                url: '<?php echo SITE_URL;?>/admin/additem/getsublocation',
                data: {
                    'dataString': id
                },
                cache: false,
                success: function(html) {
                    //alert(html);
                    $('<option>').val("").text("Select Sub Location").appendTo($(
                        "#sublocation"));
                    $.each(html, function(key, value) {
                        $('<option>').val(key).text(value).appendTo($(
                            "#sublocation"));
                    });
                }
            });
        }
    });
});
</script>

<script>
$('#mrp').on('change', function() {
    var amou = $('#saleprice').val();
    if ($(this).val() < amou) {
        alert("Mrp should be greater then sale price");
        $(this).val('');
    }
});
</script>

<script>
$('#saleprice').on('change', function() {
    var mrp = $('#mrp').val();
    if ($(this).val() > mrp) {
        alert("Sale Price should be less then mrp");
        $(this).val('');
    }
});
</script>

<style>
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

#testUL {
    position: relative;
}

#testUL ul li a {
    color: black;
}
</style>
<script>
$(function() {
    $('.secrh-retail').bind('keyup', function() {
        var pos = $(this).val();
        //alert(pos);
        var check = 0;
        //var catid=$('#subcategory').val();
        //alert(pos);
        $('#testUL').show();
        $('#retail_ids').val('');
        var count = pos.length;
        if (count > 0) {
            $.ajax({
                type: 'POST',
                url: '<?php echo ADMIN_URL; ?>branchitemrequest/getitemname',
                data: {
                    'fetch': pos,
                    'check': check
                },
                success: function(data) {
                    //alert(data);
                    $('#testUL ul').html(data);
                },
            });
        } else {
            $('#testUL').hide();
        }
    });
});
</script>
<script>
function cllbckretail(name, id) {
    $('.secrh-retail').val(name);
    $('#testUL').hide();
    //alert(cid);
    $.ajax({
        type: 'POST',
        url: '<?php echo ADMIN_URL; ?>storeitems/getitemdetail',
        data: {
            'fetch': id
        },
        success: function(data) {
            //console.log(data);
            var json = $.parseJSON(data);
            //alert(json.sale_price);
            $('#retail_ids').val(json.id);
            $('#sale-price').val(json.sale_price);
        },
    });

}
</script>

<?php $message=$this->Flash->render('pay_request'); ?>

<?php if($message){  ?>
<script>
    $( document ).ready(function() {
        $('#paysorts').modal('show');
    //$('#myModal').modal('show');
});
    </script>
    <?php } ?>

    <script>
    $('#discount').on('change', function() {
        var discount_amount = $(this).val();
        var pay_amount = $('#pay-amount').val();
       
        var disocunt_amount_data =  pay_amount-discount_amount;
        var pay_amount = $('#pay-amount').val(disocunt_amount_data);
    });
</script>