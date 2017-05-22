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
    $ImportFolder="../../Multimedia/Photos/PhotoDb/ToImport";
 }
 
  
  // construction de la liste des fichiers a importer : *.jpg sauf les *_dxo.jpg  
  
  // On commence par forcer les noms en minuscules, sinon, on se s'y retrouve plus ensuite
  
 $fichiers = scandir($ImportFolder);
 $liste = array();
 foreach ($fichiers as $f) {
     if (($f == ".") || ($f == "..")) {} 
     else if (is_dir($ImportFolder . $Sep . $f)) {} 
     else {
           if (strtolower($f) != $f) { // le nom de fichier n'est pas en minuscule pure, on le force
              rename($ImportFolder . $Sep . $f, $ImportFolder . $Sep . strtolower($f));
           }
     }
 }
 
 
 $fichiers = scandir($ImportFolder);
 $liste = array();
 foreach ($fichiers as $f) {
     if (($f == ".") || ($f == "..")) {} 
     else if (is_dir($ImportFolder . $Sep . $f)) {} 
     else {
         
         $nom = explode(".", $f);
         $base = $nom[0];
         for ($i=1;$i <  count($nom)-1;$i++) {$base.=".".$nom[$i];}
         $ext = $nom[count($nom)-1];
         $isdxo = (substr($base, -4) == "_dxo" ? TRUE : FALSE);
         if (($ext == "jpg") && !$isdxo) $liste[] = $base;
     }
 }

// Gestion de la InsertDate
date_default_timezone_set ( "UTC" );
$InsertDate=date("c");
if (substr($InsertDate,-6,6) == "+00:00") $InsertDate=substr($InsertDate,0,strlen($InsertDate)-6)."Z";

// récupération des infos Exif
$todelete=array();

print("<b>Import done :</b><br><br>");
print("<table Id='log-table'>");

foreach ($liste as $base) {
        
    $video=0;
    if (file_exists($ImportFolder.$Sep.$base.".photodb")) { // Le fichier en extension .photodb va contenir les infos exifs, et d'autres (type, keywords)
        $exif=array();
        $handle = fopen($ImportFolder.$Sep.$base.".photodb", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if ($line == "") continue;
                if (substr($line,0,2) == "//") continue; // On saute les commentaires
                $entry=explode("=",$line);
                if (array_key_exists(1,$entry))  {
                    $entry[1]=trim($entry[1]);
                    if (($entry[1] != "") && (is_string($entry[1]))) {
                        if (strlen($entry[1]) > 0) $exif[$entry[0]]=$entry[1];
                    }
                }
            }
        // process the line read.
        }

    fclose($handle);
        
    } 
    else {
        $exif=exif_read_data($ImportFolder.$Sep.$base.".jpg");
     
    }
    
    if (!array_key_exists("Type",$exif)) $exif["Type"]="photo";
          
       
    // récupération de la date
    if (array_key_exists("DateTimeOriginal", $exif)) $date=$exif["DateTimeOriginal"];
    else if (array_key_exists("DateTimeDigitized",$exif)) $date=$exif["DateTimeDigitized"];
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
    
	if ($Focale == '') $Focale=0;
	if ($Vitesse == '') $Vitesse=0;
	if ($Diaphragme == '') $Diaphragme=0;
	if ($ISO == '') $ISO=0;
	if ($Flash == '') $Flash=0;
    $Type=$exif["Type"];
    
    
       // Insertion de la ligne d'image et récupération du numéro
    $sql="INSERT INTO images (Date,InsertDate,Type,Source,ms,Focale,Vitesse,ISO,Diaphragme,Flash,portrait,paysage,largeur,hauteur) VALUES ('$date',STR_TO_DATE('$InsertDate','%Y-%m-%dT%H:%i:%sZ'),'$Type',$nsource,$ms,$Focale,$Vitesse,$ISO,$Diaphragme,$Flash,$portrait,$paysage,$largeur,$hauteur)";
    
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
     if (!is_dir($BaseFolder.$Sep.$annee.$Sep.$mois.$Sep.$jour.$Sep."smalls")) mkdir($BaseFolder.$Sep.$annee.$Sep.$mois.$Sep.$jour.$Sep."smalls");
     
     $basename=sprintf("im%06d",$N);
     $raw=0;
     $retouche=0;
     $extension="";
     
     if (MyCopy(".nef", TRUE)) {$extension="nef";$raw=1;}
     if (MyCopy(".rw2", TRUE)) {$extension="rw2";$raw=1;}
     if (array_key_exists("RawExtension",$exif)) {$extension=ltrim($exif["RawExtension"],".");MyCopy(".".$extension,TRUE);}// copie de la video ou d'un raw special associé au JPG si existe
     MyCopy(".jpg", ($raw != 0 ? FALSE : TRUE)); // Si raw, le .jpg n'est pas en readonly, sinon c'est la référence
     $retouche=MyCopy("_dxo.jpg", FALSE);
     MyCopy(".jpg.dop", FALSE);
     MyCopy(".nef.dop", FALSE);
     MyCopy(".rw2.dop", FALSE);
     
     // copie des fichiers annexes définis dans OtherExtensions
     if (array_key_exists("OtherExtensions",$exif)) {
         $otherextensions=explode(",",$exif["OtherExtensions"]);
         foreach ($otherextensions as $ex) MyCopy(".".ltrim(trim(strtolower($ex)),"."), FALSE);
         }        
    
     $sql="UPDATE images SET raw=$raw,retouche=$retouche,extension='$extension' WHERE N=$N";
     $res=$bdd->Execute($sql);
     if (!$res) die("Query failed : $sql");
     
     print("<tr><td>$N</td><td>$base</td><td>$nom_source</td><td>$date</td></tr>");  
     
     // ajout des keywords qui pourraient être dans le fichier photodb
     if (array_key_exists("Keywords",$exif)) {
         $keywords=explode(",",$exif["Keywords"]);
         foreach ($keywords as $kw) {
            $kw=trim($kw);
            $sql="SELECT * FROM motcles WHERE Nom='$kw'";
            $res=$bdd->Execute($sql);
            if (!$res) die("Query failed : $sql");
            $kwn=-1;
            while (!($res->EOF)) {
                $kwn=$res->fields["N"];
                $res->MoveNext();
            }
            
            if ($kwn == -1) print("<tr><td>mot-clés inconnu</td><td>$kw</td><td> </td><td> </td></tr>");
            else {
                $sql="INSERT INTO relmc (Image,Motcle) VALUES ($N,$kwn)";
                $res=$bdd->Execute($sql);
                }
            } 
         }        
}

print("</table>");



function myrename($old,$new) {
    global $windows;
    if ($windows) $cmd="move /Y $old $new";
    else $cmd="mv $old $new";
    exec($cmd,$returnlines,$returnvar);
    if ((file_exists($old) == FALSE ) && (file_exists($new) == TRUE)) return TRUE; else return FALSE;
}


function MyCopy($ext,$readonly) {
    global $todelete,$base,$basename,$ImportFolder,$filebase,$unix,$Sep;
    $f="";
    
    if (file_exists($ImportFolder.$Sep.$base.$ext)) $f=$ImportFolder.$Sep.$base.$ext;
    else {return 0;}
    if (myrename($f,$filebase.$basename.$ext) == FALSE) {print("move failed for $f<br>");return 0;}
    // $todelete[]=$f;
    if ($readonly && $unix) chmod($filebase.$basename.$ext,0444); // passage du fichier en readonly sous Unix
    return 1;
}
?>


