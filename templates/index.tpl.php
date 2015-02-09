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

var Page={$PAGE};
var NbPage={$NBPAGES};
var Query='{$QUERY}';

{literal}

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
 var URL="index.php?Page="+Page+"&Query="+Query;
 window.location=URL;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
//-->
{/literal}
</script>

</head>

<body  onLoad="MM_preloadImages('web_images/first_on.gif','web_images/backarrow_on.gif','web_images/forward_on.gif','web_images/last_on.gif')">
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
      <td><a href="#" onClick="movetopage(-4);" onMouseOver="MM_swapImage('Image2','','web_images/first_on.gif',1)" onMouseOut="MM_swapImgRestore()"><img src="web_images/first.gif" name="Image2" width="32" height="32" border="0"></a></td>
      <td><a href="#" onClick="movetopage(-3);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image3','','web_images/backarrow_on.gif',1)"><img src="web_images/backarrow.gif" name="Image3" width="32" height="32" border="0"></a></td>
	  <td><div align="center"> 
	      {$ROWS*$COLS*$PAGE-$ROWS*$COLS+1}-{$PAGE*$ROWS*$COLS}/{$NBPHOTOS}, 
          page 
          <input name="lapage" type="text" value="{$PAGE}" size="4" maxlength="5">
          /{$NBPAGES}
          <input name="Aller" type="button" onClick="movetopage(lapage.value);" value="Aller">
        </div>
	  </td>
      <td><a href="#" onClick="movetopage(-2);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image4','','web_images/forward_on.gif',1)"><img src="web_images/forward.gif" name="Image4" width="32" height="32" border="0"></a></td>
      <td><a href="#" onClick="movetopage(-1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image5','','web_images/last_on.gif',1)"><img src="web_images/last.gif" name="Image5" width="32" height="32" border="0"></a></td>
  </tr>
</table>

<div align="center"> 
  <button id="filtrage">Filtrer</button>
</div>



<table  class="thumb" class="tableau">
  {section name=im loop=$IM} {if ($IM[im].I % $COLS) == 0}
<tr>{/if}

<td class="tableau"><div align="center" class="thumb"><div align="center" class="thumb">{$IM[im].Date}</div><a href="{$IM[im].Link}" target="_blank"><img class="thumb" src="{$IM[im].SmallLink}"></a></div></td>

{if $IM[im].I%$COLS == $COLS-1}</tr>{/if}

{/section}
</table>

{literal}
<script>
  $(function () {
    // 6 create an instance when the DOM is ready
    //$('#jstree_query').jstree();
    // 7 bind to events triggered on the tree
    
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
