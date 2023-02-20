<?php
 
 include("dbconfig.php");
 
 date_default_timezone_set("Europe/Paris");
 
 if ($windows) {
     $BaseFolder="\\\\192.168.2.11\\Multimedia\\Photos\\PhotoDb\\Images";$Sep="\\";
     $ExportFolder="c:\\temp\\PhotoDb\\Export";
 }
else {
    $BaseFolder="/mnt/data/shares/Multimedia/Photos/PhotoDb/Images";$Sep="/";
    $ExportFolder="/mnt/data/shares/Multimedia/Photos/PhotoDb/Export";
 }

 if (isset($_POST["Allfiles"])) $Allfiles=$_POST["Allfiles"]; else die("Need Allfiles");
 if (isset($_POST["Selected"])) $Selected=$_POST["Selected"]; else die("Need Selected array");
 
 
 
 $Selected=str_replace("\\","",$Selected); 

 $s=json_decode($Selected, TRUE);
 if ($s == NULL) print("json_decode returned NULL");
 
 $filecopied=0;
 foreach ($s as $key => $value)
 {
     if ($value == "1")
     {
        $res=$bdd->Execute("SELECT * from images WHERE N=$key");
        if (!$res) die("Query failed : SELECT * from images WHERE N=$key");
        if ($res->EOF) die("no record");
        $Date=$res->fields["Date"];
        $ext=$res->fields["extension"];
        $Type=$res->fields["Type"];
        
        $ImageFolder=$BaseFolder. $Sep . substr($Date,0,4) . $Sep . substr($Date,5,2) . $Sep . substr($Date,8,2);

        $filebase="im".sprintf("%06d",$key);

        if ($Allfiles) {
            $source=$ImageFolder.$Sep.$filebase."*";

        }
        else {
            if (file_exists($ImageFolder.$Sep.$filebase."r.jpg")) $file=$filebase."r.jpg";
            else if (file_exists($ImageFolder.$Sep.$filebase."_dxo.jpg")) $file=$filebase."_dxo.jpg";
            else if (strtoupper($Type) == "VIDEO") $file=$filebase.".".$ext;
            else $file=$filebase.".jpg";
            $source=$ImageFolder.$Sep.$file;
        }

        $dest=$ExportFolder;

         //print("copy $source to $dest<br>");
        //if (copy($source,$dest) == FALSE) print("copy failed for $source<br>");
        //else {
        print(exec("cp -p $source $dest"));flush();
        $filecopied++;
        //touch($dest,strtotime($Date));
        //}
     }
 }
 
 print("$filecopied files copied in Multimedia/Photos/PhotoDb/Export<br>");
 
?>