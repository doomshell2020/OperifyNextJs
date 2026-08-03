<?php $date = date('d-M-Y'); ?>

<div class="box-body" id="example145">
   <P style="text-align:right;">Date -
      <?php echo $date; ?>
   </p>
   <div class="tableCover">
      <table class="table table-bordered table-striped">
         <thead class="fix">
            <tr>
            <tr>
               <th width="5%">S No.</th>
               <th width="35%">Product Name</th>
               <th width="20%">Type</th>
               <th width="20%">TPPL</th>
               <th width="20%">KCPL</th>
            </tr>
         </thead>
         <tbody>
            <?php $page = $this->request->params['paging']['']['page'];
            $limit = $this->request->params['paging']['']['perPage'];
            $counter = ($page * $limit) - $limit + 1;
            if ($data != null) {
               foreach ($data as $key => $intusr) {
                  $id = $intusr['id'];

              
               ?>
               <tr>
                  <td>
                     <?php echo $counter; ?>
                  </td>
                  <td>
                     <?php echo $intusr['additem']['item_name']; ?>
                  </td>

                  <td>
                     <?php echo $this->Form->input('type[' . $id . ']', array('class' => '', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off',  'required','value' => $intusr['type'], 'id' => 'issuequant-' . $key, 'oninput' => 'calculatetotalqty(this)', 'onkeypress' => 'return isNumberKey(event)','readonly')); ?>
                  </td>

                  <td>
                     <?php echo $this->Form->input('tppl[' . $id . ']', array('class' => '', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'value' => $intusr['tppl'],'id' => 'issuewei-' . $key, 'oninput' => 'calculatetotalweight(this)', 'onkeypress' => 'return isNumberKey(event)','readonly')); ?>
                  </td>

                  <td>
                     <?php echo $this->Form->input('kcpl[' . $id . ']', array('class' => '', 'type' => 'text', 'label' => false, 'autofocus', 'autocomplete' => 'off', 'required', 'value' => $intusr['kcpl'], 'id' => 'tpplquant-' . $key, 'oninput' => 'calculatetotalqty(this)', 'onkeypress' => 'return isNumberKey(event)','readonly')); ?>
                  </td>
                  <?php $counter++;
            } }else {
               ?>
               <tr>
                  <td colspan="5" align="center">No DATA Available</td>
               </tr>
            <?php }
            ?>
         </tbody>
      </table>
   </div>
</div>
<script>
   $(document).ready(function () {
      var i = 0;
      for (i = 0; i < 100; i++) {
         const opengqty = $("#opengqty-" + i).val();
         const issueqty = $("#issuequant-" + i).val();
         const tpplqty = $("#tpplquant-" + i).val();
         const kcplqty = $("#kcplquant-" + i).val();
         const totalclosingqty = parseInt(opengqty) - parseInt(issueqty) + parseInt(tpplqty) + parseInt(kcplqty);
         console.log(totalclosingqty);
         $("#closingquant-" + i).val(totalclosingqty);
      }

      for (i = 0; i < 100; i++) {
         const opnenweight = $("#opnenweight-" + i).val();
         const issuewei = $("#issuewei-" + i).val();
         const tpplwei = $("#tpplwei-" + i).val();
         const kcplwei = $("#kcplwei-" + i).val();
         const totalclosingwei = parseInt(opnenweight) - parseInt(issuewei) + parseInt(tpplwei) + parseInt(kcplwei);
         console.log(totalclosingwei);
         $("#closingwei-" + i).val(totalclosingwei);
      }
   });
</script>