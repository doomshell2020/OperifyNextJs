<style>
    th {
        font-size: 12px !important;
    }

    td {
        font-size: 12px !important;
    }

    .form-control {
        height: 30px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Demo Request Manager</h1>
        <!-- <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/enquiry">Enquiry</a></li>
    </ol>  -->
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <?php echo $this->Flash->render(); ?>
                        <?php $role_id = $this->request->session()->read('Auth.User.role_id'); ?>
                        <?php /*
            <script>
              $(document).ready(function() {
                $("#Mysubscriptions").bind("submit", function(event) {
                  $('.lds-facebook').show();
                  $.ajax({
                    async: true,
                    data: $("#Mysubscriptions").serialize(),
                    dataType: "html",
                    type: "POST",
                    url: "<?php echo ADMIN_URL; ?>enquiry/search",
                    success: function(data) {
                      $('.lds-facebook').hide();
                      $("#example2").html(data);
                    },
                  });
                  return false;
                });
              });

              $(document).on('click', '.pagination a', function(e) {
                var target = $(this).attr('href');
                // console.log("🚀 ~ file: index.ctp:91 ~ $ ~ target:", target);return false;
                var res = target.replace("/enquiry/search", "/enquiry");
                window.location = res;
                return false;
              });
            </script>
            <?php echo $this->Form->create('Mysubscription', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'Mysubscriptions', 'class' => 'form-horizontal')); ?>
            <div class="form-group">
              <div class="col-sm-2">
                <label for="inputEmail3" class="control-label">Name</label>
                <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Name', 'autocomplete' => 'off')); ?>
              </div>
              <div class="col-sm-2">
                <label for="inputEmail3" class="control-label">Mobile</label>
                <?php echo $this->Form->input('mobile', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Mobile', 'autocomplete' => 'off')); ?>
              </div>
              <div class="col-sm-2">
                <label for="inputEmail3" class="control-label">From Date</label>
                <?php echo $this->Form->input('from_date', array('class' => 'form-control input1', 'label' => false, 'placeholder' => 'From Date', 'id' => 'datepicker1', 'autocomplete' => 'off', 'readonly')); ?>
              </div>
              <div class="col-sm-2">
                <label for="inputEmail3" class="control-label">To Date</label>
                <?php echo $this->Form->input('to_date', array('class' => 'form-control input2', 'type' => 'url', 'label' => false, 'placeholder' => 'To Date', 'id' => 'datepicker2', 'autocomplete' => 'off', 'readonly')); ?>
              </div>
              <div class="col-sm-1">
                <label for="inputEmail3" class="control-label" style="color:white">.</label>
                <input type="submit" style="background-color:#00c0ef;" id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">
              </div>
              <?php echo $this->Form->end(); ?>
            </div>
          </div>
          */ ?>
                        <div class="box-body" id="example2">
                            <table class="table table-bordered table-striped" width="100%">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Title</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th style="width: 30%;">Message</th>
                                        <th>IP</th>
                                        <th style="width: 8%;">Created</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $page = $this->request->params['paging']['Demorequest']['page'];
                                    $limit = $this->request->params['paging']['Demorequest']['perPage'];
                                    $counter = ($page * $limit) - $limit + 1;

                                    if (isset($demoreqData) && !empty($demoreqData)) {
                                        foreach ($demoreqData as $order) { //pr($orders);die; 
                                    ?>
                                            <tr>
                                                <td>
                                                    <?php echo $counter; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['company_name']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['title']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['email']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['phone']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['day']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['time']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['message']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $order['ip']; ?>
                                                </td>
                                                <td>
                                                    <?php echo date('d-M-Y', strtotime($order['created'])); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    echo $this->Html->link('', [
                                                        'action' => 'delete',
                                                        $order->id
                                                    ], [
                                                        'class' => 'fas fa-trash-alt',
                                                        'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                                                        "onClick" => "javascript: return confirm('Are you sure do you want to delete this Item')"
                                                    ]); ?>

                                                    <?php
                                                    echo $this->Html->link('', [
                                                        'action' => 'userblock',
                                                        $order->id
                                                    ], ['class' => 'fa fa-ban', 'style' => 'font-size:19px; ', "onClick" => "javascript: return confirm('Are you sure do you want to block this user')"]); ?>
                                                </td>

                                            </tr>
                                    <?php $counter++;
                                        }
                                    } ?>
                                </tbody>

                            </table>
                        </div>
                        <?php echo $this->element('admin/pagination'); ?>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
    </section>
</div>

<!-- content-wrapper -->


<script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>

<script>
    $(function() {
        var dateFormat = 'dd-mm-yy',
            from = $("#datepicker1").datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                numberOfMonths: 1
            }).on("change", function() {
                to.datepicker("option", "minDate", getDate(this));
            }),
            to = $("#datepicker2").datepicker({
                dateFormat: 'dd-mm-yy',
                changeMonth: true,
                numberOfMonths: 1
            }).on("change", function() {
                from.datepicker("option", "maxDate", getDate(this));
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