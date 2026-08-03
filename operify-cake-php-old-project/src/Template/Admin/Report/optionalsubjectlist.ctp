<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
        Manage Student Subject Main/Optional
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo ADMIN_URL;?>students/index"><i class="fa fa-home"></i>Home</a></li>
        <li><a href="<?php echo ADMIN_URL;?>report/optionalsubjectlist">Manage Subject Main/Optional</a></li>
    </ol>
  </section>
  <!-- Main content -->
  <section class="content">
    <div id="load"></div>
    <div class="row">
      <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
                <i class="fa fa-search" aria-hidden="true"></i>
                <h3 class="box-title">Search Student</h3>
            </div>
            <style>
                #load{
                width:100%;
                height:100%;
                position:fixed;
                z-index:9999;
                background-color: white  !important;
                background:url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
                }
                #load2{
                width:100%;
                height:100%;
                position:fixed;
                z-index:9999;
                background-color: white  !important;
                background:url("<? echo SITE_URL; ?>images/Preloader_2.gif") no-repeat center center rgba(0,0,0,0.75)
                }
            </style>
            <script>
                document.onreadystatechange = function () {
                var state = document.readyState
                if (state == 'complete') {
                    document.getElementById('interactive');
                    document.getElementById('load').style.visibility="hidden";
                }
                }
            </script>
            <!-- /.box-header -->
            <?php echo $this->Flash->render(); ?>
            <div class="box-body">
              <div class="manag-stu">
                <script inline="1">
                    //<![CDATA[
                    $(document).ready(function () {
                    $("#TaskAdminCustomerForm").bind("submit", function (event) {
                        $.ajax({
                    
                    async:false,
                    
                    type:"POST", 
                    
                    url:"<?php echo ADMIN_URL ;?>Report/optionalsubjectslistsearch",
                    
                    data:$("#TaskAdminCustomerForm").serialize(),
                    
                    dataType:"html", 
                    
                    success:function (data) {
                      //alert(data);
                      //console.log(data);
                      $("#example12").html(data);
                    }
                    
                    });
                    return false;
                    });
                    });
                    //]]>
                </script>
                <?php echo $this->Form->create('Task',array('type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerForm','class'=>'form-horizontal')); ?>
                <div class="form-group">
                    <script>
                      $(document).ready(function(){
                      $('#class-ids').on('change',function(){
                      var id = $('#class-ids').val();
                      //alert(id);
                        $.ajax({ 
                              type: 'POST', 
                              url: '<?php echo ADMIN_URL ;?>ClasstimeTabs/find_section',
                              data: {'id':id}, 
                              success: function(data){  
                      
                        $('#section-id').empty();
                        $('#section-id').html(data);
                              }, 
                              
                          });  
                      });
                      });
                      
                    </script>
                    <div class="col-sm-3">
                      <label>Select Class <span style="color:red;">*</span></label>
                      <?php 
                          echo 
                          $this->Form->input('class_id',array('class'=>'form-control','type'=>'select','id'=>'class-ids','empty'=>'Select Class','options'=>$classes,'label' =>false, 'required'));
                          ?>
                    </div>
                    <div class="col-sm-3">
                      <label>Select Section <span style="color:red;">*</span></label>
                      <?php 
                          echo $this->Form->input('section_id',array('class'=>'form-control','type'=>'select','empty'=>'Select Section','options'=>$sectionslist,'label' =>false, 'required')); 
                          
                          ?>  
                    </div>
                    <div class="col-sm-3">
                      <label>Optional Subject</label>
                      <?php 
                          echo 
                          $this->Form->input('opt_sub',array('class'=>'form-control','type'=>'select','empty'=>'Select Optional Subject','options'=>$opt_sub,'label' =>false));
                          ?>
                    </div>
                    <div class="col-sm-3 text-xs-center" style="top: 22px;">
                      <button type="submit" class="btn btn-success">Search</button>
                      <button type="reset" class="btn btn-primary">Reset</button>
                    </div>
                    <?php
                      echo $this->Form->end();
                      ?>   
                </div>
                <a id="" style="  margin-right: 0px; margin-top: -50px; position: relative; z-index: 9;" class="btn btn-info btn-sm pull-right" href="<?php echo ADMIN_URL ;?>report/optsubjectedit"><i class="far fa-edit" aria-hidden="true"></i> Edit</a>
              </div>
            </div>
          </div>
      </div>
    </div>
    <div class="row"  >
    <div id="load2" style="display:none;"></div>
    <div class="col-xs-12">
    <div class="box" >
    <div class="box-header" style="display:flex;">
      <i class="fa fa-search" aria-hidden="true"></i>
      <h3 class="box-title" style="flex:1;"> View Students List </h3>

      <a id="" style="margin-right: 0px; margin-top: 0px;" class="btn btn-info btn-sm" href="<?php echo ADMIN_URL ;?>report/optstudent_excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="box box-info">
          <div class="box-body"  id="example12">
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
                        <th>Subject 1</th>
                        <th>Subject 2</th>
                        <th>Subject 3</th>
                        <th>Subject 4</th>
                        <th>Subject 5</th>
                        <th>Opt 1</th>
                        <th>Opt 2</th>
                        <th>Opt 3</th>
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
                          <a href="<?php echo SITE_URL; ?>admin/students/view/<?php echo $work['id']; ?>"><?php echo $work['fname']; ?> <?php echo $work['middlename']; ?><?php echo $work['lname']; ?></a>
                          <?php }else{ ?> <?php echo $work['fname']; ?> <?php echo $work['middlename']; ?> <?php echo $work['lname']; ?><?php } ?>
                        </td>
                        <td><?php echo $work['fathername']; ?></td>
                        <td><?php $class=$this->Comman->findclass($work['class_id']); echo $class['title']; ?></td>
                        <td><?php $section=$this->Comman->findsecti($work['section_id']); echo $section['title'];?>
                        <td><?php if($work['comp_sid']){
                          $rt=array();
                          $rt=explode(',',$work['comp_sid']);
                          
                          $subject=$this->Comman->findsubjectsubs2($rt[0]);
                          echo $subject['name']; 
                          } ?></td>
                        <td><?php if($work['comp_sid']){
                          $rt=array();
                          $rt=explode(',',$work['comp_sid']);
                          
                          $subject2=$this->Comman->findsubjectsubs2($rt[1]);
                          echo $subject2['name']; 
                          } ?></td>
                        <td><?php if($work['comp_sid']){
                          $rt=array();
                          $rt=explode(',',$work['comp_sid']);
                          
                          $subject3=$this->Comman->findsubjectsubs2($rt[2]);
                          echo $subject3['name']; 
                          } ?></td>
                        <td><?php if($work['comp_sid']){
                          $rt=array();
                          $rt=explode(',',$work['comp_sid']);
                          
                          $subject4=$this->Comman->findsubjectsubs2($rt[3]);
                          echo $subject4['name']; 
                          } ?></td>
                        <td><?php if($work['comp_sid']){
                          $rt=array();
                          $rt=explode(',',$work['comp_sid']);
                          
                          $subject5=$this->Comman->findsubjectsubs2($rt[4]);
                          echo $subject5['name']; 
                          } ?></td>
                        <td><?php if($work['opt_sid']){
                          $rts=array();
                          $rts=explode(',',$work['opt_sid']);
                          
                          $subject5=$this->Comman->findsubjectsubs2($rts[0]);
                          echo $subject5['name']; 
                          } ?></td>
                        <td><?php if($work['opt_sid']){
                          $rts=array();
                          $rts=explode(',',$work['opt_sid']);
                          
                          $subject6=$this->Comman->findsubjectsubs2($rts[1]);
                          echo $subject6['name']; 
                          } ?></td>
                        <td><?php if($work['opt_sid']){
                          $rts=array();
                          $rts=explode(',',$work['opt_sid']);
                          
                          $subject7=$this->Comman->findsubjectsubs2($rts[2]);
                          echo $subject7['name']; 
                          } ?></td>
                        </td>
                    </tr>
                    <?php $counter++;} }else{ ?>
                    <tr>
                        <td colspan="14">NO Data Available</td>
                    </tr>
                    <?php } ?>  
                  </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>