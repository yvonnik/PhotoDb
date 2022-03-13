<?php

include("dbconfig.php");

if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}
if ($windows) {$BaseFolder="c:\\wamp64\\Images";$Sep="\\";}
//if ($windows) {$BaseFolder="c:\\temp\\PhotoDb\\Images";$Sep="\\";}
else {$BaseFolder="../../Multimedia/Photos/PhotoDb/Images";$Sep="/";}


if (isset($_GET["Id"])) $Id=rawurldecode($_GET["Id"]); // num�ro de la photo
$small=0;
if (isset($_GET["small"])) $small=rawurldecode($_GET["small"]); //0 full size, 1 small, 2 retaillé

if (isset($_GET["mh"])) $mh=rawurldecode($_GET["mh"]); // Heigth du viewport
if (isset($_GET["mw"])) $mw=rawurldecode($_GET["mw"]); // Width du viewport

global $bdd;
$res=$bdd->Execute("SELECT * from images WHERE N=$Id");
if (!$res) die("Query failed : SELECT * from images WHERE N=$Id");
if ($res->EOF) die("no record");
$Date=$res->fields["Date"];

$BaseFolder=$BaseFolder. $Sep . substr($Date,0,4) . $Sep . substr($Date,5,2) . $Sep . substr($Date,8,2);

$filebase=$BaseFolder.$Sep."im".sprintf("%06d",$Id);
if (file_exists($filebase."r.jpg")) $file=$filebase."r.jpg";
else if (file_exists($filebase."_dxo.jpg")) $file=$filebase."_dxo.jpg";
else if (file_exists($filebase.".jpg")) $file=$filebase.".jpg";
else die("cannot find file for $filebase");

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
    list($width, $height) = $size;
    $fp = fopen($file, 'rb');
    if ($size and $fp)
    {
        if (($small != 2) ||  (($mh > 1440) || ($mw > 2560)) || (($height < 1440) || ($width < 2560))) { 
            // pas de redimensionnement si :
            // $small != -2
            // Le viewport est plus grand que 2560x1440
            // L'image de base est plus petite que 2560x1440
            header('Content-Type: '.$size['mime']);
            header('cache:private, max-age=10000');
            fpassthru($fp);
            exit;
        }
        else { // Diaporama sur réseau distant, on reisze les images avant de les envoyer
            // on est dans le cas où le viewport est <= à 2560x1440, et l'image est plus grande que le viewport
            // si l'image existe dans le cache 2560x1440, on la renvoie après resize
            // sinon on la créer et on la renvoie après resize
            
            $file2560=$BaseFolder.$Sep."2560x1440".$Sep."mim".sprintf("%06d.jpg",$Id);
            if (!is_dir($BaseFolder.$Sep."2560x1440")) mkdir($BaseFolder.$Sep."2560x1440", 0777, true);
            if (!file_exists($file2560)) update_2560($file,$file2560);
            if (filemtime($file) > filemtime($file2560)) update_2560($file,$file2560); // Le fichier de base a été modifié après le small, il faut reconstuire le small
            $file=$file2560;
  
            //header('Content-Type: '.$size['mime']);
            //header('cache:private, max-age=10000');
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
 
 function locallan() // retourne TRUE si le serveur et le navigateur sont sur le même sous-réseau xxx.yyy.zzz.???, FALSE sinon
 {
     global $_SERVER;
     if ($_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR']) return TRUE; //test en localhost
     return FALSE;
     //$serverip=explode(".",$_SERVER['SERVER_ADDR']);
     //$browserip=explode(".",$_SERVER['REMOTE_ADDR']);
     //if (($serverip[0] == $browserip[0]) && ($serverip[1] == $browserip[1]) && ($serverip[2] == $browserip[2])) return TRUE; else return FALSE;
     
 }
 
 function resize_image($big,$mh,$mw) {
    global $windows;
    list($width, $height) = getimagesize($big);
    
    $ratio=$width/$height;
    $baseratio=$mw/$mh;// l'image doit rentre dans une boite de 266x180, ratio=1.477777
    
    if ($ratio > $baseratio) { $neww=$mw;$newh=$neww/$ratio;} // image étirée en largeur, la référence est la largeur de 266
    else {$newh=$mh;$neww=$ratio*$newh;}  // image étirée en hauteur, la référence est la hauteur de 180 
    $newh=floor($newh+0.5);
    $neww=floor($neww+0.5);
// Redimensionnement
    if ($windows) { //Pas Imagick sous windows, on resize "à la main"
         $thumb = imagecreatetruecolor($neww, $newh);
         $source = imagecreatefromjpeg($big);
         imagecopyresized($thumb, $source, 0, 0, 0, 0, $neww, $newh, $width, $height);
         imagejpeg($thumb); 
    } else {
         $imagick=new Imagick($big);
         $imagick->resizeImage($neww, $newh, Imagick::FILTER_BOX, 1);
         echo $imagick; 
    }  
 
 
} 
 
function update_small($big,$small) {
    global $windows;

    list($width, $height) = getimagesize($big);
    
    $ratio=$width/$height;
    $baseratio=266/180;// l'image doit rentre dans une boite de 266x180, ratio=1.477777
    
    if ($ratio > $baseratio) { $neww=266;$newh=$neww/$ratio;} // image étirée en largeur, la référence est la largeur de 266
    else {$newh=180;$neww=$ratio*$newh;}  // image étirée en hauteur, la référence est la hauteur de 180 
   
// Redimensionnement

if ($windows) { //Pas Imagick sous windows, on resize "à la main"
    $thumb = imagecreatetruecolor($neww, $newh);
    $source = imagecreatefromjpeg($big);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $neww, $newh, $width, $height);
    imagejpeg($thumb,$small);
    imagedestroy($thumb);
    
 } else {
   $imagick=new Imagick($big);
   $imagick->resizeImage($neww, $newh, Imagick::FILTER_BOX, 1);
   $imagick->writeImage($small); 
 }  
} 

function update_2560($big,$small) {
    global $windows;

    list($width, $height) = getimagesize($big);
    
    $ratio=$width/$height;
    $baseratio=2560/1440;// l'image doit rentre dans une boite de 266x180, ratio=1.477777
    
    if ($ratio > $baseratio) { $neww=2560;$newh=$neww/$ratio;} // image étirée en largeur, la référence est la largeur de 266
    else {$newh=1440;$neww=$ratio*$newh;}  // image étirée en hauteur, la référence est la hauteur de 180 
   
// Redimensionnement

if ($windows) { //Pas Imagick sous windows, on resize "à la main"
    $thumb = imagecreatetruecolor($neww, $newh);
    $source = imagecreatefromjpeg($big);
    imagecopyresized($thumb, $source, 0, 0, 0, 0, $neww, $newh, $width, $height);
    imagejpeg($thumb,$small);
    imagedestroy($thumb);
    
 } else {
   $imagick=new Imagick($big);
   $imagick->resizeImage($neww, $newh, Imagick::FILTER_BOX, 1);
   $imagick->writeImage($small); 
 }  
} 
?>