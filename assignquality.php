<?php
 
 include("dbconfig.php");
 
 if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}
    
 if (isset($_POST["Quality"])) $Quality=$_POST["Quality"]; else die("Need Quality");   
 if (isset($_POST["Selected"])) $Selected=$_POST["Selected"]; else die("Need Selected array");
 
 
 
 $Selected=str_replace("\\","",$Selected); 

 $s=json_decode($Selected, TRUE);
 if ($s == NULL) print("json_decode returned NULL");
 
 foreach ($s as $key => $value)
 {
     if ($value == "1")
     {
         $sql="UPDATE images SET Qualite=$Quality WHERE N=$key";
         $res=$bdd->Execute($sql);
     }
 }
 
?>