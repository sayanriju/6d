<?php
 require_once('aes128.php');
 $aes=new aes128();
 
 $key=$aes->makeKey("0123456789abcdef");
 $ct=$aes->blockEncrypt("secretpass",$key);
 
 $cpt=$aes->blockDecrypt($ct,$key);	
 echo $cpt;
 
?>