<?php
 
 include("dbconfig.php");
 global $bdd;

 $Len=100;
 $Keywords=0;
 $Position=0;
 $LocalQuery="";
 $Query=0;
 $Nom="Toutes les photos";
 $Source=-1;
 $Qualite=-1;
 $Debut="1900-01-01";
 $Fin="2200-12-31";
 $Requete="1";
 if (isset($_GET["Query"])) $Query=$_GET["Query"];    
 if (isset($_GET["Position"])) $Position=$_GET["Position"]; 
 if (isset($_GET["Len"])) $Len=$_GET["Len"]; 
 if (isset($_GET["Keywords"])) $Keywords=$_GET["Keywords"]; 
 if (isset($_GET["LocalQuery"])) $LocalQuery=$_GET["LocalQuery"];  
 
 if (isset($_POST["Query"])) $Query=$_POST["Query"];    
 if (isset($_POST["Position"])) $Position=$_POST["Position"]; 
 if (isset($_POST["Len"])) $Len=$_POST["Len"]; 
 if (isset($_POST["Keywords"])) $Keywords=$_POST["Keywords"]; 
 if (isset($_POST["LocalQuery"])) $LocalQuery=$_POST["LocalQuery"];     
 
 $LocalQuery=stripslashes($LocalQuery);  
 $LocalQuery=utf8_decode($LocalQuery);  
 if ($Query == 0) // Pas de requete, rien à faire
    {
     $Nom="Toutes les photos";
     $Source=-1;
     $Qualite=-1;
     $Debut="1900-01-01";
     $Fin="2200-12-31";
     $Requete="1";
    }
 else if ($Query == -2) // Il faut utiliser la LocalQuery
    {
     $Nom="Local";
     $Requete=$LocalQuery;
    }
 else
     {
        $res=$bdd->Execute("SELECT * FROM querys WHERE N=$Query");
        if (!$res) die("Select failed : SELECT * FROM queries WHERE N=$Query");
        $Nom=utf8_encode($res->fields["Nom"]);
        $Source=$res->fields["Source"];
        $Qualite=$res->fields["Qualite"];
        $Debut=$res->fields["Debut"];
        $Fin=$res->fields["Fin"];
        $Requete= $res->fields["Requete"];
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
 

 
 
 // on compte
 $res=$bdd->Execute("SELECT COUNT(*) AS NN FROM images WHERE ".$Requete);
 if (!$res) {
     //for ($i=0; $i < strlen($Requete);$i++) echo "<br>".substr($Requete,$i,1)."-".ord(substr($Requete,$i,1));
     $Err=str_replace("'","",$bdd->ErrorMsg());
     $Json="{'Count' : '0','Name' : 'Erreur (Count) $Err', 'images':[]}";
     $Json=str_replace("'","\"",$Json);
     die($Json);
 }
 $maxx=$res->fields["NN"];
 
 
 $Requete="SELECT * FROM images WHERE ".$Requete;
 $res=$bdd->SelectLimit($Requete, $Len,$Position);
 if (!$res) {
     $Err=str_replace("'","",$bdd->ErrorMsg());
     $Json="{'Count' : '0','Name' : 'Erreur (Count) $Err', 'images':[]}";
     $Json=str_replace("'","\"",$Json);
     die($Json);
 }
  
 // on commence à fabriquer l'objet JSon
 $Json="{'Count' : '$maxx',";
 $Json.="'Name' : '$Nom',";
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
     $Json.="'Type' : '".$res->fields["Type"]."' ,";
     if ($Keywords == 1) // on rajoute les motclés
     {
        $mc="";
        $r="SELECT motcles.Nom FROM `relmc` INNER JOIN motcles ON relmc.motcle = motcles.N WHERE Image=".$res->fields["N"];
        $res2=$bdd->Execute($r);
        while (!$res2->EOF)
        {
            if ($mc != "") $mc.=",";
            $mc.=utf8_encode($res2->fields["Nom"]);
            $res2->MoveNext();
        } 
       $Json.="'keywords' : '".$mc."',";
      
     }
     $Json.="'largeur' : '".$res->fields["largeur"]."' ,";
     $Json.="'hauteur' : '".$res->fields["hauteur"]."'";
     
     $Json.="},";
     
     
     
     //$Json.="}<br>";
     
     $res->MoveNext();
 }
 
 $Json=substr($Json,0,strlen($Json)-1); // on enlève la dernière virgule
 $Json.="]}";
 $Json=str_replace("'","\"",$Json);
 print_r($Json);
     
     
 function mydie($s)
 {
  $debug=fopen("debug.log","a");
  fwrite($debug,$s."\r\n");
  fclose($debug);
  die($s);
 }
    
?>