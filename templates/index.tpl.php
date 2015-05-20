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

var ImageServer='{$IMAGESERVER}';
var Admin={$ADMIN};

// {literal} 

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
    var valeur=" (["+document.getElementById("keywords-filter").value+"] IN mots-clés) ";
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

function photo_import() {
    document.getElementById('popup-import').style.display = 'block';
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


</script>
<!-- {/literal} /-->
</head>


<!-- http://www.formget.com/how-to-create-pop-up-contact-form-using-javascript/ -->

<div Id="popup-import">
           <div Id="popup-import_interior"> 
                <img id="close" src="web_images/3.png">
                <div Id="import-log">
                    
                </div>
                
            </div>
        </div>
        
<!-- Popup de création d'un filtre manuel -->           
<div Id="popup-filter">
    <div Id="popup-filter_interior"> 
        <img id="close" src="web_images/3.png">
        <table Id="filter-exterior">
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
                                </select> 
                            </td>
                            <td>
                                <a Id="navbutton-addop" class="navbutton-small buttonnext" href="#" onClick="AddOp();"></a>   
                            </td>
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
                                </select> 
                            </td>
                            <td>
                                <a Id="navbutton-addchamp" class="navbutton-small buttonnext" href="#" onClick="AddChamp();"></a>   
                            </td>
                        </tr>
                        <tr>
                            <td>Mot-clés:</td>
                            <td>
                              <input id="keywords-filter" width="30%">  
                            </td>
                            <td>
                                <a Id="navbutton-addkeywordfilter" class="navbutton-small buttonnext" href="#" onClick="AddKeyword();"></a>
                            </td>
                        </tr>
                    </table>   
                </td>
                <td Id="Zone de texte">
                    <textarea Id="Filterzone"></textarea>    
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <button>Apply</button>
                    <button>Clear</button>
                    <button>Save</button>
                </td>
            </tr>
        </table>                
    </div>
</div>
        
<!-- Popup de choix de la requete -->	
	<div Id="popup">
			<div Id="popup_interior">
				<img id="close" src="web_images/3.png">
				<div Id="jstree_query">
					
				</div>
				<br>
				<a Id="navbutton-queryok" class="navbutton" href="#" onClick=""></a>
			</div>
		</div>

<!-- Popup de choix d'un mot-clé -->   		
		<div Id="popup-keywords">
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
        </div>

<!-- Boutons de naviguation -->  	
<table width="50%" border="0" align="center" cellpadding="1" cellspacing="5">
    <tr>
        <td class="admin"><a Id="navbutton-import" class="navbutton" href="#" onClick="photo_import();"></a></td>
    	<td><a Id="navbutton-first" class="navbutton" href="#" onClick="movetopage(-4);"></a></td>
    	<td><a Id="navbutton-rewind" class="navbutton" href="#" onClick="movetopage(-3);"></a></td>
    	<td class="Date"><b Id="navcount"></b>&nbsp;&ndash;&nbsp;<b Id="bottomline"></b></td>
    	<td><a Id="navbutton-forward" class="navbutton" href="#" onClick="movetopage(-2);"></a></td>
    	<td><a Id="navbutton-last" class="navbutton" href="#" onClick="movetopage(-1);"></a></td>
    	<td><a Id="navbutton-filter" class="navbutton" href="#" onClick="document.getElementById('popup').style.display = 'block';"></a></td>
    	<td><a Id="navbutton-select" class="navbutton" href="#" onClick="selectall();"></a></td>
    	<td><a Id="navbutton-unselect" class="navbutton" href="#" onClick="unselectall();"></a></td>
    	<td class="admin"><a Id="navbutton-keyword" class="navbutton" href="#" onClick="document.getElementById('popup-keywords').style.display = 'block';document.getElementById('keywords').value='';$('#keywords').focus();"></a></td>
    	<td class="admin"><a Id="navbutton-filter2" class="navbutton" href="#" onClick="document.getElementById('popup-filter').style.display = 'block';"></a></td>
	  </td>
  </tr>
</table>

<!-- table pour les vignettes --> 
<table  class="thumb,tableau" Id="latable"></table>


</body>


<!-- {literal} -->
<script>
  
    table_create();
    raffraichir();
    if (Admin == 0) $(".admin").hide();

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
                    document.getElementById('popup-keywords').style.display = 'block'; // Ctrl-A
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
	       $('#navbutton-queryok').css("background-image","url('web_images/check_64_grey.png')") ;
	    }
	    else {
	       $('#navbutton-queryok').css("background-image","url('web_images/check_64.png')") ; 
	       NextQuery=data.selected.toString();
	    }
    });
    
    $('#navbutton-queryok').on('click',function() {document.getElementById('popup').style.display = "none";Selected={};Query=NextQuery;start_position=0;raffraichir();});
    $('#navbutton-keywordsok').on('click',function() {document.getElementById('popup-keywords').style.display = "none";assign_keyword();});
    $('#navbutton-keywordsok').css("background-image","url('web_images/check_64.png')") ;
    $("#filtrage").on('click', function () {
    	document.getElementById('popup').style.display = "block";
    });
    $("img#close").on('click', function () {
    	document.getElementById('popup').style.display = "none";
    	document.getElementById('popup-keywords').style.display = "none";
    	document.getElementById('popup-import').style.display = "none";
    	document.getElementById('popup-filter').style.display = "none";
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
