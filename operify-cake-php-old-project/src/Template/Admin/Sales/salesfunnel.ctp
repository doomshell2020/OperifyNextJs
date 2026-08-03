<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Sales Funnel
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Sales/customerorder">Sales Funnel</a></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div id="Sale_invoice">
                        <div class="sl_inc_inr">
                            <div class="top_row" style="width: 50%;">

                                <!-- <h3>Sales Funnel</h3> -->
                                <!-- <a href="<?php echo SITE_URL; ?>admin/Sales/salesreturnadd"><button><i class="fa fa-plus-circle" aria-hidden="true"></i>Return</button></a> -->
                                <div class="prft_tp_btn">
                                    <button class="top_btn">By Orders</button>
                                    <button class="top_btn">By Customers</button>

                                </div>
                                <div class="prft_tp_btn">
                                    <button>Filter</button>
                                    <div class="dropdown-center">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-print" aria-hidden="true"></i>Print
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Sort by name</a></li>
                                            <li><a class="dropdown-item" href="#">Sort by SKU</a></li>
                                            <li><a class="dropdown-item" href="#"><input type="checkbox">In an order of folders</a></li>
                                        </ul>
                                    </div>

                                </div>
                            </div>

                            <div class="top_row_inrfrm">


                                <div class="box-body">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-4" style="margin-bottom:15px; display:flex; align-items: center;">
                                                <?php
                                                if (isset($vendor['id']) && !empty($vendor['id'])) {
                                                    echo '<button type="submit" name="button" value="update" class="btn btn-success pull-right">Update</button> ';
                                                } else {
                                                    echo '<button type="submit" class="btn btn-success pull-right frm-btn ">Find</button> ';
                                                    echo '<button type="close" class="btn btn-success cls_btn pull-right frm-btn">Close</button> ';
                                                }
                                                ?>
                                                <div class="frm-icn">
                                                    <a href=""><i class="fa fa-tag" aria-hidden="true"></i></a>
                                                    <a href=""><i class="fa fa-cog" aria-hidden="true"></i></a>
                                                </div>
                                            </div>


                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label for=""> Date Created <a href="">YAD.TDY.WTD.MTD</a></label> <br>
                                                <input type="date">
                                                <input type="date">
                                            </div>







                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label>Product/Any Product of folder</label><strong style="color:red;">*</strong> <a style="
                                    float: right;
                                " href="<?php echo SITE_URL; ?>admin/Sales/addcounterparty"></a>
                                                <?php echo $this->Form->input('contact_no', array('class' => 'form-control', 'type' => 'number',  'label' => false, 'required', 'type' => 'text', 'maxlength' => '11')); ?>
                                            </div>
                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label>Warehouse</label>
                                                <?php echo $this->Form->input('tin_dated', array('class' => 'form-control input1', 'label' => false,  'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => $date_tin)); ?>
                                            </div>
                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label>Project</label>
                                                <?php echo $this->Form->input('email', array('class' => 'form-control', 'type' => 'text', 'id' => 'title', 'label' => false, 'required')); ?>
                                            </div>
                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label>Countryparty</label>
                                                <?php echo $this->Form->input('pancard_number', array('class' => 'form-control pancard', 'type' => 'select', 'options' => $state, 'empty' => 'jaipur', 'maxlength' => '15', 'label' => false, 'required', 'autocomplete' => 'off')); ?>
                                            </div>
                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label>Countryparty Tag</label>
                                                <?php echo $this->Form->input('tin_dated', array('class' => 'form-control input1', 'label' => false,  'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => $date_tin)); ?>
                                            </div>
                                            <?php if (date('Y-m-d', strtotime($vendor['tin_date'])) == "1970-01-01") {
                                                $date_tin = '';
                                            } else {
                                                $date_tin = date('Y-m-d', strtotime($vendor['tin_date']));
                                            } ?>
                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label for="inputEmail3" style="padding-top: 0px;">Contract</label>
                                                <?php echo $this->Form->input('tin_dated', array('class' => 'form-control input1', 'label' => false,  'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => $date_tin)); ?>
                                            </div>
                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label>My Company </label>
                                                <?php echo $this->Form->input('billtostate_id', array('class' => 'form-control state', 'id' => 'billto_state_ids', 'type' => 'select', 'options' => $state, 'empty' => 'Select State', 'label' => false, 'required', 'value' => $vendor['state_id'])); ?>
                                            </div>
                                            <div class="col-sm-4" style="margin-bottom:15px;">
                                                <label>Employee</label>
                                                <?php echo $this->Form->input('billtogst_number', array('class' => 'form-control gst', 'type' => 'text', 'maxlength' => '15', 'label' => false,  'autocomplete' => 'off', 'value' => $vendor['gst_number'])); ?>
                                            </div>


                                        </div>



                                        <!-- <div class="col-sm-4" style="margin-bottom:15px;">
                                <label>Address</label><strong style="color:red;">*</strong>
                                <?php echo $this->Form->textarea('billtoaddress', array('rows' => '2', 'class' => 'form-control address', 'placeholder' => 'Address', 'label' => false, 'required', 'value' => $vendor['address'])); ?>
                            </div>
                            <div class="col-sm-4" style="margin-bottom:15px;">
                                <label>Description</label>
                                <?php echo $this->Form->textarea('description', array('rows' => '2', 'class' => 'form-control', 'label' => false, 'placeholder' => 'Enter Description', 'autocomplete' => 'off')); ?>
                            </div>
                            <div class="col-sm-4" style="margin-bottom:15px;">
                                <label for="inputEmail3" class="control-label">Type :</label><br>
                                <label class="radio-inline">
                                    <input type="radio" name="vendortype" class="mode radio-inline checkstr" value="Vendor" <?php if ($vendor['vendor_type'] == "Vendor") {
                                                                                                                                echo "checked";
                                                                                                                            } ?>>&nbsp;Vendor
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="vendortype" class="mode radio-inline checkstr" value="Customer" <?php if ($vendor['vendor_type'] == "Customer") {
                                                                                                                                    echo "checked";
                                                                                                                                } ?>>&nbsp; Customer
                                </label>
                            </div> -->




                                        <!-- <div class="col-sm-4">
                        <label class="control-lable">TDS :</label>   <br>
                          <input type="checkbox" name="tds" value="1" <? php // if($vendor['tds'] != 0){ echo 'checked' ;} 
                                                                        ?> label=false>
                        </div> -->












                                        <!-- <div class="col-sm-12">
                                            <?php
                                            if (isset($vendor['id']) && !empty($vendor['id'])) {
                                                echo '<button type="submit" name="button" value="update" class="btn btn-success pull-right">Update</button> ';
                                            } else {

                                                echo '<button type="close" class="btn btn-success cls_btn pull-right">Close</button> ';

                                                echo '<button type="submit" class="btn btn-success pull-right">Save</button> ';
                                            }
                                            ?>
                                        </div>
                                        <br> -->
                                        <!-- <div class="all_vendorsdetails">
                        <h4 style="font-weight:bold;">Bill To <strong style="color:red;">*</strong></h4>
                        <? php // if(empty($vendor['id'])){ 
                        ?>
                        <div class="form-group">
                        <div class="col-sm-2 billtos">
                        <label>State</label>
                        </div>
                        <div class="col-sm-2 billtoc">
                        <label>City</label>
                          <?php //echo $this->Form->input('billtocity_id[]',array('class'=>'form-control city', 'id'=>'billto_city_ids', 'type'=>'select', 'empty'=>'Select City', 'label' =>false,'required')); 
                            ?>
                        </div>
                        <div class="col-sm-2">
                        <label>GST NO.</label>
                          <? php // echo $this->Form->input('billtogst_number[]', array('class' => 'form-control gst','type'=>'text','maxlength'=>'15','label'=>false,'placeholder'=>'GST No.','autocomplete'=>'off','required')); 
                            ?>
                        </div>
                        <div class="col-sm-4">
                          <label>Address</label>
                          <?php //echo $this->Form->textarea('billtoaddress[]', array('rows'=>'2', 'class'=>'form-control address','placeholder'=>'Address', 'label' =>false,'required')); 
                            ?>
                        </div>
                        <div class="col-sm-2">
                          <label class="control-lable">Same As Copy</label>  <br>
                            <input type="checkbox" name="copy"  id="sameascopy" value="1" label=false> -->
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                $("#sameascopy").on('change', function() {
                                                    if ($(this).prop("checked") == true) {
                                                        var ss = $(this).closest('.all_vendorsdetails').find('.state option:selected').val();
                                                        $(this).closest('.all_vendorsdetails').find('.shipstate').val(ss);
                                                        var cs = $(this).closest('.all_vendorsdetails').find('.city option:selected').val();
                                                        $(this).closest('.all_vendorsdetails').find('.shipcity').val(cs);
                                                        var gst = $(this).closest('.all_vendorsdetails').find('.gst').val();
                                                        $(this).closest('.all_vendorsdetails').find('.shipgst').val(gst);
                                                        var shipaddress = $(this).closest('.all_vendorsdetails').find('.address').val();
                                                        $(this).closest('.all_vendorsdetails').find('.shipaddress').val(shipaddress);
                                                    } else if ($(this).prop("checked") == false) {
                                                        $(this).closest('.all_vendorsdetails').find('.shipstate option[value=""]').prop("selected", true);
                                                        $(this).closest('.all_vendorsdetails').find('.shipcity option[value=""]').prop("selected", true);
                                                        $(this).closest('.all_vendorsdetails').find('.shipgst').val('');
                                                        $(this).closest('.all_vendorsdetails').find('.shipaddress').text('');
                                                    }
                                                });
                                            });
                                        </script>
                                    </div>
                                </div>
                                <!-- <div class="row">
                                    <div class="col-3"> <button>Find</button> <button>Clear</button></div>
                                    <div class="col-3">
                                        <label for=""> Date Created <a href="">YAD.TDY.WTD.MTD</a></label>
                                        <input type="date">
                                        <input type="date">
                                    </div>

                                    <div class="col-3">
                                        <label for=""> Consider</label>
                                        <select name="" id="">
                                            <option value=""> Products, services and bundles</option>
                                            <option value="">Only Products</option>
                                            <option value="">Only Services</option>s
                                            <option value="">Only Bundles</option>

                                        </select>
                                    </div>

                                    <div class="col-3">
                                        <label for=""> Product/ Any Product of folder</label>
                                        <input type="text">
                                    </div>

                                    <div class="col-3">
                                        <label for="">Warehouse</label>
                                        <input type="text">
                                    </div>
                                    <div class="col-3">
                                        <label for="">Project</label>
                                        <input type="text">
                                    </div>
                                    <div class="col-3">
                                        <label for="">Customer</label>
                                        <input type="text">
                                    </div>
                                    <div class="col-3">
                                        <label for="">Customer Tag</label>
                                        <input type="text">
                                    </div>
                                    <div class="col-3">
                                        <label for="">Contract</label>
                                        <input type="text">
                                    </div>
                                    <div class="col-3">
                                        <label for="">Supplier</label>
                                        <input type="text">
                                    </div>
                                    <div class="col-3">
                                        <label for="">My Company</label>
                                        <input type="text">
                                    </div>
                                    <div class="col-3">
                                        <label for="">transaction Type</label>
                                        <input type="text">
                                    </div>
                                    <div class="col-3">
                                        <label for="">Sales Channel</label>
                                        <input type="text">
                                    </div>

                                </div> -->
                            </div>






                            <div class="btm_row">


                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col"> Workflow</th>
                                            <th scope="col">Qty</th>
                                            <th scope="col">Date Created</th>
                                            <th scope="col">Conversion</th>
                                            <th scope="col">Total </th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <tr>
                                            <td> <a href=""> New</a></td>
                                            <td> <a href=""> 1(100%)</a></td>
                                            <td> <a href=""> 7,8 h</a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> 100.00</a></td>


                                        </tr>
                                        <tr>
                                            <td> <a href=""> New</a></td>
                                            <td> <a href=""> 1(100%)</a></td>
                                            <td> <a href=""> 7,8 h</a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> 100.00</a></td>


                                        </tr>
                                        <tr>
                                            <td> <a href=""> New</a></td>
                                            <td> <a href=""> 1(100%)</a></td>
                                            <td> <a href=""> 7,8 h</a></td>
                                            <td> <a href=""> </a></td>
                                            <td> <a href=""> 100.00</a></td>


                                        </tr>

                                    </tbody>
                                </table>




                                <!-- <table>
                                    <tr>

                                        <th>Workflow</th>
                                        <th>Qty</th>
                                        <th>Date Created</th>
                                        <th>Conversion</th>
                                        <th> Total</th>





                                    </tr>

                                    <tr>
                                        <td> <a href=""> New</a></td>
                                        <td> <a href=""> 1(100%)</a></td>
                                        <td> <a href=""> 7,8 h</a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> 100.00</a></td>


                                    </tr>


                                    <tr>
                                        <td> <a href=""> New</a></td>
                                        <td> <a href=""> 1(100%)</a></td>
                                        <td> <a href=""></a></td>
                                        <td> <a href="">100% </a></td>
                                        <td> <a href=""> 100.00</a></td>


                                    </tr>


                                    <tr>
                                        <td> <a href=""> New</a></td>
                                        <td> <a href=""> 1(100%)</a></td>
                                        <td> <a href=""> 15 Days</a></td>
                                        <td> <a href=""> 100% </a></td>
                                        <td> <a href=""> 100.00</a></td>


                                    </tr>


                                    <tr>
                                        <td> <a href=""> New</a></td>
                                        <td> <a href=""> 1(100%)</a></td>
                                        <td> <a href=""> 7,8 h</a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> 100.00</a></td>


                                    </tr>

                                    <tr>
                                        <td> <a href=""> New</a></td>
                                        <td> <a href=""> 1(100%)</a></td>
                                        <td> <a href=""> 7,8 h</a></td>
                                        <td> <a href=""> </a></td>
                                        <td> <a href=""> 100.00</a></td>


                                    </tr>
                                </table> -->
                            </div>

                        </div>



                    </div>
                </div>
            </div>
        </div>
    </section>
</div>