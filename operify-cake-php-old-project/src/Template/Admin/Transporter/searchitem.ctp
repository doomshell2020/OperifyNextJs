<table class="table table-bordered table-striped">
   <thead>

      <tr>
         <th>S.No.</th>
         <th>Date</th>
         <th>Transporter Name</th>
         <th>To</th>
         <th>From</th>
         <th>Vehicle No</th>
         <th>GR No.</th>
         <th>Weight</th>
         <th>Freight</th>
         <th>Action</th>
      </tr>

   </thead>
   <tbody>
      <?php $page = $this->request->params['paging']['transporter']['page'];
      $limit = $this->request->params['paging']['transporter']['perPage'];
      $counter = ($page * $limit) - $limit + 1;
      if (isset($data) && !empty($data)) {
         foreach ($data as $value) {
            ?>
            <tr>
               <td><?php echo $counter; ?></td>
               <td><?php echo date("d-m-Y", strtotime($value['datefrom'])); ?></td>
               <td><?php echo ucfirst($value['vendor']['name']); ?></td>
               <td><?php echo ucfirst(strtolower($value['transport_to'])); ?></td>
               <td><?php echo ucfirst(strtolower($value['transport_from'])); ?></td>
               <td><?php echo strtoupper($value['vehicle_no']); ?></td>
               <td style="text-align:right"><?php echo $value['gr_no']; ?></td>
               <td style="text-align:right"><?php echo $value['weight']; ?></td>
               <td style="text-align:right"><?php echo $value['freight']; ?></td>
               <td> <strong>
                     <a target="_blank" href="<?php echo SITE_URL . 'transporterupload/' . $value['upload']; ?>"
                        title="Download" data-method="post" data-toggle="tooltip"><span
                           class="fa fa-download fa-lg text-green"></span></a> &nbsp;

                     <?php
                     $user_id = $_SESSION['Auth']['User']['id'];
                     $controllerName = $this->request->params['controller'];
                     $actionName = "index";
                     $user_permission = $this->comman->finduserpermisson($user_id, $controllerName, $actionName);

                     if ($user_permission['edit'] == '1') {
                        echo $this->Html->link('', [
                           'action' => 'edit',
                           $value->id,
                        ], ['class' => 'fas fa-edit', 'style' => 'font-size: 16px !important;']);
                     }
                     ?>
                     &nbsp;
                     <?php
                     if ($user_permission['delete'] == '1') {
                        echo $this->Html->link('', [
                           'action' => 'status',
                           $value->id,
                           'N'
                        ], [
                           'class' => 'fas fa-trash-alt',
                           'style' => 'font-size: 16px !important; color:#cd0404; margin-right:4px !important;',
                           "onClick" => "javascript: return confirm('Are you sure do you want to delete this Transport details')"
                        ]);
                     } ?>
                  </strong></td>
            </tr>
            <?php $counter++;
         }
      } else {
         ?>
         <tr>
            <td colspan="10" align="center">No DATA Available</td>
         </tr>
         <?php
      } ?>
   </tbody>
</table>
<?php echo $this->element('admin/pagination'); ?>