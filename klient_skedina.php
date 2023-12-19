<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Vendos skedin&euml;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="pragma" content="no-cache">
<link href="stili.css" rel="stylesheet" type="text/css" />

</head>

<body onload="document.formKerko.dita.focus(); document.formKerko.dita.select()">

<?php
require_once("settings.php");
require_once("Bileta.php");
require_once("Login.php");

$b = new Bileta(HOST, USER, PASSWORD, DATABASE);
$l = new Login(HOST, USER, PASSWORD, DATABASE);
$uid = $l->get_uid($_COOKIE[COOKIE_PREF."_uname"]);

//Security check
if (($_COOKIE[COOKIE_PREF."_rights"] > L_CLIENT) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
     die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
}

if (!isset($_GET[viti]))
     $_GET[viti] = date("Y");
if (!isset($_GET[muaji]))
     $_GET[muaji] = date("m");
if (!isset($_GET[dita]))
     $_GET[dita] = date("d");
?>

<form name="formKerko" id="formKerko" method="get" action="klient_skedina.php" target="_self">
  <p>Data:
    <br>
    <input name="dita" type="text" id="dita" value="<?php echo date("d"); ?>" size="2" maxlength="2">
    <input name="muaji" type="text" id="muaji" value="<?php echo date("m"); ?>" size="2" maxlength="2">
    <input name="viti" type="text" id="viti" value="<?php echo date("Y"); ?>" size="4" maxlength="4">
    <br>
    <input name="Shikoje" type="submit" id="Shikoje" value="Shikoje">
  </p>
</form>

<?php
error_reporting(E_ERROR);

if (isset($_GET['viti'])) {
     $date = $_GET[viti]."-".$_GET[muaji]."-".$_GET[dita];
} else {
     $date = date("Y-m-d");
}

$skedina_anulluar = $b->get_tickets_K($date, 2, $uid);
$skedina_pritje = $b->get_tickets_K($date, -1, $uid);
$skedina_fituese = $b->get_tickets_K($date, 3, $uid);
$skedina_pranuar = $b->get_tickets_K($date, 1, $uid);
$skedina_refuzuar = $b->get_tickets_K($date, 0, $uid);
$skedina_djegur = $b->get_tickets_K($date, 4, $uid);


if (!empty($skedina_pritje)) {
     print "<div id=\"skedina_adm\"><p>SKEDINA N&Euml; PRITJE:";
     print "<table id=\"skedina_adm_tab\" width=\"20%\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_pritje as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."\" target=\"_self\">".$v[nr_bilete]."</a></td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
     }
     print "</table></p></div>";
}

if (!empty($skedina_pranuar)) {
     print "<div id=\"skedina_adm\"><p>SKEDINA T&Euml; PRANUARA:";
     print "<table id=\"skedina_adm_tab\" width=\"20%\" cellpadding=\"1\" cellspacing=\"1\">";
     $t_shuma = 0;
     $t_fitimi = 0;
     foreach ($skedina_pranuar as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."\" target=\"_self\">".$v[nr_bilete]."</a></td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
          print "</tr>";
          $t_shuma += $v[shuma];
          $t_fitimi += $v[fitimi];
     }
     print "</table>SHUMA TOTAL: ".number_format($t_shuma)."; FITIMI TOTAL I MUNDSHEM: ".number_format($t_fitimi)."</p></div>";
}

if (!empty($skedina_fituese)) {
     print "<div id=\"skedina_adm\"><p>SKEDINA FITUESE:";
     print "<table id=\"skedina_adm_tab\" width=\"20%\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_fituese as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."\" target=\"_self\">".$v[nr_bilete]."</a></td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
          print "</tr>";
     }
     print "</table></p></div>";
}

if (!empty($skedina_djegur)) {
     print "<div id=\"skedina_adm\"><p>SKEDINA T&Euml; DJEGURA:";
     print "<table id=\"skedina_adm_tab\" width=\"20%\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_djegur as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."&op=".$v[id_op]."&uid=".$uid."\" target=\"_self\">".$v[nr_bilete]."</a></td>";
          print "<td>".$uinfo[username]."</td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
          print "</tr>";
     }
     print "</table></p></div>";
}

if (!empty($skedina_refuzuar)) {
     print "<div id=\"skedina_adm\"><p>SKEDINA T&Euml; REFUZUARA:";
     print "<table id=\"skedina_adm_tab\" width=\"20%\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_refuzuar as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."\" target=\"_self\">".$v[nr_bilete]."</a></td>";
          print "<td>".$uinfo[username]."</td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
          print "</tr>";
     }
     print "</table></p></div>";
}

if (!empty($skedina_anulluar)) {
     print "<div id=\"skedina_adm\"><p>SKEDINA T&Euml; ANULLUARA:";
     print "<table id=\"skedina_adm_tab\" width=\"20%\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_anulluar as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."\" target=\"_self\">".$v[nr_bilete]."</a></td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
          print "</tr>";
     }
     print "</table></p></div>";
}
?>

</body>
</html>
