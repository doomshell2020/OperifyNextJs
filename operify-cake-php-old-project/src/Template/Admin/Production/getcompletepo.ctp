<?php


foreach ($podetails as $value) {
    $itemname = $this->comman->getitemname($value['item_id']);
    ?>
    <tr>
        <td><?php echo $value['po_id']  ?></td>
        <td><?php echo date('d-m-Y',strtotime($value['issuedate']))  ?></td>
        <td><?php echo $itemname['item_name']  ?></td>
        <td><?php echo $value['plannedqty']  ?></td>
        <td><?php echo date('d-m-Y',strtotime($value['startdate']))  ?></td>
        <td><?php echo date('d-m-Y',strtotime($value['enddate']))  ?></td>
        <td><?php echo $value['totaldays']  ?></td>
    </tr>


<?php }


?>