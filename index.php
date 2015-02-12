<?php


$nb_row=4;
$nb_col=5;

$IMAGE_SERVER="https://192.168.2.11:8081/PhotoDb/";
include("dbconfig.php");


if (isset($_GET["Page"])) $Page=$_GET["Page"]; else $Page=1;


/* lecture du tableau d'entr�e */

if (isset($HTTP_GET_VARS["Query"])) $Query=rawurldecode($HTTP_GET_VARS["Query"]);
 else $Query="1";
 

/* chargement des archives */

$rs=$bdd->Execute("SELECT * FROM Archives");
$archives=array();
while (!$rs->EOF) {$archives[$rs->fields["N"]]=$rs->fields["Nom"];$rs->MoveNext();}

/* comptage des photos */

$rs=$bdd->Execute("SELECT Count(*) AS C FROM Images WHERE ($Query)");
$nbphotos=$rs->fields["C"];
$nbpages=floor($nbphotos/$nb_col/$nb_row+0.999);


$rs=$bdd->SelectLimit("SELECT * FROM Images WHERE ($Query) ORDER BY Date DESC",$nb_row*$nb_col,($Page-1)*$nb_row*$nb_col+1);
$im=array();

$i=0;
while (!$rs->EOF) {
	$im[$i]["I"]=$i;
    $i++;
	$rs->MoveNext();
}

$smarty->Assign("IMAGESERVER",$IMAGE_SERVER);
$smarty->Assign("ROWS",$nb_row);
$smarty->Assign("COLS",$nb_col);
$smarty->Assign("IM",$im);
$smarty->Display("index.tpl.php");

?>