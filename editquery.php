<script src="Tree/TreeMenu.js" language="JavaScript" type="text/javascript"></script>
<link href="Tree/TreeMenu.css" rel="stylesheet" type="text/css">
<link href="style.css" rel="stylesheet" type="text/css">

<div align="center"><strong>Mot-clés</strong></div> 

<?php

include("dbconfig.php");
include_once("Tree/TreeMenuXL.php"); 

/* lecture du tableau des mots clés */


$menu00  = new HTML_TreeMenuXL(); 
$nodeProperties = array("icon"=>"folder.gif"); 
$n=0;

function FillNode(&$bdd,&$node,$parentid,$nodeProperties,&$n) /* fonction qui crée les enfants de $node, ceux qui ont $parentid comme parent */
{
	$rs=$bdd->Execute("SELECT * FROM Motclés WHERE Parent=$parentid ORDER BY Nom");
	while (!$rs->EOF)
	{
	 $nn0="node".$n;$n++;
	 $$nn0 = new HTML_TreeNodeXL($rs->fields["Nom"], "", $nodeProperties); 
	 FillNode($bdd,$$nn0,$rs->fields["N"],$nodeProperties,$n);
	 $node->AddItem($$nn0,"",$nodeProperties);
	 $rs->MoveNext();
	}
}

FillNode($bdd,$menu00,-1,$nodeProperties,$n);

$example010 = &new HTML_TreeMenu_DHTMLXL($menu00, array("images"=>"Tree/TMimages", "linkTarget" => "mainFrame"));
print "<br>"; 
$example010->printMenu(); 


?>