<!-- Include CKEditor 5 -->

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.0/classic/ckeditor.js"></script>

<style type="text/css">
  .form-group {
    width: 50%;
  }
</style>

<div class="content-wrapper">
  <section class="content-header">
    <h1>Template Manager</h1>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <?php echo $this->Flash->render(); ?>
          <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> Manage Template</h3>
          </div>

          <?php echo $this->Form->create($newpack, [
            'url' => ['controller' => 'Template', 'action' => 'edit', $newpack->id], // Ensure correct action for edit
            'class' => 'form-horizontal',
            'enctype' => 'multipart/form-data',
            'validate' => true
          ]); ?>

          <div class="box-body">
            <div class="container-fluid">

              <!-- Template For -->
              <div class="form-group">
                <div class="col-sm-12">
                  <label for="templateFor">Template For</label>
                  <select data-placeholder="Select Type" name="type_name[]" multiple class="chosen-select form-control">
                    <option value="PO" <?php echo in_array('PO', (array)$newpack->type_name) ? 'selected' : ''; ?>>PO</option>
                    <option value="GRN" <?php echo in_array('GRN', (array)$newpack->type_name) ? 'selected' : ''; ?>>GRN</option>
                  </select>
                </div>
              </div>

              <!-- Format Description -->
              <div class="form-group" style = "width: 1100px;">
                <label for="editor">Format Description*</label>
                <textarea id="editor" name="body" class="form-control" placeholder="Enter directions here..." required><?php echo h($newpack->body); ?></textarea>
              </div>

            </div>
          </div>
          <!-- /.box-body -->
          <div class="box-footer">
            <?php echo $this->Form->submit('Update', ['class' => 'btn btn-info pull-right', 'title' => 'Update']); ?>
            <?php echo $this->Html->link('Back', ['action' => 'index'], ['class' => 'btn btn-default']); ?>
          </div>
          <?php echo $this->Form->end(); ?>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  // Initialize CKEditor
  let editorInstance;
  ClassicEditor
    .create(document.querySelector('#editor'))
    .then(editor => {
      editorInstance = editor;
      console.log(editor);
    })
    .catch(error => {
      console.error(error);
    });

  // Submit the form data on button click
  document.querySelector('.btn-info').addEventListener('click', function(event) {
      // Ensure the editor data is passed to the form
      const data = editorInstance.getData();
      document.querySelector('#editor').value = data; // Set the textarea value to the editor data
      console.log('Update button clicked');
  });
</script>
