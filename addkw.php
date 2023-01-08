<?php 
 
 include("dbconfig.php");
 
   
 if (isset($_GET["Parent"])) $Parent=$_GET["Parent"]; else die("Need Parent"); 
 if (isset($_GET["Cle"])) $Cle=$_GET["Cle"]; else die("Need Cle"); 

 if ($unix) $Cle=urldecode($Cle);
 else $Cle=utf8_decode(urldecode($Cle));
 
$res=$bdd->Execute("INSERT INTO motcles (Nom,Parent) VALUES ('$Cle',$Parent)");

?>