<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Write-offs

        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/Sales/customerorder">Write-offs</a></li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">

                    <table>
                        <div class="sls_invc_hd">
                            <div class="dropdown-center">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Edit
                                </button>
                                <ul style="left: 43px !important;" class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                    <li><a class="dropdown-item" href="#">Copy</a></li>
                                </ul>
                            </div>




                            <div class="dropdown-center">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Print
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Shipment</a></li>
                                    <li><a class="dropdown-item" href="#">Incoming Payment</a></li>
                                    <li><a class="dropdown-item" href="#">Incoming Cash Payment</a></li>

                                </ul>
                            </div>

                            <div class="dropdown-center">
                                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Send
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Sale Invoice</a></li>
                                    <li><a class="dropdown-item" href="#">Tax Invoice</a></li>
                                    <li><a class="dropdown-item" href="#">Tax Invoice (with bank details)</a></li>
                                    <li><a class="dropdown-item" href="#">Bundle Sending..</a></li>
                                </ul>
                            </div>

                        </div>


                        <tr>
                            <th>

                                <span>Write-offs#</span>
                                <input type="text">
                                <label for=""> form</label>
                                <input type="datetime-local">

                            </th>


                            <th>
                                <div class="dropdown-center">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Status
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#">Shipment</a></li>
                                        <li><a class="dropdown-item" href="#">Incoming Payment</a></li>
                                        <li><a class="dropdown-item" href="#">Incoming Cash Payment</a></li>

                                    </ul>
                                </div>
                            </th>
                            <th>




                                <a class="qut_bg" href=""> <i class="fa fa-question" aria-hidden="true"></i></a>
                                <input type="checkbox">
                                <label for="">Draft</label>
                            </th>

                        </tr>
                    </table>





                    <div class="box-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>My Company</label> <strong style="color:red;">*</strong>
                                    <?php echo $this->Form->input('name', array('class' => 'form-control', 'id' => 'title',  'label' => false, 'required')); ?>
                                </div>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Warehouse</label><strong style="color:red;">*</strong> <a style="
                                    float: right;
                                " href="<?php echo SITE_URL; ?>admin/Sales/addcounterparty"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                    <?php echo $this->Form->input('contact_no', array('class' => 'form-control', 'type' => 'number',  'label' => false, 'required', 'type' => 'text', 'maxlength' => '11')); ?>
                                </div>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Project</label>
                                    <?php echo $this->Form->input('tin_dated', array('class' => 'form-control input1', 'label' => false,  'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => $date_tin)); ?>
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
                            <div class="col-sm-12">
                                <?php
                                if (isset($vendor['id']) && !empty($vendor['id'])) {
                                    echo '<button type="submit" name="button" value="update" class="btn btn-success pull-right">Update</button> ';
                                } else {

                                    echo '<button type="close" class="btn btn-success cls_btn pull-right">Close</button> ';

                                    echo '<button type="submit" class="btn btn-success pull-right">Save</button> ';
                                }
                                ?>
                            </div>
                            <br>
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








                    <div id="btmtbs">
                        <nav>
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Items</button>
                                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Related transactions</button>

                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">

                                <div class="itm_inr">
                                    <input type="checkbox">
                                    <div class="dropdown-center">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Name
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Sort by name</a></li>
                                            <li><a class="dropdown-item" href="#">Sort by SKU</a></li>
                                            <li><a class="dropdown-item" href="#"><input type="checkbox">In an order of folders</a></li>
                                        </ul>
                                    </div>

                                    <label for="">Qty Written-off</label>
                                    <label for="">On Hand</label>



                                    <div class="dropdown-center">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Price
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Change price in transactio</a></li>
                                            <li><a class="dropdown-item" href="#">Change prices for items in catalog</a></li>

                                        </ul>
                                    </div>

                                    <label for="">Total Written-off</label>
                                    <label for="">Written-off Reason</label>



                                    <div class="dropdown-center">

                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="#">Image<input type="checkbox"></a></li>
                                            <li><a href="#">UOM<input type="checkbox"></a></li>
                                            <li><a href="#">Shipped<input type="checkbox"></a></li>
                                            <li><a href="#">Available<input type="checkbox"></a></li>
                                            <li><a href="#">On Hand<input type="checkbox"></a></li>
                                            <li><a href="#">Committed<input type="checkbox"></a></li>
                                            <li><a href="#">In Transit<input type="checkbox"></a></li>
                                            <li><a href="#">Weight<input type="checkbox"></a></li>
                                            <li><a href="#">Volume<input type="checkbox"></a></li>
                                            <li><a href="#">Tax sum<input type="checkbox"></a></li>

                                        </ul>
                                    </div>




                                </div>
                                <div class="itm_inr_sec bdr">
                                    <div style="width: 30%; margin-right:20px;" class="itm_inr_sec_f">
                                        <input type="text" placeholder="Begin typing item name, SKU, supplier code or barcode">
                                    </div>
                                    <div class="itm_inr_sec_s">
                                        <button>Add from Catalog</button>
                                        <button>Import</button>
                                    </div>
                                </div>

                                <div class="itm_inr_sec">
                                    <textarea name="" id="" cols="80" rows="3"></textarea>
                                    <div class="sbttl">
                                        <div class="suttl_hding">

                                            <span> <b> Type of Tax: <a href="">Without taxes</a> </b></span>

                                            <h4 style="margin: 0px !important;"> <b> Total</b></h4>
                                        </div>

                                        <div class="suttl_calcu">
                                            <br>
                                            <h4 style="margin: 0px !important;"> <b> 0.00</b></h4>
                                        </div>


                                    </div>

                                </div>


                            </div>
                            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                                <div class="itm_inr_sec2">
                                    <button>Quantity Checking</button>
                                    <!-- /* input file bg */ -->


                                    <div class='upload-field-customized'>
                                        <input type="file" multiple="multiple" name="file" id="file_upload">
                                        <span>
                                            <i class="fa fa-plus-square" aria-hidden="true"></i> file
                                        </span>
                                    </div>


                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>
</div>