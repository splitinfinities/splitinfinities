<?php
if (array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'] == 'true') include ('../kit/section/_resume.kit');
else header( 'Location: http://'.$_SERVER['HTTP_HOST'].'?trigger=resume' ) ;
?>
