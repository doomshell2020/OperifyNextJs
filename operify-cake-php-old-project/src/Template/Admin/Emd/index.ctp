<style>
    .input_fields_wrap .form-control {
        margin-bottom: 15px;
    }

    .control-label {
        display: block;
        margin-bottom: 10px;
    }

    label[for="consumble-y"] {
        width: 47%;
        padding: 4px 8px;
        border: 1px solid #ccc;
        margin-right: 6%;
        border-radius: 3px;
    }

    label[for="consumble-n"] {
        width: 47%;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    #itemtestUL {
        position: relative;
    }

    #itemtestUL ul {
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

    #itemtestUL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
    }

    #itemtestUL ul li a {
        color: black;
    }

    #contractUL {
        position: relative;
    }

    #contractUL ul {
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

    #contractUL ul li {
        padding: 5px 8px;
        border: 1px solid lightgray;
        margin-left: 0px !important;
    }

    #contractUL ul li a {
        color: black;
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
            EMD Manager
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo SITE_URL; ?>admin/emd"><i class="fa fa-home"></i>Home</a></li>
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
                        <!-- <?php echo $this->Form->create('', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription', 'class' => 'form-horizontal', 'style' => 'margin-bottom:0px;')); ?> -->
                        <div class="form-group" style="margin-bottom:0px;">

                            <?php
                            echo $this->Form->create(null, [
                                'type' => 'get',
                                'url' => ['controller' => 'Emd', 'action' => 'index', 'prefix' => 'admin'],
                                'inputDefaults' => ['div' => false, 'label' => false],
                                'id' => 'mysubscription',
                                'class' => 'form-horizontal',
                                'style' => 'margin-bottom:0px;'
                            ]);
                            ?>

                            <div class="row">

                                <div class="col">
                                    <label class="control-label">BG For</label>
                                    <?= $this->Form->input('bg_for', [
                                        'type' => 'select',
                                        'options' => [
                                            'PG-HDFC' => 'PG - HDFC Bank',
                                            'PG-Axis Bank' => 'PG - Axis Bank',
                                            'EMD-HDFC' => 'EMD - HDFC Bank',
                                            'EMD-Axis Bank' => 'EMD - Axis Bank',
                                            'EMD-Cheque/Online' => 'EMD - Cheque/Online',
                                        ],
                                        'label' => false,
                                        'class' => 'form-control',
                                        'empty' => '-- Select Type --',
                                        'value' => $this->request->query('bg_for')
                                    ]) ?>
                                </div>
                                <div class="col">
                                    <label class="control-label">BG No.</label>
                                    <?= $this->Form->input('bankguaranteeno', [
                                        'type' => 'text',
                                        'class' => 'form-control',
                                        'placeholder' => 'Enter BG No.',
                                        'label' => false,
                                        'value' => $this->request->query('bankguaranteeno')
                                    ]) ?>
                                </div>

                                <div class="col">
                                    <label class="control-label">Date Type</label>
                                    <?= $this->Form->input('filter_type', [
                                        'type' => 'select',
                                        'options' => [
                                            'date' => 'Date Range',
                                            'due' => 'Due Range'
                                        ],
                                        'label' => false,
                                        'class' => 'form-control',
                                        'id' => 'filterTypeSelect',
                                        'value' => $this->request->query('filter_type') ?: 'date'
                                    ]) ?>
                                </div>

                                <div class="col" id="dateRangeFields">
                                    <label class="control-label">From Date</label>
                                    <?= $this->Form->input('from_date', [
                                        'type' => 'text',
                                        'class' => 'form-control',
                                        'id' => 'fdatefrom',
                                        'readonly',
                                        'placeholder' => 'From Date',
                                        'label' => false,
                                        'value' => $this->request->query('from_date')
                                    ]) ?>
                                </div>

                                <div class="col" id="dateRangeFields2">
                                    <label class="control-label">To Date</label>
                                    <?= $this->Form->input('to_date', [
                                        'type' => 'text',
                                        'class' => 'form-control',
                                        'id' => 'fdateto',
                                        'readonly',
                                        'placeholder' => 'To Date',
                                        'label' => false,
                                        'value' => $this->request->query('to_date')
                                    ]) ?>
                                </div>

                                <div class="col d-none" id="dueRangeFields">
                                    <label class="control-label">Due From</label>
                                    <?= $this->Form->input('due_from', [
                                        'type' => 'text',
                                        'class' => 'form-control',
                                        'id' => 'fduefrom',
                                        'readonly',
                                        'placeholder' => 'Due From Date',
                                        'label' => false,
                                        'value' => $this->request->query('due_from')
                                    ]) ?>
                                </div>

                                <div class="col d-none" id="dueRangeFields2">
                                    <label class="control-label">Due To</label>
                                    <?= $this->Form->input('due_to', [
                                        'type' => 'text',
                                        'class' => 'form-control',
                                        'id' => 'fdueto',
                                        'readonly',
                                        'placeholder' => 'Due To Date',
                                        'label' => false,
                                        'value' => $this->request->query('due_to')
                                    ]) ?>
                                </div>


                                <div class="col">
                                    <label class="control-label">Status</label>
                                    <?= $this->Form->input('status', [
                                        'type' => 'select',
                                        'options' => [
                                            'N' => 'Pending',
                                            'Y' => 'Completed'
                                        ],
                                        'label' => false,
                                        'class' => 'form-control',
                                        'value' => $this->request->query('status')
                                    ]) ?>
                                </div>

                                <script>
                                    $(document).ready(function() {
                                        var dateFormat = "dd-mm-yy";

                                        var from = $("#fdatefrom")
                                            .datepicker({
                                                dateFormat: dateFormat,
                                                yearRange: '2018:2030',
                                                changeMonth: true,
                                                changeYear: true,
                                            })
                                            .on("change", function() {
                                                to.datepicker("option", "minDate", getDate(this));
                                            });

                                        var to = $("#fdateto")
                                            .datepicker({
                                                dateFormat: dateFormat,
                                                yearRange: '2018:2030',
                                                changeMonth: true,
                                                changeYear: true,
                                            })
                                            .on("change", function() {
                                                from.datepicker("option", "maxDate", getDate(this));
                                            });
                                        // Due From / Due To Date Pickers
                                        var dueFrom = $("#fduefrom").datepicker({
                                            dateFormat: dateFormat,
                                            yearRange: '2018:2030',
                                            changeMonth: true,
                                            changeYear: true,
                                        }).on("change", function() {
                                            dueTo.datepicker("option", "minDate", getDate(this));
                                        });

                                        var dueTo = $("#fdueto").datepicker({
                                            dateFormat: dateFormat,
                                            yearRange: '2018:2030',
                                            changeMonth: true,
                                            changeYear: true,
                                        }).on("change", function() {
                                            dueFrom.datepicker("option", "maxDate", getDate(this));
                                        });

                                        function getDate(element) {
                                            var date;
                                            try {
                                                date = $.datepicker.parseDate(dateFormat, element.value);
                                            } catch (error) {
                                                date = null;
                                            }
                                            return date;
                                        }
                                    });
                                </script>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const filterSelect = document.getElementById('filterTypeSelect');
                                        const dateFields = [document.getElementById('dateRangeFields'), document.getElementById('dateRangeFields2')];
                                        const dueFields = [document.getElementById('dueRangeFields'), document.getElementById('dueRangeFields2')];

                                        function toggleFields() {
                                            if (filterSelect.value === 'due') {
                                                dateFields.forEach(el => el.classList.add('d-none'));
                                                dueFields.forEach(el => el.classList.remove('d-none'));
                                            } else {
                                                dateFields.forEach(el => el.classList.remove('d-none'));
                                                dueFields.forEach(el => el.classList.add('d-none'));
                                            }
                                        }

                                        filterSelect.addEventListener('change', toggleFields);

                                        toggleFields();
                                    });
                                </script>


                                <div class="col">
                                    <input type="submit"
                                        style="background-color:#00c0ef; color:#fff;width:100px !important;margin-top:19px;" id=""
                                        class="btn btn4 btn_pdf myscl-btn date" value="Search">

                                    <a href="<?php echo SITE_URL; ?>admin/emd/index" class="excelbtn btn"
                                        style="background-color:#00c0ef; !important; margin-top: 19px; color:#fff; padding:6px 18px;">Reset</a>
                                    <?php echo $this->Form->end(); ?>
                                </div>



                                <div class="col">
                                    <?php
                                    $role_permissions = $this->Permission->permissioncheck();
                                    $fileurl = "admin/emd/add";
                                    if (in_array($fileurl, $role_permissions)) { ?>
                                        <a class="btn btn-success pull-right m-top10"
                                            href="<?php echo SITE_URL; ?>admin/emd/add"
                                            style="background-color:#2d95e3;color:#fff;margin-top:19px;">
                                            <i class="fa fa-plus" aria-hidden="true"></i>Add</a>
                                    <?php } ?>

                                    <a href="<?php echo SITE_URL; ?>admin/emd/excel" class="excelbtn btn pull-right" style="padding:0;margin-top: 23px;"><i class="fa fa-file-excel-o" style="font-size:28px; margin-right:10px;"></i></a>

                                </div>

                            </div>

                        </div>
                    </div>


                    <!-- box-header -->
                    <div id="load2" style="display:none;"></div>
                    <div class="box-body" style="padding:0px; margin-top:10px;">
                        <table class="table table-bordered table-striped" id="example23" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th style="width: 7%;">Date</th>
                                    <th style="width: 8%;">BG For</th>
                                    <th>BG No.</th>
                                    <th>Favour</th>
                                    <th>PO.No./Tender No.</th>
                                    <th style="width: 13%;">Vaild Dates</th>
                                    <th>Amount</th>
                                    <th>Contect Person</th>
                                    <th style="width: 7%;">File</th>
                                    <th style="width: 6%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // pr($this->request->params);exit;
                                $page = $this->request->params['paging']['EmdGuarantees']['page'];
                                $limit = $this->request->params['paging']['EmdGuarantees']['perPage'];
                                $counter = ($page * $limit) - $limit + 1;
                                if (isset($EmdGuarantees) && !empty($EmdGuarantees)) {
                                    foreach ($EmdGuarantees as $bg) {
                                        $lastremark = $this->Comman->getemdremark($bg['id']);
                                ?>
                                        <tr>
                                            <td><?php echo $counter; ?></td>
                                            <td><?php echo !empty($bg['datefrom']) ? date('d-m-Y', strtotime($bg['datefrom'])) : '-'; ?></td>
                                            <td><?php echo h($bg['bg_for']); ?></td>
                                            <td><a href="<?php echo SITE_URL; ?>admin/emd/viewremarks/<?php echo $bg['id']; ?>"
                                                    class="designsheetdetails"><?php echo $bg['bankguaranteeno']; ?></a></td>


                                            <td><?php echo !empty($bg['favour_of']) ? $bg['favour_of'] : '-';
                                                ?></td>
                                            <td><?php echo
                                                !empty($bg['po_no']) ? $bg['po_no'] : '-'; ?></td>


                                            <td>
                                                <?php if (!empty($bg['validupto'])): ?>
                                                    <?= 'Valid Upto: ' . date('d-m-Y', strtotime($bg['validupto'])) ?><br>
                                                <?php endif; ?>

                                                <?php if (!empty($bg['claim_upto'])): ?>
                                                    <?= 'Claim Date: ' . date('d-m-Y', strtotime($bg['claim_upto'])) ?><br>
                                                <?php endif; ?>

                                                <?php if (!empty($bg['extenstionupto'])): ?>
                                                    <?= 'Extension Date: ' . date('d-m-Y', strtotime($bg['extenstionupto'])) ?><br>
                                                <?php endif; ?>
                                            </td>

                                            <td style="text-align: right;">
                                                <?php
                                                $symbol = '';
                                                if ($bg['currency_type'] == 'USD') {
                                                    $symbol = '$';
                                                } elseif ($bg['currency_type'] == 'INR') {
                                                    $symbol = '';
                                                } elseif ($bg['currency_type'] == 'EUR') {
                                                    $symbol = '€';
                                                } elseif ($bg['currency_type'] == 'GBP') {
                                                    $symbol = '£';
                                                }
                                                echo h($symbol . number_format($bg['amount']));
                                                ?>
                                            </td>


                                            <td><?php echo
                                                !empty($bg['contect_per']) ? $bg['contect_per'] : '-'; ?></td>
                                            <td style="text-align:center;">
                                                <?php if (!empty($bg['invoice_file'])) {
                                                    $db = $this->request->session()->read('Auth.User.db');
                                                    $filePath = '/images/' . $db . '_image/emd/' . h($bg['invoice_file']);
                                                ?>
                                                    <a href="javascript:void(0);" onclick="openFilePopup('<?= $this->Url->build($filePath, ['fullBase' => true]) ?>')" class="btn btn-sm btn-primary">
                                                        View File
                                                    </a>
                                                <?php } else { ?>
                                                    N/A
                                                <?php } ?>
                                            </td>
                                            <td>

                                                <?php
                                                $role_permissions = $this->Permission->permissioncheck();
                                                if ($bg['status'] == 'Y') { ?>
                                                    <a href="<?php echo SITE_URL; ?>admin/emd/viewamount/<?php echo $bg['id']; ?>"
                                                        class="Viewamount fa fa-inr text-success"></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo SITE_URL; ?>admin/emd/viewamount/<?php echo $bg['id']; ?>"
                                                        class="Viewamount fa fa-inr text-danger"></a>
                                                <?php }





                                                $fileurl = "admin/emd/edit";
                                                if (in_array($fileurl, $role_permissions)) {
                                                    echo $this->Html->link('', [
                                                        'action' => 'edit',
                                                        $bg->id
                                                    ], ['class' => 'fas fa-edit', 'style' => 'font-size: 18px;  padding-left: 6px;padding-right: 6px;']);
                                                }
                                                $fileurl = "admin/emd/delete";
                                                if (in_array($fileurl, $role_permissions)) {
                                                    echo $this->Html->link('', [
                                                        'action' => 'delete',
                                                        $bg->id
                                                    ], ['class' => 'fas fa-trash-alt', 'style' => 'font-size: 18px; color:#c12020;', "onClick" => "javascript: return confirm('Are you sure you want to delete this EMD ?')"]);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                <?php
                                        $counter++;
                                    }
                                } else {
                                    echo '<tr><td colspan="15" style="text-align:center;">No Records Found</td></tr>';
                                }
                                ?>
                            </tbody>

                        </table>
                        <?php echo $this->element('admin/pagination'); ?>
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

<script>
    $(document).ready(function() {
        // $("#mysubscription").bind("submit", function(event) {
        //     $.ajax({
        //         async: true,
        //         data: $("#mysubscription").serialize(),
        //         dataType: "html",
        //         type: "GET",
        //         url: "<?php echo ADMIN_URL; ?>emd/searchitem",

        //         // success: function (data) {
        //         //    console.log(data);
        //         //    $("#example23").html(data);
        //         // },
        //         beforeSend: function(xhr) {
        //             xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
        //             $('#load2').css("display", "block"); // Show loader
        //         },
        //         success: function(data) {
        //             $('.lds-facebook').hide();
        //             $("#example23").html(data);
        //         },
        //         complete: function() {
        //             $('#load2').css("display", "none"); // Hide loader
        //         },
        //         error: function() {
        //             alert("An error occurred. Please try again.");
        //             $('#load2').css("display", "none"); // Hide loader on error
        //         }

        //     });
        //     return false;
        // });

        $(document).on('click', '.pagination a', function(e) {
            var target = $(this).attr('href');
            var res = target.replace("/emd/searchitem", "/emd");
            window.location = res;
            return false;
        });
    });
</script>

<script>
    $('.Viewamount').click(function(e) {
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

<script>
    $('.designsheetdetails').click(function(e) {
        e.preventDefault();
        $('#designsorts').modal('show').find('.modal-body').load($(this).attr('href'));
    });
</script>

<div class="modal fade" id="designsorts">
    <div class="modal-dialog" style="max-width:900px !important;">
        <div class="modal-content">
            <div class="modal-body"></div>
        </div>
    </div>
</div>
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
    function openFilePopup(fileUrl) {
        const isPdf = fileUrl.toLowerCase().endsWith('.pdf');
        const popup = window.open('', '_blank', 'width=800,height=600');

        if (isPdf) {
            popup.document.write(`<iframe src="${fileUrl}" width="100%" height="100%" style="border:none;"></iframe>`);
        } else {
            popup.document.write(`<img src="${fileUrl}" style="max-width:100%;max-height:100%;" />`);
        }

        popup.document.title = "View File";
    }
</script>