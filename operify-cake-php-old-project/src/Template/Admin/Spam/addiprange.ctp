<script>
    $(document).ready(function() {
        $("#Mysubscriptions").bind("submit", function(event) {
            $('.lds-facebook').show();
            $.ajax({
                async: true,
                data: $("#Mysubscriptions").serialize(),
                dataType: "html",
                type: "POST",
                url: "<?php echo ADMIN_URL; ?>Whatsappmanager/search",
                success: function(data) {
                    $('.lds-facebook').hide();
                    $("#example2").html(data);
                },
            });
            return false;
        });
    });
</script>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Add IP Range
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/spam"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/spam">IP Range</a></li>
        </ol>
    </section>
    <!-- content header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header" style="padding-bottom:0px;">
                        <?php echo $this->Flash->render(); ?>
                        <?php echo $this->Form->create($sms_details, array(
                            'class' => 'form-horizontal',
                            'enctype' => 'multipart/form-data',
                            'controller' => 'spam',
                            'validate'
                        )); ?>
                        <div class="form-group" style="margin-bottom:0px;">




                            <div class="col-sm-4">
                                <label for="inputEmail3" class="control-label">Start IP<strong
                                        style='color:red;'>*</strong></label>
                                <?php echo $this->Form->input('start_ip', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Start IP', 'autofocus', 'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-sm-4">
                                <label for="inputEmail3" class="control-label">End IP<strong
                                        style='color:red;'>*</strong></label>
                                <?php echo $this->Form->input('end_ip', array('class' => 'form-control', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter End IP', 'autofocus', 'autocomplete' => 'off')); ?>
                            </div>

                            <div class="col-sm-12" style="margin-top:15px;">
                                <?php
                                if (isset($cat['id'])) {
                                    echo $this->Form->submit(
                                        'Update',
                                        array('class' => 'btn btn-info pull-right', 'title' => 'Update')
                                    );
                                } else {
                                    echo $this->Form->submit(
                                        'Add',
                                        array('class' => 'btn btn-info', 'title' => 'Add')
                                    );
                                }
                                ?>
                                <?php echo $this->Form->end(); ?>
                            </div>
                        </div>

                        <!-- /.box-header -->
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>