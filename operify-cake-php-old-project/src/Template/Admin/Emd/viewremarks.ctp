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
<?php
$k = 1;
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

            <tr>
                <td><b>Favour:-</b>
                    <?php echo $EmdGuarantees['favour_of']; ?>
                </td>
                <td><b>Amount:-</b>
                    <?php echo number_format($EmdGuarantees['amount']); ?>
                </td>

            </tr>
        </table>
    </div>


    <p style="text-align:center;font-size:15px;"><b>Remarks</b></p>
    <div class="table-responsive" style="padding: 10px;">
        <table class="table-bordered" cellpadding="3">
            <thead>
                <tr>
                    <th width="05%">S.No.</th>
                    <th width="50%">Massage</th>
                    <th width="13%">By</th>
                    <th width="11%">Date</th>
                    <th width="21%">File</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($EmdRemarks as $Remarks) {
                ?>
                    <tr>
                        <td>
                            <?php echo $k; ?>.
                        </td>
                        <td>
                            <?php echo $Remarks['remark']; ?>
                        </td>
                        <td style="text-align:left;">
                            <?php echo $Remarks['remarked_by']; ?>
                        </td>

                        <td style="text-align:left;">
                            <?php echo date('d-m-Y', strtotime($Remarks['created'])); ?>
                        </td>
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
                    </tr>
                <?php $k++;
                } ?>
            </tbody>
        </table>
        </table>

        <div style="margin-top: 15px; text-align: center;">
            <button type="button" onclick="document.getElementById('remarkForm').style.display='block'" class="btn btn-primary">+ Add Remark</button>
        </div>

        <div id="remarkForm" style="display: none; margin-top: 15px; border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;">
            <p style="text-align:center;font-size:15px;"><b>Add New Remark</b></p>

            <?= $this->Form->create(null, ['type' => 'file','class' => 'form-horizontal']) ?>
            <table width="100%" cellpadding="5">
                <?= $this->Form->hidden('bank_guarantee_id', ['value' => $EmdGuarantees['id']]) ?>
                <tr>
                    <td width="20%"><label for="remark"><b>Message:</b></label></td>
                    <td>
                        <?= $this->Form->textarea('remark', ['rows' => 3, 'required' => true, 'style' => 'width: 100%;']) ?>
                    </td>
                </tr>
                <tr>
                    <?php $remark_by = $this->request->session()->read('Auth.User.user_name');
                    ?>
                    <td><label for="remarked_by"><b>Remark By:</b></label></td>
                    <td>
                        <?= $this->Form->control('remarked_by', ['label' => false, 'readonly', 'value' => $remark_by, 'style' => 'width: 100%;']) ?>
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
