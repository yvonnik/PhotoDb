<?php
// script de transfert des querys dans la base de données
// on boucle sur le répoertoire des querys de filtre, et on construit une page avec des ul et des li pour chargement dans l'arbre de la page index
include("dbconfig.php");

// Effacement des tables
$bdd->Execute("DELETE FROM queryfolders");
$bdd->Execute("DELETE FROM querys");

if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}
if ($windows) {$BaseFolder="Sandbox\\Filtres";$Sep="\\";}
else {$BaseFolder="../../Multimedia/Photos/PhotoDb/Filtres";$Sep="/";}


$index=1;


traite_dir("root",$BaseFolder,0);


function traite_dir($nom,$dir,$Parent)
	
{
	global $index, $Sep,$bdd;	
	
	$me=$index;
	$bdd->Execute("INSERT queryfolders (N,Nom,Parent) VALUES ($index,'$nom',$Parent)");
	$index++;
	$fichiers=scandir($dir);
	
	foreach ($fichiers as $f)
	{
		if (($f == ".") || ($f == ".."))	
		 {}
		else if (is_dir($dir.$Sep.$f))
		{
			$index++;
			
			traite_dir($f,$dir.$Sep.$f,$me);
			
		}	
		else 
		
			{
				
				$Id=$dir.$Sep.$f;
				$nom=explode(".",$f);$nom=$nom[0];
				
				$myfile = fopen($Id, "r") or die("Unable to open file $Id!");
				$source=intval(fgets($myfile));
				$qualite=intval(fgets($myfile));
				
				$debut=fgets($myfile);echo $nom." - ".$debut." - ".strlen($debut);
				if (strlen($debut) <= 2) {$debut="1900-01-01";}
				else 
				 {
				 	 $x=explode("/",$debut);
				 	 $d=intval($x[0]);$m=intval($x[1]);$y=intval($x[2]);
					 if ($y < 1900) $y+=1900;
					 $debut=sprintf("%4d-%02d-%02d",$y,$m,$d);
				 }
				echo $nom." - ".$debut."<br>";
				
				$fin=fgets($myfile);
				if (strlen($fin) <= 2) {$fin="2200-12-31";}
				else 
				 {
				 	 $x=explode("/",$fin);
				 	 $d=intval($x[0]);$m=intval($x[1]);$y=intval($x[2]);
					 if ($y < 1900) $y+=1900;
					 $fin=sprintf("%4d-%02d-%02d",$y,$m,$d);
				 }
				 
				$requete=fgets($myfile);
				
				fclose($myfile);
				$bdd->Execute("INSERT querys (N,Nom,Source,Qualite,Debut,Fin,Requete,Parent) VALUES ($index,'$nom',$source,$qualite,'$debut','$fin','$requete',$me)");
				$index++;
			}
	}

	
}
?>