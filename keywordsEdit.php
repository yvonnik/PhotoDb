<?php


//$IMAGE_SERVER="https://192.168.2.11:8081/PhotoDb/";
$IMAGE_SERVER="http://localhost/PhotoDb/";
include("dbconfig.php");

if (isset($_GET["Admin"])) $Admin=1; else $Admin=0;


$smarty->Assign("IMAGESERVER",$IMAGE_SERVER);
$smarty->Assign("ADMIN",$Admin);
$smarty->Display("keywordsEdit.tpl.php");
 
?>