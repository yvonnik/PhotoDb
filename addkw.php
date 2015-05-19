<?php 
 
 include("dbconfig.php");
 
 if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;} 
    
 if (isset($_GET["Parent"])) $Parent=$_GET["Parent"]; else die("Need Parent"); 
 if (isset($_GET["Cle"])) $Cle=$_GET["Cle"]; else die("Need Cle"); 
   
   
 
$res=$bdd->Execute("INSERT INTO motcles (Nom,Parent) VALUES ('$Cle',$Parent)");

?>