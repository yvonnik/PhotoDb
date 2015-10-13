<?php
 include("dbconfig.php");
 
 $res=$bdd->Execute("SELECT max(UPDATE_TIME) AS LASTMODIFIED FROM information_schema.tables WHERE TABLE_SCHEMA = 'PhotoDb' ");
 if (!$res) die("Select failed : SELECT max(UPDATE_TIME) AS LASTMODIFIED FROM information_schema.tables WHERE TABLE_SCHEMA = 'PhotoDb'");
 $lm=$res->fields["LASTMODIFIED"];
 echo strtotime($lm);
?>
