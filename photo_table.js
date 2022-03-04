var start_position=0;
var Query=0;
var LocalQuery="";
var QueryName="Toutes les photos";
var Len=20;
var Count=0;

var NextQuery;

function displayexifpanel(Id)
{
    if ($("#sidepanel").width() != 0)
    {
        LocalQuery="N="+Id;
        $.ajax({
            type: 'POST',
            url: 'listimages.php',
            data: {'Query': -2, 'Position': 0, 'Len': 1, 'Keywords': 1, 'LocalQuery': LocalQuery},
            dataType: 'json',
            success: refresh_panel
        });
    }
}

function refresh_panel(data) {
    message="";
    $.each(data, function(index, element) {
        if (index == "Count") { }
        else if (index == "Name") { }
        else {
            // Ici, on a dans le tableau element toutes les images

            message="<table>";
            for (i=0;i < element.length;i++) {
              message+='<tr><td><b>Id</b> </td><td>'+element[i].N+"</td></tr>";
              message+='<tr><td><b>Date</b> </td><td>'+element[i].Date+"</td></td>";
              message+='<tr><td><b>Resolution</b> </td><td>'+element[i].largeur+"x"+element[i].hauteur+"</td></td>";
              message+='<tr><td><b>Source</b> </td><td>'+element[i].SourceText+ " ("+element[i].Source+")</td></td>";
              message+='<tr><td><b>Qualite</b> </td><td>'+element[i].Qualite+"</td></td>";
              message+='<tr><td><b>Retouche</b> </td><td>'+element[i].Retouche+"</td></td>";
              message+='<tr><td><b>Raw</b> </td><td>'+element[i].Raw+"</td></td>";
              message+='<tr><td><b>Focale</b> </td><td>'+element[i].Focale+" mm</td></td>";
              message+='<tr><td><b>Vitesse</b> </td><td>1/'+element[i].Vitesse+" s</td></td>";
              message+='<tr><td><b>Ouverture</b> </td><td>F'+element[i].Diaphragme+"</td></td>";
              message+='<tr><td><b>ISO</b> </td><td>'+element[i].ISO+"</td></td>";
              message+='<tr><td><b>Mots-clés</b> </td><td>'+element[i].keywords+"</td></td>";



            }
            message+="</table>"

        }
    });
   $('#sidepanel').html(message);
}

function table_destroy()
{
    for (i=0; i < Rows;i++) document.getElementById("latable").removeChild(document.getElementById("r"+i));
}

function table_create()
{
    var l=0;
    if (FullScreen) {Rows=1;Cols=1;}
    else {
        Rows=Math.floor((document.body.clientHeight-50)/200);if (Rows < 2) Rows=2;
        Cols=Math.floor((document.body.clientWidth-10)/286);if (Cols <= 2) Cols=3;    
    }
    
    Len=Rows*Cols;
    if (start_position+Len >= Count) start_position=Count-Len;
    if (start_position < 0) start_position=0;
    for (i=0; i < Rows;i++) {
      var ligne=document.createElement("tr");
      ligne.setAttribute("id","r"+i);
      for (j=0;j < Cols;j++) {
          var cellule=document.createElement("td");
          cellule.setAttribute("class","tableau");
          cellule.setAttribute("Id","cl"+l);
              
          var div2=document.createElement("div");div2.setAttribute("Id","d"+l);div2.setAttribute("class","textontop");      
          div2.setAttribute("Id","dd"+l);
          
          var ouvrir=document.createElement("a");ouvrir.setAttribute("Id","o"+l);ouvrir.setAttribute("target","_blank");
          var oimg=document.createElement("img");oimg.setAttribute("Id","oi"+l);oimg.setAttribute("align","left");
          ouvrir.setAttribute("class","thumbontop");
          ouvrir.appendChild(oimg);
          
          //var full=document.createElement("a");full.setAttribute("Id","fs"+l);full.setAttribute("target","_blank");
          //var fimg=document.createElement("img");fimg.setAttribute("Id","fsi"+l);fimg.setAttribute("align","left");
          //full.setAttribute("class","thumbontop2");
          //full.appendChild(fimg);
          
          var play=document.createElement("a");play.setAttribute("Id","pl"+l);play.setAttribute("target","_blank");
          var pimg=document.createElement("img");pimg.setAttribute("Id","pi"+l);pimg.setAttribute("align","left");
          play.setAttribute("class","thumbontop3");
          play.appendChild(pimg);
                 
          var limg=document.createElement("img");
          if (Len != 1) limg.setAttribute("class","thumbimg");
          else limg.setAttribute("style","max-height :"+(document.body.clientHeight-40)+";max-width :"+(document.body.clientWidth-30));
          
          limg.setAttribute("Id","i"+l);
          //limg.setAttribute("style","z-index:1000");
        
          var div4=document.createElement("div");div4.setAttribute("class","thumb");div4.setAttribute("Id","z"+l);div4.setAttribute("align","center");
          
          div4.appendChild(ouvrir);
          //div4.appendChild(full);
          div4.appendChild(play);
          div4.appendChild(limg);         
          div4.appendChild(div2);
          div4.setAttribute("style","position:relative;");
          cellule.appendChild(div4);
          ligne.appendChild(cellule);
          
          l++;
      }
     document.getElementById("latable").appendChild(ligne);
  }  
}

function raffraichir() {
    if ((Query == -2) && (LocalQuery == '')) Query = 0;
    $.ajax({
        type: 'POST',
        url: 'listimages.php',
        data: {'Query': Query, 'Position': start_position, 'Len': Len, 'Keywords': 1, 'LocalQuery': LocalQuery},
        dataType: 'json',
        success: success_images
    });
}

function success_images(data) { 
        $.each(data, function(index, element) {
            if (index == "Count") {
                Count=element;
                if (Len == 1) document.getElementById("navcount").innerHTML=(start_position+1)+" sur "+Count+", "+(Math.round(start_position*100/Count)+"%");
                else document.getElementById("navcount").innerHTML=(start_position+1)+"-"+(start_position+Len)+" sur "+Count+", "+(Math.round(start_position*100/Count)+"%");
            }
            else if (index == "Name") {
                 }
            else {
                // Ici, on a dans le tableau element toutes les images
                    var small=(element.length == 1 ? 2 : 1);
                    for (i=0;i < element.length;i++) {
                        $('#i'+i).attr("src",ImageServer+"display_image.php?Id="+element[i].N+"&small="+small+"&mh="+(document.body.clientHeight-40)+"&mw="+(document.body.clientWidth-30));
                        $('#i'+i).attr("onclick","toggleselect("+element[i].N+","+i+")");
                        $('#i'+i).attr("onMouseOver","displayexifpanel("+element[i].N+","+i+")");
                        $('#i'+i).attr("title",element[i].keywords);
                        $('#oi'+i).attr("src","web_images/preview_24.png");
                        $('#dd'+i).text("("+element[i].N+") "+element[i].Date);
                        $('#o'+i).attr("onclick","window.open('"+ImageServer+"display_image.php?Id="+element[i].N+"&small=0&Date="+element[i].Date+"')");
                        
                        $('#pi'+i).attr("src","web_images/video.png");
                        if (element[i].Type != "photo") {
                            $('#pl'+i).removeAttr("hidden");
                            $('#pl'+i).attr("onclick","window.open('"+ImageServer+"display_video.php?Id="+element[i].N+"&small=0&Date="+element[i].Date+"')");
                        }
                        else $('#pl'+i).attr("hidden","true");
                        
                        
                        //if (Len != 1) $('#fsi'+i).attr("src","web_images/full_screen_reading_24.png");
                        //else $('#fsi'+i).attr("src","web_images/clipart_24.png");
                        //$('#fs'+i).attr("onclick","FullScreenToggle("+i+")");
                        
                        
                        $('#cl'+i).attr("class","tableau"+element[i].Qualite);
                        if (Selected[element[i].N] == "1")  {$("#z"+i).addClass("cellselected");$("#dd"+i).addClass("cellselected");}
                        else {$("#z"+i).removeClass("cellselected");$("#dd"+i).removeClass("cellselected");}
                    }
                    for (; i < Len;i++) { //pas d'intialisation de i, volontaire, c'est pour les images "vides"
                        $('#i'+i).attr("src","web_images/empty.png");
                        $('#dd'+i).text("");
                        $('#a'+i).attr("href","");
                        $("cl"+i).attr("class","tableau");
                        $("#z"+i).removeClass("cellselected");$("#dd"+i).removeClass("cellselected");
                    }
                     
                 }
        });
        $('#bottomline').text(QueryName);
    }

    