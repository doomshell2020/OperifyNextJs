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


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
        Machine Manager
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/machine"><i class="fa fa-home"></i>Home</a></li>
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
                    <?php
                    echo $this->Form->create($machines, array(
                        'class' => 'form-horizontal',
                        'enctype' => 'multipart/form-data',
                        'id' => 'sevice_form',
                        'validate'
                    )); ?>
                    <div class="box-body">

                        <div class="form-group">
                            <div class="row">

                                <div class="col-md col-sm-6">

                                <div class="col-md-6">
                                    <label for="inputEmail3" class=" control-label" style="text-align: left !important">Machine Name<strong style="color:red;">*</strong></label>
                                    <?php echo $this->Form->input('machine_name', array('class' => 'form-control', 'type' => 'text', 'label' => false, 'placeholder' =>'Enter Machine Name', 'autofocus', 'autocomplete' => 'off',
                                    'value' => ($machines['machine_name']),'required',)); ?>
                                </div>
                               
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
                        if (isset($machines['id'])) {
                            echo $this->Form->submit(
                                'Edit',
                                array('class' => 'btn btn-info pull-right ', 'title' => 'Edit','id'=> 'formsubmitbtn')
                            );
                        } else {
                            echo $this->Form->submit(
                                'Add',
                                array('class' => 'btn btn-info pull-right addgen', 'title' => 'Add','id'=> 'formsubmitbtn')
                            );
                        }
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
<script>
    $(document).ready(function () {

        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>