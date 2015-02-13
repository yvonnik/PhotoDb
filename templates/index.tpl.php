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

var start_position=0;
var Query=0;
var QueryName="Toutes les photos";
var Len=20;
var Count=0;
var ImageServer='{$IMAGESERVER}';
var NextQuery;

// {literal} 

function table_destroy()
{
    for (i=0; i < Rows;i++) document.getElementById("latable").removeChild(document.getElementById("r"+i));
}

function table_create()
{
    var l=0;
    Rows=Math.floor((document.body.clientHeight-80)/230);if (Rows <= 2) Rows=3;
    Cols=Math.floor((document.body.clientWidth-20)/278);if (Cols <= 2) Cols=3;
    Len=Rows*Cols;
    if (start_position+Len >= Count) start_position=Count-Len;
    if (start_position < 0) start_position=0;
    for (i=0; i < Rows;i++) {
      var ligne=document.createElement("tr");
      ligne.setAttribute("id","r"+i)
      for (j=0;j < Cols;j++) {
          var cellule=document.createElement("td");
          cellule.setAttribute("class","tableau");
          cellule.setAttribute("Id","cl"+l);
          
          var div1=document.createElement("div");div1.setAttribute("class","thumb");div1.setAttribute("align","center");
          
          var div2=document.createElement("div");div2.setAttribute("class","thumb");div2.setAttribute("align","center");
          div2.setAttribute("Id","d"+l);
          
          var aa=document.createElement("a");aa.setAttribute("Id","a"+l);aa.setAttribute("target","_blank");
          
          var limg=document.createElement("img");limg.setAttribute("class","thumb");limg.setAttribute("Id","i"+l);
          
          aa.appendChild(limg);
          
          div1.appendChild(div2);
          div1.appendChild(aa);
          
          cellule.appendChild(div1);
          ligne.appendChild(cellule);
          l++;
      }
     document.getElementById("latable").appendChild(ligne);
  }  
}
// Callback de l'appel Ajax pour récupérer la liste des images
function success_images(data) { 
        $.each(data, function(index, element) {
            if (index == "Count") {
                Count=element;
                document.getElementById("navcount").innerHTML=(start_position+1)+"-"+(start_position+Len)+" sur "+Count+", "+(Math.round(start_position*100/Count)+"%");
            }
            else if (index == "Name") {
                QueryName=element;
            }
            else {
                // Ici, on a dans le tableau element toutes les images
                    for (i=0;i < element.length;i++) {
                        $('#i'+i).attr("src",ImageServer+"display_image.php?Id="+element[i].N+"&small=1&Date="+element[i].Date);
                        $('#d'+i).text("("+element[i].N+") "+element[i].Date);
                        $('#a'+i).attr("href",ImageServer+"display_image.php?Id="+element[i].N+"&small=0&Date="+element[i].Date);
                        $('#cl'+i).attr("class","tableau"+element[i].Qualite);
                    }
                    for (i=i; i < Len;i++) {
                        $('#i'+i).attr("src","web_images/empty.png");
                        $('#d'+i).text("");
                        $('#a'+i).attr("href","");
                        $("cl"+i).attr("class","tableau");
                    }
                     
                 }
        });
        $('#bottomline').text(QueryName);
    }
    
function movetopage(sens)
{
 var OldPosition=start_position;
 if (sens == -4) /* rtour au d�but */ start_position=0;
 if (sens == -3) /* moins une page */ {start_position-=Len;if (start_position < 0) start_position=0;}
 if (sens == -2) /* avant une page */ {start_position+=Len;if (start_position+Len >= Count) start_position=Count-Len;}
 if (sens == -1) /* � la fin */ start_position=Count-Len;;
 
 if (OldPosition != start_position) raffraichir();
 
}

function raffraichir()
{
    $.ajax({ 
    type: 'GET', 
    url: 'listimages.php', 
    data: { 'Query': Query, 'Position': start_position, 'Len': Len }, 
    dataType: 'json',
    success: success_images
});  
 /*var URL="index.php?Page="+Page+"&Query="+Query;
 window.location=URL;*/
}

function select_query()
{
    
}


</script>
<!-- {/literal} /-->
</head>


<!-- http://www.formget.com/how-to-create-pop-up-contact-form-using-javascript/ -->	
	<div Id="popup">
			<div Id="popup_interior">
				<img id="close" src="web_images/3.png">
				<div Id="jstree_query">
					
				</div>
				<br>
				<a Id="navbutton-queryok" class="navbutton" href="#" onClick="select_query();"></a>
			</div>
		</div>
	
<table width="50%" border="0" align="center" cellpadding="1" cellspacing="5">
    <tr>
    	<td><a Id="navbutton-first" class="navbutton" href="#" onClick="movetopage(-4);"></a></td>
    	<td><a Id="navbutton-rewind" class="navbutton" href="#" onClick="movetopage(-3);"></a></td>
    	<td class="Date"><b Id="navcount"></b>&nbsp;&ndash;&nbsp;<b Id="bottomline"></b></td>
    	<td><a Id="navbutton-forward" class="navbutton" href="#" onClick="movetopage(-2);"></a></td>
    	<td><a Id="navbutton-last" class="navbutton" href="#" onClick="movetopage(-1);"></a></td>
    	<td><a Id="navbutton-filter" class="navbutton" href="#" onClick="document.getElementById('popup').style.display = 'block';"></a></td>
	  </td>
  </tr>
</table>


<table  class="thumb,tableau" Id="latable"></table>


</body>


<!-- {literal} -->
<script>
  
    table_create();
    raffraichir();


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
	    if (data.selected.toString().charAt(0) == "f") { // On est sur un folder
	       $('#navbutton-queryok').css("background-image","url('web_images/check_64_grey.png')") ;
	    }
	    else {
	       $('#navbutton-queryok').css("background-image","url('web_images/check_64.png')") ; 
	       NextQuery=data.selected.toString();
	    }
    });
    $('#navbutton-queryok').on('click',function() {document.getElementById('popup').style.display = "none";Query=NextQuery;start_position=0;raffraichir();});
    $("#filtrage").on('click', function () {
    	document.getElementById('popup').style.display = "block";
    });
    $("img#close").on('click', function () {
    	document.getElementById('popup').style.display = "none";
    });
    $(window).resize(function() {
        table_destroy();table_create();raffraichir();
    });
  });
  </script>
<!-- {/literal} -->

</html>
