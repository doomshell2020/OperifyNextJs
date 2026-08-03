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


    #load2 {
        width: 100%;
        height: 100%;
        position: fixed;
        z-index: 9999;
        background-color: white !important;
        background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Inspection Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/inspection"><i class="fa fa-home"></i>Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>admin/inspection">Inspection Report</a></li>
        </ol>
    </section>
    <!-- content header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <?php echo $this->Flash->render(); ?>
                        <a href="<?php echo SITE_URL; ?>admin/inspection/add">
                            <button class="btn btn-success pull-right m-top10" style="margin-top: 20px;">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                Add Inspection Report
                            </button>
                        </a>

                        <script>
                            $(document).ready(function() {
                                $("#Mysubscriptions").bind("submit", function(event) {
                                    $('.lds-facebook').show();
                                    $.ajax({
                                        async: true,
                                        data: $("#Mysubscriptions").serialize(),
                                        dataType: "html",
                                        type: "POST",
                                        url: "<?php echo ADMIN_URL; ?>Inspection/searchitem",
                                       

                                        beforeSend: function(xhr) {
                                            xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                                            $('#load2').css("display", "block"); // Show loader
                                        },
                                        success: function(data) {
                                            $('.lds-facebook').hide();
                                            $("#example2").html(data);
                                        },
                                        complete: function() {
                                            $('#load2').css("display", "none"); // Hide loader
                                        },
                                        error: function() {
                                            alert("An error occurred. Please try again.");
                                            $('#load2').css("display", "none"); // Hide loader on error
                                        }



                                    });
                                    return false;
                                });
                            });
                        </script>
                        <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => 'form-horizontal')); ?>
                        <div class="form-group" style="display:flex; align-items: flex-end;">
                            <div class="col-sm-10">
                                <div class="row">
                                    <!-- <div class="col-sm-3">
                                        <label for="inputEmail3" class="control-label">W.O No.</label>
                                        <?php echo $this->Form->input('work_order_no', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Enter Work Order No', 'autocomplete' => 'off'));
                                        ?>
                                    </div> -->


                                    <div class="col-sm-3">
                                        <label for="inputEmail3" class=" control-label"
                                            style="text-align: left !important">Contract
                                            Name</label>
                                        <input type="hidden" name="work_order_no" id="contrselectid">
                                        <?php echo $this->Form->input('contractname', array('class' => 'form-control secrhcontract', 'id' => 'contractnameid', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Contract Name')); ?>
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

                                    <div class="col-sm-3">
                                        <label for="inputEmail3" class="control-label"
                                            style="text-align: left !important;">Inspection Date</label>
                                        <?php echo $this->Form->input('inspection_date', array('class' => 'form-control', 'id' => 'fdatefrom1', 'readonly', 'placeholder' => 'Date', 'label' => false, 'readonly')); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3" style="margin-left: -397px;">
                                <input type="submit" style="background-color:#00c0ef; color:#fff;" id="Mysubscriptions"
                                    class="btn btn4 btn_pdf myscl-btn date" value="Search">
                                <a href="<?php echo SITE_URL; ?>admin/Inspection" class="excelbtn btn"
                                    style="background-color:#00c0ef; !important;  color:#fff; padding:6px 18px; margin-left: 7px;">Reset</a>
                            </div>
                        </div>
                        <!-- </div>box-header -->
                        <div id="load2" style="display:none;"></div>
                        <div class="box-body" id="example2" style="padding:0px; margin-top:10px;">
                            <table id="example14" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Contract Name</th>
                                        <th>Name</th>
                                        <th>Remark</th>
                                        <th>Inspection Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $page = $this->request->params['paging']['']['page'];
                                    $limit = $this->request->params['paging']['']['perPage'];
                                    $counter = ($page * $limit) - $limit + 1;
                                    if (isset($users) && !empty($users)) {
                                        foreach ($users as $intusr) {
                                            $contractname = $this->Comman->findcontractname($intusr['work_order_no']);
                                            $bomid = $this->Comman->findbomdetails($intusr['work_order_no']);
                                    ?>
                                            <tr>
                                                <td>
                                                    <?php echo $counter; ?>
                                                </td>
                                                <td><a href="<?php echo SITE_URL; ?>admin/production/viewcontractdetail/<?php echo $intusr['work_order_no']; ?>"
                                                        class="viewdetails">
                                                        <?php echo $contractname['title'] . '(' . $contractname['workorder'] . ')'; ?>
                                                    </a></td>
                                                <td>
                                                    <?php echo $intusr['name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $intusr['remark']; ?>
                                                </td>
                                                <td>
                                                    <?php echo date('Y-m-d', strtotime($intusr['inspection_date'])); ?>
                                                </td>
                                                <td><a target="_blank"
                                                        href="<?php echo SITE_URL . 'InspectionReport/' . $intusr['file']; ?>"
                                                        title="Download Inspection Report"><span
                                                            class="fa fa-download fa-lg text-green"></span></a>
                                                    &nbsp;
                                                    <?php
                                                    echo $this->Html->link('', [
                                                        'action' => 'delete',
                                                        $intusr->id
                                                    ], [
                                                        'class' => 'fas fa-trash-alt',
                                                        'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                                        "onClick" => "javascript: return confirm('Are you sure do you want to delete this Inspection Report')"
                                                    ]); ?>


                                                </td>

                                            </tr>
                                        <?php $counter++;
                                        }
                                    } else { ?>
                                    <?php } ?>
                                </tbody>
                            </table>
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
<!-- /.   content-wrapper -->
<!-- <script>
   $(document).ready(function() {
      $(".add-batch-fields").click(function() {
         $.ajax({
            type: "POST",
            url: '<?php // echo SITE_URL; 
                    ?>admin/additem/add',
            cache: false,
            success: function(html) {
               //alert(html);   
               $(".product_containes").append(html);
            }
         });
      });

      $("body").on("click", ".remove", function() {
         //alert('hello');
         $(this).closest('.formdetails').remove();
      });
   });
</script> -->
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
<script>
    function cllbckretail2(id, cid) {
        $('.secrhcontract').val(id);
        $('#contrselectid').val(cid);
        $('#contractUL').hide();
        $('#contractUL1').hide();
    }
    $(function() {
        $('.secrhcontract').bind('keyup', function() {
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
                    success: function(data) {
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
    $('.viewdetails').click(function(e) {
        e.preventDefault();
        $('#editsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>

<div class="modal fade" id="editsorts">
    <div class="modal-dialog" style="max-width:900px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>