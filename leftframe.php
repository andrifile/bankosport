<?php
require_once("settings.php");
require_once("Skedina.php");

$datenow = date("Y-m-d");
$timenow = date("H:m:s", time() - (60*10));
$fail = TRUE;
$link = mysql_connect(HOST, USER, PASSWORD);
mysql_select_db(DATABASE);

$sql = "SELECT id, kodi, ndeshja, ora, aktive FROM `skedina_ditore` WHERE `data`>='{$datenow}' AND `aktive`='1' ;";
$result = mysql_query($sql, $link);

$s = new Skedina(HOST, USER, PASSWORD, DATABASE);
$ndeshjet = $s->table_vlist(date("Y-m-d"), date("H:i:s", time()+(60*10)));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="refresh" content="120;leftframe.php" />
<meta http-equiv="pragma" content="no-cache" />
<title>Ndeshjet</title>
<link href="stili.css" rel="stylesheet" type="text/css" />
<script>
var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
  if(popUpWin)
  {
    if(!popUpWin.closed) popUpWin.close();
  }
  popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbar=no,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}
</script>
</head>

<body style="margin: 0px, 0px, 0px, 0px">

<table id="LegjendaNdeshjet" width="150" border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td width="20"><div id="LejohetTeke"></div></td>
    <td>Lejohet teke </td>
  </tr>
  <tr>
    <td width="20"><div id="NukMundTeVihet"></div></td>
    <td>Nuk mund te vihet </td>
  </tr>
  <tr>
    <td width="20">&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
<?php
if (!isset($_COOKIE[COOKIE_PREF."_uname"]) OR !isset($_COOKIE[COOKIE_PREF."_rights"]) OR $_COOKIE[COOKIE_PREF."_rights"] == 5) {
     die();
} else {

     if ($_COOKIE[COOKIE_PREF."_rights"] < L_CLIENT)
          $act = 'edit';
     else
          $act = 'view';
          
     echo '<table width="95%"  border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td width="8" bgcolor="#CCCCCC" align="center">&nbsp;</td>
    <td width="30" bgcolor="#CCCCCC" align="center"><span class="ndeshjet_lista"><strong>KODI</strong></span></td>
    <td bgcolor="#EAEAEA" align="center"><span class="ndeshjet_lista"><strong>NDESHJA</strong></span></td>
    <td width="40" bgcolor="#FFDBCA" align="center"><span class="ndeshjet_lista"><strong>ORA</strong></span></td>
  </tr>';
     foreach ($ndeshjet as $k=>$v) {
          echo "<tr>\n";
          if ($v[aktive] == 0)
               echo "<td bgcolor=\"#996666\">&nbsp;</td>";
          else if ($v[teke] == 1)
               echo "<td bgcolor=\"#669966\">&nbsp;</td>";
          else
               echo "<td bgcolor=\"#666666\">&nbsp;</td>";
          echo "<td bgcolor=\"#999999\">$v[kodi]</td>\n";
          echo "<td bgcolor=\"#C0C0C0\"><a href=\"#\" onClick=\"popUpWindow('ndeshje.php?act=".$act."&id={$v[id]}', 300, 150, 400, 500)\">{$v[ndeshja]}</a><br>\n</td>";
          echo "<td bgcolor=\"#CFBA94\"><span class=\"ndeshjet_lista\">".substr($v[ora],0,5)."</span></td>\n";
          echo "</tr>\n";
     }

     echo '</table>';
}
?>

</body>
</html>
