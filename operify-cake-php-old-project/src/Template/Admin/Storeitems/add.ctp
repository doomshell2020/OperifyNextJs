<style>
  .input_fields_wrap .form-control{ margin-bottom:15px;}
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Add Store Item Manager
      <?php 

// pr($item);die;
?>
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/storeitems"><i class="fa fa-home"></i>Home</a></li>
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
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i> <?php if(isset($location['id'])){ echo 'Edit Post New'; }else{ echo 'Create New Items';} ?></h3>
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
            
          <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Item Name <strong style="color:red;">*</strong></label>

          <div class="col-md-12">
          <input type="hidden" name="item_id" id="retail_ids">
          <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retail', 'type' => 'text', 'required', 'label' => false, 'placeholder' => 'Enter Item name', 'autofocus', 'autocomplete' => 'off','id'=>'itemname')); ?>
          <div id="testUL" style = "display:none;"><ul></ul></div>
          </div> 

          </div>


          <div class="col-md-4">
            
          <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">MRP Price <strong style="color:red;">*</strong></label>

          <div class="col-md-12">
          <?php echo $this->Form->input('mrp_price', array('class' => 'form-control', 'type' => 'number', 'required', 'label' => false, 'placeholder' => 'Enter MRP Price', 'autofocus', 'autocomplete' => 'off')); ?>
          </div> 

          </div>

          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Cost Price <strong style="color:red;">*</strong></label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('cost_price', array('class' => 'form-control', 'type' => 'number', 'required', 'label' => false, 'placeholder' => 'Enter Cost Price', 'autofocus', 'autocomplete' => 'off')); ?>
            </div> 
  
            </div>


            <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Sale Price <strong style="color:red;">*</strong></label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('sale_price', array('class' => 'form-control', 'type' => 'number', 'required', 'label' => false, 'placeholder' => 'Enter Sale Price', 'autofocus', 'autocomplete' => 'off','readonly')); ?>
            </div> 
  
          </div>

          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Quantity<strong style="color:red;">*</strong></label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('quantity', array('class' => 'form-control', 'type' => 'number', 'required', 'label' => false, 'placeholder' => 'Enter Quantity', 'autofocus', 'autocomplete' => 'off')); ?>
           </div> 
          </div> 

          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Min Stock Level:<strong style="color:red;">*</strong></label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('min_stock', array('class' => 'form-control', 'type' => 'number', 'required', 'label' => false, 'placeholder' => 'Enter Min Stock Level', 'autofocus', 'autocomplete' => 'off')); ?>
           </div> 
          </div> 


          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;">Max Stock Level:<strong style="color:red;">*</strong></label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('max_stock', array('class' => 'form-control', 'type' => 'number', 'required', 'label' => false, 'placeholder' => 'Enter Max Stock Level', 'autofocus', 'autocomplete' => 'off')); ?>
           </div> 
          </div> 
              
          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important;"> HSN/ISBN Number<strong style="color:red;">*</strong></label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('item_isbn', array('class' => 'form-control', 'type' => 'number', 'required', 'label' => false, 'placeholder' => 'Enter Item ISBN Number', 'autofocus', 'autocomplete' => 'off')); ?>

           </div> 
          </div> 


          <div class="col-md-4">
            
            <label for="inputEmail3" class="col-sm-12 control-label" style="text-align: left !important">Supplier Name<strong style="color:red;">*</strong></label>
  
            <div class="col-md-12">
            <?php echo $this->Form->input('supplier_name', array('class' => 'form-control', 'type' => 'select', 'options' => $supplier, 'label' => false, 'empty' => 'Supplier Name', 'autofocus','required','autocomplete' => 'off')); ?>
           </div> 
          </div> 

          <div class="col-md-4">
                <label for="inputEmail3" class="control-label" style="padding-left:15px;">Expiry Date</label>
                <div class="col-md-12">
                <?php echo $this->Form->input('expiry_date', array('class' => 'form-control input1','label'=>false,'placeholder'=>'Expiry Date','id'=>'datepicker1','autocomplete'=>'off','readonly')); ?>
              </div> 
          </div>
      

          <div class="col-md-6">
                <label for="inputEmail3" class="control-label" style="padding-left:15px;">Description</label>
                <div class="col-md-12">
                <?php echo $this->Form->input('description', array('class' => 'form-control','type'=>'textarea','required','label'=>false,'placeholder'=>'Enter Description','autofocus','autocomplete'=>'off')); ?>
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


<script src="https://code.jquery.com/ui/1.11.0/jquery-ui.js"></script>
<script>
  $( function() {
    var dateFormat = 'dd-mm-yy',
    from = $( "#datepicker1" )
    .datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    })
    .on( "change", function() {
      to.datepicker( "option", "minDate", getDate( this ) );
    }),
    to = $( "#datepicker2" ).datepicker({
      dateFormat: 'dd-mm-yy',
      changeMonth: true,
      numberOfMonths: 1
    })
    .on( "change", function() {
      from.datepicker( "option", "maxDate", getDate( this ) );
    });
    function getDate( element ) {
      var date;
      try {
        date = $.datepicker.parseDate( dateFormat, element.value );
      } catch( error ) {
        date = null;
      }
      return date;
    }
  } );
</script>

<!-- add item fecth -->

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

<!-- call break script -->
  
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