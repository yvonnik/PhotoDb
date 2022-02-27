<?php
//TODO : exemple de To do
//$IMAGE_SERVER="/PhotoDb/"; // Machine locale au serveur Web, configuration standard en production
//$IMAGE_SERVER="http://localhost/PhotoDb/";
$IMAGE_SERVER="https://192.168.2.11/PhotoDb/"; // Configuration de debug
//$IMAGE_SERVER="http://localhost/PhotoDb/";
include("dbconfig.php");

if (isset($_GET["Admin"])) $Admin=1; else $Admin=0;


$smarty->Assign("IMAGESERVER",$IMAGE_SERVER);
$smarty->Assign("ADMIN",$Admin);
$smarty->Display("index.tpl.php");

?>