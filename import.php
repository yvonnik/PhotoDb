<?php
 
 include("dbconfig.php");
 
 if (isset($_GET["Exif"])) $Exif=rawurldecode($_GET["Exif"]); else $Exif=0;// num�ro de la photo
 
 
 if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}
 
 if ($windows) {
     $BaseFolder="c:\\temp\\PhotoDb\\Images";$Sep="\\";
     $ImportFolder="c:\\temp\\PhotoDb\\ToImport";
 }
else {
    $BaseFolder="../../Multimedia/Photos/PhotoDb/Images";$Sep="/";
    $ImportFolder="../../Multimedia/Photos/PhotoDb/Images";
 }
 
  
  // construction de la liste des fichiers a importer : *.jpg sauf les *_dxo.jpg  
 $fichiers = scandir($ImportFolder);
 $liste = array();
 foreach ($fichiers as $f) {
     if (($f == ".") || ($f == "..")) {} 
     else if (is_dir($ImportFolder . $Sep . $f)) {} 
     else {
         
         $nom = explode(".", $f);
         $base = $nom[0];
         $ext = $nom[count($nom)-1];
         $isdxo = (strtoupper(substr($base, -4)) == "_DXO" ? TRUE : FALSE);
         if ((strtoupper($ext) == "JPG") && !$isdxo) $liste[] = $base;
     }
 }


// récupération des infos Exif
$todelete=array();

print("<b>Import done :</b><br><br>");
print("<table Id='log-table'>");

foreach ($liste as $base) {
    $exif=exif_read_data($ImportFolder.$Sep.$base.".jpg");
 
    if ($Exif != 0) {
        print "<br>Base : $base<br>";
        foreach ($exif as $key => $value) {
            if (!is_array($value)) print("$key:$value<br>"); else print_r($value);
        }   
    }
    
    // récupération de la date
    if (array_key_exists("DateTimeOriginal", $exif)) $date=$exif["DateTimeOriginal"];
    else if (array_key_exists("DateTimeDigitized")) $date=$exif["DateTimeDigitized"];
    else if (array_key_exists("FileDateTime", $exif)) $date=date("c",$exif["FileDateTime"]);
    else $date=filectime($$ImportFolder.$Sep.$base.".jpg");
    
    // Base de copie
    $filebase=$BaseFolder. $Sep . substr($date,0,4) . $Sep . substr($date,5,2) . $Sep . substr($date,8,2).$Sep;
    
    // Gestion de la source 
    $nom_source="unkown";
    $nsource=0;
    if (array_key_exists("Model",$exif)) {
        $nom_source=$exif["Model"];
        $sql="SELECT * FROM sources WHERE Nom='$nom_source'";
        $res=$bdd->Execute($sql);
        if (!$res) die("Failed : $sql");
        $nsource=-1;
        while (!$res->EOF) {$nsource=$res->fields["N"];$res->MoveNext();}
        if ($nsource == -1) { // La source n'existe pas encore
            $sql="INSERT INTO sources (Nom) VALUES ('$nom_source')";
            $res=$bdd->Execute($sql);
            if (!$res) die("Failed : $sql");
            $nsource=$bdd->Insert_ID();
        }
    }
    
    // Traitement des infos exifs
    
    if (array_key_exists("SubSecTimeDigitized",$exif)) $ms=$exif["SubSecTimeDigitized"];
    else if (array_key_exists("SubSecTimeOriginal",$exif)) $ms=$exif["SubSecTimeOriginal"];
    else if (array_key_exists("SubSecTime",$exif)) $ms=$exif["SubSecTimeOriginal"];
    else $ms=0;
    
    if (array_key_exists("FocalLength",$exif)) {$f=explode("/",$exif["FocalLength"]);$Focale=$f[0]/$f[1];}
    else $Focale=0;
    
    if (array_key_exists("ExposureTime",$exif)) {
        $f=explode("/",$exif["ExposureTime"]);
        $Vitesse=$f[1]/$f[0];
    }
    else $Vitesse=0;
    
    if (array_key_exists("ISOSpeedRatings",$exif)) $ISO=$exif["ISOSpeedRatings"];
    else $ISO=0;
    
    if (array_key_exists("FNumber",$exif)) {$f=explode("/",$exif["FNumber"]);$Diaphragme=$f[0]/$f[1];}
    else $Diaphragme=0;
    
    if (array_key_exists("Flash",$exif)) $Flash=$exif["Flash"];
    else $Flash=0;
    
    if (array_key_exists("COMPUTED",$exif)) $largeur=$exif["COMPUTED"]["Width"];
    else if (array_key_exists("ExifImageWidth",$exif)) $largeur=$exif["ExifImageWidth"];
    else $largeur=0;
    
    if (array_key_exists("COMPUTED",$exif)) $hauteur=$exif["COMPUTED"]["Height"];
    else if (array_key_exists("ExifImageLength",$exif)) $hauteur=$exif["ExifImageLength"];
    else $hauteur=0;
    
    if ($largeur >= $hauteur) {$portrait=0;$paysage=1;}
    else {$portrait=1;$paysage=0;}
    
    
    
       // Insertion de la ligne d'image et récupération du numéro
    $sql="INSERT INTO images (Date,Source,ms,Focale,Vitesse,ISO,Diaphragme,Flash,portrait,paysage,largeur,hauteur) VALUES ('$date',$nsource,$ms,$Focale,$Vitesse,$ISO,$Diaphragme,$Flash,$portrait,$paysage,$largeur,$hauteur)";
    
    $res=$bdd->Execute($sql);
    if (!$res) die("Query failed : $sql");
    $N=$bdd->Insert_ID();
    
    // copie des fichiers et chargement de la liste des fichiers a supprimer
     $annee=substr($date,0,4);
     $mois=substr($date,5,2);
     $jour=substr($date,8,2).$Sep;
     
     if (!is_dir($BaseFolder.$Sep.$annee)) mkdir($BaseFolder.$Sep.$annee);
     if (!is_dir($BaseFolder.$Sep.$annee.$Sep.$mois)) mkdir($BaseFolder.$Sep.$annee.$Sep.$mois);
     if (!is_dir($BaseFolder.$Sep.$annee.$Sep.$mois.$Sep.$jour)) mkdir($BaseFolder.$Sep.$annee.$Sep.$mois.$Sep.$jour);
     
     $basename=sprintf("im%06d",$N);
     $raw=0;
     $retouche=0;
     
     $raw=MyCopy(".nef", TRUE);
     
     MyCopy(".jpg", ($raw != 0 ? FALSE : TRUE); // Si raw, le .jpg n'est pas en readonly, sinon c'est la référence
     
     $retouche=MyCopy("_dxo.jpg", FALSE);
     MyCopy(".jpg.dop", FALSE);
     MyCopy(".nef.dop", FALSE);
           
     $sql="UPDATE images SET raw=$raw,retouche=$retouche WHERE N=$N";
     $res=$bdd->Execute($sql);
     if (!$res) die("Query failed : $sql");
     
    
    print("<tr><td>$N</td><td>$base</td><td>$nom_source</td><td>$date</td></tr>");  
       
}

print("</table>");


 // Déplacement des fichiers dans le répertoire "trash" du répertoire d'import
foreach ($todelete as $value)  {
    $file=explode($Sep,$value);$file=$file[count($file)-1];
    $dest=$ImportFolder.$Sep."Trash";
    if (!is_dir($dest)) mkdir($dest);
    $dest.=$Sep.$file;
    if (!rename($value,$dest)) print("Rename $value failed<br>");
    
}   


function MyCopy($ext,$readonly) {
    global $todelete,$base,$basename,$ImportFolder,$filebase,$unix,$Sep;
    $f="";
    if (file_exists($ImportFolder.$Sep.$base.strtoupper($ext))) $f=$ImportFolder.$Sep.$base.strtoupper($ext);
    if (file_exists($ImportFolder.$Sep.$base.$ext)) $f=$ImportFolder.$Sep.$base.$ext;
    if ($f == "") return 0; // pas de fichier, rien à faire
    if (copy($f,$filebase.$basename.$ext) == FALSE) {print("copy failed for $f<br>");return 0;}
    $todelete[]=$f;
    if ($readonly && $unix) chmod($filebase.$basename.$ext,0444); // passage du fichier en readonly sous Unix
    return 1;
}
?>


