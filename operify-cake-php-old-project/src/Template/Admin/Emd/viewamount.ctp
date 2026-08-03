<?php //pr($_SESSION); 
?>
<style>
    /* .tableContainer {
    border: 1px solid #ccc;
  } */

    .tableContainer p {
        margin-bottom: 5px;
    }

    .tableContainer table thead {
        background: #fff;
        color: #333;
    }

    .tableContainer .tableHeader {
        padding: 10px;
        /* border-bottom: 1px solid #ccc; */
    }
</style>
<?php $k = 1;
$receivedTotal = array_sum(array_column($Emdamount, 'recive_amount'));
$pendingAmount = $EmdGuarantees['amount'] - $receivedTotal;
?>

<div class="tableContainer " style=" border:1px solid #ccc !important;">

    <div class="tableHeader">
        <p style="text-align:center;font-size:15px;"><b>EMD Details</b></p>
        <table>

            <tr>
                <td><b>BG No:-</b>
                    <?php echo $EmdGuarantees['bankguaranteeno']; ?>
                </td>
                <td><b>Date:-</b>
                    <?php echo date('d-m-Y', strtotime($EmdGuarantees['datefrom'])); ?>
                </td>
                <td><b>BG For:-</b>
                    <?php echo  $EmdGuarantees['bg_for']; ?>
                </td>
            </tr>
            <tr>
                <?php if (!empty($EmdGuarantees['validupto'])) { ?>
                    <td><b>Valid Upto:-</b>
                        <?php echo !empty($EmdGuarantees['validupto']) ? date('d-m-Y', strtotime($EmdGuarantees['validupto'])) : ''; ?>
                    </td>
                <?php }
                if (!empty($EmdGuarantees['claim_upto'])) { ?>
                    <td><b>Claim Date:-</b>
                        <?php echo !empty($EmdGuarantees['claim_upto']) ? date('d-m-Y', strtotime($EmdGuarantees['claim_upto'])) : ''; ?>
                    </td>
                <?php }
                if (!empty($EmdGuarantees['extenstionupto'])) { ?>
                    <td><b>Extension Upto:-</b>
                        <?php echo !empty($EmdGuarantees['extenstionupto']) ? date('d-m-Y', strtotime($EmdGuarantees['extenstionupto'])) : ''; ?>

                    </td>
                <?php } ?>
            </tr>


            <td><b>Favour:-</b>
                <?php echo $EmdGuarantees['favour_of']; ?>
            </td>
            <td><b>Amount:-</b>
                <?php echo number_format($EmdGuarantees['amount']); ?>
            </td>

            </tr>

        </table>
    </div>

    <!-- Received Table -->
    <p style="text-align:center;font-size:15px;"><b>Received</b></p>
    <div class="table-responsive" style="padding: 10px;">
        <table class="table-bordered" cellpadding="3" style="width: 100%;">
            <thead>
                <tr>
                    <th width="5%">S.No.</th>
                    <th width="13%">Date</th>
                    <th width="50%">Description</th>
                    <th width="21%">File</th>
                    <th width="11%">Amount</th>
                </tr>

            </thead>
            <tbody>
                <?php foreach ($Emdamount as $Remarks): ?>
                    <tr>
                        <td><?= $k ?>.</td>
                        <td><?= date('d-m-Y', strtotime($Remarks['recive_date'])) ?></td>
                        <td><?= h($Remarks['description']) ?></td>
                        <td style="text-align:center;">
                            <?php if (!empty($Remarks['invoice_file'])) {
                                $db = $this->request->session()->read('Auth.User.db');
                                $filePath = '/images/' . $db . '_image/emd/' . h($Remarks['invoice_file']);
                            ?>
                                <a href="javascript:void(0);" onclick="openFilePopup('<?= $this->Url->build($filePath, ['fullBase' => true]) ?>')" class="btn btn-sm btn-primary">
                                    View File
                                </a>
                            <?php } else { ?>
                                N/A
                            <?php } ?>
                        </td>
                        <td style="text-align:right;"><?= number_format($Remarks['recive_amount']) ?></td>
                    </tr>

                    <?php $k++; ?>
                <?php endforeach; ?>
                <tr>

                    <td colspan="4" style="text-align:right; font-weight:bold;">
                        Total
                    </td>
                    <td style="text-align:right;">
                        <?php echo number_format($receivedTotal); ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Add Payment Button -->
        <?php if ($remainingAmount > 0) {
        ?>
            <div style="margin-top: 15px; text-align: center;">
                <button type="button" onclick="document.getElementById('remarkForm').style.display='block'" class="btn btn-primary">+ Add Amount</button>
            </div>
        <?php } ?>
        <!-- Add Payment Form -->
        <div id="remarkForm" style="display: none; margin-top: 15px; border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;">
            <p style="text-align:center;font-size:15px;"><b>Add New Amount</b></p>

            <?= $this->Form->create(null, ['type' => 'file','class' => 'form-horizontal']) ?>
            <?= $this->Form->hidden('particular_id', ['value' => $EmdGuarantees['id']]) ?>

            <table width="100%" cellpadding="5">
                <tr>
                    <td width="20%"><label for="recive_amount"><b>Received Amount:</b></label></td>
                    <td>
                        <?= $this->Form->control('recive_amount', [
                            'label' => false,
                            'type' => 'text',
                            'step' => '0.01',
                            'required' => true,
                            'max' => $remainingAmount,
                            'style' => 'width: 100%;'
                        ]) ?>
                        <span id="recive_amount_error" style="color: red; font-size: 12px;"></span>
                    </td>
                </tr>
                <tr>
                    <td><label for="description"><b>Description:</b></label></td>
                    <td>
                        <?= $this->Form->textarea('description', [
                            'rows' => 3,
                            // 'required' => true,
                            'style' => 'width: 100%;'
                        ]) ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="recive_date"><b>Received Date:</b></label></td>
                    <td>
                        <?= $this->Form->control('recive_date', [
                            'label' => false,
                            'type' => 'text',
                            'required' => true,
                            'style' => 'width: 100%;',
                            'autocomplete' => 'off',
                            'placeholder' => 'dd/mm/yyyy',
                            'class' => 'datepicker',
                            'onkeydown' => 'return false;'
                        ]) ?>

                    </td>
                </tr>

                <tr>
                    <td><label for="invoice_file"><b>File:</b></label></td>
                    <td>
                        <?= $this->Form->control('invoice_file', [
                            'type' => 'file',
                            'label' => false,
                            'accept' => '.pdf,.jpg,.jpeg,.png',
                            'style' => 'width: 100%;'
                        ]) ?>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-align: right;">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('remarkForm').style.display='none'">Cancel</button>
                    </td>
                </tr>
            </table>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>

<!-- JS Datepicker + Amount Validation -->
<script>
    $(function() {
        $(".datepicker").datepicker({
            dateFormat: "dd/mm/yy"
        });

        const maxAmount = <?= json_encode($remainingAmount) ?>;
        const amountField = document.querySelector('[name="recive_amount"]');
        const errorMessage = document.getElementById('recive_amount_error');

        amountField?.addEventListener('keyup', function() {
            let value = this.value;

            value = value.replace(/[^0-9.]/g, '');

            if ((value.match(/\./g) || []).length > 1) {
                value = value.replace(/\.(?=.*\.)/, '');
            }

            this.value = value;

            let numericValue = parseFloat(value);
            if (isNaN(numericValue)) return;

            if (numericValue < 1) {
                errorMessage.textContent = "";
                this.value = "";
            } else if (numericValue > maxAmount) {
                errorMessage.textContent = "Amount cannot be greater than: " + maxAmount;
                this.value = "";
            } else {
                errorMessage.textContent = "";
            }
        });


    });
</script>

<script>
    function openFilePopup(fileUrl) {
        const isPdf = fileUrl.toLowerCase().endsWith('.pdf');
        const popup = window.open('', '_blank', 'width=800,height=600');

        if (isPdf) {
            popup.document.write(`<iframe src="${fileUrl}" width="100%" height="100%" style="border:none;"></iframe>`);
        } else {
            popup.document.write(`<img src="${fileUrl}" style="max-width:100%;max-height:100%;" />`);
        }

        popup.document.title = "View File";
    }
</script>


<script>
$(document).ready(function () {
  $(".form-horizontal").on("submit", function () {
    const submitBtn = $(this).find(":submit");
    submitBtn.prop("disabled", true).text("Submitting...");
  });
});
</script>
