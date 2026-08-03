<!-- <link rel="stylesheet" href="<?php //echo WEBROOT_URL; ?>css/admin/StyleSheet.css"> -->
<script type="text/javascript">
  function isLspecial(e) {
   var e = e || window.event;
   var k = e.which || e.keyCode;
   var s = String.fromCharCode(k);
   if(/^[\\\"\'\;\:\>\<\[\]\/\?\=\+\_\|~`!@#\$%^&*\(\)0-9]$/i.test(s)){
     $('#msg').css('display','block');
     return false;
   }
   $('#msg').hide();
 }
</script>
<script type="text/javascript">
  function isCspecial(e) {
   var e = e || window.event;
   var k = e.which || e.keyCode;
   var s = String.fromCharCode(k);
   if(/^[\\\"\'\;\:\>\<\[\]\-\.\,\/\?\=\+\_\|~`!@#\$%^&*\(\)0-9]$/i.test(s)){
    alert("Special characters not acceptable");
    return false;
  }
}
</script>
<style type="text/css">
  .form-horizontal .form-group {
    margin-right: 0px;
    margin-left: 0px;
  }
  .text{
    color:red; 
  }
  .form-control {
    width: 50%;
  }
  textarea.form-control {
    height: 130px;
    width: 36%;
  }
</style>

<?php echo $this->Form->create($newpack,array(
 'class'=>'form-horizontal',
 'controller'=>'seo',
 'action'=>'edit',
 'enctype' => 'multipart/form-data',
 'validationDefault' )); ?>

 <div class="content-wrapper">
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-primary">
          <?php echo $this->Flash->render(); ?>
          <div class="box-header with-border">
            <h3 class="box-title">Edit Seo</h3>
          </div>
          <form role="form">
            <div class="box-body">
              <div class="form-group">
                <label for="exampleInputEmail1">Page Name</label>
                <?php echo $this->Form->input('page', array('class' => 
                'form-control','id'=>'exampleInputEmail1','placeholder'=>'Page','label'=>false,'autocomplete'=>'off','onkeypress'=>'return isCspecial()','required')); ?>

              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Page Location</label>
                <?php echo $this->Form->input('location', array('class' => 
                'form-control','id'=>'exampleInputEmail1','type'=>'url','placeholder'=>'Page Location','label'=>false,'autocomplete'=>'off','required')); ?>
              </div>
              <div class="form-group">
                <label for="exampleInputEmail1">Title</label>
                <?php echo $this->Form->input('title', array('class' => 
                'form-control','id'=>'exampleInputEmail1','placeholder'=>'Title','label'=>false,'autocomplete'=>'off','onkeypress'=>'return isCspecial()','required')); ?>
              </div>
              <div class="form-group" style="width:68%;">
                <label for="exampleInputPassword1">Keywords</label>
                <?php echo $this->Form->input('keyword', array('class' => 
                'form-control','placeholder'=>'Keywords','label'=>false,'type'=>textarea,'autocomplete'=>'off','onkeypress'=>'return isLspecial()','required')); ?>
                <h5 id="msg" style="display:none;" class="text">**Special characters not acceptable</h5>
              </div>
              <div class="form-group" style="width:68%;">
                <label for="exampleInputPassword1">Description</label>
                <?php echo $this->Form->input('description', array('class' => 
                'form-control','placeholder'=>'Description','label'=>false,'type'=>textarea,'autocomplete'=>'off','required')); ?>
              </div>
              <div class="box-footer">
                <button type="submit"  onClick="return validationDefault();"/ class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>




  