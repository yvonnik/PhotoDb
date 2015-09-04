<?php 
// on boucle sur le répoertoire des motclés, et on construit une liste récursive des mot-clés
include("dbconfig.php");


if (isset($_GET["Parent"])) $Parent=$_GET["Parent"]; else $Parent=-1;  

traite($Parent);


function traite($me)
{
	
	global $bdd,$unix,$windows;	
	
	echo "<ul>";
    
	$res=$bdd->Execute("SELECT * from motcles WHERE Parent=$me ORDER BY Nom");
	if ($res)
	{
	 while (!$res->EOF)
	  {
	    $Id=$res->fields["N"];
	 	echo '<li Id="'.$Id.'">'.utf8_encode($res->fields["Nom"]) ;
		traite($res->fields["N"]);
		echo "</li>";
		$res->MoveNext();
	  }
	} 
		
    echo "</ul>";
	
}
?>