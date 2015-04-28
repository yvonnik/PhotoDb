var SelectedKW=-1;
var Selected={};

function toggleselect(Id,pos)
{
  if(Id in Selected) {
      if (Selected[Id] == "1") Selected[Id]="0"; else Selected[Id]=1;
  } else {
      Selected[Id]="1";
  }
  
  if (Selected[Id] == "1")  {$("#z"+pos).addClass("cellselected");$("#dd"+pos).addClass("cellselected");}
  else {$("#z"+pos).removeClass("cellselected");$("#dd"+pos).removeClass("cellselected");}
     
}  

function assign_keyword()
{
    if (SelectedKW < 0) return;
    $.ajax({ 
    type: 'POST', 
    url: 'assignkw.php', 
    data: { 'Keyword': SelectedKW, 'Selected' : JSON.stringify(Selected)}, 
    dataType: 'json',
    success: assignkwsuccess
    }); 
}

function assignkwsuccess(data)
{
    raffraichir();
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
    $.ajax({ 
    type: 'GET', 
    url: 'listimages.php', 
    data: { 'Query': Query, 'Position': 0, 'Len': 100000, 'Keywords':0}, 
    dataType: 'json',
    success: selectallsuccess
    });   
}

function unselectall()
{
    Selected={};
    raffraichir();
}