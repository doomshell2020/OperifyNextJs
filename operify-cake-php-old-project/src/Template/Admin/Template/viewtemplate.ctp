<style type="text/css">
    .modal-header .close {
        margin-top: -33px; 
    }

    .modal-content {
        width: 131%; 
    }
</style>

<?php

$popupdat = str_replace('{logo_url}', LOGO_URL, $popupdata['body']);


echo $popupdat;
?>
