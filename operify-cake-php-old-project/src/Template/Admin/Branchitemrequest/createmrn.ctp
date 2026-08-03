<style>
.input_fields_wrap .form-control {
    margin-bottom: 15px;
}
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
          Create MRN (Material Received Note)
            <?php 

// pr($item);die;
?>
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="<?php //echo SITE_URL; ?>admin/Solditems"><i class="fa fa-home"></i>Home</a></li>
        </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">

            <!-- right column -->
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-info">
                    <?php echo $this->Flash->render(); ?>
                    <div class="box-header with-border">
                        <!-- <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php //if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Item';} ?></h3> -->
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php //pr($mrn); ?>
                    <div class="box-body">
                        <?php echo $this->Form->create('', array(
                            'class'=>'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'controller'=>'branchitemrequest',
                            'action'=>'mrnadd'
                          )); ?>
                        <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">


                            


                            <div class="col-md-4">

                                <label for="inputEmail3" class="col-md-12 control-label"
                                    style="text-align: left !important;">MRN NO.</label>

                                <div class="col-md-12">

                          <?php if($mrn){
                               $mrn = $mrn['id']+1;; 
                              }else{
                                $mrn = 1001; 
                          } ?>
                                    <?php echo $this->Form->input('mrn_no', array('class' => 'form-control category_id','type'=>'text','label'=>false,'autofocus','autocomplete'=>'off','value'=>$mrn,'readonly')); ?>
                                    <input type = "hidden" name = "branchrequest_id" value = "<?php echo $branch_request['id']; ?>">
                                </div>

                            </div>


                            <div class="col-md-4">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">MRN Date</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('mrn_date', array('class' => 'form-control category_id','type'=>'text','value'=>date('d-m-Y'),'label'=>false,'autofocus','autocomplete'=>'off','id'=>'datepicker6','autocomplete'=>'off','readonly',)); ?>
                            </div>

                            </div>


                            <div class="col-md-4">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Bill/challan No</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('bill_challan_no', array('class' => 'form-control category_id','type'=>'text','label'=>false,'autofocus','autocomplete'=>'off','value'=>$mrn)); ?>
                            </div>

                            </div>


                            <div class="col-md-4">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Purchase Order No</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('purchase_order_no', array('class' => 'form-control category_id','type'=>'text','label'=>false,'autofocus','autocomplete'=>'off','value'=>$id,'readonly')); ?>
                            </div>

                            </div>


                            <div class="col-md-4">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Suplier Name</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('suppliername', array('class' => 'form-control category_id','type'=>'text','label'=>false,'autofocus','autocomplete'=>'off','value'=>"Canvas International Pre School (Unit of Ingenious Edu Scholars Private Limited)",'readonly')); ?>
                            </div>

                            </div>

                            <div class="col-md-4">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Bill/Challan Date</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('bill_challan_date', array('class' => 'form-control category_id','type'=>'text','label'=>false,'autofocus','autocomplete'=>'off','value'=>date('d-m-Y'),'readonly')); ?>
                            </div>

                            </div>

                         


                            <div class="col-md-4">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Transport Charges</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('transport_charges', array('class' => 'form-control category_id','type'=>'number','label'=>false,'autofocus','autocomplete'=>'off')); ?>
                            </div>

                            </div>


                            <div class="col-md-4">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Other Charges</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('other_charges', array('class' => 'form-control category_id','type'=>'number','label'=>false,'autofocus','autocomplete'=>'off')); ?>
                            </div>

                            </div>
                            <div class="col-md-4">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">BILL Type</label>

                            <div class="col-md-12">

                            <input type = "radio" name = "bill_type" value = "Bill" checked> Bill
                            <input type = "radio" name = "bill_type" value = "Bill"> Challan
                            </div>

                            </div>


                            <div class="col-md-8">

                            <label for="inputEmail3" class="col-md-12 control-label"
                            style="text-align: left !important;">Remark</label>

                            <div class="col-md-12">


                            <?php echo $this->Form->input('remark', array('class' => 'form-control category_id','type'=>'textarea','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off')); ?>
                            </div>

                            </div>
                            



                        </div>
                    </div>

                    <div class="box-body">
               

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


                    <!-- /.box-footer -->

<br><br>
                    <table class="table table-bordered table-striped">
                        <thead style= "background:#333; color:#fff;">
                            <tr>
                                <th>S.No.</th>
                                <th>Item Name</th>
                                <th>Unit Rate</th>
                                <th>Quantity</th>
                                <th>Item Amount</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Tax Amount</th>
                                <th>Taxable Amount</th>
                                <th>Amount</th>

                            </tr>
                        </thead>

                        <tbody>
                            <?php $page = $this->request->params['paging']['']['page'];
                            $limit = $this->request->params['paging']['']['perPage'];
                            $counter = ($page * $limit) - $limit + 1;
                  // pr($item); die;
                  if(isset($branch_request) && !empty($branch_request)){ 
                    foreach($branch_request['branchrequestdetail'] as $intusr){ //pr($intusr); die;
                      ?>
                                <?php 
                                $totalitem_amount = $intusr['item_amount']*$intusr['item_qty'];
                                ?>
                            <tr>
                                <td><?php echo $counter;?></td> 
                                <td> <?php echo ucfirst(strtolower($intusr['additem']['item_name']));?></td>
                                <td align="right"> <?php echo sprintf('%.2f', $intusr['item_amount']); ?></td>
                                <td align="right"> <?php echo $intusr['item_qty']; ?></td>
                                <td align="right"> <?php echo $totalitem_amount; ?></td>
                                <td align="right"> <?php

                                if($intusr['discount']){
                                $discount =$intusr['discount']*$intusr['item_qty'];
                                }else{
                                $discount = 0;
                                }

                               
                                echo  sprintf('%.2f', $discount);  ?></td>
                                <td align="right"> <?php 
                                $tax = $intusr['item_tax'];
                                    
                              
                                echo sprintf('%.2f', $tax)."%";
                                ?></td>
                                <?php 
                                 $total=$intusr['item_amount']*$intusr['item_qty']- $discount;
                                 $total_tax = $total*$tax/100;
                                 $total_tax_data = $total*$tax/100;
                                ?>
                        <td align="right">₹ <?php echo  sprintf('%.2f', $total_tax); ?></td>
                        <td align="right">₹ <?php echo  sprintf('%.2f', $total); ?></td>
                        <td align="right">₹ <?php echo  sprintf('%.2f', $total+$total_tax); ?></td>


                               <?php 
                                $qty+= $intusr['item_qty'];
                                $unit_rate += $intusr['item_amount'];
                                $totaltem_amount +=$totalitem_amount;

                                $totaltaxable += $total;
                                $totalamt +=  $total+$total_tax;
                                $total_taxss += $total_tax;
                                ?>
                            </tr>
                            <?php $counter++; } }else {   ?>

                                <tr>
                                <td colspan = "4" style= "text-align:center;"> <h4> No Item Added </h4> </td>
                                </tr>
                              

                            <?php } ?>
                            <tr>
                           
                              <td></td>
                              <?php 
                              ?>
                                  <td><b>Total</b></td>
                                  <td align="right"><b>₹ <?php echo sprintf('%.2f',$unit_rate); ?></b></td>
                                  <td align="right"><b></i><?php echo $qty; ?></b></td>
                                  <td align="right"><b>₹ <?php echo $totaltem_amount; ?></b></td>
                                  <td></td>
                                  <td></td>
                                  <td align="right"></i><b>₹<?php echo sprintf('%.2f',  $total_taxss); ?></b></td>
                                  <td align="right"><b>₹ <?php echo $totaltaxable; ?></b></td>

                                  <td align="right"><b>₹ <?php echo  sprintf('%.2f',round($totalamt)); ?></b></td>
                            <?php  
                              ?>
                              
                           </tr>

                        </tbody>

                    </table>

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
    $( "#datepicker6" ).datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    });
});
    </script>






<!-- Relation Beetween Location and Sublocation  -->
<script>



$(document).ready(function() {
    $('.category_request').on('click', function(e) {
        
      e.preventDefault();
        var category_id = $('.category_id').val();
        var category_qty = $('.category_qty').val();

        $(".error").hide();

      var hasError = false;
      if(category_id == '')
    {
        $(".category_id").after('<span class="error" style = "color:red;">Select Atleast one category</span>');
        hasError = true;
    }

    if(category_qty == '' || category_qty <= 0 )
    {
        $(".category_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
        hasError = true;
    }
        if(hasError == true)
    {
    return false;
    }

        $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL;?>/admin/solditems/categoryrequest',
            data: {
                'category_id': category_id,'category_qty': category_qty
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
        if(item_id == '')
        {
            $(".item_id").after('<span class="error" style = "color:red;">Select Atleast one category</span>');
            hasError = true;
        }

        if(item_qty == '' || item_qty <= 0 )
        {
            $(".item_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
            hasError = true;
        }
        if(hasError == true)
        {
         return false;
        }

        $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL;?>/admin/branchitemrequest/itemrequest',
            data: {
              'item_id': item_id,'item_qty': item_qty
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
<script>
  $( function() {
    $( "#datepicker1" ).datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    });

  } );
</script>