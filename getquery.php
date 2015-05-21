<?php 
// on boucle sur le répoertoire des querys de filtre, et on construit une page avec des ul et des li pour chargement dans l'arbre de la page index
include("dbconfig.php");

if (stristr(php_uname(),"windows")) {$unix=0;$windows=1;} else {$unix=1;$windows=0;}

if (isset($_GET["N"])) $N=$_GET["N"];


$res=$bdd->Execute("SELECT * from querys WHERE N=$N");
if ($res)
 {
    $Nom=$res->fields["Nom"];
    $Source=$res->fields["Source"];
    $Qualite=$res->fields["Qualite"];
    $Debut=$res->fields["Debut"];
    $Fin=$res->fields["Fin"];
    $Requete=($windows ? $res->fields["Requete"] : utf8_decode($res->fields["Requete"]));
     
    str_replace("Qualité","Qualite",$Requete);
    $Requete=str_replace("mots-cl".chr(233)."s", "mots-cles", $Requete); 
    if (($Debut != "1900-01-01") && ($Debut != "") && ($Debut != "0000-00-00")) $Requete=$Requete." AND Date >= '$Debut'";
    if (($Fin != "2200-12-31") && ($Fin != "") && ($Fin != "0000-00-00")) $Requete=$Requete." AND Date <= '$Fin'";
    if ($Source > 0) $Requete=$Requete." AND Source=$Source";
    if ($Qualite > 0) $Requete=$Requete." AND Qualite >= $Qualite";
    
    $Json="{\"Name\":\"$Nom\",\"SQL\":\"$Requete\"}";
    $Json=utf8_encode($Json);
    //$Json=str_replace("'","\"",$Json);
	print($Json); 
		
 }
else {
	$Json="{'Name':'Empty','SQL':''}";
    $Json=str_replace("'","\"",$Json);
    print($Json); 
 }	
?>