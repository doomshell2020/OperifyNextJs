<?php echo $this->Form->create('Tasks2',array('url'=>array('controller'=>'Report','action'=>'savesubj'),'type'=>'file','inputDefaults'=>array('div'=>false,'label'=>false),'id'=>'TaskAdminCustomerForm2','class'=>'form-horizontal')); ?>
<div class="row" >
  <div class="col-xs-12">
    
   <div class="box" >
   

        <!-- /.box-header -->
        <div class="box-body">
        <div class="table-responsive">
          <table id="example1" class="table table-bordered table-striped">
          <button type="submit" class="btn btn-success" style="position: absolute;
top: -37px;
/* right: 0px; */
right: 19px;">Quick Update</button> 
            
           <thead>
            <tr>
              <th>#</th>

              <th>Sr. No.</th>
              <th>Name</th>
          
              <th>Class</th>
            
              <th>Subject 1</th>
              <th>Subject 2</th>
              <th>Subject 3</th>
              <th>Subject 4</th>
              <th>Subject 5</th>
              <?php $findoptional=$this->Comman->findoptionalsubject($students[0]['class_id']); 
              $krt=1;
             foreach($findoptional as $kry=>$irt){ ?>
              <th>Opt <?php echo $krt++; ?></th>
          

              <?php } ?>
             
        
             <!-- <th>Admission Year</th>-->
              
                 
            </tr>
          </thead>
          <tbody id="example2">
            <?php $page = $this->request->params['paging']['Students']['page'];
            $limit = $this->request->params['paging']['Students']['perPage'];
            $counter = ($page * $limit) - $limit + 1; 
            if(isset($students) && !empty($students)){ 
              foreach($students as $work){
                
                //pr($work); die;
                $comp=explode(',',$work['comp_sid']);
               
             
                $opt=explode(',',$work['opt_sid']);
                //pr($opt); die;
                ?>
                <tr>
                 <td><?php echo $counter;?></td>

                 <td><?php echo $work['enroll']; $id=$work['id'];?></td>
                 
                 <td><?php echo $work['fname']; ?> <?php echo $work['middlename']; ?> <?php echo $work['lname']; ?></td>
     
                 <td><?php $class=$this->Comman->findclass($work['class_id']); 
                 $section=$this->Comman->findsecti($work['section_id']); 
                 echo $class['title']."-".$section['title']; ?></td>
            <?php $findcomp=$this->Comman->findcompulsorysubject($work['class_id']); 

            $compsub=explode(',',$work['comp_sid']);
            $optsub=explode(',',$work['opt_sid']);
             foreach($findcomp as $kry=>$irt){ ?>
                 <td>
                    	<select name="comp_id[<?php echo $id ?>][]">
                      <?php if($irt['exprint']=="Biology"  || $irt['exprint']=="Mathematics"){ ?>
                      <option value="">Select Subject</option>
                      <?php } ?>
                        <option <?php if(in_array($irt['id'],$compsub)) {?> selected <?php }?> value="<?php echo $irt['id']; ?>"><?php echo $irt['exprint']; ?></option>
                        </select>
                            
                 </td>

                 <?php } ?>

                 <?php $findoptional=$this->Comman->findoptionalsubject($work['class_id']); 
             foreach($findoptional as $kry=>$irts){ ?>
                 <td>
      <select name="opt_id[<?php echo $id ?>][]">
      <option value="">Select Optional</option>
     <option <?php if(in_array($irts['id'],$optsub)) {?> selected <?php }?> value="<?php echo $irts['id']; ?>"><?php echo $irts['exprint']; ?></option>
                        </select>
                            
                 </td>

                 <?php } ?>
               
                  

           
       
     </tr>
     <?php $counter++;} }else{ ?>
     <tr>
      <td>No Data Available</td>
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

<?php    if(isset($students) && !empty($students)){ 
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
</div>
<?php
echo $this->Form->end();
?> 	 
