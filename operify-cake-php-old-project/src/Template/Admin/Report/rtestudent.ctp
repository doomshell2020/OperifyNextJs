<?php $this->request->session()->delete('rtestud'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
      <h1>
        RTE Student Manager
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php echo ADMIN_URL;?>report/rtestudent"><i class="fa fa-home"></i>Home</a></li>
        <li><a href="<?php echo ADMIN_URL;?>report/rtestudent">Manage RTE Students</a></li>
      </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div class="row">
        <div class="col-xs-12">
          <?php echo $this->Flash->render(); ?>
          <div class="box">
              <!-- /.box-header -->
              <div class="box-header">
                <div class="manag-stu">
                    <script inline="1">
                      //<![CDATA[
                      $(document).ready(function () {
                        $("#TaskAdminCustomerForm").bind("submit", function (event) {
                          $.ajax({
                            async:true,
                            data:$("#TaskAdminCustomerForm").serialize(),
                            dataType:"html", 
                            success:function (data, textStatus) {
                              $("#example2").html(data);}, 
                              type:"POST", 
                              url:"<?php echo ADMIN_URL ;?>report/searchrte"});
                          return false;
                        });
                      });
                      //]]>
                    </script>
                    <?php echo $this->Form->create('Task',array('url'=>array('controller'=>'students','action'=>'search'),'type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerForm','class'=>'form-horizontal')); ?>
                    <div class="form-group margin_btmcol" style="display:flex; align-items: flex-end; margin-bottom:0px;">
                      <div class="col-md-3 col-sm-4 col-xs-12">
                          <label>Class</label>
                          <select class="form-control" name="class_id">
                            <option value="">Select</option>
                            <?php  foreach($classes as $esr=>$es) { ?>
                            <option value="<?php echo $esr; ?>"><?php echo $es; ?></option>
                            <?php } ?>
                          </select>
                      </div>
                      <!--<div class="col-sm-2">
                          <label>Batch</label>
                          <select class="form-control" name="admissionyear">
                          <?= $year='2016'; echo $year; $exyear=$year+4; ?>
                          <option value="">Select Batch</option>
                          <?php for ($i = $year; $i <= $exyear; $i++) :  $rt=$i+1; $rt= substr($rt,2);?> 
                            <option value="<?php echo $i; ?>-<?php echo  $rt;?>"><?php echo $i; ?>-<?php echo  $rt;?></option>
                          <?php endfor; ?>
                          </select>
                          </div>   -->
                      <div class="col-md-3 col-sm-4 col-xs-12 text-xs-center" style="">
                          <label></label>
                          <button type="submit" class="btn btn-success">Search</button>
                          <button type="reset" class="btn btn-primary">Reset</button>
                      </div>
                    </div>
                    <?php
                      echo $this->Form->end();
                      ?>
                </div>
              </div>
          
              <!-- /.box-header -->
              <div class="box-body">
                <div class="text-right">
                  <a id="" style="" class="btn btn-info btn-sm text-right" href="<?php echo ADMIN_URL ;?>report/rtestudent_excel">
                  <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                  Excel
                  </a>
                </div>
                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                      <thead>
                          <tr>
                            <th>#</th>
                            <th>Sr. No.</th>
                            <th>Name</th>
                            <th>Father's Name</th>
                            <th>Class</th>
                            <th>Section</th>
                            <th>Disc. Scheme</th>
                            <!-- <th>Admission Year</th>-->
                          </tr>
                      </thead>
                      <tbody id="example2">
                          <?php $page = $this->request->params['paging']['Students']['page'];
                            $limit = $this->request->params['paging']['Students']['perPage'];
                            $counter = ($page * $limit) - $limit + 1;
                            if(isset($students) && !empty($students)){
                              foreach($students as $work){
                                ?>
                          <tr>
                            <td><?php echo $counter;?></td>
                            <td><?php echo $work['enroll']; ?></td>
                            <td><?php if($role_id=='1'){ ?>
                                <a href="<?php echo SITE_URL; ?>admin/students/view/<?php echo $work['id']; ?>"><?php echo $work['fname']; ?></a>
                                <?php }else{ ?> <?php echo $work['fname']; ?><?php } ?>
                            </td>
                            <td><?php echo $work['fathername']; ?></td>
                            <td><?php $class=$this->Comman->findclass($work['class_id']); echo $class['title']; ?></td>
                            <td><?php $section=$this->Comman->findsecti($work['section_id']); echo $section['title'];?>
                            <td><?php echo $work['category']; ?></td>
                            </td>
                          </tr>
                          <?php $counter++;} }else{ ?>
                          <tr>
                            <td colspan="7">NO Data Available</td>
                          </tr>
                          <?php } ?>
                      </tbody>
                    </table>
                </div>
                <?php    if(isset($students) && !empty($students)){
                    foreach($students as $work){
                      ?>
                <div class="modal" id="globalModal<?php echo $work->id; ?>" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
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
                    $("#globalModal<?php echo $work->id; ?>").click(function(event){
                      $('.modal-content').load($(this).attr("href"));  //load content from href of link
                    });
                    }); 
                </script>
                <?php } }?>
                <?php    if(isset($students) && !empty($students)){ 
                    foreach($students as $work){
                      ?>
                <div class="modal" id="global<?php echo $work->id; ?>" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
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
                    $("#globalModal<?php echo $work->id; ?>").click(function(event){
                      $('.modal-content').load($(this).attr("href"));  //load content from href of link
                    });
                    }); 
                </script>
                <?php } }?>         
                <?php if(isset($students) && !empty($students)){ 
                  foreach($students as $work){
                ?>
                <div class="modal" id="globaldiscount<?php echo $work->id; ?>" tabindex="-1" role="dialog" aria-labelledby="esModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
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
                    $("#globaldiscount<?php echo $work->id; ?>").click(function(event){
                      $('.modal-content').load($(this).attr("href"));  //load content from href of link
                    });
                    }); 
                </script>
                <?php } }?> 
              </div>
              <!-- /.box-body -->   
          </div>
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
  </section>
</div>