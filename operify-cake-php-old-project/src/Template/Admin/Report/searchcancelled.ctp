<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

<?php $datefrom = date('d-m-Y', strtotime($datefrom));
$dateto2 = date('d-m-Y', strtotime($dateto2)); ?>
<script>
  $(document).ready(function() {
    $("#btnExport").click(function(e) {

      //getting data from our table
      var data_type = 'data:application/vnd.ms-excel';
      var table_div = document.getElementById('example1');
      var table_html = table_div.outerHTML.replace(/ /g, '%20');
      var title = '<?php echo $datefrom; ?>-<?php echo $dateto2; ?>';
      var a = document.createElement('a');
      a.href = data_type + ', ' + table_html;
      a.download = 'Fees_reciept_' + title + '.xls';
      a.click();
    });


  });
</script>
<div class="pull-right">
<button class="btnp btn btn-success pull-left" style="margin-bottom: 10px;" id="btnExport">Export to xls</button>
</div>
<table id="example1" class="table table-bordered table-striped">
  <tbody>
    <tr>
      <th>S.No</th>
      <th>Recpt.No.</th>
      <th>PayDate</th>
      <th>Academic Year</th>
      <th>Sr.No.</th>
      <th style="width:20%">Name of Student</th>
      <th style="width:20%">Father's Name</th>
      <th>Class</th>
      <th>Quater</th>
      <th>Discount</th>
      <th>Add.Discount</th>
      <th>Late Fee</th>
      <th>Due Amount</th>
      <th>Deposit Amt.</th>
      <th>Payment Mode</th>
      <th>Bank Detail</th>
      <th>Status</th>
      <th>Remarks</th>

    </tr>

    <?php
    $page = $this->request->params['paging']['Services']['page'];
    $limit = $this->request->params['pagc_c_iding']['Services']['perPage'];
    $counter = ($page * $limit) - $limit + 1;
    $total = 0;
    $totalfee = 0;
    $out = 0;
    $total_discount = 0;
    $session = $this->request->session();


    $session->delete($Classectionsfees);
    $session->write('Classectionsfees', $Classectionsfees);
    if ($s_id) {
      $session->delete($s_id);
      $session->write('s_id', $s_id);
    }
    $session->delete($acedmicyear);
    $session->write('acedmicyear', $acedmicyear);
    $session->delete($datefrom);
    $session->write('datefrom', $datefrom);
    $session->delete($dateto2);
    $session->write('dateto2', $dateto2);

    $session->delete($status);
    $session->write('status', $status);

    $total = 0;
    if (isset($Classectionsfees) && !empty($Classectionsfees)) {

      foreach ($Classectionsfees as $key => $element) {

        $s_id = $element['student']['class_id'];
        $c_id = $element['student']['section_id'];
    ?>
        <tr <? if ($element['status'] == "N") { ?>style="color:red;" <? } ?>>
          <td><?php echo $counter; ?></td>
          
          <td>            
            <?php 
              echo $element['recipetno'];
             ?>
          </td>

          <td><?php echo date('d-m-Y', strtotime($element['paydate'])); ?></td>
          <td><?php echo $element['acedmicyear']; ?></td>
          <td><?php if ($element['type'] == "Fee") {
                echo $element['student']['enroll'];
              } else {

              ?><b style="color:green;"><? if ($element['type'] == "Prospectus") {
                                        $prospect = $this->Comman->findprospectus($element['recipetno'], $element['formno']);
                                        $cl = $this->Comman->findclass($prospect['class_id']);
                                        echo "Prospectus";
                                      }
                                      if ($element['type'] == "Registration") {
                                        $applicant = $this->Comman->findapplicant($element['recipetno'], $element['formno']);
                                        $cls = $this->Comman->findclass($applicant['class_id']);
                                        echo "Registration";
                                      }
                                      if ($element['type'] == "Other") {
                                        $other = $this->Comman->findotherfees($element['recipetno']);

                                        if (isset($other[0]['s_id']) && !empty($other[0]['s_id'])) {
                                          echo $other[0]['s_id']; ?> (Other Fees)<?php
                                                        } else {
                                                          echo "Other Fees";
                                                        }
                                                      } ?></b><?

                            } ?></td>
          <td><? if ($element['type'] == "Fee") { ?><?php echo $element['student']['fname'] . " ";
                                                echo $element['student']['middlename'] . " ";
                                                echo $element['student']['lname']; ?> <? } else {

                                                                                                                                                                              if ($element['type'] == "Prospectus") {
                                                                                                                                                                                $prospect = $this->Comman->findprospectus($element['recipetno'], $element['formno']);
                                                                                                                                                                                echo $prospect['s_name'];
                                                                                                                                                                              }

                                                                                                                                                                              if ($element['type'] == "Registration") {
                                                                                                                                                                                $applicant = $this->Comman->findapplicant($element['recipetno'], $element['formno']);
                                                                                                                                                                                echo $applicant['fname'] . " " . $applicant['middlename'] . " " . $applicant['lname'];
                                                                                                                                                                              }
                                                                                                                                                                              if ($element['type'] == 'Other') {

                                                                                                                                                                                echo $other[0]['pupilname'];
                                                                                                                                                                              }
                                                                                                                                                                            } ?></td>

          <td><? if ($element['type'] == "Fee") {
              ?><?php echo $element['student']['fathername']; ?><? } else {
                                                                if ($element['type'] == "Prospectus") {
                                                                  $prospect = $this->Comman->findprospectus($element['recipetno'], $element['formno']);

                                                                  echo $prospect['fee_submittedby'];
                                                                }
                                                                if ($element['type'] == "Registration") {
                                                                  $applicant = $this->Comman->findapplicant($element['recipetno'], $element['formno']);
                                                                  echo $applicant['f_name'];
                                                                }
                                                                if ($element['type'] == 'Other') {

                                                                  echo $other[0]['parentsname'];
                                                                }
                                                              } ?></td>

          <td><?php if ($element['type'] == "Fee") {

                $findstu = $this->Comman->findridacademicreert($element['student_id'], $element['acedmicyear']);

                if ($findstu['id'] == '') {


                  $detained = $this->Comman->gethistoryyearstudentinfo2($element['student_id'], $element['acedmicyear']);
                  if ($detained['class_id']) {
                    $class = $this->Comman->findclasses($detained['class_id']);
                    $section = $this->Comman->findsections($detained['section_id']);
                  } else {
                    $class = $this->Comman->findclasses($s_id);

                    $section = $this->Comman->findsections($c_id);
                  }
                } else {
                  $class = $this->Comman->findclasses($s_id);

                  $section = $this->Comman->findsections($c_id);
                }
                echo $class[0]['title'] . " " . $section[0]['title'];
              } else {
                if ($element['type'] == "Prospectus") {
                  $prospect = $this->Comman->findprospectus($element['recipetno'], $element['formno']);
                  $cl = $this->Comman->findclass($prospect['class_id']);
                  echo $cl['title'];
                }
                if ($element['type'] == "Registration") {
                  $applicant = $this->Comman->findapplicant($element['recipetno'], $element['formno']);
                  $cls = $this->Comman->findclass($applicant['class_id']);
                  echo $cls['title'];

              ?></b><?  }
                    if ($element['type'] == 'Other') {

                      echo '--';
                    }
                  }
                      ?> </td>
          <td><?php $qua = unserialize($element['quarter']);

              foreach ($qua as $fg => $cg) {
                if (ctype_digit(trim($fg, '"'))) {
                  $sert = $this->Comman->findridacademicer($fg);
                  echo "<b>Pending As Per Reference No" . $sert['recipetnos'] . ": </b>" . $cg . "<br>";
                } else {
                  echo "<b>" . $fg . ": </b> " . $cg . "<br>";
                }
              } ?></td>
          <td><?php echo "<b>" . $element['discountcategory'] . "</b> -" . $element['discount']; ?></td>
          <td><?php echo $element['addtionaldiscount']; ?></td>
          <td><?php echo $element['lfine']; ?></td>
          <td><?php $due = $this->Comman->findpendingsfee($element['student_id'], $element['id']);
              echo $due[0]['sum']; ?></td>

          <td><?php $total += intval($element['deposite_amt']);
              echo $element['deposite_amt']; ?></td>
          <td><?php echo $element['mode']; ?></td>
          <td><?php if ($element['bank']) {
                echo $element['bank'];
              }
              if ($element['cheque_no']) {
                echo "  CHEQUE No.- " . $element['cheque_no'];
              } ?></td>
          <td><?php if ($element['status'] == "Y") {


                echo "<span
					  style='color:green;'><b>Deposited</b></span>";
              } else {


                echo "<span
						  style='color:red;'><b>Cancelled</b></span>";
              }
              ?></td>
          <td><?php echo $element['remarks']; ?></td>
        </tr>
      <?php $counter++;
      }
    } else { ?>
      <tr>
        <td colspan="9" style="text-align:center;">No Data Available</td>
      </tr>
    <?php  } ?>
    <tr>
      <td colspan="7" style="text-align: right;
    font-weight: bold;
    color: red;">Grand Total</td>
      <td colspan="3" style="text-align: left;
    font-weight: bold;
    color: red;"><?php setlocale(LC_MONETARY, 'en_IN');

                  $total = money_format('%!i', $total);
                  echo $total; ?></td>
    </tr>
  </tbody>
</table>



<?php $students = $this->Comman->findstudentnameid($element['student_id']); ?>

<div class="modal" id="chequedetails" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display:none">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <?php echo $this->Form->create($enquires, array('class' => '', 'id' => 'attn_form', 'enctype' => 'multipart/form-data')); ?>

      <div class="modal-header" style="padding:0px; padding-left:15px; padding-right:15px;  line-height:20px;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Cheque details-(<span class="studinfo"></span>)</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" class="tid">
        <div class="row" style="margin-bottom:5px">
          <div class="col-sm-3">
            <label><b> Cheque No:</b></label>
          </div>
          <div class="col-sm-6">
            <input type="text" name="cheque_no" class="cheq" placeholder="Cheque No">

          </div>
        </div>
        <div class="row" style="margin-bottom:5px">
          <div class="col-sm-3">
            <label><b>Bank Name</b></label>
          </div>
          <div class="col-sm-6">
            <input type="text" name="bank" class="bank" placeholder="Bank Name">
          </div>
        </div>


      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default pull-left">Submit</button>
        <button type="button" class="btn btn-default close1" data-dismiss="modal">Close</button>
      </div>
      <?php echo $this->Form->end(); ?>
    </div>

  </div>
</div>
<div class="modal" id="receipt-details" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display:none">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <?php echo $this->Form->create($enquires, array('class' => '', 'id' => 'receipt_form', 'autocomplete' => 'off')); ?>

      <div class="modal-header" style="padding:0px; padding-left:15px; padding-right:15px;  line-height:20px;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Edit Receipt details</h4>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" class="tid">
        <div class="row" style="margin-bottom:5px">
          <div class="col-sm-3">
            <label><b> Old Receipt No:</b></label>
          </div>
          <div class="col-sm-6">
            <input type="text" name="old_receipt" class="old-receipt" placeholder="Old Receipt" readonly>

          </div>
        </div>
        <div class="row" style="margin-bottom:5px">
          <div class="col-sm-3">
            <label><b>New Receipt Number</b></label>
          </div>
          <div class="col-sm-6">
            <input type="text" name="new_receipt" class="new_receipt" placeholder="New Receipt Number">
          </div>
        </div>
        <div class="row" style="margin-bottom:5px">
          <div class="col-sm-3">
            <label><b> Old Form No:</b></label>
          </div>
          <div class="col-sm-6">
            <input type="text" name="old_form" class="old-form" placeholder="Old Form No" readonly>

          </div>
        </div>
        <div class="row" style="margin-bottom:5px">
          <div class="col-sm-3">
            <label><b>New Form Number</b></label>
          </div>
          <div class="col-sm-6">
            <input type="text" name="new_form" class="new_form" placeholder="New Form Number">
          </div>
        </div>
        <div class="row" style="margin-bottom:5px">
          <div class="col-sm-3">
            <label><b>Date</b></label>
          </div>
          <div class="col-sm-6">
            <input type="text" name="date" class="new_date" required>
          </div>
        </div>

      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-default pull-left">Submit</button>
        <button type="button" class="btn btn-default close1" data-dismiss="modal">Close</button>
      </div>
      <?php echo $this->Form->end(); ?>
    </div>

  </div>
</div>
<script>
  $(document).ready(function() {
    $('.cheque').click(function() {
      //alert();

      var id = $(this).data('id');
      var cheq = $(this).data('val');
      var name = $(this).data('bank');
      var stud = $(this).data('name');
      //alert(cheq);;
      //alert(name);;
      $('.tid').val(id);
      $('.cheq').val(cheq);
      $('.bank').val(name);
      $('.studinfo').html(stud);
      $("#chequedetails").show();

    });
    $("#attn_form").bind("submit", function(event) {

      $.ajax({
        async: true,
        data: $("#attn_form").serialize(),
        dataType: "html",
        type: "POST",
        beforeSend: function() {
          $("#chequedetails").hide();
          $('#load2').css("display", "block");
        },
        url: "<?php echo ADMIN_URL; ?>studentfees/updatecheque",

        success: function(data) {
          //alert(data);

          $('#stud_sub').trigger('click');

        },

      });

      return false;
    });
    $('.close,.close1').click(function() {
      //alert();
      $("#chequedetails").hide();

    });
    $('.receipt-id').click(function() {
      //alert();

      var id = $(this).data('id');
      var old = $(this).data('old');
      var form = $(this).data('formno');
      var date = $(this).data('date');

      $('.tid').val(id);
      $('.old-receipt').val(old);
      $('.old-form').val(form);
      $('.new_date').val(date);
      $("#receipt-details").show();

    });
    $("#receipt_form").bind("submit", function(event) {

      $.ajax({
        async: true,
        data: $("#receipt_form").serialize(),
        dataType: "html",
        type: "POST",
        beforeSend: function() {
          $("#receipt-details").hide();
          $('#load2').css("display", "block");
        },
        url: "<?php echo ADMIN_URL; ?>studentfees/updatereceipt",

        success: function(data) {
          if (data == 2) {
            $('#load2').css("display", "none");
            alert('This Receipt number already in use');
            return false;
          } else if (data == 1) {
            alert('This Receipt number updated successfully');
            $('#stud_sub').trigger('click');
          } else if (data == 3) {
            $('#load2').css("display", "none");
            alert('This Form number already in use');
            return false;
          }

        },

      });

      return false;
    });
    $('.close,.close1').click(function() {
      //alert();
      $("#receipt-details").hide();

    });
  });
</script>