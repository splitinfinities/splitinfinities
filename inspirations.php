<?php
if ($_SERVER['HTTP_X_PJAX'] == 'true') include ('kit/section/_inspirations.kit');
else header( 'Location: http://'.$_SERVER['HTTP_HOST'].'?trigger=inspirations' ) ;
?>
