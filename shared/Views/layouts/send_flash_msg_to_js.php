<?php

$flashMessages = getFlash() ?? [];

$flashMsg_encoded = json_encode($flashMessages, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ;


echo <<< JS
<script>



    window.__FLASH_MESSAGES__ = $flashMsg_encoded;
    
 //there is an EVENT LISTENER FOR FLASH MESSAGE & display of message
</script>
JS;

?>

