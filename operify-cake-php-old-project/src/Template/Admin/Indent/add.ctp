<style>
  /* #customers {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-bottom:20px;
  }

#customers td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#customers tr:nth-child(even){background-color: #f2f2f2;}

#customers tr:hover {background-color: #ddd; }

#customers th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #c8c8c8;
  color: #333333; 
} */

  #testUL {
    position: relative;
  }

  #testUL ul {
    position: absolute;
    z-index: 999;
    top: 100%;
    left: 0px;
    right: 0px;
    list-style-type: none;
    background-color: white;
    padding-left: 0px;
  }

  #testUL ul li {
    padding: 5px 8px;
    border: 1px solid lightgray;
  }

  #testUL ul li a {
    color: black;

  }

  .preview {
    margin-right: 15px;
  }
</style>

<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Purchase Requisition Manager
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/indent"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/itemname">Add Indent</a></li>
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
            <a target="_blank" href="<?php echo SITE_URL; ?>admin/additem/index">
              <button class="btn btn-success pull-right m-top10"><i class="fa fa-plus"></i>&nbsp;Add Item</button></a>
            <h3 class="box-title"><i class="fa fa-plus-square" aria-hidden="true"></i>
              <?php if (isset($location['id'])) {
                echo 'Edit Indent';
              } else {
                echo 'New Indent Id : ' . $newindenttemp;
              } ?>
            </h3>
          </div>
          <!-- /.box-header -->
          <!-- form start -->
          <?php echo $this->Form->create($location, array(
            'class' => 'form-horizontal',
            'id' => 'indentform',
            'enctype' => 'multipart/form-data',
            'validate'
          )
          );
          //pr($location); die;
          
          ?>
          <div class="box-body">


            <div class="form-group" style="display:flex; align-items: flex-end; gap: 8px;">
              <input type="hidden" name="indent" value="<?php echo $newindenttemp; ?>" id="indent">
              <div class="col-sm-3 autocomplete">
                <label for="inputEmail3">Select Items</label>
                <input type="hidden" name="item_id" id="retail_ids">
                <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name')); ?>
                <div id="testUL">
                  <ul></ul>
                </div>
              </div>

              <input type="hidden" name="size_id" id="size">



              <div class="col-sm-3">
                <label for="inputEmail3">Quantity</label>
                <?php //echo $this->Form->input('nquant', array('class' => 'form-control', 'id' => 'quantity', 'type' => 'text', 'label' => false, 'placeholder' => 'Quantity', 'autofocus', 'autocomplete' => 'off')); 
                ?>
                <input name="nquant" type="text" class="form-control" id="quantity"
                  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" placeholder="Quantity"
                  autocomplete="off">
              </div>


              <a href="javascript:void(0)" class="add-batch-fields btn btn-info" style="">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
                <b>Add Indent Item</b>
              </a>
            </div>
          </div>
          <div class="product_containes">
          </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">

          <?php
          echo $this->Form->submit(
            'Save && Finalize',
            array('class' => 'btn btn-success pull-right', 'id' => 'formsubmit', 'title' => 'Add', 'formaction' => 'add/1')
          );

          ?>
          <?php
          echo $this->Form->submit(
            'Preview',
            array('class' => 'btn btn-info pull-right preview', 'id' => 'formpreview', 'title' => 'Preview', 'formaction' => 'add/0')
          );
          ?>
          <?php
          echo $this->Html->link('Back', [
            'action' => 'index'

          ], ['class' => 'btn btn-default']); ?>
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
  $(document).ready(function () {

    $(".add-batch-fields").click(function () {
      var indentId = $('#indent').val();
      var itemId = $('#retail_ids').val();
      var sizeId = $('#size').val();
      var quanId = $('#quantity').val();
      var numItems = $('.video_details').length;

    
      numItems++;

      if (itemId != "" && quanId != "") {
        $.ajax({
          type: "POST",
          url: '<?php echo SITE_URL; ?>admin/indent/indenttemp',
          data: {
            'indent_id': indentId,
            'srno': numItems,
            'item_id': itemId,
            'size_id': sizeId,
            'quantity': quanId
          },
          cache: false,
          success: function (html) {
            // alert(html);   
            $(".product_containes").append(html);
            $('#itemname').val('');
            // $('#unitna').val('');
            // $('#size').val('');
            $('#quantity').val('');
          }
        });
      } else {
        alert("All fields are mandatory");
      }

    });

    $("body").on("click", ".remove", function () {
      $(this).closest('.video_details').remove();
      var indentId = $(this).attr('data');
      //alert(indentId);
      $.ajax({
        type: "POST",
        url: '<?php echo SITE_URL; ?>admin/indent/removeindenttemp',
        data: {
          'indent_id': indentId
        },
        cache: false,
        success: function (data) {
          alert('This item is successfully removed');
        }
      });
      var numItems = $('.video_details').length;
      if (numItems < 1) {
        $(".ctpcontent").css("display", "none");
      }
    });
  });
</script>






<script>
  function cllbckretail(id, cid, sid) {
    $('.secrh-retail').val(id);
    $('#retail_ids').val(cid);
    $('#size').val(sid);
    $('#testUL').hide();
    //alert(cid);
    $.ajax({
      type: 'POST',
      url: '<?php echo ADMIN_URL; ?>indent/getitemdetail',
      data: {
        'fetch': cid
      },
      success: function (data) {
        console.log(data);
        //alert(data);
        // $('#unitna').val(data);
      },
    });

  }

  $(function () {
    $('.secrh-retail').bind('keyup', function () {
      var pos = $(this).val();
      //alert(pos);
      var check = 0;
      //var catid=$('#subcategory').val();
      //alert(pos);
      $('#testUL').show();
      $('#retail_ids').val('');
      var count = pos.length;
      if (count > 0) {
        $.ajax({
          type: 'POST',
          url: '<?php echo ADMIN_URL; ?>indent/getitemname',
          data: {
            'fetch': pos,
            'check': check
          },
          success: function (data) {
            console.log(data);
            $('#testUL ul').html(data);
          },
        });
      } else {
        $('#testUL').hide();
      }
    });
  });
</script>

<script>
  $(document).ready(function () {
    $('#formpreview').on('click', function (e) {
      $("#indentform").attr("target", "_blank");
    });

    $('#formsubmit').on('click', function (e) {
      $("#indentform").removeAttr("target");
    });
  });
</script>
<script>
    $(document).ready(function () {
        $('#indentform').on('submit', function (e) {
            $("#formsubmit").css("display", "none");
        });
        });
</script>