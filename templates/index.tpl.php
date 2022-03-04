<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<html>


<head>
<title>PhotoDb</title>

<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="jsTree/dist/themes/default/style.min.css" />
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="jQuery/jquery.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="photo_table.js"></script>
<script src="select_keywords.js"></script>

<!-- http://www.jstree.com/ -->
<script src="jsTree/dist/jstree.min.js"></script>



<script language="JavaScript" type="text/JavaScript">

var ImageServer="{$IMAGESERVER}";
var Admin={$ADMIN};
var FullScreen=false;
var Diapovar=null;

// {literal} 

function FullScreenToggle(Nimage)
{
    start_position=start_position+Nimage;
    FullScreen=!FullScreen;
    if (FullScreen) {
        $(".admin").hide();
        $("#navbutton-first").hide();
        $("#navbutton-rewind").hide();  
        $("#navbutton-forward").hide();
        $("#navbutton-last").hide();
        $("#navbutton-filter").hide();
        $("#navbutton-select").hide();
        $("#navbutton-unselect").hide();
        $("#navbutton-copy").hide();
        $('#navbutton-monomulti').css("background-image","url('web_images/multi_64.png')") ;
        $("#navbutton-diapo").show();
        $("#slidercontainer").show();
    } else {
        if (Admin) $(".admin").show();
        $("#navbutton-first").show();
        $("#navbutton-rewind").show();  
        $("#navbutton-forward").show();
        $("#navbutton-last").show();
        $("#navbutton-filter").show();
        $("#navbutton-select").show();
        $("#navbutton-unselect").show();
        $("#navbutton-copy").show();
        $('#navbutton-monomulti').css("background-image","url('web_images/mono_64.png')") ;
        $("#navbutton-diapo").hide();
        $("#slidercontainer").hide();
    }
    table_destroy();table_create();raffraichir();
}

function Diapo()

{
   if (Diapovar == null) {
       Diapovar=setInterval(function () {movetopage(-2);}, document.getElementById("myRange").value*1000);
       $('#navbutton-diapo').css("background-image","url('web_images/pause_64.png')") ;
   }
   else {
       clearInterval(Diapovar);
       Diapovar=null;
       $('#navbutton-diapo').css("background-image","url('web_images/play_64.png')") ;
   }
      
}

function AddChamp() {
    var fz=document.getElementById("Filterzone");
    var valeur=" "+document.getElementById("ListeChamps").value+" ";
    fz.insertAtCaret(valeur);
    fz.focus();
}

function AddOp() {
    var fz=document.getElementById("Filterzone");
    var valeur=" "+document.getElementById("ListeOps").value+" ";
    fz.insertAtCaret(valeur);
    fz.focus();
}

function AddKeyword() {
    var fz=document.getElementById("Filterzone");
    var valeur=" (["+document.getElementById("keywords-filter").value+"] IN mots-cles) ";
    fz.insertAtCaret(valeur);
    fz.focus();
}

HTMLTextAreaElement.prototype.insertAtCaret = function (text) {
  text = text || '';
  if (document.selection) {
    // IE
    this.focus();
    var sel = document.selection.createRange();
    sel.text = text;
  } else if (this.selectionStart || this.selectionStart === 0) {
    // Others
    var startPos = this.selectionStart;
    var endPos = this.selectionEnd;
    this.value = this.value.substring(0, startPos) +
      text +
      this.value.substring(endPos, this.value.length);
    this.selectionStart = startPos + text.length;
    this.selectionEnd = startPos + text.length;
  } else {
    this.value += text;
  }
};


// Callback de l'appel Ajax pour récupérer la liste des images


function movetopage(sens)
{
 var OldPosition=start_position;
 if (sens == -4) /* rtour au d�but */ start_position=0;
 if (sens == -3) /* moins une page */ {start_position-=Len;if (start_position < 0) start_position=0;}
 if (sens == -2) /* avant une page */ {start_position+=Len;if (start_position+Len >= Count) start_position=Count-Len;}
 if (sens == -1) /* � la fin */ start_position=Count-Len;;
 
 if (OldPosition != start_position) raffraichir();
 
}

function togglesize() {
    if ($('#resize_check').prop('checked')) $('#resize_block').show();
    else $('#resize_block').hide();
    
}

function photo_import() {
    $('#popup').show();
    $('#popup-import_interior').show();
    $('#import-log').html("");
    $.ajax({ 
    type: 'GET', 
    url: 'import.php', 
    dataType: 'html',
    success: function (data) {
        $('#import-log').html(data);
        Selected={};Query=-1;
        start_position=0;
        raffraichir();
        }
    });  
    
    
}
function photo_copy() {
    $('#popup').show();
    $('#popup-import_interior').show();
    $('#import-log').html("");
    $.ajax({ 
    type: 'POST', 
    url: 'copy.php', 
    data: { 'Resize': $('#resize_check').prop('checked'), 'MaxSize' : $('#resize_size').val(), 'Selected' : JSON.stringify(Selected)}, 
    dataType: 'html',
    success: function (data) {
        $('#import-log').html(data);
        }
    });  
    
    
}


</script>
<!-- {/literal} /-->
</head>

<!-- Div pour le fond grisé /-->

<div Id="sidepanel">
</div>

<div id="Main">
<div Id="popup"></div>

<!-- http://www.formget.com/how-to-create-pop-up-contact-form-using-javascript/ -->
       
<!-- Popup de création d'un filtre manuel -->           

   <div Id="popup-filter_interior">
		<img id="close" src="web_images/3.png">
		<div Id="jstree_query" style="max-height:300px;overflow:auto;">

        </div>
        <br>
		<table Id="filter-exterior" style="display:none">
			<tr>
				<td Id="table des champs">
				<table Id="filter-interior" width="50%">
					<tr>
						<td>Op:</td>
						<td>
						<select id="ListeOps" onchange="">
							<option value="AND">AND</option>
							<option value="OR">OR</option>
							<option value="NOT">NOT</option>
							<option value="<">&lt;</option>
							<option value="<=">&lt;=</option>
							<option value=">">&gt;</option>
							<option value=">=">&gt;=</option>
						</select></td>
						<td><a Id="navbutton-addop" class="navbutton-small buttonnext" href="#" onClick="AddOp();"></a></td>
					</tr>
					<tr>
						<td>Champs:</td>
						<td>
						<select id="ListeChamps" onchange="">
							<option value="Date">Date</option>
							<option value="Qualite">Qualité</option>
							<option value="Source">Source</option>
							<option value="Raw">Raw</option>
							<option value="Focale">Focale</option>
							<option value="Vitesse">Vitesse</option>
							<option value="ISO">ISO</option>
							<option value="Diaphragme">Diaphragme</option>
							<option value="Flash">Vitesse</option>
							<option value="portrait">Portrait</option>
							<option value="paysage">Paysage</option>
							<option value="largeur">Largeur</option>
							<option value="hauteur">Hauteur</option>
							<option value="N">N</option>
							<option value="Type">Type</option>
							<option value="Duration">TDuration</option>
							<option value="VideoWidth">VideoWidth</option>
							<option value="VideoHeight">VideoHeight</option>
						</select></td>
						<td><a Id="navbutton-addchamp" class="navbutton-small buttonnext" href="#" onClick="AddChamp();"></a></td>
					</tr>
					<tr>
						<td>Mot-clés:</td>
						<td>
						<input id="keywords-filter" width="30%">
						</td>
						<td><a Id="navbutton-addkeywordfilter" class="navbutton-small buttonnext" href="#" onClick="AddKeyword();"></a></td>
					</tr>
				</table></td>
				<td Id="Zone de texte"><textarea Id="Filterzone"></textarea></td>
			</tr>
		</table>
		<table>
			<tr>
			    
                <td><a Id="navbutton-queryok" class="navbutton" href="#" ></a></td>
                <td><a Id="navbutton-editfilter" class="navbutton" href="#" onClick="$('#filter-exterior').toggle();"></a></td>
				<td ><a Id="navbutton-filterok" class="navbutton" href="#" onClick=""></a> </td> 
				<td><a Id="navbutton-filterclear" class="navbutton" href="#" onClick="document.getElementById('Filterzone').value='';Query=0;QueryName='Toutes les photos';raffraichir();"></a></td>
			</tr>
		</table>
	</div>

        
        
<!-- Popup de copy dans répertoire cible -->   
        
    <div Id="popup-copy_interior">
        <img id="close" src="web_images/3.png">
        <br>Les photos sélectionnées vont être copiées vers Multimedia/Photos/PhotoDb/Export<br><br>
        <input Id="resize_check" type="checkbox" value="1" onclick="togglesize();">Resize
        <div Id="resize_block">
            <br>Taille maximum :
            <input Id="resize_size" type="text" value="1024">
        </div>
        
        <a Id="navbutton-copyok" class="navbutton" href="#" onClick=""></a>
    </div>
            
<!-- Popup de choix de la requete -->	
		
	<div Id="popup-import_interior">
		<img id="close" src="web_images/3.png">
		<div Id="import-log"></div>
		
	</div>
		

<!-- Popup de choix d'un mot-clé -->   		
		
    <div Id="popup-keywords_interior">
		<img id="close" src="web_images/3.png">
		<a Id="navbutton-addkw2" class="navbutton-small" href="keywordsEdit.php" target="_blank"></a>
		<div Id="keywords-list" class="ui-widget" style="margin-top:2em; font-family:Arial">
			Mot-clé&nbsp;:&nbsp;
			<input id="keywords">
		</div>

		<br>
		<a Id="navbutton-keywordsok" class="navbutton" href="#" onClick=""></a>
	</div>
	
	<!-- Popup de choix d'une qualité -->           
        
    <div Id="popup-quality_interior">
        <img id="close" src="web_images/3.png">
        <a Id="navbutton-addquality" class="navbutton-small" href="QualityEdit.php" target="_blank"></a>
        <div Id="quality-list" class="ui-widget" style="margin-top:2em; font-family:Arial">
            Qualité&nbsp;:&nbsp;
            <select id="qualitylist">
                <option value="0">Non notée</option>
                <option value="1">Mauvaise</option>
                <option value="2">Moyenne</option>
                <option value="3">Bonne</option>
                <option value="4">Exceptionnelle</option>
            </select> 
        </div>

        <br>
        <a Id="navbutton-qualityok" class="navbutton" href="#" onClick=""></a>
    </div>
       

<!-- Boutons de naviguation -->
    <!-- {literal} -->
<table Id="boutons_nav" width="80%" border="0" align="center" cellpadding="1" cellspacing="2" class="ontop">
    <tr>
        <td style="min-width:140px">
            <a Id="navbutton-monomulti" class="navbutton" title="Mode Diaporama/Vignettes" href="#" onClick="FullScreenToggle(0);"></a>
            <a Id="navbutton-diapo" class="navbutton" title="Lancer/Arrêter Diaporama" href="#" onClick="Diapo();"></a>
            <div id="slidercontainer"><input type="range" min="1" max="10" value="3" class="slider" id="myRange">
            </div></td>
        <td class="admin"><a Id="navbutton-import" title="Import de photos" class="navbutton" href="#" onClick="photo_import();"></a></td>
    	<td><a Id="navbutton-first" class="navbutton" href="#" title="Début" onClick="movetopage(-4);"></a>
    	   <a Id="navbutton-rewind" class="navbutton" href="#" title="Page/Photo précédente" onClick="movetopage(-3);"></a></td>
    	<td class="Date"><b Id="navcount" ></b>&nbsp;&ndash;&nbsp;<b Id="bottomline"></b></td>
    	<td><a Id="navbutton-forward" class="navbutton" title="Page/Photo suivante" href="#" onClick="movetopage(-2);"></a>
    	   <a Id="navbutton-last" class="navbutton" title="Fin" href="#" onClick="movetopage(-1);"></a></td>
    	<td><a Id="navbutton-filter" class="navbutton" title="Filtre" href="#" onClick="$('#popup').show();$('#popup-filter_interior').show();"></a></td>
    	<td><a Id="navbutton-select" class="navbutton" title="Selectionner tout" href="#" onClick="selectall();"></a>
    	<a Id="navbutton-unselect" class="navbutton" title="Desélectionner tout" href="#" onClick="unselectall();"></a></td>
    	<td><a Id="navbutton-copy" class="navbutton" title="Copier" href="#" onClick="togglesize();$('#popup').show();$('#popup-copy_interior').show();"></a></td>
    	<td class="admin"><a Id="navbutton-keyword" class="navbutton" title="Ajouter mot-clé" href="#" onClick="$('#popup').show();$('#popup-keywords_interior').show();document.getElementById('keywords').value='';$('#keywords').focus();"></a>
    	   <a Id="navbutton-quality" class="navbutton" title="Qualité" href="#" onClick="$('#popup').show();$('#popup-quality_interior').show();document.getElementById('keywords').value='';$('#keywords').focus();"></a></td>
        <td class="navbutton-info">
            <a Id="navbutton-info" class="navbutton" title="Info Exif" href="#" onClick="if ($('#sidepanel').width() == 0) {$('#sidepanel').width(200);$('#Main').css('marginLeft','200px');} else {$('#sidepanel').width(0);$('#Main').css('marginLeft','0px');} "></a>
        </td>
        <td class="navbutton-xl">
            <a Id="navbutton-xl" class="navbutton" title="Export Excel" href="#" onClick="import_excel();"></a>
        </td>
 
    	
	  </td>
  </tr>
</table>
    <!-- {/literal} -->
<!-- table pour les vignettes --> 
<table  class="thumb,tableau" Id="latable" align="center"></table>
</div>

</body>


<!-- {literal} -->
<script>
  
    table_create();
    raffraichir();
    if (Admin == 0) $(".admin").hide();
    $("#navbutton-diapo").hide();
    $("#slidercontainer").hide();

    document.onkeydown = function (e) {
         e = e || window.event;//Get event
         if (e.ctrlKey) {
            var c = e.which || e.keyCode;//Get key code
        switch (c) {
            case 83:selectall(); // Ctrl-S
                    e.preventDefault();     
                    e.stopPropagation();
                    break;
            case 68:unselectall(); // Ctrl-D
                    e.preventDefault();     
                    e.stopPropagation();
                    break;
            case 65:if (Admin == 0) break;
                    document.getElementById('popup').style.display = 'block';
                    document.getElementById('popup-keywords_interior').style.display = 'block'; // Ctrl-A
                    document.getElementById('keywords').value='';
                    $("#keywords").focus();
                    e.preventDefault();     
                    e.stopPropagation();
                    break;
            }
        }
    }

  $(function () {
  
    $("#keywords").keyup(function(event){
        if(event.keyCode == 13){
        $("#navbutton-keywordsok").click();
        }
    });
    $('#jstree_query').jstree({
    'core' : {
        'data' : {
            'url' : 'querylist.php',
            'data' : function (node) {
                return { 'id' : node.id };
            }
        }
     }
    });
    
	$('#jstree_query').on("changed.jstree", function (e, data) {
	    if (data.selected.toString().charAt(0) == "f") { // On est sur un folder
	       $('#navbutton-queryok').css("background-image","url('web_images/file_grey_64.png')") ;
	    }
	    else {
	       $('#navbutton-queryok').css("background-image","url('web_images/file_64.png')") ; 
	       NextQuery=data.selected.toString();
	    }
    });
    
    $('#navbutton-queryok').on('click',function() {
        $('#popup').hide();$('#popup-filter_interior').hide();Selected={};
        
        $.ajax({ 
                 type: 'GET', 
                 url: 'getquery.php', 
                 data: { 'N': NextQuery},
                 dataType: 'json',
                 success: function (data) {
                    var SQL=data.SQL;
                    document.getElementById("Filterzone").value=SQL;
                    LocalQuery=SQL;
                    Query=-2;start_position=0;
                    QueryName=data.Name;
                    raffraichir();
                 }
        });  
        
    });
    $('#navbutton-filterok').on('click',function() {Selected={};Query=-2;QueryName='Local';LocalQuery=document.getElementById("Filterzone").value;start_position=0;raffraichir();});
    $('#navbutton-keywordsok').on('click',function() {$('#popup-keywords_interior').hide();$('#popup').hide();assign_keyword();});
    $('#navbutton-copyok').on('click',function() {$('#popup-copy_interior').hide();$('#popup').hide();photo_copy();});
    $('#navbutton-keywordsok').css("background-image","url('web_images/check_64.png')") ;
    $('#navbutton-qualityok').on('click',function() {$('#popup-quality_interior').hide();$('#popup').hide();assign_quality();});
    $('#navbutton-qualityok').css("background-image","url('web_images/check_64.png')") ;
    $('#navbutton-filterok').css("background-image","url('web_images/check_64.png')") ;
    $('#navbutton-copyok').css("background-image","url('web_images/check_64.png')") ;
    $("#filtrage").on('click', function () {
    	document.getElementById('popup').style.display = "block";
    });
    $("img#close").on('click', function () {
    	$('#popup').hide();
    	$('#popup-keywords_interior').hide();
    	$('#popup-quality_interior').hide();
    	$('#popup-import_interior').hide();
    	$('#popup-filter_interior').hide();
    	$('#popup-copy_interior').hide();
    });
    $(window).resize(function() {
        table_destroy();table_create();raffraichir();
    });
  });
  
  $(function() {
    $( "#keywords" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "listkw.php",
          dataType:'json',
          data: {
            q: request.term
          },
          success: function( data ) {
            response( data );
          }
        });
      },
      minLength: 3,
      select: function( event, ui ) {
          if (ui.item) SelectedKW=ui.item.id;
      },
    });
    $( "#keywords-filter" ).autocomplete({
      source: function( request, response ) {
        $.ajax({
          url: "listkw.php",
          dataType:'json',
          data: {
            q: request.term
          },
          success: function( data ) {
            response( data );
          }
        });
      },
      minLength: 3,
      select: function( event, ui ) {},
    });
  });
  </script>
<!-- {/literal} -->

</html>
