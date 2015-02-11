<?php
 
 include("dbconfig.php");
    
 if (isset($_GET["Query"])) $Query=$_GET["Query"]; else die("Need query");   
 if (isset($_GET["Page"])) $Page=$_GET["Page"]; else die("Need Page");
 if (isset($_GET["Len"])) $Len=$_GET["Len"]; else die("Need Len");     
    
    
 if ($Query == 0) // Pas de requete, rien à faire
    {
     $Source=-1;
     $Qualite=-1;
     $Debut="1900-01-01";
     $Fin="2200-12-31";
     $Requete="1";
    }
 else 
     {
         $res=$bdd->Execute("SELECT * FROM querys WHERE N=$Query");
        if (!$res) die("Select failed : SELECT * FROM queries WHERE N=$Query");
 
        $Source=$res->fields["Source"];
        $Qualite=$res->fields["Qualite"];
        $Debut=$res->fields["Debut"];
        $Fin=$res->fields["Fin"];
        $Requete=$res->fields["Requete"];
     }
 
 
 // Premier traitement, on remplace les [keyword] par le numéro des keywords
 
 preg_match_all("/\[.*?\]/",$Requete,$mcs); // voir là https://www.regex101.com/
 
 foreach ($mcs[0] as $cle)
 {
     $cle=substr($cle,1,strlen($cle)-2);
     $res=$bdd->Execute("SELECT * FROM motcles WHERE Nom='$cle'");
     if (!$res) die("Failed : SELECT * FROM motcles WHERE Nom='$cle'");
     $num=$res->fields["N"];
     $Requete=str_replace("[".$cle."]", $num, $Requete);
 }
 
 // Ensuite, on remplace "mots-clés" par la sous-requête
 // attention en dessous, encore des histoires avec les accents : é dans mysql c'est 195, dans le php c'est 233...Et en unix ça va donner quoi
 $Requete=str_replace("mots-cl".chr(233)."s", "(SELECT Motcle FROM RELMC WHERE IMAGE=N)", $Requete);
 
 // On rajoute le header
 
 
 
 // Ajout des dates
 
 $Requete=$Requete." AND Date >= '$Debut' AND Date <= '$Fin'";
 if ($Source > 0) $Requete=$Requete." AND Source=$Source";
 if ($Qualite > 0) $Requete=$Requete." AND Qualite >= $Qualite";
 
 $Requete=$Requete." ORDER BY Date,ms";
 
 // on compte
 $res=$bdd->Execute("SELECT COUNT(*) AS NN FROM Images WHERE ".$Requete);
 if (!$res) die("Failed : SELECT COUNT(*) AS NN FROM Images WHERE ".$Requete);
 $maxx=$res->fields["NN"];
 
 
 $Requete="SELECT * FROM Images WHERE ".$Requete;
 $res=$bdd->SelectLimit($Requete, $Len,($Page-1)*$Len);
 if (!$res) die("Failed : $Requete");
 
 // on commence à fabriquer l'objet JSon
 $Json="{'Count' : '$maxx',";
 $Json.="'images' : [";
 
 while (!$res->EOF)
 {
     $Json.="{";
     $Json.="'N' : '".$res->fields["N"]."' ,";
     $Json.="'Date' : '".$res->fields["Date"]."' ,";
     $Json.="'ms' : '".$res->fields["ms"]."' ,";
     $Json.="'Source' : '".$res->fields["Source"]."' ,";
     $Json.="'Qualite' : '".$res->fields["Qualite"]."' ,";
     $Json.="'Retouche' : '".$res->fields["Retouche"]."' ,";
     $Json.="'Commentaire' : '".urlencode($res->fields["Commentaire"])."' ,";
     $Json.="'Raw' : '".$res->fields["Raw"]."' ,";
     $Json.="'Focale' : '".$res->fields["Focale"]."' ,";
     $Json.="'Vitesse' : '".$res->fields["Vitesse"]."' ,";
     $Json.="'ISO' : '".$res->fields["ISO"]."' ,";
     $Json.="'Diaphragme' : '".$res->fields["Diaphragme"]."' ,";
     $Json.="'portrait' : '".$res->fields["portrait"]."' ,";
     $Json.="'paysage' : '".$res->fields["paysage"]."' ,";
     $Json.="'largeur' : '".$res->fields["largeur"]."' ,";
     $Json.="'hauteur' : '".$res->fields["hauteur"]."' ,";
     
     
     //$Json.="}<br>";
     
     $res->MoveNext();
 }
 
 $Json.="]}";
 $Json=str_replace("'","\"",$Json);
 print_r($Json);
     
    
?>