<?php
  $session = $this->request->session();
  $role_id = $session->read('Auth.User.role_id');
?>

 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">

  <section class="content-header">
    <h1>
      Overdue Without Fine Manager
    </h1>
       <ol class="breadcrumb">
    <li><a href="<?php echo ADMIN_URL;?>dashboards"><i class="fa fa-home"></i>Home</a></li>
<li><a href="<?php echo ADMIN_URL;?>ReturnRenewBooks/index">Fine Report</a></li>
        </ol>

</section>



  <!-- Main content -->
  <section class="content">

    <div class="row">
      <div class="col-xs-12">

        <div class="box">

          <div>
            <?php echo $this->Flash->render(); ?>
          </div>

          <div class="box-header">
           


          </div>
          <!-- /.box-header -->
<script>

  $(document).ready(function () {

    $("#TaskAdminCustomerForm").bind("submit", function (event) {
      event.preventDefault();
          
      $.ajax({

        async:false,

        type:"POST", 
        
        url:"<?php echo ADMIN_URL ;?>report/finesearch",
        
        data:$("#TaskAdminCustomerForm").serialize(),
        
        dataType:"html", 

        success:function (data) {
          // alert(data);
          $("#example1").html(data);
        }

      });
   

    });

  });

</script>

  <script>
    $(document).ready(function(){     
        $('#fdatefrom').datepicker({    
  dateFormat: 'yy-mm-dd',
        onSelect: function(date){ 

        var selectedDate = new Date(date);
        var endDate = new Date(selectedDate);
         endDate.setDate(endDate.getDate());
     
        $("#fendfrom").datepicker( "option", "minDate", endDate );
        $("#fendfrom").val(date);
    }
    
    });
    
    
  $('#fendfrom').datepicker({    
  dateFormat: 'yy-mm-dd'});
       });
         </script>  


<!--
  <div class="box-body"><a id="" style="margin-right: 20px; margin-top: 10px;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/overduewithoutfineexcel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</a></div>
-->




          <div class="box-body">
            <div id="srch-rslt">
            <div class="table-responsive">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>#</th>
                 
                  <th style="width: 334.021px;">Book Name</th>
                  <th>Acc. No.</th>
                  <th>Holder Name</th>
                  <th>Holder Type</th>
                  <th>Issue Date</th>
                  <th>Due Date</th>
                  <th>Deposit Date</th>
                 
                </tr>
              </thead>
              <tbody>

                <?php 

                $page = $this->request->params['paging']['books']['page'];
                $limit = $this->request->params['paging']['books']['perPage'];
                $counter = ($page * $limit) - $limit + 1;

                if(isset($results) && !empty($results)){ 
                  foreach($results as $work){ //pr($work);
                     //pr($work);die;
                     $hol=explode('-',$work['holder_name']);
                     $asn=trim($work['asn_no']);
                    
                     $bn= $this->Comman->findbookname12($asn);
                     $bname=$bn['name'];
                


                    //pr($d1); die;
                   

                    ?>
                    <tr>
                      <td><?php echo $counter;?></td>

                                           
                      <td><?php if(isset($bname)){ echo ucfirst($bname);}else{ echo 'N/A';}?></td>

                      <td><?php if(isset($work['asn_no'])){ echo ucfirst($work['asn_no']);}else{ echo 'N/A';}?></td>

                      <td><?php if(isset($work['holder_name'])){ echo ucfirst($work['holder_name']);}else{ echo 'N/A';}?></td>

                      <td><?php if(isset($work['holder_type_id'])){ echo ucfirst($work['holder_type_id']);}else{ echo 'N/A';}?></td>

                      <td><?php if(isset($work['issue_date'])){ echo date('d-m-Y',strtotime($work['issue_date']));}else{ echo 'N/A';}?></td>
                      <td><?php if(isset($work['due_date'])){ echo date('d-m-Y',strtotime($work['due_date']));}else{ echo 'N/A';}?></td>
                      <td><?php if(isset($work['dep_date'])){ echo date('d-m-Y',strtotime($work['dep_date']));}else{ echo 'N/A';}?></td>

                     
 </tr>
                    <?php $counter++;} ?>

<?php    }else{?>
                    <tr>
                      <td>NO Data Available</td>
                    </tr>
                    <?php } ?>  
                  </tbody>

                </table>
</div>
              </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </section>
      <!-- /.content -->
    </div>


    <!-- /.content-wrapper -->


    <div class="modal" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true" style="display: none;">
      <div class="modal-dialog">
        <div class="modal-content testeingprogress">
          <div class="modal-body">
            <div class="loader">
              <div class="es-spinner">
                <i class="fa fa-spinner fa-pulse fa-5x fa-fw"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

<script>
 $(document).ready(function() {
    //prepare the dialog

    //respond to click event on anything with 'overlay' class
    $(".global1").click(function(event){

        //load content from href of link
        $('.modal-content').load($(this).attr("href"));

      });
  }); 
</script>



<script>
      $(document).ready(function() {
    //prepare the dialog

    //respond to click event on anything with 'overlay' class
   $(".global2").click(function(event){
        //load content from href of link
   $('.testeingprogress').load($(this).attr("href"));
      });
  }); 
</script>

