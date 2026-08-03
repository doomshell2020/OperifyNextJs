<style>
    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }
</style>
<style>
    #poidUL {
        position: relative;
    }

    #poidUL ul {
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

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
      Add Inspection Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/production"><i class="fa fa-home"></i>Home</a></li>
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
                    <?php echo $this->Form->create($productions, array(
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'id' => 'sevice_form',
                        'validate'
                    )); ?>
                    <div class="box-body">

                        <div class="form-group">
                            <div class="row">
                                <script>
                                    $(document).ready(function() {
                                        $('#fdatefrom1').datepicker({
                                            dateFormat: 'dd-mm-yy',
                                            yearRange: '2018:2030',
                                            changeMonth: true,
                                            changeYear: true,
                                            autoclose: true,
                                            onSelect: function(date) {
                                                var selectedDate = new Date(date);
                                                var endDate = new Date(selectedDate);
                                                endDate.setDate(selectedDate);
                                            }
                                        });
                                        $('#fdatefrom1').datepicker('setDate', 'today');
                                    });
                                </script>

                                <!-- <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label " style="text-align: left !important">Work Order No.</label>

                                    <?php echo $this->Form->input('wo_no', array('class' => 'form-control searchpo', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Work Order No.')); ?>
                                    <div id="poidUL" style="display:none;">
                                        <ul></ul>
                                    </div>
                                    <div id="poidUL1" style="display:none;">
                                        <ul>
                                            <li style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                                No Record Found</li>
                                        </ul>
                                    </div>
                                </div> -->

                                <div class="col-sm-4">
                                <label for="inputEmail3" class=" control-label"
                                    style="text-align: left !important">Contract
                                    Name</label>
                                <input type="hidden" name="wo_no" id="contrselectid">
                                <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Contract Name')); ?>
                                <div id="contractUL" style="display:none;">
                                    <ul></ul>
                                </div>
                                <div id="contractUL1" style="display:none;">
                                    <ul>
                                        <li
                                            style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">
                                            No Record Found</li>
                                    </ul>
                                </div>
                            </div>
                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label " style="text-align: left !important">Name</label>
                                    <?php echo $this->Form->input('name', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Name')); ?>

                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label " style="text-align: left !important">Date</label>
                                    <?php echo $this->Form->input('inspection_date', array('class' => 'form-control', 'id' => 'fdatefrom1', 'readonly', 'placeholder' => 'Date', 'label' => false, 'readonly')); ?>
                                </div>


                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label " style="text-align: left !important">Upload</label>
                                    <?php echo $this->Form->input('doc_upload', array('class' => 'form-control', 'type' => 'file', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required')); ?>

                                </div>

                                <div class="col-md-12">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">
                                        Remark </label>
                                    <?php echo $this->Form->input('remark', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Remark', 'autofocus', 'autocomplete' => 'off')); ?>
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
                        if (isset($category['id'])) {
                            echo $this->Form->submit(
                                'Update',
                                array('class' => 'btn btn-info pull-right ', 'title' => 'Update','id'=> 'formsubmitbtn')
                            );
                        } else {
                            echo $this->Form->submit(
                                'Add',
                                array('class' => 'btn btn-info pull-right addgen', 'title' => 'Add','id'=> 'formsubmitbtn')
                            );
                        }
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
<!-- 
 <script>
    function cllbckretail(id) {
        $('.searchpo').val(id);
        $('#porduction_id').val(id);
        finishedproduct(id);
        $('#poidUL').hide();
        $('#poidUL1').hide();
    }
    $(function() {
        $('.searchpo').bind('keyup', function() {
            var pos = $(this).val();
            // alert(pos);
            var check = 2;
            $('#poidUL').show();
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>inspection/getworkorderID',
                    data: {
                        'fetch': pos,
                        'check': check
                    },
                    success: function(data) {
                        if (data) {
                            // console.log(data);
                            $('#poidUL ul').html(data);
                            $('#poidUL1').hide();
                        } else {
                            $('#poidUL').hide();
                            $('#poidUL1').show();
                        }
                    },
                });
            } else {
                $('#poidUL').hide();
                $('#poidUL1').hide();
            }
        });
    });
</script>  -->

<script>
    function cllbckretail2(id, cid) {
        $('.secrhcontract').val(id);
        $('#contrselectid').val(cid);
        $('#contractUL').hide();
        $('#contractUL1').hide();
    }
    $(function () {
        $('.secrhcontract').bind('keyup', function () {
            var pos = $(this).val();
            var check = 2;
            $('#contractUL').show();
            $('#contrselectid').val('');
            var count = pos.length;
            if (count > 0) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo ADMIN_URL; ?>production/getcontract',
                    data: {
                        'fetch': pos,
                        'check': check
                    },
                    success: function (data) {
                        if (data) {
                            console.log(data);
                            $('#contractUL ul').html(data);
                            $('#contractUL1').hide();
                        } else {
                            $('#contractUL').hide();
                            $('#contractUL1').show();
                        }
                    },
                });
            } else {
                $('#contractUL').hide();
                $('#contractUL1').hide();
            }
        });
    });
</script>
<script>
    $(document).ready(function () {

        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>