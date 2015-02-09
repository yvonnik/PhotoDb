<?php
// on boucle sur le répoertoire des querys de filtre, et on construit une page avec des ul et des li pour chargement dans l'arbre de la page index
global $Sep;

if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}
if ($windows) {$BaseFolder="Sandbox\\Filtres";$Sep="\\";}
else {$BaseFolder="../../Multimedia/Photos/PhotoDb/Filtres";$Sep="/";}

echo "<ul>";
traite_dir($BaseFolder,$Sep);
echo "</ul>";

function traite_dir($dir,$Sep)
{
	
		
	$fichiers=scandir($dir);
	
	//d'abord les répoertoires
	foreach ($fichiers as $f)
	{
		if (($f == ".") || ($f == ".."))	
		 {}
		else if (is_dir($dir.$Sep.$f))
		{
			echo utf8_encode("<li>$f");
			echo "<ul>";
			traite_dir($dir.$Sep.$f,$Sep);
			echo "</ul>";
			echo "</li>";
		}	
	}

// Puis les fichiers normaux
	
	foreach ($fichiers as $f)
	{
		if (($f == ".") || ($f == ".."))	
		 {}
		else if (!is_dir($dir.$Sep.$f))
		
			{
				$Id=$dir.$Sep.$f;
				$nom=explode(".",$f);
				echo utf8_encode("<li Id='$Id'>$nom[0]</li>");
			}
		
	}
	
}
?>