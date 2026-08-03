<h3> Manager Entry   </h3>
<hr>

<?php echo $this->Flash->render(); ?>

<?php
echo $this->Form->create(
   '',
    array(
        'class' => 'form-horizontal',
        'id' => 'student_form',
        'enctype' => 'multipart/form-data'
    )
);
?>

<div class="box-body">
   

    <div class="form-group">
        <div class="col-md-12">
            <label for="name">Manager name <span style="color:red;">*</span></label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>
    </div>
</div>

<div class="box-footer">
    <?php echo $this->Form->submit(
        'Add',
        array('class' => 'btn btn-info pull-right', 'title' => 'Add')
    ); ?>

  
</div>

<?php echo $this->Form->end(); ?>

<script>
      $('#name').on('change', function() {
    var name = $('#name').val();
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>Permission/dup_name',
      data: {
        'name': name
      },
      success: function(data) {
        if (data > 0) {
          $('#name').val('');
          $('#name_exits').show('');
        }else{
          $('#name_exits').hide('');
        }

      },

    });
  });

</script>