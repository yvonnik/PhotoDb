<?php

include("dbconfig.php");


if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}
if ($windows) {$BaseFolder="\\\\192.168.2.11\\Multimedia\\Photos\\PhotoDb\\Images";$Sep="\\";}
//if ($windows) {$BaseFolder="c:\\temp\\PhotoDb\\Images";$Sep="\\";}
else {$BaseFolder="../../Multimedia/Photos/PhotoDb/Images";$Sep="/";}


if (isset($_GET["Id"])) $Id=rawurldecode($_GET["Id"]); // num�ro de la photo
$small=0;
if (isset($_GET["small"])) $small=rawurldecode($_GET["small"]); //0 full size, 1 small, 2 retaillé

if (isset($_GET["mh"])) $mh=rawurldecode($_GET["mh"]);
if (isset($_GET["mw"])) $mw=rawurldecode($_GET["mw"]);

$res=$bdd->Execute("SELECT * from images WHERE N=$Id");
if (!$res) die("Query failed : SELECT * from images WHERE N=$Id");
if ($res->EOF) die("no record");
$Date=$res->fields["Date"];

$BaseFolder=$BaseFolder. $Sep . substr($Date,0,4) . $Sep . substr($Date,5,2) . $Sep . substr($Date,8,2);

$filebase=$BaseFolder.$Sep."im".sprintf("%06d",$Id);
if (file_exists($filebase."r.jpg")) $file=$filebase."r.jpg";
else if (file_exists($filebase."_dxo.jpg")) $file=$filebase."_dxo.jpg";
else $file=$filebase.".jpg"; 

if ($small == 1) 
 {
  $filesmall=$BaseFolder.$Sep."smalls".$Sep."sim".sprintf("%06d.jpg",$Id);
  if (!file_exists($filesmall)) update_small($file,$filesmall);
  if (filemtime($file) > filemtime($filesmall)) update_small($file,$filesmall); // Le fichier de base a été modifié après le small, il faut reconstuire le small
  $file=$filesmall;
 }

 if (file_exists($file))
{
    $size = getimagesize($file);
    $fp = fopen($file, 'rb');
    if ($size and $fp)
    {
        if ($small != 2) { // Pas de redimensionnement, small ou large
            header('Content-Type: '.$size['mime']);
            header('cache:private, max-age=10000');
            fpassthru($fp);
            exit;
        }
        else {
            //header('Content-Type: '.$size['mime']);
            header('cache:private, max-age=10000');
            resize_image($file,$mh,$mw);
            exit;
        }
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
 
 function resize_image($big,$mh,$mw) {
    list($width, $height) = getimagesize($big);
    
    $ratio=$width/$height;
    $baseratio=$mw/$mh;// l'image doit rentre dans une boite de 266x180, ratio=1.477777
    
    if ($ratio > $baseratio) { $neww=$w;$newh=$neww/$ratio;} // image étirée en largeur, la référence est la largeur de 266
    else {$newh=$mh;$neww=$ratio*$newh;}  // image étirée en hauteur, la référence est la hauteur de 180 
   
// Redimensionnement
    $image_p = imagecreatetruecolor($neww, $newh);
    $image = imagecreatefromjpeg($big);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $neww, $newh, $width, $height);
    imagejpeg($image_p, $small);
} 
 
function update_small($big,$small) {
    list($width, $height) = getimagesize($big);
    
    $ratio=$width/$height;
    $baseratio=266/180;// l'image doit rentre dans une boite de 266x180, ratio=1.477777
    
    if ($ratio > $baseratio) { $neww=266;$newh=$neww/$ratio;} // image étirée en largeur, la référence est la largeur de 266
    else {$newh=180;$neww=$ratio*$newh;}  // image étirée en hauteur, la référence est la hauteur de 180 
   
// Redimensionnement
    $image_p = imagecreatetruecolor($neww, $newh);
    $image = imagecreatefromjpeg($big);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $neww, $newh, $width, $height);

    imagejpeg($image_p, $small);
} 
?>