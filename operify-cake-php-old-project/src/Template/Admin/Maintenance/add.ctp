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
    function cllbckretail0(id, cid, sid) {
        $('.secrh-retail').val(id);
        $('#retail_ids').val(cid);
        $('#testUL').hide();
        $('#testUL1').hide();
    }
    $(function() {
        $('.secrh-retail').bind('keyup', function() {
            var pos = $(this).val();
            var check = 0;
            $('#testUL').show();
            $('#retail_ids').val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>production/getname',
                    data: {
                        'fetch': pos,
                        'check': check
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
            Maintenance Manager
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/maintenance"><i class="fa fa-home"></i>Home</a></li>
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
                    <?php echo $this->Form->create(
                        '',
                        array(
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'id' => 'sevice_form',
                            'validate'
                        )
                    ); ?>
                    <div class="box-body">

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md col-sm-4">
                                    <label for="inputEmail3" class="control-label">Date </label>
                                    <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date', 'label' => false, 'readonly'));
                                    ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Machine Name<strong
                                            style="color:red;">*</strong></label>

                                    <input type="hidden" required="required" name="machines_id" id="retail_ids">
                                    <?php echo $this->Form->input('machine_id', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Machine Name')); ?>
                                    <div id="testUL" style="display:none;">
                                        <ul></ul>
                                    </div>
                                    <div id="testUL1" style="display:none;">
                                        <ul>
                                            <li
                                                style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                                No Record Found</li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Type Of Breakdown<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('breakdown_type', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Type Of Breakdown', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Total Time Required (Hrs.)<strong
                                            style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('total_time', array('class' => 'form-control', 'type' => 'number', 'label' => false, 'placeholder' => 'Enter Total Time Takes', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Assigned To<strong style="color:red;">*</strong></label>
                                    <?php
                                    echo $this->Form->control('assigned_to', [
                                        'type' => 'select',
                                        'options' => $assignedToOptions,
                                        'class' => 'form-control',
                                        'label' => false,
                                        'empty' => '------select------',
                                        'required' => true
                                    ]);
                                    ?>
                                </div>

                              
                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Shift Incharge<strong style="color:red;">*</strong></label>

                                    <?php
                                    echo $this->Form->control('shift_incharge', [
                                        'type' => 'select',
                                        'options' => $assigned1,
                                        'class' => 'form-control',
                                        'label' => false,
                                        'empty' => '------select------',
                                        'required' => true
                                    ]);
                                    ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Maintenance Incharge<strong
                                            style="color:red;">*</strong></label>
                                    <?php
                                    echo $this->Form->control('maintenance_incharge', [
                                        'type' => 'select',
                                        'options' => $assignedin,
                                        'class' => 'form-control',
                                        'label' => false,
                                        'empty' => '------select------',
                                        'required' => true
                                    ]);
                                    ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label"
                                        style="text-align: left !important">Production Head<strong
                                            style="color:red;">*</strong></label>
                                    <?php
                                    echo $this->Form->control('production_head', [
                                        'type' => 'select',
                                        'options' => $assignedpro,
                                        'class' => 'form-control',
                                        'label' => false,
                                        'empty' => '------select------',
                                        'required' => true
                                    ]);
                                    ?>
                                </div>

                                <div class="col-md-12">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Remark <strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('remark', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Enter Remark', 'autofocus', 'autocomplete' => 'off', 'required')); ?>
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
                            'Add',
                            array('class' => 'btn btn-info pull-right addgen', 'title' => 'Add', 'id' => 'formsubmitbtn')
                        );
                        ?>
                        <?php
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
        $('#fdatefrom').datepicker('setDate', 'today');
        $('#fendfrom').datepicker({
            dateFormat: 'dd-mm-yy'
        });
        $('#fendfrom').datepicker('setDate', 'today');
    });
</script>
<script>
    $(document).ready(function() {

        $('#sevice_form').on('submit', function(e) {
            $("#formsubmitbtn").css("display", "none");
        });
    });
</script>