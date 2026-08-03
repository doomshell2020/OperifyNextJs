<style>
   #testUL {
        position: relative;
    }

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

    #testUL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
    }

    #testUL ul li a {
        color: black;
    }

    .preview {
        margin-right: 15px;
    }

    .dataTables_wrapper.form-inline.dt-bootstrap.no-footer {
        margin-top: 0px;
    }

    .input_fields_wrap .form-control {
        margin-bottom: 15px;

    }

    .radio-panel .radio-panel-selected {
        margin-bottom: 10px;
    }

    .radio-panel-selected input[type="radio"] {
        display: inline;
    }

    .radio-panel-selected span label {
        padding: 0px !important;
        border: none !important;
    }

    input[type=radio] {
        margin: 4px 0 0;
        margin-top: 1px\9;
        line-height: normal;
        display: none;
    }

    .radio-panel-selected input[type="text"] {
        width: 10%;
    }

    .radio-panel-selected input[type="radio"] {
        margin-right: 10px;
    }

    .dropdown__button:hover {
        background-color: lightgray;
    }

    .dropdown__items {

        position: absolute;
        margin-top: 0.5rem;
        left: 0;
        width: 200px;
        padding: 0.5rem;
        border-radius: 0.5rem;
        box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
        animation: open 0.3s;
        transform-origin: top left;
    }

    .dropdown__item {
        width: 100%;
        text-align: left;
        padding: 1rem;
    }

    .dropdown__item:hover {
        background-color: lightgray;
        border-radius: 0.5rem;
    }

    .dropdown--hide {
        display: none;
    }

    .dropdown--show {
        display: block;
    }

    @keyframes open {
        from {
            transform: scaleY(0);
        }

        to {
            transform: scaleY(1);
        }
    }

    */ .dropdown {
        position: relative;
    }

    .dropdown3 {
        position: relative;
    }

    .dropdown3 ul li a {
        display: flex;
        justify-content: space-between;
    }

    .dropdown2 {
        position: relative;
    }

    button.btn.btn-primary.dropdown-toggle {
        background-color: lightgray;
        color: #000;
    }


    .btn-primary:hover {
        background-color: lightgray !important;
        color: #000 !important;
    }

    .gwt-RadioButton {
        margin-right: 10px;
    }

    .gwt-RadioButton label {
        width: 20%;
        text-align: left !important;
    }

    .sbttl h3 {
        margin: 0px;
        font-weight: 600;
        font-size: 20px;
    }


    .popup-link {
        display: flex;
        flex-wrap: wrap;
    }

    .popup-link a {
        background: #d5d5d5;
        color: #2b2b2b;
        padding: 5px 7px;
        border-radius: 2px;
        font-size: 17px;
        cursor: pointer;

        text-decoration: none;
    }

    .popup-container {
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease-in-out;
        transform: scale(1.3);
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(21, 17, 17, 0.61);
        display: flex;
        align-items: center;
    }

    .popup-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
    }

    .popup-content p {
        font-size: 17px;

        line-height: 20px;
    }

    .popup-content a.close {
        color: #aaaaaa;
        float: right;
        font-size: 28px !important;
        font-weight: bold;
        background: none;
        padding: 0;
        margin: 0;
        text-decoration: none;
    }

    .popup-content a.close:hover {
        color: #333;
    }

    .popup-content span:hover,
    .popup-content span:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .popup-container:target {
        visibility: visible;
        opacity: 1;
        transform: scale(1);
    }

    .popup-container h3 {
        margin: 10px 0px;
    }


    .radio_panel label {
        display: inline-block;
        margin: 0 0 -1px;
        padding: 0px;



        border: 0px;
    }



    .add_dtl {
        width: 100%;
        display: flex;
        margin-top: 50px;
    }

    .add_dtl .smntd_lft {
        width: 70%;
    }

    .add_dtl .sbttl {
        width: 30%;
        display: flex;
        justify-content: space-between;
    }


    /* The Close Button */
    .close2 {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close2:hover,
    .close2:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .content-wrapper {
        min-height: auto !important;
    }

    .smntd_lft table,
    tr {
        width: 100%;
        border: 1px solid gray;
    }

    .smntd_lft table td img {
        width: 50px;
        height: 50px;

    }

    .upload-field-customized {
        width: 50px;
        background: #ccc;
        position: relative;
        cursor: pointer;
        margin-top: 30px;
        margin-bottom: 10px;
    }

    .upload-field-customized input[type="file"] {
        position: absolute;
        width: 100%;
        height: 50px;
        opacity: 0;
        cursor: pointer;
        left: 0px;
        top: 0px;
        z-index: 10;
    }

    .upload-field-customized span {
        text-align: center;
        width: 100%;
        display: block;
        height: 35px;
        line-height: 35px;
    }



    #content2 table,
    tr {
        width: 100%;
        border: 1px solid gray;
    }



    #content2 table td img {
        width: 50px;
        height: 50px;

    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Sales Order
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/vendors"><i class="fa fa-home"></i>Home</a></li>
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
                        <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> 
                        <?php if (isset($location['id'])) {
                        echo 'Edit Vendor Name';
                        } else {
                        echo 'Create New Vendor ';
                        } ?></h3>
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php echo $this->Form->create($vendor, array('class' => 'form-horizontal'));
                    // pr($vendor); die;
                    ?>
                    <div class="box-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>My Company</label> <strong style="color:red;">*</strong>
                                    <?php echo $this->Form->input('name', array('class' => 'form-control', 'id' => 'title',  'label' => false, 'required')); ?>
                                </div>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Customer</label><strong style="color:red;">*</strong> <a style="
                                    float: right;
                                " href="<?php echo SITE_URL; ?>admin/Sales/addcounterparty"><i class="fa fa-plus" aria-hidden="true"></i></a>

                                    <input type="hidden" required="required" name="customer_id" id="retail_ids">
                                    <?php echo $this->Form->input('contact_no', array('class' => 'form-control secrh-retail','label' => false, 'required', 'type' => 'text')); ?>
                                    <div id="testUL" style="display:none;">
                                        <ul></ul>
                                    </div>
                                </div>

                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Expected Shipment Date</label>
                                    <?php echo $this->Form->input('tin_dated', array('class' => 'form-control input1', 'label' => false,  'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => $date_tin)); ?>
                                </div>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Sales Channel</label>
                                    <?php echo $this->Form->input('email', array('class' => 'form-control', 'type' => 'text', 'id' => 'title', 'label' => false, 'required')); ?>
                                </div>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Warehouse</label>
                                    <?php echo $this->Form->input('pancard_number', array('class' => 'form-control pancard', 'type' => 'select', 'options' => $state, 'empty' => 'jaipur', 'maxlength' => '15', 'label' => false, 'required', 'autocomplete' => 'off')); ?>
                                </div>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Contract</label>
                                    <?php echo $this->Form->input('tin_no', array('class' => 'form-control pancard', 'type' => 'text', 'maxlength' => '15', 'label' => false, 'placeholder' => 'Tin No.', 'required', 'autocomplete' => 'off')); ?>
                                </div>
                                <?php if (date('Y-m-d', strtotime($vendor['tin_date'])) == "1970-01-01") {
                                    $date_tin = '';
                                } else {
                                    $date_tin = date('Y-m-d', strtotime($vendor['tin_date']));
                                } ?>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label for="inputEmail3" style="padding-top: 0px;">Project</label>
                                    <?php echo $this->Form->input('tin_dated', array('class' => 'form-control input1', 'label' => false,  'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly', 'value' => $date_tin)); ?>
                                </div>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Shipping Address</label>
                                    <?php echo $this->Form->input('billtostate_id', array('class' => 'form-control state', 'id' => 'billto_state_ids', 'type' => 'select', 'options' => $state, 'empty' => 'Select State', 'label' => false, 'required', 'value' => $vendor['state_id'])); ?>
                                </div>
                                <div class="col-sm-4" style="margin-bottom:15px;">
                                    <label>Comments</label>
                                    <?php echo $this->Form->input('billtogst_number', array('class' => 'form-control gst', 'type' => 'text', 'maxlength' => '15', 'label' => false,  'autocomplete' => 'off', 'value' => $vendor['gst_number'])); ?>
                                </div>
                            </div>

                            <div class="col-sm-12">
                                <?php
                                if (isset($vendor['id']) && !empty($vendor['id'])) {
                                    echo '<button type="submit" name="button" value="update" class="btn btn-success pull-right">Update</button> ';
                                } else {
                                    echo '<button type="submit" class="btn btn-success pull-right">Save</button> ';
                                }
                                ?>
                            </div>
                            <br>
                        </div>
                    </div>

                    <div class="billto_product_containes">
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

                                    <label for="">Qty ordered</label>
                                    <label for="">Qty shipped</label>
                                    <label for="">Available</label>

                                    <div class="dropdown-center">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Price
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Change price in transactio</a></li>
                                            <li><a class="dropdown-item" href="#">Change prices for items in catalog</a></li>

                                        </ul>
                                    </div>
                                    <label for="">Tax Rate</label>



                                    <button type="button" class="btn btn-primary_mdl" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">Discount</button>


                                    <div class="dis_mdl">
                                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title fs-5" id="exampleModalLabel">Discount & Markup</h3>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Changes will be applied to 0 items</p>
                                                        <form>
                                                            <div class="dis_mdl_inrf">

                                                                <input type="radio" id="discount" name="dis_mar"> <label for="discount">Discount</label>
                                                                <input type="text" id="discount">%
                                                            </div>
                                                            <div class="dis_mdl_inrs">
                                                                <input type="radio" id="addmarkup" name="dis_mar"> <label for="addmarkup">Add Markup</label>
                                                                <input type="text" id="addmarkup">%
                                                            </div>


                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary">Save</button>

                                                        <button type="button" class="btn btn-primary_mdl" data-bs-dismiss="modal">Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dropdown-center">
                                        <label for="">Total</label>
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

                                    <style>
                                        #test1UL {
                                            position: relative;
                                        }

                                        #test1UL ul {
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

                                        #test1UL ul li {
                                            padding: 5px 8px;
                                            border: 1px solid lightgray;
                                        }

                                        #test1UL ul li a {
                                            color: black;
                                        }

                                        .preview {
                                            margin-right: 15px;
                                        }

                                        .input_fields_wrap .form-control {
                                            margin-bottom: 15px;
                                        }
                                    </style>

                                </div>
                                <div class="itm_inr_sec bdr">
                                    <div style="width: 30%; margin-right:20px;" class="itm_inr_sec_f">
                                        <input type="hidden" required="required" name="item_id" id="retail_id">
                                        <input type="text" name="item_name" class="secrh-retails" placeholder="Begin typing item name, SKU, supplier code or barcode">
                                        <div id="test1UL" style="display:none;">
                                            <ul></ul>
                                        </div>
                                    </div>
                                    <div class="itm_inr_sec_s">
                                        <button>Add from Catalog</button>
                                        <button>Quantity Checking</button>
                                        <button>Import</button>
                                    </div>
                                </div>

                                <div class="itm_inr_sec">
                                    <textarea name="" id="" cols="80" rows="3"></textarea>
                                    <div class="sbttl">
                                        <div class="suttl_hding">
                                            <h3>Subtotal</h3>
                                            <span>Type of Tax </span> <br>
                                            <span>Tax </span> <br>
                                            <span> <input type="checkbox"> Tax Included </span>
                                            <h3>Total</h3>
                                        </div>

                                        <div class="suttl_calcu">
                                            <h3>0.00</h3>
                                            <br>
                                            <span>0.00 </span>
                                            <br>
                                            <br>
                                            <h3>0.00</h3>
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
            <!--/.col (right) -->
        </div>
        <!-- /.row -->

    </section>



    <!-- /.content -->
</div>

<script>
    $(document).ready(function() {
        $('#datepicker1').datepicker({
            dateFormat: 'dd-mm-yy',
        });
        //$('#datepicker1').datepicker('setDate', 'today');
    });
</script>
<script>


    function cllbckretail(id, cid, sid) {
        $('.secrh-retail').val(id);
        $('#retail_ids').val(cid);
        $('#testUL').hide();
    }

    $(function() {
        $('.secrh-retail').bind('keyup', function() {
            var pos = $(this).val();
   
            $('#testUL').show();
            $('#retail_ids').val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>sales/getname',
                    data: {
                        'fetch': pos
                    },
                    success: function(data) {
                        // console.log(data);
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
    // const dropdownBtn = document.querySelector(".dropdown__button");
    // const dropdownItems = document.querySelector(".dropdown__items");

    // let isOpen = false;

    // const openDropdown = () => {
    //     isOpen = !isOpen;

    //     if (isOpen) {
    //         dropdownItems.classList.replace("dropdown--hide", "dropdown--show");
    //     } else {
    //         dropdownItems.classList.replace("dropdown--show", "dropdown--hide");
    //     }
    // };

    // dropdownBtn.addEventListener("click", openDropdown);

    //item name
    // function cllbckretail0(id, cid, sid) {
    //     $('.secrh-retails').val(id);
    //     $('#retail_id').val(cid);
    //     $('#test1UL').hide();
    //     testtt(cid);
    //     $.ajax({
    //         type: 'POST',
    //         url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemdetail',
    //         data: {
    //             'fetch': cid
    //         },
    //         success: function(data) {
    //             $('.secrh-retails').val('');
    //             $('.secrh-retails').prop('required', false);
    //         },
    //     });
    // }
    // //get item name
    // $(function() {
    //     $('.secrh-retails').bind('keyup', function() {
    //         var pos = $(this).val();
    //         var check = 0;
    //         $('#test1UL').show();
    //         $('#retail_id').val('');
    //         var count = pos.length;
    //         if (count > 0) {
    //             $.ajax({
    //                 type: 'POST',
    //                 url: '<?php echo ADMIN_URL; ?>Purchaseorder/getitemname',
    //                 data: {
    //                     'fetch': pos,
    //                     'check': check
    //                 },
    //                 success: function(data) {
    //                     $('#test1UL ul').html(data);

    //                 },
    //             });
    //         } else {
    //             $('#test1UL').hide();
    //         }
    //     });
    // });
</script>