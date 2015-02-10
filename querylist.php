<?php
// on boucle sur le répoertoire des querys de filtre, et on construit une page avec des ul et des li pour chargement dans l'arbre de la page index
include("dbconfig.php");

traite_dir(1);


function traite_dir($me)
{
	
	global $bdd;	
	
	echo "<ul>";
	$res=$bdd->Execute("SELECT * from queryfolders WHERE Parent=$me");
	if ($res)
	{
	 while (!$res->EOF)
	  {
	 	echo "<li>".$res->fields["Nom"];
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
	 	echo "<li Id='$Id'>".$res->fields["Nom"]."</li>";
		$res->MoveNext();
	  }
	}
	echo "</ul>";
	
}
?>