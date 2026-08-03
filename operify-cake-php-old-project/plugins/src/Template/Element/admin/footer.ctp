<footer class="main-footer">
    

    <strong>Copyright &copy; 2016-2017 <a href="<?php echo SITE_URL;?>">School ERP</a>.</strong> All rights
    reserved.
  </footer>

  
  
</div>
<!-- ./wrapper -->

<!-- jQuery 2.2.3 -->


<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
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

<script>
  $(function () {
    $("#example1").DataTable();
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false
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
<script src="http://demo.edusec.org/assets/3a401da0/jquery-ui.js?v=1469444669"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script>
  $(function () {
    //Initialize Select2 Elements
    $(".select2").select2();
    //Datemask dd/mm/yyyy
   // $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
    
     $("[data-mask]").inputmask();
  $('#stuecdetail-secd_date').datepicker({"changeMonth":true,"maxDate": 0,"yearRange":"1980:2018","changeYear":true,"autoSize":true,"dateFormat":"dd-mm-yy"});
 });
</script>
<!-- AdminLTE App -->
<?= $this->Html->script('admin/app.min.js') ?>
<script src="https://cdn.ckeditor.com/4.5.7/standard/ckeditor.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<?= $this->Html->script('admin/dashboard.js') ?>



<!-- AdminLTE for demo purposes -->
<?= $this->Html->script('admin/demo.js') ?>
<?= $this->Html->script('admin/morris.min.js') ?>


</body>
</html>
