<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<html>


<head>
<title>PhotoDb</title>

<link href="style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="jsTree/dist/themes/default/style.min.css" />
<script src="jQuery/jquery.js"></script>
<!-- http://www.jstree.com/ -->

<script src="jsTree/dist/jstree.min.js"></script>

<script language="JavaScript" type="text/JavaScript">

var Page=1;
var NbPage=2;
var Query=0;
var Rows={$ROWS};
var Cols={$COLS};
var Len=Rows*Cols;
var Count=0;
var ImageServer='{$IMAGESERVER}';
raffraichir();

{literal}

// Callback de l'appel Ajax pour récupérer la liste des images
function success_images(data) { 
        $.each(data, function(index, element) {
            if (index == "Count") {
                Count=element;
                NbPage=Math.ceil( Count/Len );
            }
            else {
                // Ici, on a dans le tableau element toutes les images
                    for (i=0;i < element.length;i++) {
                        $('#i'+i).attr("src",ImageServer+"display_image.php?Id="+element[i].N+"&small=1&Date="+element[i].Date);
                        $('#d'+i).text(element[i].Date);
                        $('#a'+i).attr("href",ImageServer+"display_image.php?Id="+element[i].N+"&small=0&Date="+element[i].Date);
                    }
                    for (i=i; i < Len;i++) {
                        $('#i'+i).attr("src","web_images/empty.png");
                        $('#d'+i).text("");
                        $('#a'+i).attr("href","");
                    }
                     
                 }
        });
    }
    
function movetopage(sens)
{
 var OldPage=Page;
 if (sens == -4) /* rtour au d�but */ Page=1;
 if (sens == -3) /* moins une page */ {Page--;if (Page <= 0) Page=1;}
 if (sens == -2) /* avant une page */ {Page++; if (Page > NbPage) Page=NbPage;}
 if (sens == -1) /* � la fin */ Page=NbPage;
 if ((sens > 0) && (sens <= NbPage)) Page=sens;
 if (OldPage != Page) raffraichir();
}

function raffraichir()
{
   $.ajax({ 
    type: 'GET', 
    url: 'listimages.php', 
    data: { 'Query': Query, 'Page': Page, 'Len': Len }, 
    dataType: 'json',
    success: success_images
});  
 /*var URL="index.php?Page="+Page+"&Query="+Query;
 window.location=URL;*/
}

//-->
{/literal}

</script>

</head>


<!-- http://www.formget.com/how-to-create-pop-up-contact-form-using-javascript/ -->	
	<div Id="popup">
			<div Id="popup_interior">
				<img id="close" src="web_images/3.png">
				<div Id="jstree_query">
					
				</div>
				<br>
				<button Id="query_close">
					Filtrer
				</button>
			</div>
		</div>
	
<table width="50%" border="0" align="center" cellpadding="1" cellspacing="5">
    <tr>
    	<td><a Id="navbutton-first" class="navbutton" href="#" onClick="movetopage(-4);"></a></td>
    	<td><a Id="navbutton-rewind" class="navbutton" href="#" onClick="movetopage(-3);"></a></td>
    	<td><a Id="navbutton-forward" class="navbutton" href="#" onClick="movetopage(-2);"></a></td>
    	<td><a Id="navbutton-last" class="navbutton" href="#" onClick="movetopage(-1);"></a></td>
    	<td><a Id="navbutton-filter" class="navbutton" href="#" onClick="document.getElementById('popup').style.display = 'block';"></a></td>
	  </td>
  </tr>
</table>





<table  class="thumb" class="tableau">
  {section name=im loop=$IM} {if ($IM[im].I % $COLS) == 0}
<tr>{/if}

<td class="tableau" "><div align="center" class="thumb"><div id="d{$IM[im].I}" align="center" class="thumb"></div><a id="a{$IM[im].I}" href="" target="_blank"><img id="i{$IM[im].I}" class="thumb" src=""></a></div></td>

{if $IM[im].I%$COLS == $COLS-1}</tr>{/if}

{/section}
</table>

{literal}
<script>
  $(function () {
  
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
      console.log(data.selected);
    });
    $("#filtrage").on('click', function () {
    	document.getElementById('popup').style.display = "block";
    });
    $("img#close").on('click', function () {
    	document.getElementById('popup').style.display = "none";
    });
  });
  </script>
{/literal}
</body>
</html>
