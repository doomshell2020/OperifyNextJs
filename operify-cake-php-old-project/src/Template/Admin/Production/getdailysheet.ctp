<?php 
$i = 1;
foreach ($processname as $process) {
    $checkdailysheet = $this->comman->checkdailysheet($productionorder['po_id'], $process['id']);
    $poqty = $this->Comman->findproductionorder($productionorder['po_id']);
    // pr($poqty);
    if (!empty($checkdailysheet)) {

        $quantity = '';
        $startdate = '';
        $completedate = '';
        foreach ($checkdailysheet as $key => $value) {
            $quantity += $value['production_shift_a'] + $value['production_shift_b'];

            if ($key === array_key_first($checkdailysheet)) {
                $startdate = date('d-m-Y', strtotime($value['production_date']));
            }
            if ($key === array_key_last($checkdailysheet)) {
                $completedate = date('d-m-Y', strtotime($value['production_date']));
            }
        }
        ?>
        <tr>
            <td>
                <?php echo $i; ?>.
            </td>
            <td>
            <a target="blank" href="<?php echo SITE_URL; ?>admin/production/index/<?php echo $productionorder['po_id']; ?>/<?php echo $process['id']; ?>"><?php echo $process['process_name']; ?></a>
            </td>
            <td>
                <?php echo $startdate; ?>
            </td>
            <td>
                <?php echo $completedate; ?>
            </td>
            <td style="text-align:right;">
                <?php echo sprintf('%.2f', $poqty['plannedqty']); ?>
            </td>
            <td style="text-align:right;">
                <?php echo sprintf('%.2f', $quantity); ?>
            </td>
        </tr>
        <?php
        $i++;
    } else {
        continue;
    }
} ?>