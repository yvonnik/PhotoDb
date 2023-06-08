<?php 
 
 include("dbconfig.php");
 
   
 if (isset($_GET["Parent"])) $Parent=$_GET["Parent"]; else die("Need Parent"); 
 if (isset($_GET["Cle"])) $Cle=$_GET["Cle"]; else die("Need Cle");

 $Cle=urldecode($Cle);
 $liste=explode(",",$Cle);
 foreach ($liste as $C) {
     $C=trim($C," ");
     $res=$bdd->Execute("INSERT INTO motcles (Nom,Parent) VALUES ('$C',$Parent)");
     echo $C."<br>";
}



?>