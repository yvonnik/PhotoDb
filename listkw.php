<?php
 
 include("dbconfig.php");
 
 if (isset($_GET["q"])) $q=$_GET["q"]; else die("Need query");   
  
 $q=utf8_decode(urldecode($q));  
   
$res=$bdd->Execute("SELECT * FROM motcles WHERE Nom LIKE '%$q%'");
if (!$res) die("Select failed : SELECT * FROM queries WHERE N=$Query");

$Json="[";

while (!$res->EOF)
 {
     $Json.="{";
     $Json.="'id' : '".$res->fields["N"]."' ,";
     $Json.="'value' : '".utf8_encode($res->fields["Nom"])."'";
     
     $Json.="},";
        
     $res->MoveNext();
 }
 
 $Json=substr($Json,0,strlen($Json)-1); // on enlève la dernière virgule
 $Json.="]";
 $Json=str_replace("'","\"",$Json);
 print_r($Json);
     
    
?>