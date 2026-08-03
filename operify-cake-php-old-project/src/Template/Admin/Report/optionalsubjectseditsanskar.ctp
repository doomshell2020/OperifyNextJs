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
              <th>Opt 1</th>
              <th>Opt 2</th>
             
        
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
     
                 <td><?php $class=$this->Comman->findclass($work['class_id']); $section=$this->Comman->findsecti($work['section_id']); 
                 echo $class['title']."-".$section['title']; ?></td>
            
                 <td>
                    	<select name="comp_id[<?php echo $id ?>][]">
                        <option value="2">English Core</option>
                        </select>
                            
                 </td>
                 <td><?php if($work['class_id']=='12' || $work['class_id']=='17'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]">
                        <option value="68">Physics</option>
                        </select>
                            
                 <?php } else if($work['class_id']=='13' || $work['class_id']=='20'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]">
                        <option value="58">Accountancy</option>
                        </select>
                            
                 <?php } else if($work['class_id']=='15' || $work['class_id']=='22'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]">
                        <option value="65">Geography</option>
                        </select>
                            
                 <?php }?></td>
                 <td><?php if($work['class_id']=='12' || $work['class_id']=='17'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]">
                        <option value="67">Chemistry</option>
                        </select>
                            
                 <?php }  else if($work['class_id']=='13' || $work['class_id']=='20'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]">
                        <option value="56">Business Studies</option>
                        </select>
                        
                            
                 <?php } else if($work['class_id']=='15' || $work['class_id']=='22'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]"> 
                      <option value="">Select</option>
                        <option value="141" <?php if(in_array('141',$comp)) {?> selected <?php }?>>Psychology</option>
                        <option value="70" <?php if(in_array('70',$comp)) {?> selected <?php }?>>History</option>
                        </select>
                        
                            
                 <?php } ?></td>
                 <td><?php if($work['class_id']=='12' || $work['class_id']=='17'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]" multiple>
                      <option value="">Select</option>
                        <option value="66"<?php if(in_array('66',$comp)) {?> selected <?php }?>>Biology</option>
                        <option value="1"<?php if(in_array('1',$comp)) {?> selected <?php }?>>Mathematics</option>
                        </select>
                            
                 <?php } else if($work['class_id']=='13' || $work['class_id']=='20'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]">
                        <option value="57">Economics</option>
                        </select>
                            
                 <?php } else if($work['class_id']=='15' || $work['class_id']=='22'){ ?>
                    	<select name="comp_id[<?php echo $id ?>][]">
                        <option value="">select</option>
                        <option value="55"<?php if(in_array('55',$comp)) {?> selected <?php }?>>Political Science</option>
                        <option value="57"<?php if(in_array('57',$comp)) {?> selected <?php }?>>Economics</option>
                        </select>
                        
                            
                 <?php }?></td>
                   <td><?php echo $this->Form->input('opt_id['.$id.'][]',array('class'=>'form-control','type'=>'select','empty'=>'Select Option1','options'=>$opt_sub,'value'=>$opt[0],'label' =>false));?></td>
                     </td>
                    <td><?php  echo $this->Form->input('opt_id['.$id.'][]',array('class'=>'form-control','type'=>'select','empty'=>'Select Option2','options'=>$opt_sub,'value'=>$opt[1],'label' =>false)); ?></td>
                         

           
       
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
