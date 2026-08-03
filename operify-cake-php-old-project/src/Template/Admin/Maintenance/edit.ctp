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
                                  if(data){
                                    console.log(data);
                                    $('#testUL ul').html(data);
                                  }else{
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
                                    <?php echo $this->Form->input('datefrom', array('class' => 'form-control', 'id' => 'fdatefrom', 'readonly', 'placeholder' => 'Date', 'label' => false, 'readonly','value' => date('d-m-Y', strtotime($users['datefrom']))));
                                    ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Machine Name<strong style="color:red;">*</strong></label>

                                    <input type="hidden"  name="machines_id" id="retail_ids" value = "<?php echo $users['machine_id']; ?> ">
                                    <?php echo $this->Form->input('machine_name', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'placeholder' => 'Enter Machine Name','value' => $machine_name['machine_name'])); ?>
                                    <div id="testUL" style="display:none;">
                                        <ul></ul>
                                    </div>
                                    <div id="testUL1"  style="display:none;">
                                    <ul>
                                    <li style="padding: 5px 8px;list-style:none;color: black;font-weight: bold;margin-left:-32px; border: 1px solid lightgray;">No Record Found</li>
                                    </ul>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Type Of Breakdown<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('breakdown_type', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Type Of Breakdown', 'autofocus', 'autocomplete' => 'off','required','value' => $users['breakdown_type'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important"> Assigned To<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('assigned_to', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Assigned To', 'autofocus', 'autocomplete' => 'off','required','value' => $users['assigned_to'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Total Time Takes<strong style="color:red;">*</strong></label>
                                <?php echo $this->Form->input('total_time', array('class' => 'form-control', 'type' => 'number', 'label' => false, 'placeholder' => 'Enter Total Time Takes', 'autofocus', 'autocomplete' => 'off','required','value' => $users['total_time'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important"> Shift Incharge<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('shift_incharge', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Shift Incharge', 'autofocus', 'autocomplete' => 'off','required','value' => $users['shift_incharge'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Maintenance Incharge<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('maintenance_incharge', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Maintenance Incharge', 'autofocus', 'autocomplete' => 'off','required','value' => $users['maintenance_incharge'])); ?>
                                </div>

                                <div class="col-md-4">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Production Head<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('production_head', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' => 'Enter Production Head', 'autofocus', 'autocomplete' => 'off','required','value' => $users['production_head'])); ?>
                                </div>

                                <div class="col-md-12">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important"> Remark <strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('remark', array('class' => 'form-control', 'type' => 'textarea', 'label' => false, 'placeholder' => 'Enter Remark', 'autofocus', 'autocomplete' => 'off','required','value' => $users['remark'])); ?>
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
    $(document).ready(function () {

        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>