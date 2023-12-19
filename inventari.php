<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Inventari</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="stili.css" rel="stylesheet" type="text/css">
</head>

<body onload="document.formKerko.txtDataS.focus(); document.formKerko.txtDataS.select()">
<?php
require_once("settings.php");
require_once("Login.php");
     //Security check
     if (($_COOKIE[COOKIE_PREF."_rights"] > L_CLIENT) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
          die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
     }

$l = new Login(HOST, USER, PASSWORD, DATABASE);
$uid = $l->get_uid($_COOKIE[COOKIE_PREF."_uname"]);
if (!isset($_POST[txtDataS])) {
     $date_s = date("Y-m-d");
     $date_e = date("Y-m-d");
} else {
     $date_s = $_POST[txtVitiS]."-".$_POST[txtMuajiS]."-".$_POST[txtDataS];
     $date_e = $_POST[txtVitiE]."-".$_POST[txtMuajiE]."-".$_POST[txtDataE];
}
?>
<form name="formKerko" method="post" action="<?php echo $_SERVER[PHP_SELF]; ?>">
<table>
<tr>
<td>Data e fillimit:</td>
<td><input name="txtDataS" type="text" id="txtDataS" value="<?php echo date('d'); ?>" size="2" maxlength="2"></td>
<td>Muaji i fillmit:</td>
<td><input name="txtMuajiS" type="text" id="txtMuajiS" value="<?php echo date('m'); ?>" size="2" maxlength="2"></td>
<td>Viti i fillimit:</td>
<td><input name="txtVitiS" type="text" id="txtVitiS" value="<?php echo date('Y'); ?>" size="4" maxlength="4"></td>
<tr>
<td>Data e mbarimit:</td>
<td><input name="txtDataE" type="text" id="txtDataE" value="<?php echo date('d'); ?>" size="2" maxlength="2"></td>
<td>Muaji i mbarimit:</td>
<td><input name="txtMuajiE" type="text" id="txtMuajiE" value="<?php echo date('m'); ?>" size="2" maxlength="2"></td>
<td>Viti i mbarimit:</td>
<td><input name="txtVitiE" type="text" id="txtVitiE" value="<?php echo date('Y'); ?>" size="4" maxlength="4"></td>
</tr>
</table>
<input name="Shiko" type="submit" id="Shiko" value="Shiko">
</form>
<br>
<?php
if (($_COOKIE[COOKIE_PREF."_rights"] < L_EDITOR) & (isset($_COOKIE[COOKIE_PREF."_rights"]))) {
$ulist = $l->enum_users();

echo "<table width=\"500\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\">
  <tr bgcolor=\"#000066\">
    <td>#</td>
    <td>XHIRO</td>
    <td>% PIKA </td>
    <td>FITIMI I LOJTAR&Euml;VE</td>
    <td>P&Euml;R DOR&Euml;ZIM </td>
  </tr>";

foreach ($ulist as $v) {
$hx = $l->get_amnt2($date_s, $date_e, $v[uid]);
$xhiro = $hx['xhiro'];
$perqind = $hx['perqind'];
$fitimi = $hx['fitimi'];
$dorezim = $xhiro - $perqind - $fitimi;

$xhiro_t += $xhiro;
$perqind_t += $perqind;
$fitimi_t += $fitimi;
$dorezim_t += $dorezim;
echo"
  <tr bgcolor=\"#66CCFF\" class=\"koka\">
    <td><font color=\"#FF0000\">{$v[uname]}</font></td>
    <td><font color=\"#000000\">".$xhiro."</font></td>
    <td><font color=\"#000000\">".$perqind."</font></td>
    <td><font color=\"#000000\">".$fitimi."</font></td>
    <td><font color=\"#000000\">".$dorezim."</font></td>
  </tr>
";
}
//TOTAL
echo "
  <tr bgcolor=\"#33AADD\" class=\"koka\">
    <td><font color=\"#FFFF00\">TOTAL</font></td>
    <td><font color=\"#000000\">".$xhiro_t."</font></td>
    <td><font color=\"#000000\">".$perqind_t."</font></td>
    <td><font color=\"#000000\">".$fitimi_t."</font></td>
    <td><font color=\"#000000\">".$dorezim_t."</font></td>
  </tr>
";

echo "</table><br>";
}
?>
<table width="500" border="0" cellspacing="1" cellpadding="1">
  <tr bgcolor="#000066">
    <td>XHIRO</td>
    <td>% PIKA </td>
    <td>FITIMI I LOJTAR&Euml;VE </td>
    <td>P&Euml;R DOR&Euml;ZIM </td>
  </tr>
  <tr bgcolor="#66CCFF" class="koka">
  <?php $h = $l->get_amnt2($date_s, $date_e, $uid); ?>
    <td><font color="#000000"><?php echo $h['xhiro']; ?></font></td>
    <td><font color="#000000"><?php echo $h['perqind']; ?></font></td>
    <td><font color="#000000"><?php echo $h['fitimi']; ?></font></td>
    <td><font color="#000000"><?php echo $h['xhiro']-$h['perqind']-$h['fitimi']; ?></font></td>
  </tr>
</table>
</body>
</html>
