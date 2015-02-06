<?php

if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}
if ($windows) {$BaseFolder="\\\\192.168.2.11\\Multimedia\\Photos\\PhotoDb\\Images";$Sep="\\";}
else {$BaseFolder="../../Multimedia/Photos/PhotoDb/Images";$Sep="/";}
if (isset($_GET["Id"])) $Id=rawurldecode($_GET["Id"]); // num�ro de la photo
$small=0;
if (isset($_GET["small"])) $small=rawurldecode($_GET["small"]); //0 full size, <> 0 small
if (isset($_GET["Date"])) $Date=rawurldecode($_GET["Date"]); // Date de la photo
$file=$BaseFolder. $Sep . substr($Date,0,4) . $Sep . substr($Date,5,2) . $Sep . substr($Date,8,2);
if ($small) 
 {
  $file=$file.$Sep."smalls".$Sep."sim".sprintf("%06d.jpg",$Id);
 }
else
 $file=$file.$Sep."im".sprintf("%06d.jpg",$Id);
 if (file_exists($file))
{
    $size = getimagesize($file);

    $fp = fopen($file, 'rb');

    if ($size and $fp)
    {
        header('Content-Type: '.$size['mime']);
        thru($fp);
        exit;
    }
   else
    {
	 echo "cannot open file $file";
	}
}
else
 {
  echo "file $file does not exists";
 }
?>