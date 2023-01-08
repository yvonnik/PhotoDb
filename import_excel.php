<?php

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=file.csv");

include("dbconfig.php");
global $bdd;

$Keywords=1;
$LocalQuery="";
$Query=0;
$Requete="1";
if (isset($_GET["Query"])) $Query=$_GET["Query"];
if (isset($_GET["LocalQuery"])) $LocalQuery=$_GET["LocalQuery"];

if (isset($_POST["Query"])) $Query=$_POST["Query"];
if (isset($_POST["LocalQuery"])) $LocalQuery=$_POST["LocalQuery"];

$LocalQuery=stripslashes($LocalQuery);
if (!$unix) $LocalQuery=utf8_decode($LocalQuery);
if ($Query != 0) // si Pas de requete, rien à faire
{
    if ($Query == -2) // Il faut utiliser la LocalQuery
    {
        $Nom = "Local";
        $Requete = $LocalQuery;
    } else {
        $res = $bdd->Execute("SELECT * FROM querys WHERE N=$Query");
        if (!$res) die("Select failed : SELECT * FROM queries WHERE N=$Query");
        if (!$unix) $Nom = utf8_encode($res->fields["Nom"]);
        else $Nom = $res->fields["Nom"];
        $Source = $res->fields["Source"];
        $Qualite = $res->fields["Qualite"];
        $Debut = $res->fields["Debut"];
        $Fin = $res->fields["Fin"];
        $Requete = $res->fields["Requete"];
    }
}


// Premier traitement, on remplace les [keyword] par le numéro des keywords

preg_match_all("/\[.*?\]/",$Requete,$mcs); // voir là https://www.regex101.com/


foreach ($mcs[0] as $cle)
{
    $cle=substr($cle,1,strlen($cle)-2);$cle8=$cle;
    //if ($unix) $cle8=utf8_encode($cle);
    $res=$bdd->Execute("SELECT * FROM motcles WHERE Nom='$cle8'");
    if (!$res) mydie("Failed : SELECT * FROM motcles WHERE Nom='$cle8'");
    if ($res->EOF) mydie("no records : SELECT * FROM motcles WHERE Nom='$cle8'");
    $num=$res->fields["N"];
    $Requete=str_replace("[".$cle."]", $num, $Requete);
}


// Ensuite, on remplace "mots-clés" par la sous-requête
// attention en dessous, encore des histoires avec les accents : é dans mysql c'est 195, dans le php c'est 233...Et en unix ça va donner quoi

$Requete=str_replace("mots-cl".chr(233)."s", "(SELECT Motcle FROM relmc WHERE Image=N)", $Requete);
$Requete=str_replace("mots-cles", "(SELECT Motcle FROM relmc WHERE Image=N)", $Requete);
$Requete=str_replace("Qualit".chr(233), "Qualite", $Requete);
$Requete=str_replace("Qualit".chr(195).chr(169), "Qualite", $Requete);
// On rajoute le header



// Ajout des dates
if ($Query > 0) {
    $Requete=$Requete." AND Date >= '$Debut' AND Date <= '$Fin'";
    if ($Source > 0) $Requete=$Requete." AND Source=$Source";
    if ($Qualite > 0) $Requete=$Requete." AND Qualite >= $Qualite";
}

$Requete=$Requete." ORDER BY Date,ms";


//Todo : charger les sources dans un tableau est faire le lookup en mémoire pour éiter le JOIN
$res4=$bdd->Execute("SELECT * FROM sources");
if (!$res4) die("Erreur de requête : SELECT * FROM sources");

while (!$res4->EOF) {
 $array_sources[$res4->fields['N']] = $res4->fields['Nom'];
 $res4->MoveNext();
}

$Requete="SELECT * FROM `images` WHERE ".$Requete;
$res=$bdd->Execute($Requete);
if (!$res) die("Erreur de requête : $Requete");

// Ligne de titre
print("N;Date;ms;Source;Source Texte;Qualite;Retouche;Commentaire;Raw;Focale;Vitesse;ISO;Diaphragme;Portrait;Paysage;Type;Mots-cles;largeur;hauteur;InsertDate\n");

    // construction du tableau
while (!$res->EOF)
{

    $ligne=$res->fields["N"];
    $ligne.=";".$res->fields["Date"];
    $ligne.=";".$res->fields["ms"];
    $ligne.=";".$res->fields["Source"];
    $ligne.=";".$array_sources[$res->fields["Source"]];
    $ligne.=";".$res->fields["Qualite"];
    $ligne.=";".$res->fields["Retouche"];
    $ligne.=";".urlencode($res->fields["Commentaire"]);
    $ligne.=";".$res->fields["Raw"];
    $ligne.=";".$res->fields["Focale"];
    $ligne.=";".$res->fields["Vitesse"];
    $ligne.=";".$res->fields["ISO"];
    $ligne.=";".$res->fields["Diaphragme"];
    $ligne.=";".$res->fields["portrait"];
    $ligne.=";".$res->fields["paysage"];
    $ligne.=";".$res->fields["Type"];


    $mc="";
    $r="SELECT motcles.Nom FROM `relmc` INNER JOIN motcles ON relmc.motcle = motcles.N WHERE Image=".$res->fields["N"];
    $res2=$bdd->Execute($r);
    while (!$res2->EOF)
     {
         if ($mc != "") $mc.=",";
         $mc.=$res2->fields["Nom"];
         $res2->MoveNext();
     }
    $ligne.=";".$mc;

    $ligne.=";".$res->fields["largeur"];
    $ligne.=";".$res->fields["hauteur"];
    $ligne.=";".$res->fields["InsertDate"];

    print("$ligne\n");
    $res->MoveNext();
}




