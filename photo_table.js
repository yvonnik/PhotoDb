var start_position=0;
var Query=0;
var LocalQuery="";
var QueryName="Toutes les photos";
var Len=20;
var Count=0;

var NextQuery;


function table_destroy()
{
    for (i=0; i < Rows;i++) document.getElementById("latable").removeChild(document.getElementById("r"+i));
}

function table_create()
{
    var l=0;
    if (FullScreen) {Rows=1;Cols=1;}
    else {
        Rows=Math.floor((document.body.clientHeight-80)/230);if (Rows <= 2) Rows=3;
        Cols=Math.floor((document.body.clientWidth-20)/278);if (Cols <= 2) Cols=3;    
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
                   
          var div2=document.createElement("div");div2.setAttribute("Id","d"+l);div2.setAttribute("vertical-align","top");
          var div3=document.createElement("div");div3.setAttribute("class","thumb");div3.setAttribute("align","right");
          div3.setAttribute("Id","dd"+l);
          
          var ouvrir=document.createElement("a");ouvrir.setAttribute("Id","o"+l);ouvrir.setAttribute("target","_blank");
          var oimg=document.createElement("img");oimg.setAttribute("align","left");oimg.setAttribute("Id","oi"+l);
          ouvrir.appendChild(oimg);
          div2.appendChild(ouvrir);
          div2.appendChild(div3);
                 
          var limg=document.createElement("img");
          if (Len != 1) limg.setAttribute("class","thumbimg");
          else limg.setAttribute("style","max-height :"+(document.body.clientHeight-150)+";max-width :"+(document.body.clientWidth-80));
          
          limg.setAttribute("Id","i"+l);limg.setAttribute("align","center");
        
          var div4=document.createElement("div");div4.setAttribute("class","thumb");div4.setAttribute("align","center");div4.setAttribute("Id","z"+l);
          div4.appendChild(limg);
          
          cellule.appendChild(div2);
          cellule.appendChild(div4);
          ligne.appendChild(cellule);
          l++;
      }
     document.getElementById("latable").appendChild(ligne);
  }  
}

function raffraichir()
{
    if ((Query == -2) && (LocalQuery == '')) Query=0;
    $.ajax({ 
    type: 'POST', 
    url: 'listimages.php', 
    data: { 'Query': Query, 'Position': start_position, 'Len': Len, 'Keywords':1, 'LocalQuery':LocalQuery }, 
    dataType: 'json',
    success: success_images
    });  


function success_images(data) { 
        $.each(data, function(index, element) {
            if (index == "Count") {
                Count=element;
                document.getElementById("navcount").innerHTML=(start_position+1)+"-"+(start_position+Len)+" sur "+Count+", "+(Math.round(start_position*100/Count)+"%");
            }
            else if (index == "Name") {
                 }
            else {
                // Ici, on a dans le tableau element toutes les images
                    var small=(element.length == 1 ? 0 : 1);
                    for (i=0;i < element.length;i++) {
                        $('#i'+i).attr("src",ImageServer+"display_image.php?Id="+element[i].N+"&small="+small+"&Date="+element[i].Date);
                        $('#i'+i).attr("onclick","toggleselect("+element[i].N+","+i+")");
                        $('#i'+i).attr("title",element[i].keywords);
                        $('#oi'+i).attr("src","web_images/preview_24.png");
                        $('#dd'+i).text("("+element[i].N+") "+element[i].Date);
                        $('#o'+i).attr("onclick","window.open('"+ImageServer+"display_image.php?Id="+element[i].N+"&small=0&Date="+element[i].Date+"')");
                       
                        $('#cl'+i).attr("class","tableau"+element[i].Qualite);
                        if (Selected[element[i].N] == "1")  {$("#z"+i).addClass("cellselected");$("#dd"+i).addClass("cellselected");}
                        else {$("#z"+i).removeClass("cellselected");$("#dd"+i).removeClass("cellselected");}
                    }
                    for (i=i; i < Len;i++) {
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
}
    