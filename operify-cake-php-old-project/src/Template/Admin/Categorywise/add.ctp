<style>
    
      #testUL{
      position: relative;
      }
      #testUL ul{
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
      #testUL ul li{
      padding : 5px 8px;
      border: 1px solid lightgray;
      }
      #testUL ul li a{
      color: black;
      }
      .preview{
      margin-right:15px;
      }
  
  .input_fields_wrap .form-control{ margin-bottom:15px;}
</style>

<script>
                    function cllbckretail(id,cid,sid) {
                      $('.secrh-retail').val(id);
                      $('#retail_ids').val(cid);
                      $('#size').val(sid);
                      $('#testUL').hide();
                      //alert(cid);
                      $.ajax({
                        type: 'POST',
                        url: '<?php echo ADMIN_URL; ?>categorywise/getitemdetail',
                        data: {'fetch':cid},
                        success: function(data){
                          //console.log(data);
                      //  alert(data);
                          $('#sale_price').val(data);
                        },
                      });
                    }
                    $( function() {
                      $('.secrh-retail').bind('keyup',function(){
                      var pos=$(this).val();
                      var check = 0;
                      $('#testUL').show();
                      $('#retail_ids').val('');
                      var count=pos.length;
                      if(count > 0)
                      {
                      $.ajax({
                        type: 'POST',
                        url: '<?php echo ADMIN_URL; ?>stockregister/getitemname',
                        data: {'fetch':pos,'check':check},
                        success: function(data){
                          $('#testUL ul').html(data);
                        },
                      });
                      }else{
                      $('#testUL').hide();
                      }
                      });
                    });
                  </script>


<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
      <h1>
       Add Category Wise Items
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo SITE_URL; ?>admin/categorywise"><i class="fa fa-home"></i>Home</a></li>
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
       
              <!-- /.box-header -->
              <!-- form start -->
              <?php echo $this->Form->create($item, array('class'=>'form-horizontal','id' => 'sevice_form'));
                  // pr($vendor); die;
                  ?>
              <div class="box-body">
                  <div class="form-group">
                    <div class="col-sm-4" style="margin-bottom:15px;">
                        <label>Group Category Name:</label> 
                        <?php 
                        echo $this->Form->input('category_name',array('class'=>'form-control','type'=>'select','options'=>$categary,'label' =>false)); ?>
                    </div>

             
                    <div class="col-sm-4">
                        <label for="inputEmail3" class="control-label">Item Name (Code)<strong style="color:red;">*</strong></label>
                        <input type="hidden" required="required" name="item_id" id="retail_ids">
                        <?php echo $this->Form->input('item_name', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false,'required', 'autofocus', 'autocomplete' => 'off','placeholder'=>'Enter Item Name')); ?>
                        <div id="testUL" style="display:none;">
                          <ul></ul>
                        </div>
                    </div>
                    <div class="col-sm-4" style="margin-bottom:15px;">
                        <label>Item Price</label> 
                        <?php echo $this->Form->input('sale_price',array('class'=>'form-control','type'=>'number','placeholder'=>'Price','label' =>false,'required','id'=>'sale_price','readonly')); ?>
                    </div>

                    <div class="col-sm-4" style="margin-bottom:15px;">
                        <label>Quantity</label>
                        <?php echo $this->Form->input('quantity',array('class'=>'form-control','type'=>'number','placeholder'=>'Enter Quantity','label' =>false,'required')); ?>
                    </div>
                    
                    <div class="col-sm-4" style="margin-bottom:15px;" >
                      <label>Discount Type:</label><br>
                      <label class="radio-inline">
                        <input type="radio" name="discount_type" class="mode radio-inline checkstr" value="Amount"<?php if ($item['discount_type'] == "Amount"){ echo "checked"; }?>>&nbsp;Amount
                      </label>
                      <label class="radio-inline">
                        <input type="radio" name="discount_type" class="mode radio-inline checkstr" value="Per%" <?php if ($item['discount_type'] == "Per%"){ echo "checked"; }?>>Per%
                      </label>
                    </div>


                    <div class="col-sm-4" style="margin-bottom:15px;">
                        <label>Discount</label>
                        <?php echo $this->Form->input('discount',array('class'=>'form-control','type'=>'text','placeholder'=>'Enter discount','label' =>false)); ?>
                    </div>
                    
                    <div class="col-sm-4" style="margin-bottom:15px;">
                        <label>Group</label>
                        <?php 
                          $group=array('Top'=>'Top','Bottom'=>'Bottom','Socks'=>'Socks');
                        echo $this->Form->input('group_type',array('class'=>'form-control','type'=>'select','options'=>$group,'empty'=>'Select Group','label' =>false)); ?>
                    </div>
                    

                    
                   
                    <div class="col-sm-12">
                        <?php
                    if(isset($item['id'])){
              echo $this->Form->submit(
                'Update', 
                array('class' => 'btn btn-info pull-right','id'=> 'formsubmitbtn', 'title' => 'Update')
              ); }else{ 
                echo $this->Form->submit(
                  'Submit', 
                  array('class' => 'btn btn-info pull-right','id'=> 'formsubmitbtn', 'title' => 'Add')
                );
              }
              ?><?php
              echo $this->Html->link('Back', [
                'action' => 'index'

              ],['class'=>'btn btn-default']); ?>
                    </div>
                    <br>
                 
        <?php echo $this->Form->end(); ?>
      </div>
</div>
</div>
<!--/.col (right) -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>
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
      url: '<?php echo ADMIN_URL; ?>indent/getitemname',
      data: {'fetch':pos,'check':check},
      success: function(data){  
        console.log(data);
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
    $(document).ready(function () {
        $('#sevice_form').on('submit', function (e) {
            $("#formsubmitbtn").css("display", "none");
        });
        });
</script>