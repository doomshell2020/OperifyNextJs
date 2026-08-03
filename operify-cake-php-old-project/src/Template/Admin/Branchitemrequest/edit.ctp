<style>
  .input_fields_wrap .form-control{ margin-bottom:15px;}
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
    Branch Request Items
      <?php 

// pr($item);die;
?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/Branchitemrequest"><i class="fa fa-home"></i>Home</a></li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">

      <!-- right column -->
      <div class="col-md-12">
        <!-- Horizontal Form -->
        <div class="box box-info">
          <?php echo $this->Flash->render(); ?>
          <div class="box-header with-border">
            <!-- <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php //if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Item';} ?></h3> -->
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create($items, array(
              'class'=>'form-horizontal',
              'enctype' => 'multipart/form-data',
              'validate'
            )); ?>
        <div class="box-body">
        <div class="row">  
        

   <div class="col-md-4">

          <label for="inputEmail3" class="col-md-12 control-label" style="text-align: left !important;">Category</label>

          <div class="col-md-12">
              <?php echo $this->Form->input('category_name', array('class' => 'form-control','type'=>'select','options'=>$categary,'required','label'=>false,'empty'=>'Select Category','autofocus','autocomplete'=>'off')); ?>
          </div> 

          </div>


          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Item Name <strong style="color:red;">*</strong></label>
  
            <div class="col-md-12">
            <input type="hidden" name="item_id" id="retail_ids">
            <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retail', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off','id'=>'itemname')); ?>
            <div id="testUL" style = "display:none;"><ul></ul></div>
            </div> 
  
            </div>
         
          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Quantity</label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('Quantity', array('class' => 'form-control', 'type' => 'text',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
           </div> 
          </div> 

          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Description</label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('Quantity', array('class' => 'form-control', 'type' => 'textarea',   'label' => false,  'autofocus', 'autocomplete' => 'off')); ?>
           </div> 
          </div> 

        </div>
        </div>
        

  <div class="box-footer">
           
        <?php
            if(isset($location['id'])){
              echo $this->Form->submit(
                'Update', 
                array('class' => 'btn btn-info pull-right', 'title' => 'Update')
              ); }else{ 
                echo $this->Form->submit(
                  'Add', 
                  array('class' => 'btn btn-info pull-right', 'title' => 'Add')
                );
              }
              ?>
               
                </div>
            <!-- /.box-footer -->
            <?php echo $this->Form->end(); ?>


          </div>

        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div> 

  <!-- Relation Beetween Location and Sublocation  -->
  <script>
      $(document).ready(function() {
        $('#location-name').on('change', function() {
          var id = $('#location-name').val();
          // alert(id);
          $.ajax({
            type: 'POST',
            url: '<?php echo SITE_URL;?>/admin/additem/find_sublocation',
            data: {
              'id': id
            },
            success: function(data) {
              $('#sub-location').empty();
              $('#sub-location').html(data);
            },

          });
        });
      });
    </script>
    <!-- end  -->

<script>
  $(function() {
    $("#imagename").change(function() {
     // alert('hello');
      var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.pdf|.jpg|.png)$/;
      if (regex.test($(this).val().toLowerCase())) {
        return true;

      } else {
        $('#imagename').val('');
        alert("Please upload pdf/jpg/png files.");
      }
    });
  });
 </script>

 
<script type="text/javascript">
$(document).ready(function() {
  $("#category_ids").on('change',function() {
    var id = $(this).val();
    $("#subcategory").find('option').remove();
    //$("#city").find('option').remove();
    if (id) {
      var dataString =id;
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL;?>/admin/additem/getsubcategory',
        data: {'dataString':id},
        cache: false,
        success: function(html) {
          //alert(html);
          $('<option>').val("").text("Select Sub Category").appendTo($("#subcategory"));
          $.each(html, function(key, value) {        
            $('<option>').val(key).text(value).appendTo($("#subcategory"));
          });
        }
      });
    }
  });
});
</script>

<script type="text/javascript">
$(document).ready(function() {
  $("#location").on('change',function() {
    var id = $(this).val();
    $("#sublocation").find('option').remove();
    //$("#city").find('option').remove();
    if (id) {
      var dataString =id;
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL;?>/admin/additem/getsublocation',
        data: {'dataString':id},
        cache: false,
        success: function(html) {
          //alert(html);
          $('<option>').val("").text("Select Sub Location").appendTo($("#sublocation"));
          $.each(html, function(key, value) {        
            $('<option>').val(key).text(value).appendTo($("#sublocation"));
          });
        }
      });
    }
  });
});
</script>

<script>
$('#mrp').on('change',function() {
  var amou = $('#saleprice').val();
  if ($(this).val() < amou){
    alert("Mrp should be greater then sale price");
    $(this).val('');
  }
});
</script>

<script>
$('#saleprice').on('change',function() {
  var mrp = $('#mrp').val();
  if ($(this).val() > mrp){
    alert("Sale Price should be less then mrp");
    $(this).val('');
  }
});
</script>

<style>
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
#testUL {
    position: relative;
}
#testUL ul li a {
    color: black;
}
  </style>
<script>
$( function() {
    $('.secrh-retail').bind('keyup',function(){  
    var pos=$(this).val();
    //alert(pos);
    var check = 0;
    //var catid=$('#subcategory').val();
    //alert(pos);
    $('#testUL').show();
    $('#retail_ids').val('');
    var count=pos.length;
    if(count > 0)
    {
    $.ajax({ 
      type: 'POST', 
      url: '<?php echo ADMIN_URL; ?>storeitems/getitemname',
      data: {'fetch':pos,'check':check},
      success: function(data){  
        //alert(data);
        $('#testUL ul').html(data);
      },    
    }); 
    }else{
    $('#testUL').hide();  
    }   
    });     
  });
</script>
<script>
  function cllbckretail(name,id) { 
    $('.secrh-retail').val(name);
    $('#testUL').hide();
    //alert(cid);
    $.ajax({ 
      type: 'POST', 
      url: '<?php echo ADMIN_URL; ?>storeitems/getitemdetail',
      data: {'fetch':id},
      success: function(data){  
        //console.log(data);
        var json = $.parseJSON(data);
         //alert(json.sale_price);
         $('#retail_ids').val(json.id);
         $('#sale-price').val(json.sale_price);
      },    
    }); 

  }
  </script>