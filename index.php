<?php


$IMAGE_SERVER="https://192.168.2.11:8081/PhotoDb/";
include("dbconfig.php");


$smarty->Assign("IMAGESERVER",$IMAGE_SERVER);
$smarty->Display("index.tpl.php");

?>