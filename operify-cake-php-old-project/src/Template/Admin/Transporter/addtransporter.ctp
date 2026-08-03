<style>
  h4 {
    margin-top: 0px !important;
    margin-bottom: 0px !important;
  }

  table,
  tr,
  th,
  td {
    border: 1px solid black;
    margin-top: 10px;
  }
</style>
<script>
  $(document).ready(function () {
    $("#mysubscription").bind("submit", function (event) {
      var task = $("#mysubscription").serialize();
      // alert()
      $.ajax({
        async: true,
        data: $("#mysubscription").serialize(),
        dataType: "html",
        type: "POST",
        url: "<?php echo ADMIN_URL; ?>transporter/addtransporter",

        success: function (data) {
          $('.alert-success').show();
          $('#cancelsorts').modal('hide');
        },

      });
      return false;
    });
  })

</script>

<div class="modal-header" style="background:#3399CC;">
  <h4>Add Transporter</h4>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="alert alert-success alert-dismissible" style="display:none;" role="alert"> <button type="button"
    class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Transporter
  Saved Successfuly</div>

<?php echo $this->Form->create($enquires, array(
  'class' => '',
  'id' => 'mysubscription',
  'enctype' => 'multipart/form-data',
  'validate',
  'autocomplete' => 'off'

)
); ?>
<div class="modal-body prchc_ord_popup">

  <div class="box-body">
    <div class="form-group">
      <div class="row">

        <div class="col-sm-4" style="margin-bottom:15px;">
          <label>Transporter Name</label> <strong style="color:red;">*</strong>
          <?php echo $this->Form->input('name', array('class' => 'form-control', 'id' => 'title', 'placeholder' => 'Transporter Name', 'label' => false, 'required')); ?>
        </div>

        <div class="col-sm-4" style="margin-bottom:15px;">
          <label>Contact No</label><strong style="color:red;">*</strong>
          <?php echo $this->Form->input('contact_no', array('class' => 'form-control', 'type' => 'number', 'placeholder' => 'Contact Number', 'label' => false, 'required', 'type' => 'text', 'maxlength' => '10')); ?>
        </div>


        <div class="col-sm-4" style="margin-bottom:15px;">
          <label>Email Id</label><strong style="color:red;">*</strong>
          <?php echo $this->Form->input('email', array('class' => 'form-control', 'type' => 'text', 'placeholder' => 'Email', 'id' => 'title', 'label' => false, 'required')); ?>
        </div>

        <div class="col-sm-4" style="margin-bottom:15px;">
          <label>PAN NO.</label><strong style="color:red;">*</strong>
          <?php echo $this->Form->input('pancard_number', array('class' => 'form-control pancard', 'type' => 'text', 'maxlength' => '15', 'label' => false, 'placeholder' => 'PAN No.', 'required', 'autocomplete' => 'off')); ?>
        </div>

        <div class="col-sm-4" style="margin-bottom:15px;">
          <label>State</label><strong style="color:red;">*</strong>
          <?php echo $this->Form->input('billtostate_id', array('class' => 'form-control state', 'id' => 'billto_state_ids', 'type' => 'select', 'options' => $state, 'empty' => 'Select State', 'label' => false, 'required')); ?>
        </div>

        <div class="col-sm-4" style="margin-bottom:15px;">
          <label>GST No.</label><strong style="color:red;">*</strong>
          <?php echo $this->Form->input('billtogst_number', array('class' => 'form-control gst', 'type' => 'text', 'maxlength' => '15', 'label' => false, 'placeholder' => 'GST No.', 'autocomplete' => 'off')); ?>
        </div>

        <div class="col-sm-12" style="margin-bottom:15px;">
          <label>Address</label><strong style="color:red;">*</strong>
          <?php echo $this->Form->textarea('billtoaddress', array('rows' => '2', 'class' => 'form-control ', 'placeholder' => 'Address', 'label' => false, 'required')); ?>
        </div>

        <?php
        echo $this->Form->submit(
          'Submit',
          array(
            'class' => 'btn btn-info pull-left submitbtn',
            'style' => 'margin: 10px 0px;
                  ',
            'title' => 'Submit'
          )
        );
        ?>

      </div>
    </div>
  </div>
</div>