<?php
if ($_SERVER['HTTP_X_PJAX'] == 'true') include ('kit/section/_resume.kit');
else header( 'Location: http://'.$_SERVER['HTTP_HOST'].'?trigger=resume' ) ;
?>
