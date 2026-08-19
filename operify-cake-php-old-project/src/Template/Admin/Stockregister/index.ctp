<div class="content-wrapper" style="min-height: 410px;">
  <section class="content-header">
    <h1>
      Stock Register
    </h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo SITE_URL; ?>admin/dashboards"><i class="fa fa-home"></i>Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>admin/stockregister">Goods Received Manager</a></li>
    </ol>
  </section>
  <!-- content header -->
  <style>
    #testUL {
      position: relative;
    }

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

    #load2 {
      width: 100%;
      height: 100%;
      position: fixed;
      z-index: 9999;
      background-color: white !important;
      background: url("<?php echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0, 0, 0, 0.75)
    }
    
    .dropdown-box {
       position: relative;
    }

    .dropdown-input {
       border: 1px solid #ced4da;
       padding: 6px 10px;
       cursor: pointer;
       background: #fff;
    }

    .dropdown-list {
       display: none;
       position: absolute;
       background: #fff;
       border: 1px solid #ced4da;
       width: 100%;
       max-height: 220px;
       overflow-y: auto;
       z-index: 1000;
       padding: 5px;
    }
  </style>
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <script>
              function updateDateLimits() {
                 var productId = $('#retail_ids').val();
                 var minDate = $('#fdatefrom').datepicker('getDate');
                 
                 if (!minDate) return;
                 
                 if (productId === '') {
                    // All products: restrict to single date
                    $("#fendfrom").datepicker("option", "minDate", minDate);
                    $("#fendfrom").datepicker("option", "maxDate", minDate);
                    $("#fendfrom").datepicker("setDate", minDate);
                    $("#fendfrom").prop('readonly', true);
                 } else {
                    // Product selected: max 31 days
                    var maxDate = new Date(minDate.getTime());
                    maxDate.setDate(maxDate.getDate() + 31);
                    $("#fendfrom").datepicker("option", "minDate", minDate);
                    $("#fendfrom").datepicker("option", "maxDate", maxDate);
                    $("#fendfrom").prop('readonly', false);
                    
                    var currentEndDate = $("#fendfrom").datepicker('getDate');
                    if (currentEndDate && (currentEndDate < minDate || currentEndDate > maxDate)) {
                      $("#fendfrom").val("");
                    }
                 }
              }

              function cllbckretail(id, cid, sid) {
                $('.secrh-retail').val(id);
                $('#retail_ids').val(cid);
                $('#size').val(sid);
                $('#testUL').hide();
                updateDateLimits();
                $.ajax({
                  type: 'POST',
                  url: '<?php echo ADMIN_URL; ?>indent/getitemdetail',
                  data: {
                    'fetch': cid
                  },
                  success: function(data) {
                    $('#unitna').val(data);
                  },
                });
              }
              $(function() {
                $('.secrh-retail').bind('keyup', function() {
                  var pos = $(this).val();
                  if (pos.trim() === '') {
                     $('#retail_ids').val('');
                     updateDateLimits();
                  }
                  
                  var check = 0;
                  $('#testUL').show();
                  var count = pos.length;
                  if (count > 0) {
                    $.ajax({
                      type: 'POST',
                      url: '<?php echo ADMIN_URL; ?>stockregister/getitemname',
                      data: {
                        'fetch': pos,
                        'check': check
                      },
                      success: function(data) {
                        $('#testUL ul').html(data);
                      },
                    });
                  } else {
                    $('#testUL').hide();
                  }
                });
              });
            </script>

            <script inline="1">
              $(document).ready(function() {
                $("#mysubscription").bind("submit", function(event) {
                  event.preventDefault(); // Stop default form submit just in case

                  var dateFrom = $("#fdatefrom").val();
                  var dateTo = $("#fendfrom").val();
                  
                  if (dateFrom === '' || dateTo === '') {
                      alert("Please select both Date From and Date To before searching.");
                      return false;
                  }

                  $.ajax({
                    async: true,
                    data: $("#mysubscription").serialize(),
                    dataType: "html",
                    type: "POST",
                    url: "<?php echo ADMIN_URL; ?>stockregister/searchstockregister",
                    beforeSend: function(xhr) {
                      xhr.setRequestHeader('X-CSRF-Token', $('[name="_csrfToken"]').val());
                      $('#load2').css("display", "block"); // Show loader
                    },
                    success: function(data) {
                      $("#updt").html(data);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                      console.log(textStatus, errorThrown);
                    },
                    complete: function() {
                      $('#load2').css("display", "none"); // Hide loader
                    }
                  });
                  return false;
                });
              });
            </script>

            <?php echo $this->Form->create('Fees', array('type' => 'file', 'inputDefaults' => array('div' => false, 'label' => false), 'id' => 'mysubscription', 'class' => 'form-horizontal', 'style' => 'margin-bottom:0px;')); ?>
            <div class="form-group" style="margin-bottom:0px;">
              <div class="row">
                <div class="col-sm-2">
                  <label for="inputEmail3" class="control-label">Product</label>
                  <input type="hidden" name="item_id" id="retail_ids">
                  <?php echo $this->Form->input('nitem', array('class' => 'form-control secrh-retail', 'id' => 'itemname', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'placeholder' => 'Enter Item Name (Optional)')); ?>
                  <div id="testUL" style="display:none;">
                    <ul></ul>
                  </div>
                </div>

                <div class="col-sm-3">
                  <label class="control-label">Category (Optional)</label>

                  <div class="dropdown-box" id="productDropdown">
                    <div class="form-control" id="categoryDropdownText" style="cursor: pointer;" onclick="toggleProduct(event)">
                      Select Category <i class="fa fa-caret-down pull-right" style="margin-top: 3px;"></i>
                    </div>

                    <div class="dropdown-list" id="productList">
                      <label>
                        <input type="checkbox" id="selectAllProducts">
                        <strong>All Category</strong>
                      </label><br>
                      <hr style="margin:4px 0;">

                      <?php 
                      if (!empty($categories)) {
                          foreach ($categories as $cat_id => $cat_name) {
                              echo '<label><input type="checkbox" class="product-checkbox" name="category_id[]" value="'.$cat_id.'"> '.$cat_name.'</label><br>';
                          }
                      }
                      ?>
                    </div>
                  </div>
                </div>

                <script>
                  function toggleProduct(e) {
                    e.stopPropagation();
                    let box = document.getElementById('productList');
                    box.style.display = box.style.display === 'block' ? 'none' : 'block';
                  }

                  function updateCategoryCount() {
                    let checkboxes = document.querySelectorAll('.product-checkbox');
                    let checkedCount = 0;
                    checkboxes.forEach(cb => {
                      if (cb.checked) checkedCount++;
                    });
                    
                    let textElem = document.getElementById('categoryDropdownText');
                    let caret = ' <i class="fa fa-caret-down pull-right" style="margin-top: 3px;"></i>';
                    
                    if (checkedCount === 0) {
                      textElem.innerHTML = 'Select Category' + caret;
                    } else if (checkedCount === checkboxes.length && checkboxes.length > 0) {
                      textElem.innerHTML = 'All Selected' + caret;
                    } else {
                      textElem.innerHTML = checkedCount + ' Selected' + caret;
                    }
                  }

                  document.getElementById('selectAllProducts').addEventListener('change', function() {
                    let checked = this.checked;
                    document.querySelectorAll('.product-checkbox')
                      .forEach(cb => cb.checked = checked);
                    updateCategoryCount();
                  });
                  
                  document.querySelectorAll('.product-checkbox').forEach(cb => {
                    cb.addEventListener('change', function() {
                      // Optional: if one is unchecked, uncheck "All Category"
                      if (!this.checked) {
                        document.getElementById('selectAllProducts').checked = false;
                      }
                      updateCategoryCount();
                    });
                  });

                  document.addEventListener('click', function(event) {
                    let dropdown = document.getElementById('productDropdown');
                    let list = document.getElementById('productList');
                    if (dropdown && list) {
                      if (!dropdown.contains(event.target)) {
                        list.style.display = 'none';
                      }
                    }
                  });
                </script>

                <script>
                  $(document).ready(function() {
                    $('#fdatefrom').datepicker({
                      dateFormat: 'dd-mm-yy',
                      yearRange: '2018:2030',
                      changeMonth: true,
                      changeYear: true,
                      onSelect: function(dateText, inst) {
                         updateDateLimits();
                      }
                    });
                    
                    $('#fendfrom').datepicker({
                      dateFormat: 'dd-mm-yy',
                      yearRange: '2018:2030',
                      changeMonth: true,
                      changeYear: true,
                    });
                  });
                </script>
                
                <div class="col-sm-2">
                  <label for="inputEmail3" class="control-label">Date From <strong style="color:red;">*</strong></label>
                  <input type="text" name="datefrom" class="form-control" id="fdatefrom" readonly required placeholder="Date From">
                </div>

                <div class="col-sm-2">
                  <label for="inputEmail3" class="control-label">Date To <strong style="color:red;">*</strong></label>
                  <input type="text" name="dateto" class="form-control" id="fendfrom" readonly required placeholder="Date To">
                </div>


                <div class="col-sm-3">
                  <label for="inputEmail3" class="control-label"></label>
                  <input type="submit"
                    style="background-color:#00c0ef; color:#fff; margin-top: 23px;"
                    id="Mysubscriptions" class="btn btn4 btn_pdf myscl-btn date" value="Search">
                  <a href="<?php echo SITE_URL; ?>admin/stockregister" class="excelbtn btn"
                    style="background-color:#00c0ef; !important; margin-top: 23px; color:#fff; padding:6px 18px; margin-left: 7px;">Reset</a>

                </div>
                
              </div>
            </div>
            <?php echo $this->Form->end(); ?>
          </div>

          <div class="box-body" id="example2">
            <div id="load2" style="display:none;"></div>
            <table id="" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th style="width: 16%;">S.No</th>
                  <th style="width: 16%;">Date</th>
                  <th>Opening Stock</th>
                  <th>Received Stock </th>
                  <th>Dispatched Stock </th>
                  <th>Closing Stock </th>
                </tr>
              </thead>
              <tbody id="updt">
                <tr>
                  <td colspan="7" align="center">No Record Found</td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
      </div>
    </div>
  </section>
  <!-- /.content -->
</div>