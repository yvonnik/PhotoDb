<html>
<head>
<title>Document sans titre</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="jsTree/dist/themes/default/style.min.css" />
</head>
<script src="jQuery/jquery.js"></script> 
<script src="jsTree/dist/jstree.min.js"></script>
<!-- {literal} -->
<script language="JavaScript" type="text/JavaScript">

var NSelected=-1;
var TSelected='';

function Addkw()
{
    var texte=encodeURI(document.getElementById("newkw-input").value);
    $.ajax({ 
    type: 'GET', 
    url: 'addkw.php', 
    data: { 'Parent': NSelected, 'Cle': texte }, 
    dataType: 'html',
    success: function (data) {location.reload(true);}
    });  
}

</script>

<body>
    
<table width="100%">
<tr><td>
    <div Id="newkw">
    <table width="30%" border="0" align="left" cellpadding="1" cellspacing="5">
        <tr>
         <td class="admin"><input id="newkw-input"></td>
         <td class="admin"><a Id="navbutton-addkw" class="navbutton-small" href="#" onClick="Addkw();"></a></td>
         <td class="admin"><p Id="Selected"></p></td>
        </tr>
    </table>
    </div>
</td></tr>
<tr><td>
    <div Id="jstree_query">
                    
    </div>
</td></tr>
</table>
</body>


<script>
  
    
  $(function () {
  
    $('#jstree_query').jstree({
    'core' : {
        'data' : {
            'url' : 'kwtreelist.php',
            'data' : function (node) {
                return { 'id' : node.id };
            }
        }
     }
    });
    
    $('#jstree_query').on("changed.jstree", function (e, data) {
        $('#Selected').text(data.node.text) ; 
           NSelected=data.selected.toString();
           });
  });
  
  
  </script>
<!-- {/literal} -->
</html>
