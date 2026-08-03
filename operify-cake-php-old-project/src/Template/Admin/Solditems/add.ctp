<style>
    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }

    .modal-header {
        background-color: #2d95e3 !important;
        display: flex;
        align-items: center;
    }

    .cash_pay,
    .cheque_pay,
    .online_pay {
        background: #21b354;
        padding: 5px 15px;
        color: #fff !important;
        margin-left: 10px;
        border-radius: 3px;
    }

    .control-label {
        margin-bottom: 8px !important;
    }

    .btn.btn-primary.pull-left {
        background-color: #21b354;
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Sold items
            <?php

            // pr($item);die;
            ?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/Solditems"><i class="fa fa-home"></i>Home</a></li>
        </ol>
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
                        <!-- <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php //if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Item';} 
                                                                                                        ?></h3> -->
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->

                    <div class="box-body">
                        <?php echo $this->Form->create($item, array(
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'validate'
                        )); ?>
                        <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">


                            <div class="col-md-4">

                                <label for="inputEmail3" class="col-md-12 control-label" style="text-align: left !important;">Category</label>
                                <div class="col-md-12">
                                    <?php echo $this->Form->input('category_name', array('class' => 'form-control category_id', 'type' => 'select', 'options' => $categary, 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off')); ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Quantity</label>
                                <div class="col-md-12">
                                    <?php echo $this->Form->input('quantity', array('class' => 'form-control category_qty', 'type' => 'number', 'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <a href="" class="btn btn-danger category_request">Add</a>
                            </div>

                        </div>
                    </div>

                    <div class="box-body">

                        <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">
                            <div class="col-md-4">
                                <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Item Name</label>
                                <div class="col-md-12">
                                    <input type="hidden" name="item_id" id="retail_ids">
                                    <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retail item_id', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off', 'id' => 'itemname')); ?>
                                    <div id="testUL" style="display:none;">
                                        <ul></ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">

                                <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Quantity</label>

                                <div class="col-md-12">
                                    <?php echo $this->Form->input('quantity', array('class' => 'form-control item_qty ', 'type' => 'number', 'label' => false, 'autofocus', 'autocomplete' => 'off')); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <a href="" class="btn btn-danger item_request">Add</a>
                            </div>

                            <!-- <div class="col-sm-4">
                <label for="inputEmail3" class="col-sm-12 control-label" >Bill Type:</label><br>
                <label class="radio-inline">

        <input type="radio" name="billtype" class="mode radio-inline checkstr" value="Sale">&nbsp;Sale
        </label>
        <label class="radio-inline">
        <input type="radio" name="billtype" class="mode radio-inline checkstr" value="Return">&nbsp;Return
        </label>
        </div>  


        <div class="col-sm-4">
                <label for="inputEmail3" class="col-sm-12 control-label" >User Type:</label><br>
                <label class="radio-inline">

        <input type="radio" name="billtype" class="mode radio-inline checkstr" value="Sale">&nbsp;Student
        </label>
        <label class="radio-inline">
        <input type="radio" name="billtype" class="mode radio-inline checkstr" value="Return">&nbsp;Return
        </label> -->
                            <!-- </div>   -->

                        </div>
                        <div class="row" style="margin-top:20px">
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <label for="inputEmail3" class="control-label">Name</label>
                                    <input type="hidden" name="stu_name" id="retail_id" value="id">

                                    <?php echo $this->Form->input('name', array('class' => 'form-control secrh-students stu_name', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Student Name', 'required')); ?>
                                    <div id="test" style="display:none;">
                                        <ul></ul>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-4">

                                <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Sale Date:</label>

                                <div class="col-md-12">
                                    <?php echo $this->Form->input('sale_date', array('class' => 'form-control', 'type' => 'text', 'label' => false,  'autofocus', 'autocomplete' => 'off', 'id' => '', 'required', 'value' => date('d-m-Y'), 'readonly')); ?>
                                </div>
                            </div>

                        </div>
                        <div class="row" style="margin-top:20px">
                            <div class="col-md-8">

                                <label for="inputEmail3" class="col-md-12 control-label" style="text-align: left !important;">Description</label>

                                <div class="col-md-12">
                                    <?php echo $this->Form->input('description', array('class' => 'form-control', 'type' => 'textarea',   'label' => false,  'autofocus', 'autocomplete' => 'off', 'required')); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">


                        <?php
                        if (isset($item['id'])) {
                            echo $this->Form->submit(
                                'Update',
                                array('class' => 'btn btn-info pull-right', 'title' => 'Update')
                            );
                        } else {
                            echo $this->Form->submit(
                                'Submit',
                                array('class' => 'btn btn-info pull-right', 'title' => 'Add')
                            );
                        }
                        ?>
       
                    </div>

                </div>


                <!-- /.box-footer -->

                <br><br>
                <table class="table table-bordered table-striped" width="100%">
                    <thead style="background:#333; color:#fff;">
                        <tr>
                            <th width="5%">S.No.</th>
                            <th width="5%">Category</th>
                            <th width="55%">Item Name</th>
                            <th>Stock Available</th>
                            <th width="5%">Unit Rate</th>
                            <th width="3%">Quantity</th>

                            <th width="5%">Discount</th>
                            <th width="5%">Tax</th>

                            <th width="5%">Tax Amount</th>
                            <th width="5%">Taxable Amount</th>

                            <th width="5%"> Amount</th>
                            <th width="2%">Action</th>

                        </tr>
                    </thead>

                    <tbody>
                        <?php $page = $this->request->params['paging']['']['page'];
                        $limit = $this->request->params['paging']['']['perPage'];
                        $counter = ($page * $limit) - $limit + 1;
                        // pr($item); die;

                        if (isset($temp_item) && !empty($temp_item)) {
                            $total_amount = 0;
                            foreach ($temp_item as $intusr) { //print_r($intusr);  
                        ?>
                                <?php  //$gname=$this->Comman->finditems($intusr['item_id']); 
                                //pr($gname); //die;
                                ?>
                                <?php
                                $total_amt = $intusr['additem']['sale_price'] * $intusr['quantity'];
                                $total_amt_data =  $total_amt * $intusr['additem']['taxmaster']['tax'] / 100;
                                $total_fund_amt = $total_amt_data + $total_amt;
                                ?>
                                <?php
                                $stock_avail = $this->comman->stockavailable($intusr['additem']['id']);
                                $stock_avail_data = $stock_avail;
                                if ($stock_avail_data == "0") {
                                    $stock_check_data = 1;
                                }
                                ?>
                                <?php if ($stock_avail_data == "0") { ?>
                                    <tr class="stock_aval">
                                    <?php } else { ?>
                                    <tr class="">
                                    <?php } ?>
                                    <td><?php echo $counter; ?></td>

                                    <td> <?php echo $intusr['itemcategory']['category_name'];  ?></td>
                                    <td> <?php echo ucfirst(strtolower($intusr['additem']['item_name'])); ?></td>
                                    <td> <?php echo $stock_avail_data; ?></td>
                                    <td align="right"> <?php echo  sprintf('%.2f', $intusr['additem']['sale_price']); ?></td>
                                    <td align="right"> <?php echo $intusr['quantity']; ?></td>
                                    <td align="right"> <?php
                                                        if ($intusr['discount_amount']) {
                                                            $discount = $intusr['discount_amount'] * $intusr['quantity'];
                                                        } else {
                                                            $discount = 0;
                                                        }


                                                        echo  sprintf('%.2f', $discount); ?></td>
                                    <td align="right"> <?php
                                                        if ($intusr['additem']['taxmaster']['tax']) {
                                                            $tax = $intusr['additem']['taxmaster']['tax'];
                                                        } else {
                                                            $tax = 0;
                                                        }

                                                        echo sprintf('%.2f', $tax) . "%";


                                                        ?></td>


                                    <?php
                                    $total = $intusr['additem']['sale_price'] * $intusr['quantity'] - $discount;
                                    // echo $total; die;

                                    $total_tax = $total * $tax / 100;
                                    //echo $total_tax; die;
                                    ?>
                                    <td align="right"> <?php echo sprintf('%.2f', $total_tax); ?></td>
                                    <td align="right"> <?php echo sprintf('%.2f',  $total); ?></td>
                                    <td align="right"> <?php echo sprintf('%.2f', round($total + $total_tax)); ?></td>


                                    <td>
                                        &nbsp;
                                        <?php
                                        echo $this->Html->link('', [
                                            'action' => 'delete',
                                            $intusr->id
                                        ], [
                                            'class' => 'fas fa-trash-alt', 'style' => 'font-size: 21px; color:#cf1212;', "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                                        ]); ?>
                                        </strong></td>

                                    </tr>
                                    <?php


                                    $unit_rate += $intusr['additem']['sale_price'];
                                    $total_qty += $intusr['quantity'];
                                    $total_amount += $total_fund_amt;

                                    $total_tax_amount += $total_tax;
                                    $total_taxable_amount += $total;
                                    $final_amount += $total + $total_tax;
                                    ?>


                                <?php $counter++;
                            } ?>

                                <input type="hidden" name="category_id" value="<?php echo $temp_item[0]['category_id']; ?>" class="category_id_data">
                                <?php // TOP 
                                ?>
                                <?php if ($temp_item_top) { ?>

                                    <tr>
                                        <td> <?php echo $counter; ?></td>
                                        <td> <?php echo $temp_item_top[0]['itemcategory']['category_name']; ?></td>
                                        <td>
                                            <strong style="text-align:left !important; display:block !important">TOP </strong>
                                            <div style="display:flex;flex-wrap:wrap">

                                                <?php for ($x = 1; $x <= $intusr['quantity']; $x++) { ?>
                                                    <select class="form-control top_prod_data" style="width:170px; margin-right:10px; margin-bottom:10px" name="top_product[]" required>
                                                        <option value="">--Select--</option>

                                                        <?php foreach ($temp_item_top as $topval) { //pr($topval); 
                                                        ?>
                                                            <option value="<?php echo $topval['id']; ?>" data-val="<?php echo $topval['additem']['id']; ?>"><?php echo $topval['additem']['item_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } ?>
                                            </div>

                                        </td>
                                        <td align="right" class="top_stock_check">0</td>
                                        <td align="right" class="top_unit_rate">0</td>
                                        <td align="right"><?php echo $temp_item_top[0]['quantity']; ?></td>
                                        <td align="right" class="top_discount">0</td>
                                        <td align="right" class="top_tax"> 0</td>
                                        <td align="right" class="top_tax_amount"> 0</td>
                                        <td align="right" class="top_taxable_amount"> 0</td>
                                        <td align="right" class="top_total_amount">0</td>


                                        <td>
                                            &nbsp;<?php
                                                    echo $this->Html->link('', [
                                                        'action' => 'topitemdelete',
                                                        $topval->id
                                                    ], [
                                                        'class' => 'fas fa-trash-alt', 'style' => 'font-size: 21px; color:#cf1212;', "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                                                    ]); ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php } ?>

                                <?php // BOTTOM 
                                ?>
                                <?php if ($temp_item_bottom) { ?>

                                    <tr>
                                        <td> <?php echo $counter + 1; ?></td>
                                        <td> <?php echo $temp_item_bottom[0]['itemcategory']['category_name'];; ?></td>
                                        <td>
                                            <strong style="text-align:left !important; display:block !important">Bottom </strong>
                                            <div style="display:flex;flex-wrap:wrap">
                                                <?php for ($x = 1; $x <= $intusr['quantity']; $x++) { ?>

                                                    <select class="form-control bottom_prod_data" style="width:170px; margin-right:10px; margin-bottom:10px" name="bottom_product[]" required>
                                                        <option value="">--Select--</option>
                                                        <?php foreach ($temp_item_bottom as $topval) { //pr($topval); 
                                                        ?>
                                                            <option value="<?php echo $topval['id']; ?>" data-val="<?php echo $topval['additem']['id']; ?>"><?php echo $topval['additem']['item_name']; ?></option>
                                                        <?php } ?>
                                                    </select>

                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td align="right" class="bottom_stock_check">0</td>
                                        <td align="right" class="bottom_unit_rate">0</td>
                                        <td align="right"><?php echo $temp_item_bottom[0]['quantity']; ?></td>
                                        <td align="right" class="bottom_discount">0</td>
                                        <td align="right" class="bottom_tax"> 0</td>
                                        <td align="right" class="bottom_tax_amount"> 0</td>
                                        <td align="right" class="bottom_taxable_amount"> 0</td>
                                        <td align="right" class="bottom_total_amount">0</td>
                                        <td>
                                            &nbsp;<?php
                                                    echo $this->Html->link('', [
                                                        'action' => 'bottomitemdelete',
                                                        $topval->id
                                                    ], [
                                                        'class' => 'fas fa-trash-alt', 'style' => 'font-size: 21px; color:#cf1212;', "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                                                    ]); ?>
                                            </strong>
                                        </td>
                                    </tr>
                                <?php } ?>


                                <?php // Socks 
                                ?>

                                <?php if ($temp_item_socks) { ?>

                                    <tr>
                                        <td> <?php echo $counter + 2; ?></td>
                                        <td> <?php echo $temp_item_socks[0]['itemcategory']['category_name'];; ?></td>
                                        <td>
                                            <strong style="text-align:left !important; display:block !important">Socks </strong>
                                            <div style="display:flex;flex-wrap:wrap">

                                                <?php for ($x = 1; $x <= $intusr['quantity']; $x++) { ?>
                                                    <select class="form-control socks_prod_data" style="width:170px; margin-right:10px; margin-bottom:10px" name="socks_product[]" required>
                                                        <option value="">--Select--</option>

                                                        <?php foreach ($temp_item_socks as $topval) { //pr($topval); 
                                                        ?>
                                                            <option value="<?php echo $topval['id']; ?>" data-val="<?php echo $topval['additem']['id']; ?>"><?php echo $topval['additem']['item_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <?php } ?>
                                        <td align="right" class="socks_stock_check">0</td>
                                        <td align="right" class="socks_unit_rate">0</td>
                                        <td align="right"><?php echo $temp_item_socks[0]['quantity']; ?></td>
                                        <td align="right" class="socks_discount">0</td>
                                        <td align="right" class="socks_tax"> 0</td>
                                        <td align="right" class="socks_tax_amount"> 0</td>
                                        <td align="right" class="socks_taxable_amount"> 0</td>
                                        <td align="right" class="socks_total_amount">0</td>
                                        <td><?php
                                                    echo $this->Html->link('', [
                                                        'action' => 'socksitemdelete',
                                                        $topval->id
                                                    ], [
                                                        'class' => 'fas fa-trash-alt', 'style' => 'font-size: 21px; color:#cf1212;', "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                                                    ]); ?></td>
                                    </tr>
            </div>


            </td>

            &nbsp;
            </strong>
            </td>

        <?php } ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td><b>Total</b></td>
            <td align="right"><b>₹ <span class="unit_price_total"><?php echo sprintf('%.2f', $unit_rate); ?></span></b></td>
            <td align="right"></i><b><?php echo $total_qty + $temp_item_top[0]['quantity'] + $temp_item_bottom[0]['quantity'] + $temp_item_socks[0]['quantity']; ?></b></td>
            <td> </td>
            <td> </td>
            <td align="right"><b>₹ <span class="total_tax_amount_tt"> <?php echo sprintf('%.2f', $total_tax_amount); ?></span></b></td>
            <td align="right"><b>₹ <span class="total_taxable_amount_tt"><?php echo sprintf('%.2f', $total_taxable_amount); ?></span></b></td>
            <td align="right"><b>₹ <span class="final_amount_tt"><?php echo sprintf('%.2f', round($final_amount)); ?></span></b></td>
            <td></td>
        </tr>

    <?php  } else { ?>
        <tr>
            <td colspan="11" style="text-align:center;">
                <h4> No Item Added </h4>
            </td>
        </tr>

    <?php } ?>


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

<?php echo $this->Form->end(); ?>


<input type="hidden" class="unit_rate_total" value="<?php echo $unit_rate; ?>">
<input type="hidden" class="total_tax_amount_total" value="<?php echo $total_tax_amount; ?>">
<input type="hidden" class="total_taxable_amount_total" value="<?php echo $total_taxable_amount; ?>">
<input type="hidden" class="final_data_amount_total" value="<?php echo $final_amount; ?>">



<!-- Relation Beetween Location and Sublocation  -->
<script>
    //top

    $('.top_prod_data').on('change', function(e) {
        e.preventDefault();
        item_name = $(this).find(':selected').data('val');

        categ_name = $('.category_id_data').val();
        temp_item_qty = '<?php echo $temp_item_socks[0]['quantity']; ?>';

        $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL; ?>/admin/solditems/toprequest_check',
            data: {
                'item_name': item_name,
                'categ_name': categ_name
            },
            success: function(data) {
                obj = JSON.parse(data);
                item_name = $('.top_unit_rate').text(obj.saleprice + '.00');
                discount = obj.discount * temp_item_qty;
                $('.top_stock_check').text(obj.stockavl);

                item_name = $('.top_discount').text(discount);
                item_name = $('.top_tax').text(obj.tax + '.00%');
                tax = obj.tax;

                total = obj.saleprice * temp_item_qty - discount;
                total_tax = total * tax / 100;
                item_name = $('.top_tax_amount').text((total_tax).toFixed(2));
                item_name = $('.top_taxable_amount').text((total).toFixed(2));
                item_name = $('.top_total_amount').text((total + total_tax).toFixed(2));

                //unit rate
                tot_final = parseFloat($('.unit_rate_total').val()) + parseFloat(obj.saleprice);
                $('.unit_rate_total').val(tot_final.toFixed(2));
                $('.unit_rate_total').val();
                $('.unit_price_total').text(tot_final.toFixed(2));

                //totaltax
                tot_tax_final = parseFloat($('.total_tax_amount_total').val()) + parseFloat(total_tax);
                $('.total_tax_amount_total').val(tot_tax_final.toFixed(2));
                $('.total_tax_amount_total').val();
                $('.total_tax_amount_tt').text(tot_tax_final.toFixed(2));

                //totaltaxable
                tot_taxable_final = parseFloat($('.total_taxable_amount_total').val()) + parseFloat(total);
                $('.total_taxable_amount_total').val(tot_taxable_final.toFixed(2));
                $('.total_taxable_amount_total').val();
                $('.total_taxable_amount_tt').text(tot_taxable_final.toFixed(2));

                //totalamount
                tot_final_final = parseFloat($('.final_data_amount_total').val()) + parseFloat(total + total_tax);
                $('.final_data_amount_total').val(tot_final_final.toFixed(2));
                $('.final_data_amount_total').val();
                $('.final_amount_tt').text(Math.round(tot_final_final) + '.00');

            },

        });
    });


    $('.bottom_prod_data').on('change', function(e) {
        e.preventDefault();
        item_name = $(this).find(':selected').data('val');
        categ_name = $('.category_id_data').val();
        temp_item_qty = '<?php echo $temp_item_socks[0]['quantity']; ?>';

        $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL; ?>/admin/solditems/bottomrequest_check',
            data: {
                'item_name': item_name,
                'categ_name': categ_name
            },
            success: function(data) {
                obj = JSON.parse(data);
                item_name = $('.bottom_unit_rate').text(obj.saleprice + '.00');
                discount = obj.discount * temp_item_qty;
                $('.bottom_stock_check').text(obj.stockavl);
                item_name = $('.bottom_discount').text(discount);
                item_name = $('.bottom_tax').text(obj.tax + '.00%');
                tax = obj.tax;

                total = obj.saleprice * temp_item_qty - discount;
                total_tax = total * tax / 100;

                item_name = $('.bottom_tax_amount').text((total_tax).toFixed(2));
                item_name = $('.bottom_taxable_amount').text((total).toFixed(2));
                item_name = $('.bottom_total_amount').text((total + total_tax).toFixed(2));

                //unit rate
                tot_final = parseFloat($('.unit_rate_total').val()) + parseFloat(obj.saleprice);
                $('.unit_rate_total').val(tot_final.toFixed(2));
                $('.unit_rate_total').val();
                $('.unit_price_total').text(tot_final.toFixed(2));

                //totaltax
                tot_tax_final = parseFloat($('.total_tax_amount_total').val()) + parseFloat(total_tax);
                $('.total_tax_amount_total').val(tot_tax_final.toFixed(2));
                $('.total_tax_amount_total').val();
                $('.total_tax_amount_tt').text(tot_tax_final.toFixed(2));

                //totaltaxable
                tot_taxable_final = parseFloat($('.total_taxable_amount_total').val()) + parseFloat(total);
                $('.total_taxable_amount_total').val(tot_taxable_final.toFixed(2));
                $('.total_taxable_amount_total').val();
                $('.total_taxable_amount_tt').text(tot_taxable_final.toFixed(2));

                //totalamount
                tot_final_final = parseFloat($('.final_data_amount_total').val()) + parseFloat(total + total_tax);
                $('.final_data_amount_total').val(tot_final_final.toFixed(2));
                $('.final_data_amount_total').val();
                $('.final_amount_tt').text(Math.round(tot_final_final) + '.00');
            },

        });
    });


    $('.socks_prod_data').on('change', function(e) {
        e.preventDefault();
        item_name = $(this).find(':selected').data('val');
        categ_name = $('.category_id_data').val();
        temp_item_qty = '<?php echo $temp_item_socks[0]['quantity']; ?>';

        $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL; ?>/admin/solditems/socksrequest_check',
            data: {
                'item_name': item_name,
                'categ_name': categ_name
            },
            success: function(data) {
                obj = JSON.parse(data);
                item_name = $('.socks_unit_rate').text(obj.saleprice + '.00');
                discount = obj.discount * temp_item_qty;
                $('.socks_stock_check').text(obj.stockavl);
                item_name = $('.socks_discount').text(discount);
                item_name = $('.socks_tax').text(obj.tax + '.00%');
                tax = obj.tax;

                total = obj.saleprice * temp_item_qty - discount;
                total_tax = total * tax / 100;

                item_name = $('.socks_tax_amount').text(total_tax.toFixed(2));
                item_name = $('.socks_taxable_amount').text((total).toFixed(2));
                item_name = $('.socks_total_amount').text((total + total_tax).toFixed(2));
                //unit rate
                tot_final = parseFloat($('.unit_rate_total').val()) + parseFloat(obj.saleprice);
                $('.unit_rate_total').val(tot_final.toFixed(2));
                $('.unit_rate_total').val();
                $('.unit_price_total').text(tot_final.toFixed(2));

                //totaltax
                tot_tax_final = parseFloat($('.total_tax_amount_total').val()) + parseFloat(total_tax);
                $('.total_tax_amount_total').val(tot_tax_final.toFixed(2));
                $('.total_tax_amount_total').val();
                $('.total_tax_amount_tt').text(tot_tax_final.toFixed(2));

                //totaltaxable
                tot_taxable_final = parseFloat($('.total_taxable_amount_total').val()) + parseFloat(total);
                $('.total_taxable_amount_total').val(tot_taxable_final.toFixed(2));
                $('.total_taxable_amount_total').val();
                $('.total_taxable_amount_tt').text(tot_taxable_final.toFixed(2));

                //totalamount
                tot_final_final = parseFloat($('.final_data_amount_total').val()) + parseFloat(total + total_tax);
                $('.final_data_amount_total').val(tot_final_final.toFixed(2));
                $('.final_data_amount_total').val();
                $('.final_amount_tt').text(Math.round(tot_final_final) + '.00');
            },

        });
    });





    $(document).ready(function() {
        $('.category_request').on('click', function(e) {

            e.preventDefault();
            var category_id = $('.category_id').val();
            var category_qty = $('.category_qty').val();

            $(".error").hide();

            var hasError = false;
            if (category_id == '') {
                $(".category_id").after('<span class="error" style = "color:red;">Select Atleast one category</span>');
                hasError = true;
            }

            if (category_qty == '' || category_qty <= 0) {
                $(".category_qty").after('<span class="error" style = "color:red;">Enter Qty </span>');
                hasError = true;
            }
            if (hasError == true) {
                return false;
            }
            var branch_name = '<?php echo $this->request->session()->read('Auth.User.db'); ?>';
            $.ajax({
                type: 'POST',
                url: '<?php echo SITE_URL; ?>/admin/solditems/categoryrequest',
                data: {
                    'category_id': category_id,
                    'category_qty': category_qty,
                    'branch_name': branch_name
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
                $(".item_id").after('<span class="error" style = "color:red;">Select Atleast one category</span>');
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
                url: '<?php echo SITE_URL; ?>/admin/solditems/itemrequest',
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
                url: '<?php echo SITE_URL; ?>/admin/additem/find_sublocation',
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
                    url: '<?php echo SITE_URL; ?>/admin/additem/getsubcategory',
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
                    url: '<?php echo SITE_URL; ?>/admin/additem/getsublocation',
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
                    url: '<?php echo ADMIN_URL; ?>solditems/getitemname',
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
    $(function() {
        $("#datepicker1").datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            numberOfMonths: 1
        });

    });
</script>



<div class="modal fade" id="paysorts">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" style="margin:0px !important;">Pay Amount</h4>
                <ul class="list-unstyled" style="display:flex; margin-left:auto;">
                    <li><a href="#" class="cash_pay">Cash</a></li>
                    <li><a href="#" class="cheque_pay">Cheque</a></li>

                    <li><a href="#" class="online_pay">Online</a></li>
                </ul>
            </div>
            <?php if (round($_SESSION['soldItem']['final_amt'])) { ?>
                <style>
                    .main-footer {
                        display: none;
                    }
                </style>
            <?php } ?>
            <div class="modal-body">
                <?php //pr($_SESSION); 
                ?>
                <div id="other_total">
                    
                    <h5>Total Payable Amount : <span class='pay_amt_total'><?php echo sprintf('%.2f', round($_SESSION['soldItem']['final_amt'])); ?></span><h5>
                </div>

                        <div class="box-body" style="padding:0px !important;">
                            <?php echo $this->Form->create($item, array(
                                'class' => 'form-horizontal',
                                'enctype' => 'multipart/form-data',
                                'controller' => 'solditems',
                                'action' => 'payamount',
                                'id' => 'sevice_form'

                            )); ?>
                            <div class="row" style="display: flex; align-items: end; flex-wrap:wrap;">
                                <div class="col-md-4">
                                    <label for="inputEmail3" class="control-label" style="text-align: left !important;">Pay Amount</label>
                                    <div class="">
                                        <input type="hidden" name="mode_pay" id="mode_pay" value="Cash">
                                        <input type="hidden" name="name" value="<?php echo $_SESSION['soldItem']['customer_name']; ?>">
                                        <input type="hidden" name="description" value="<?php echo $_SESSION['soldItem']['description']; ?>">
                                        <input type="hidden" name="saledate" value="<?php echo $_SESSION['soldItem']['saledate']; ?>">


                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <?php echo $this->Form->input('pay_amount', array('class' => 'form-control category_id', 'type' => 'text', 'value' => '', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'value' => sprintf('%.2f', round($_SESSION['soldItem']['final_amt'])), 'readonly')); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Date</label>
                                    <div class="">
                                        <?php echo $this->Form->input('pay_date', array('class' => 'form-control category_id', 'type' => 'text', 'value' => date('d-m-Y'), 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off', 'id' => 'datepicker6', 'autocomplete' => 'off', 'readonly')); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Discount Amount</label>
                                    <div class="">
                                        <?php echo $this->Form->input('discount', array('class' => 'form-control', 'type' => 'text', 'value' => '', 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Hosteler indent No:</label>
                                    <div class="">
                                        <?php echo $this->Form->input('indent_no', array('class' => 'form-control', 'type' => 'text', 'value' => '', 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Manual Reciept No:</label>
                                    <div class="">
                                        <?php echo $this->Form->input('manual_receipt_no', array('class' => 'form-control', 'type' => 'text',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Manual Reciept Date</label>
                                    <div class="">
                                        <?php echo $this->Form->input('manual_receipt_date', array('class' => 'form-control category_id', 'type' => 'text', 'value' => '', 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off', 'id' => 'datepicker3', 'autocomplete' => 'off', 'readonly')); ?>
                                    </div>
                                </div>


                                <div class="cheque_data" style="display:none">
                                    <div class="col-md-3">
                                        <label for="inputEmail3" class=" control-label" style="text-align: left !important;"> Bank</label>
                                        <div class="">
                                            <?php echo $this->Form->input('bank_name', array('class' => 'form-control category_id', 'type' => 'text', 'value' => '', 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off', 'autocomplete' => 'off')); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Branch</label>
                                        <div class="">
                                            <?php echo $this->Form->input('bankbranch_name', array('class' => 'form-control category_id', 'type' => 'text', 'value' => '', 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off', 'autocomplete' => 'off')); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Cheque No</label>
                                        <div class="">
                                            <?php echo $this->Form->input('chequeno', array('class' => 'form-control', 'type' => 'text', 'value' => '', 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off', 'autocomplete' => 'off')); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Cheque Date</label>
                                        <div class="">
                                            <?php echo $this->Form->input('cheque_date', array('class' => 'form-control category_id', 'type' => 'text', 'value' => '', 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off', 'id' => 'datepicker2', 'autocomplete' => 'off', 'readonly')); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Other Amount</label>
                                    <div class="">
                                        <?php echo $this->Form->input('other_amt', array('class' => 'form-control other_amt', 'type' => 'text', 'value' => '', 'label' => false, 'empty' => 'Select Category', 'autofocus', 'autocomplete' => 'off')); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important;">Description</label>
                                    <div class="">
                                        <?php echo $this->Form->input('pay_remark', array('class' => 'form-control', 'type' => 'textarea',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
                                    </div>
                                </div> 
                                  <script>
                       $(document).ready(function() {
                            $('#sevice_form').submit(function(event) {
                              
                            $('.addgen').hide();
                       
                            });
                        });

                </script>

                                <div class="col-md-12" style="margin-top:15px">
                                    <?php
                                    if (isset($item['id'])) {
                                        echo $this->Form->submit(
                                            'Update',
                                            array('class' => 'btn btn-primary pull-left', 'title' => 'Update')
                                        );
                                    } else {
                                        echo $this->Form->submit(
                                            'Submit',
                                            array('class' => 'btn btn-primary pull-left addgen', 'title' => 'Add')
                                        );
                                    }
                                    ?>
                                </div>
                                <?php echo $this->Form->end(); ?>
                            </div>
                        </div>
            </div>
        </div>
        <?php $message = $this->Flash->render('pay_request'); ?>
        <?php if ($message) {  ?>
            <script>
                $(document).ready(function() {
                    $('#paysorts').modal('show');
                    //$('#myModal').modal('show');
                });
            </script>
        <?php } ?>

        <script>
            $(function() {
                $("#datepicker6").datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    numberOfMonths: 1
                });

                $("#datepicker2").datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    numberOfMonths: 1
                });


                $("#datepicker3").datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    numberOfMonths: 1
                });


            });
        </script>

        <script>
            $("#bank-name").prop("disabled", true);
            $("#bankbranch-name").prop("disabled", true);
            $("#chequeno").prop("disabled", true);
            $("#datepicker2").prop("disabled", true);

            $(".online_pay").click(function() {
                $("#mode_pay").val("Online");
                // $("#").show();   
            });

            $(".cheque_pay").click(function() {
                $(".cheque_data").css("display", "block")
                $("#bank-name").prop("disabled", false);
                $("#bankbranch-name").prop("disabled", false);
                $("#chequeno").prop("disabled", false);
                $("#datepicker2").prop("disabled", false);
                $("#mode_pay").val("Cheque");
                // $("#").show();   
            });
            $(".cash_pay").click(function() {
                $(".cheque_data").css("display", "none")
                $("#bank-name").prop("disabled", true);
                $("#bankbranch-name").prop("disabled", true);
                $("#chequeno").prop("disabled", true);
                $("#datepicker2").prop("disabled", true);
                $("#mode_pay").val("Cash");
                // $("#").show();   
            });
        </script>

        <script>
            $('#discount').on('change', function() {
                var discount_amount = $(this).val();
                var pay_amount = $('#pay-amount').val();

                var disocunt_amount_data = pay_amount - discount_amount;
                var pay_amount = $('#pay-amount').val(disocunt_amount_data);
            });
        </script>

<!----------- other amount --------------->
<script>
            $('.other_amt').on('change', function() {
                
                var other_amt = parseFloat($(this).val());
                var other_total = '<?php echo sprintf('%.2f', round($_SESSION['soldItem']['final_amt'])); ?>';
                var disocunt_amount_data = parseFloat(other_total) + parseFloat(other_amt);
                var tt = $('.pay_amt_total').text(disocunt_amount_data);  
            });
        </script>

  

<!------------ other amount end ------------>
        <style>
            #test {
                position: relative;
            }

            #test ul {
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

            #test ul li {
                padding: 5px 8px;
                border: 1px solid lightgray;
            }

            #test ul li a {
                color: black;
            }

            .preview {
                margin-right: 15px;
            }

            .dataTables_wrapper.form-inline.dt-bootstrap.no-footer {
                margin-top: 0px;
            }
        </style>


        <script>
            function cllbckretail0(id, cid, sid) {
                $('.secrh-students').val(id);
                $('#retail_id').val(cid);
                $('#test').hide();
            }
            $(function() {
                $('.secrh-students').bind('keyup', function() {
                    var pos = $(this).val();
                    //alert(pos);
                    var check = 0;
                    //var catid=$('#subcategory').val();
                    //alert(pos);
                    $('#test').show();
                    $('#retail_id').val('');
                    var count = pos.length;
                    if (count > 0) {
                        $.ajax({
                            type: 'POST',
                            url: '<?php echo ADMIN_URL; ?>Solditems/getstudentname',
                            data: {
                                'fetch': pos,
                                'check': check,

                            },
                            success: function(data) {
                                /// alert(data);
                                console.log(data);
                                $('#test ul').html(data);
                            },
                        });
                    } else {
                        $('#test').hide();
                    }
                });
            });
        </script>