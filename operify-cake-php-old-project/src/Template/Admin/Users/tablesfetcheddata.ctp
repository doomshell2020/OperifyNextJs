<?php //pr($tables); ?>

<?php foreach($tables as $value){ ?>

    <style>
        #tabledata .checkContainer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        #tabledata input {
            margin:0px !important;
            margin-right:5px !important;
        }
    </style>

    <div class="checkContainer">
<input type = "checkbox" name = "datatables[]" value = "<?php echo $value; ?>">
<label style="margin-right:15px; font-weight:400; margin-bottom:0px;"><?php echo $value; ?></label>
</div>

<?php } ?>
