<?php
 
 include("dbconfig.php");
 
 if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;} 
    
 if (isset($_GET["q"])) $q=$_GET["q"]; else die("Need query");   
  
    
    
 
$res=$bdd->Execute("SELECT * FROM motcles WHERE Nom LIKE '%$q%'");
if (!$res) die("Select failed : SELECT * FROM queries WHERE N=$Query");

$Json="[";

while (!$res->EOF)
 {
     $Json.="{";
     $Json.="'id' : '".$res->fields["N"]."' ,";
     $Json.="'value' : '".($windows ? utf8_encode($res->fields["Nom"]) : $res->fields["Nom"])."'";
     
     $Json.="},";
        
     $res->MoveNext();
 }
 
 $Json=substr($Json,0,strlen($Json)-1); // on enlève la dernière virgule
 $Json.="]";
 $Json=str_replace("'","\"",$Json);
 print_r($Json);
     
    
?>