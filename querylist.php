<?php
// on boucle sur le répoertoire des querys de filtre, et on construit une page avec des ul et des li pour chargement dans l'arbre de la page index
include("dbconfig.php");

if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}

traite_dir(1);


function traite_dir($me)
{
	
	global $bdd,$unix,$windows;	
	
	echo "<ul>";
    if ($me == 1) echo '<li id="0">Toutes les photos</li>';
	$res=$bdd->Execute("SELECT * from queryfolders WHERE Parent=$me");
	if ($res)
	{
	 while (!$res->EOF)
	  {
	    $Id=$res->fields["N"];
	 	echo '<li Id="folder-'.$Id.'">'.($windows ? utf8_encode($res->fields["Nom"]) : $res->fields["Nom"]);
		traite_dir($res->fields["N"]);
		echo "</li>";
		$res->MoveNext();
	  }
	} 
	
	$res=$bdd->Execute("SELECT * from querys WHERE Parent=$me");
	if ($res)
	{
	 while (!$res->EOF)
	  {
	 	$Id=$res->fields["N"];
	 	echo "<li Id='$Id'>".($windows ? utf8_encode($res->fields["Nom"]) : $res->fields["Nom"])."</li>";
		$res->MoveNext();
	  }
	}
	echo "</ul>";
	
}
?>