<footer class="main-footer">


  <strong>Copyright &copy; <?php echo date('Y'); ?>-<?php echo date('Y') + 1; ?> <a href="#" style="color:#3c8dbc">All
      Rights Reserved by Doomshell</a> </strong>
</footer>



</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->


<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>


<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.6 -->


<!-- Morris.js charts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>


<!-- Sparkline -->
<?= $this->Html->script('admin/jquery.sparkline.min.js') ?>

<!-- jvectormap -->
<?= $this->Html->script('admin/jquery-jvectormap-1.2.2.min.js') ?>
<?= $this->Html->script('admin/jquery-jvectormap-world-mill-en.js') ?>

<!-- jQuery Knob Chart -->
<?= $this->Html->script('admin/jquery.knob.js') ?>

<!-- daterangepicker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<?= $this->Html->script('admin/daterangepicker.js') ?>

<!-- datepicker -->
<?= $this->Html->script('admin/bootstrap-datepicker.js') ?>

<!-- Bootstrap WYSIHTML5 -->
<?= $this->Html->script('admin/bootstrap3-wysihtml5.all.min.js') ?>

<!-- Slimscroll -->
<?= $this->Html->script('admin/jquery.slimscroll.min.js') ?>

<!-- FastClick -->
<?= $this->Html->script('admin/fastclick.js') ?>
<?= $this->Html->script('admin/jquery.dataTables.min.js') ?>
<?= $this->Html->script('admin/dataTables.bootstrap.min.js') ?>
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<!-- Commented the following line 60 and used the line 61 following it instead -->
<!-- <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
<?php echo $this->Html->css('admin/jquery-ui.css'); ?>

<script>
  $(function () {
    $("#example1").DataTable();
    $("#example").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });

    $('#example14').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": false,
      "info": true,
      "autoWidth": false
    });
  });
</script>
<script>
  $(document).ready(function () {

    $('#emp_att').DataTable({

      "paging": false,
      "ordering": false,
      "ordering": true,
      "info": false

    });

  });
</script>
<!-- Select2 -->


<?= $this->Html->css('select2/select2.min.css') ?>

<?= $this->Html->script('select2/select2.full.min.js') ?>

<!-- input date -->
<?= $this->Html->script('input-mask/jquery.inputmask.js') ?>
<?= $this->Html->script('input-mask/jquery.inputmask.date.extensions.js') ?>
<?= $this->Html->script('input-mask/jquery.inputmask.extensions.js') ?>
<script>
  $('#datepicksd123').datepicker();
</script>

<script>
  $('#dp1').datepicker();
  $('#dp2').datepicker();
  // To use in EmployeeAttendance/index.ctp
  $('#dp3').datepicker({

    dateFormat: 'dd-mm-yy',
    minDate: 0,
    maxDate: 0

  }).datepicker("setDate", new Date());

  // To use in EmployeeAttendance/manage.ctp
  $('#dp4').datepicker({

    dateFormat: 'dd-mm-yy'

  }).datepicker("setDate", new Date());
</script>

<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
    //Datemask dd/mm/yyyy
    var date = new Date();

    tenYearBefore = new Date().setYear(new Date().getFullYear() - 6);

    $('#datepick').datepicker({
      "changeMonth": true,
      'maxDate': '0',
      "yearRange": "1976:2018",
      "changeYear": true,
      "autoSize": true
    }).on('change', function () {

      today = new Date();
      eighteenYearBefore = new Date().setYear(new Date().getFullYear() - 18);
      selecteds = new Date($('#datepick').val());

      if (selecteds > eighteenYearBefore) {
        $('#datepick').val('')
        $(".display_errors").show();
      } else {
        $(".display_errors").hide();
      }
    });

    $('#datepick1').datepicker({
      dateFormat: 'dd/mm/yy',
      "changeMonth": true,
      minDate: '-21Y',
      maxDate: '-1Y',
      "changeYear": true,
      "autoSize": true
    }).on('change', function () {
      today = new Date();
      tenYearBefore = new Date().setYear(new Date().getFullYear() - 3);
      selected = new Date($('#datepick1').val());

    });
    $('#joindate').datepicker({
      "changeMonth": true,
      "changeYear": true,
      "autoSize": true,
      "dateFormat": "dd-mm-yy"
    });
    $('#datepicks').datepicker({
      "beforeShowDay": function (date) {
        return [date.getDay() == 1, ""]
      },

      "changeMonth": true,
      'maxDate': '0',
      "changeYear": true,
      "autoSize": true,
      "dateFormat": "dd-mm-yy"
    });
    // $("[data-mask]").inputmask();
  });
</script>

<!-- AdminLTE App -->
<?= $this->Html->script('admin/app.min.js') ?>

<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<?php //= $this->Html->script('admin/dashboard.js') 
?>



<!-- AdminLTE for demo purposes -->
<?= $this->Html->script('admin/demo.js') ?>
<?= $this->Html->script('confirmation.js') ?>
<?php //= $this->Html->script('admin/morris.min.js') 
?>

<?php
$findmenumodule = $this->Comman->findrolemenu();
$Not_featured = [];
foreach ($findmenumodule as $val) {
  $Not_featured[] = $val['featured'];
}

$cond = 0; // Define the condition you want to check against

?>
<style>
  <?php echo (in_array($cond, $Not_featured)) ? '.content-wrapper { margin-left: 80px; }' : '.content-wrapper { margin-left: 0 !important; }'; ?>
</style>




<script>
  window.onload = function () {
    // Select the element to change CSS for
    var element = document.getElementByClass("content-wrapper");
  };
</script>


</body>

</html>





<script>
  // Function to validate mobile number
  function validateMobile() {
    let mobileInput = document.getElementById('mobile').value;
    let errorDiv = document.getElementById('erpfd');

    // Regex to match a 10-digit mobile number
    let mobileRegex = /^[0-9]{10}$/;

    if (!mobileRegex.test(mobileInput)) {
      errorDiv.style.display = 'block';
      errorDiv.innerText = 'Please enter a valid 10-digit mobile number.';
      return false;
    } else {
      errorDiv.style.display = 'none';
      return true;
    }
  }

  // Function to allow only numbers to be entered
  function isNumber(event) {
    let charCode = event.charCode ? event.charCode : event.keyCode;
    return charCode >= 48 && charCode <= 57;
  }


  // PAN Number 

  function isValidPanCardNo(panCardNo) {
    // Regex to check valid
    // PAN Number
    alert(panCardNo)
    let regex = new RegExp(/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/);

    // if PAN Number 
    // is empty return false
    if (panCardNo == null) {
      return "false";
    }

    // Return true if the PAN NUMBER
    // matched the ReGex
    if (regex.test(panCardNo) == true) {
      return "true";
    }
    else {
      return "false";
    }
  }




  function isValidPanCardNo(panCardNo) {
    // Regex to check valid PAN Number
    let regex = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

    // Return true if PAN Number matches the regex
    return regex.test(panCardNo);
}

// Validate input dynamically on keypress
function validatePanInput(event) {
    let input = event.target.value; // Current input value
    let key = event.key; // The key pressed

    // Check if the key is valid based on PAN rules
    let regex = /^[A-Z0-9]$/; // Only allow letters (A-Z) and numbers (0-9)

    // Allow only 10 characters and valid PAN format
    if (!regex.test(key) || input.length >= 10) {
        event.preventDefault(); // Block invalid input
    }
  }
</script>

<script>
  function addElement() {
    // Create a new div element
    var newDiv = document.createElement("div");
    // Give your new div an id
    newDiv.id = "modalBtnClos";
    // Add some content to the new div
    newDiv.innerHTML = " <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>";
    // Append the new div to the body or any other existing element
    document.body.appendChild(".modal");
  }

  // Call the function to add the element
  addElement();
</script>

