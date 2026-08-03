<div class="content-wrapper">

   <section class="content-header">
     <h1>
       Import Employee Manager
     </h1>
     <ol class="breadcrumb">
     <li><a href="<?php echo ADMIN_URL;?>Datarecord/"><i class="fa fa-home"></i>Home</a></li>
</ol>
   </section>

   <div class="box">
           <div class="box-body">
           <?php echo $this->Form->create($classes, array('class'=>'form-horizontal','id' => 'sevice_form','type'=>'file',
                       'enctype' => 'multipart/form-data','novalidate')); ?>
           <table class="table table-bordered table-striped">
            <thead>
                <tr>
               <th>Chosse File</th>

                </tr>
            </thead>
               <tbody>
                   <tr>
                       <td>
                       <input type="file"  name = "file" value="">
                       </td>
                          
                    </tr>
               </tbody>
              
           </table>  <br>  
           <?php 
		
    echo $this->Form->submit(
        'Upload', 
        array('class' => 'btn btn-info pull-right', 'title' => 'Upload','style'=>'margin-top: -33px;')
    );
       ?>
           <?php echo $this->Form->end(); ?>
           </div>
         </div>
        </div>

  








</div>