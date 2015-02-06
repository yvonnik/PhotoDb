<?php /* Smarty version Smarty-3.1.21-dev, created on 2015-02-04 14:06:19
         compiled from "C:\wamp\www\Photodb\templates\index.tpl.php" */ ?>
<?php /*%%SmartyHeaderCode:255054d2194ba5b839-60578033%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3b10453f62b91a7037ebe24e3929f5cc572309a8' => 
    array (
      0 => 'C:\\wamp\\www\\Photodb\\templates\\index.tpl.php',
      1 => 1206611163,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '255054d2194ba5b839-60578033',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'PAGE' => 0,
    'NBPAGES' => 0,
    'QUERY' => 0,
    'ROWS' => 0,
    'COLS' => 0,
    'NBPHOTOS' => 0,
    'RAWQUERY' => 0,
    'IM' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.21-dev',
  'unifunc' => 'content_54d2194bbc2e87_00003729',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54d2194bbc2e87_00003729')) {function content_54d2194bbc2e87_00003729($_smarty_tpl) {?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>

<head>
<title>PhotoDb</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="style.css" rel="stylesheet" type="text/css">
 
<?php echo '<script'; ?>
 language="JavaScript" type="text/JavaScript">
<!--

var Page=<?php echo $_smarty_tpl->tpl_vars['PAGE']->value;?>
;
var NbPage=<?php echo $_smarty_tpl->tpl_vars['NBPAGES']->value;?>
;
var Query='<?php echo $_smarty_tpl->tpl_vars['QUERY']->value;?>
';



function movetopage(sens)
{
 var OldPage=Page;
 if (sens == -4) /* rtour au début */ Page=1;
 if (sens == -3) /* moins une page */ {Page--;if (Page <= 0) Page=1;}
 if (sens == -2) /* avant une page */ {Page++; if (Page > NbPage) Page=NbPage;}
 if (sens == -1) /* à la fin */ Page=NbPage;
 if ((sens > 0) && (sens <= NbPage)) Page=sens;
 if (OldPage != Page) raffraichir();
}

function raffraichir()
{
 var URL="index.php?Page="+Page+"&Query="+Query;
 window.location=URL;
}

function toggleQuery()
{
 window.open("editquery.php");
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

<?php echo '</script'; ?>
>

</head>

<body  onLoad="MM_preloadImages('web_images/first_on.gif','web_images/backarrow_on.gif','web_images/forward_on.gif','web_images/last_on.gif')">
<table width="50%" border="0" align="center" cellpadding="1" cellspacing="5">
    <tr>
      <td><a href="#" onClick="movetopage(-4);" onMouseOver="MM_swapImage('Image2','','web_images/first_on.gif',1)" onMouseOut="MM_swapImgRestore()"><img src="web_images/first.gif" name="Image2" width="32" height="32" border="0"></a></td>
      <td><a href="#" onClick="movetopage(-3);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image3','','web_images/backarrow_on.gif',1)"><img src="web_images/backarrow.gif" name="Image3" width="32" height="32" border="0"></a></td>
	  <td><div align="center"> 
	      <?php echo $_smarty_tpl->tpl_vars['ROWS']->value*$_smarty_tpl->tpl_vars['COLS']->value*$_smarty_tpl->tpl_vars['PAGE']->value-$_smarty_tpl->tpl_vars['ROWS']->value*$_smarty_tpl->tpl_vars['COLS']->value+1;?>
-<?php echo $_smarty_tpl->tpl_vars['PAGE']->value*$_smarty_tpl->tpl_vars['ROWS']->value*$_smarty_tpl->tpl_vars['COLS']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['NBPHOTOS']->value;?>
, 
          page 
          <input name="lapage" type="text" value="<?php echo $_smarty_tpl->tpl_vars['PAGE']->value;?>
" size="4" maxlength="5">
          /<?php echo $_smarty_tpl->tpl_vars['NBPAGES']->value;?>

          <input name="Aller" type="button" onClick="movetopage(lapage.value);" value="Aller">
        </div>
	  </td>
      <td><a href="#" onClick="movetopage(-2);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image4','','web_images/forward_on.gif',1)"><img src="web_images/forward.gif" name="Image4" width="32" height="32" border="0"></a></td>
      <td><a href="#" onClick="movetopage(-1);" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image5','','web_images/last_on.gif',1)"><img src="web_images/last.gif" name="Image5" width="32" height="32" border="0"></a></td>
  </tr>
</table>
<div align="left">WHERE : <?php echo $_smarty_tpl->tpl_vars['RAWQUERY']->value;?>
 
  &nbsp;&nbsp;&nbsp;<input name="EditQuery" type="button" onClick="toggleQuery();" value="Editer">
</div>
<table border="1" cellpadding="3" cellspacing="2" bgcolor="#333333">
  <?php if (isset($_smarty_tpl->tpl_vars['smarty']->value['section']['im'])) unset($_smarty_tpl->tpl_vars['smarty']->value['section']['im']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['name'] = 'im';
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['loop'] = is_array($_loop=$_smarty_tpl->tpl_vars['IM']->value) ? count($_loop) : max(0, (int) $_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['im']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['im']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['im']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['im']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['im']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['im']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['im']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['im']['total']);
?> <?php if (($_smarty_tpl->tpl_vars['IM']->value[$_smarty_tpl->getVariable('smarty')->value['section']['im']['index']]['I']%$_smarty_tpl->tpl_vars['COLS']->value)==0) {?>
<tr><?php }?>

<td><div align="center"><?php echo $_smarty_tpl->tpl_vars['IM']->value[$_smarty_tpl->getVariable('smarty')->value['section']['im']['index']]['Date'];?>
<a href="<?php echo $_smarty_tpl->tpl_vars['IM']->value[$_smarty_tpl->getVariable('smarty')->value['section']['im']['index']]['Link'];?>
" target="_blank"><img src="<?php echo $_smarty_tpl->tpl_vars['IM']->value[$_smarty_tpl->getVariable('smarty')->value['section']['im']['index']]['SmallLink'];?>
"></a></div></td>

<?php if ($_smarty_tpl->tpl_vars['IM']->value[$_smarty_tpl->getVariable('smarty')->value['section']['im']['index']]['I']%$_smarty_tpl->tpl_vars['COLS']->value==$_smarty_tpl->tpl_vars['COLS']->value-1) {?></tr><?php }?>

<?php endfor; endif; ?>
</table>

</body>
</html>
<?php }} ?>
