var SelectedKW=-1;
var Selected={};

function toggleselect(Id,pos)
{
  if(Id in Selected) {
      if (Selected[Id] == "1") Selected[Id]="0"; else Selected[Id]=1;
  } else {
      Selected[Id]="1";
  }
  
  if (Selected[Id] == "1")  {$("#z"+pos).addClass("cellselected");/*$("#dd"+pos).addClass("cellselected");*/}
  else {$("#z"+pos).removeClass("cellselected");/*$("#dd"+pos).removeClass("cellselected");*/}
     
}  


function assign_quality()
{
    $.ajax({ 
    type: 'POST', 
    url: 'assignquality.php', 
    data: { 'Quality': $( "#qualitylist" ).val(), 'Selected' : JSON.stringify(Selected)}, 
    dataType: 'html',
    success: function (data) {raffraichir();}
    }); 
}
function assign_keyword()
{
    if (SelectedKW < 0) return;
    $.ajax({ 
    type: 'POST', 
    url: 'assignkw.php', 
    data: { 'Keyword': SelectedKW, 'Selected' : JSON.stringify(Selected)}, 
    dataType: 'html',
    success: function (data) {raffraichir();}
    }); 
}

function selectallsuccess(data)
{
    $.each(data, function(index, element) {
            if (index == "Count") { }
            else if (index == "Name") {}
            else {
                // Ici, on a dans le tableau element toutes les images
                    for (i=0;i < element.length;i++) Selected[element[i].N]=1;
                    }
        });
    raffraichir();       
}

function selectall()
{
    if ((Query == -2) && (LocalQuery == '')) Query=0;
    $.ajax({ 
    type: 'POST', 
    url: 'listimages.php', 
    data: { 'Query': Query, 'Position': start_position, 'Len': 100000, 'Keywords':0, 'LocalQuery':LocalQuery }, 
    dataType: 'json',
    success: selectallsuccess
    });   
}

function unselectall()
{
    Selected={};
    raffraichir();
}