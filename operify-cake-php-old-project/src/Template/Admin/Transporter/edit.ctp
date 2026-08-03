<style>
    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }
</style>
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
</style>
<script>
    function cllbckretail(name, id) {
        $('.secrh-retail').val(name);
        $('#retail_ids').val(id);
        $('#testUL').hide();
        $('#testUL1').hide();
    }

    $(document).ready(function() {
        $('.secrh-retail').bind('keyup', function() {
            var pos = $(this).val();
            $('#testUL').show();
            $('#retail_ids').val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>vendors/gettransoptername',
                    data: {
                        'fetch': pos,
                    },

                    success: function(data) {
                        if (data) {
                            console.log(data);
                            $('#testUL ul').html(data);
                        } else {
                            $('#testUL').hide();
                            $('#testUL1').show();
                        }
                    },
                });
            } else {
                $('#testUL').hide();
                $('#testUL1').hide();
            }
        });
    });
</script>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dispatch Manager
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/transporter"><i class="fa fa-home"></i>Home</a></li>
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
                    </div>
                    <!-- /.box-header -->
                    <!-- form start -->
                    <?php echo $this->Form->create('', array(
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'id' => 'sevice_form',
                        'validate'
                    )); ?>
                    <div class="box-body">

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md col-sm-4">

                                    <label for="inputEmail3" class="control-label">Date </label>
                                    <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date', 'label' => false, 'readonly', 'value' => date('d-m-Y', strtotime($users['datefrom']))));
                                    ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Transporter Name<strong style="color:red;">*</strong></label>

                                    <input type="hidden" required="required" name="transports_id" id="retail_ids" value="<?php echo $users['transport_id'] ?>">
                                    <?php echo $this->Form->input('transport_id', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Transporter Name', 'value' => $company_name['name'])); ?>
                                    <div id="testUL" style="display:none;">
                                        <ul></ul>
                                    </div>
                                    <div id="testUL1" style="display:none;">
                                        <ul>
                                            <li style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">No Record Found</li>
                                        </ul>
                                    </div>
                                    <a title="Add Transporter" href="<?php echo ADMIN_URL; ?>transporter/addtransporter" style="color:#2d95e3;  margin-right:5px;" data-toggle="modal" class="delivery_note">
                                        <i class="fa fa-plus" style=" font-size: 16px !important;"></i>
                                    </a>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">To<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('transport_to', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Place', 'autofocus', 'autocomplete' => 'off', 'required', 'value' => $users['transport_to'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important"> From<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('transport_from', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Place', 'autofocus', 'autocomplete' => 'off', 'required', 'required', 'value' => $users['transport_from'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Vehicle No.<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('vehicle_no', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Vehicle No.', 'autofocus', 'autocomplete' => 'off', 'required', 'required', 'value' => $users['vehicle_no'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important"> GR No.<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('gr_no', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter GR No.', 'autofocus', 'autocomplete' => 'off', 'required', 'required', 'value' => $users['gr_no'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Weight<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('weight', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Weight', 'autofocus', 'autocomplete' => 'off', 'required', 'required', 'value' => $users['weight'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Freight<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('freight', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Freight', 'autofocus', 'autocomplete' => 'off', 'required', 'required', 'value' => $users['freight'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Upload</label>
                                    <?php echo $this->Form->input('upload_doc', array('class' => 'form-control', 'type' => 'file', 'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
                                </div>

                            </div>
                        </div>
                    </div>

                    <script>
                        $(document).ready(function() {
                            $('#sevice_form').submit(function(event) {
                                $('.addgen').hide();
                            });
                        });
                    </script>

                    <!-- /.box-body -->
                    <div class="box-footer">
                        <?php
                        echo $this->Form->submit(
                            'Update',
                            array('class' => 'btn btn-info pull-right addgen', 'title' => 'Update','id'=> 'formsubmitbtn')
                        );
                        ?><?php
                            echo $this->Html->link('Back', [
                                'action' => 'index'
                            ], ['class' => 'btn btn-default']); ?>
                    </div>
                    <!-- /.box-footer -->
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<!-------- Data Date picker !---->
<script>
    $(document).ready(function() {
        $('#fdatefrom').datepicker({
            dateFormat: 'dd-mm-yy',
            yearRange: '2018:2030',
            changeMonth: true,
            changeYear: true,
            autoclose: true,
            onSelect: function(date) {

                var selectedDate = new Date(date);
                var endDate = new Date(selectedDate);
                endDate.setDate(selectedDate);

                $("#fendfrom").datepicker("option", "minDate", endDate);
                $("#fendfrom").val(date);
            }
        });

        $('#fendfrom').datepicker({
            dateFormat: 'dd-mm-yy'
        });
        $('#fendfrom').datepicker('setDate', 'today');
    });
</script>
<script>
    $('.delivery_note').click(function(e) {
        e.preventDefault();
        $('#cancelsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>
<div class="modal fade" id="cancelsorts">
    <div class="modal-dialog" style="max-width:999px !important;">
        <div class="modal-content">
            <div class="modal-body purc_mdl_body"></div>
        </div>
    </div>
</div>
<!-- , 'value' => $users['upload'] -->
<script>
    $(document).ready(function () {

        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>