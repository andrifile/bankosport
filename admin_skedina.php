<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Vendos skedin&euml;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="refresh" content="30;admin_skedina.php" />
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
if (($_COOKIE[COOKIE_PREF."_rights"] > L_EDITOR) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
     die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
}

if (!isset($_GET[viti]))
     $_GET[viti] = date("Y");
if (!isset($_GET[muaji]))
     $_GET[muaji] = date("m");
if (!isset($_GET[dita]))
     $_GET[dita] = date("d");
?>
hh
<form name="formKerko" id="formKerko" method="get" action="admin_skedina.php" target="_self">
  <p>Data:
    <br>
    <input name="dita" type="text" id="dita" value="<?php echo $_GET[dita]; ?>" size="2" maxlength="2">
    <input name="muaji" type="text" id="muaji" value="<?php echo $_GET[muaji]; ?>" size="2" maxlength="2">
    <input name="viti" type="text" id="viti" value="<?php echo $_GET[viti]; ?>" size="4" maxlength="4">
    <br>
    <input name="Shikoje" type="submit" id="Shikoje" value="Shikoje">
  </p>
</form>

<?php

if (isset($_GET['viti'])) {
     $date = $_GET[viti]."-".$_GET[muaji]."-".$_GET[dita];
} else {
     $date = date("Y-m-d");
}

if ($_GET[action] == 'accept') {
     $b->set_ticket_status(1, $_GET[ticketid], $date, $_GET[opcode]);
     $ucredit = $l->get_user_credit($_GET[opcode]);
     $newcredit = $ucredit - $_GET[sum];
     $l->set_user_credit($newcredit, $_GET[opcode]);
} else if ($_GET[action] == 'refuse') {
     $b->set_ticket_status(0, $_GET[ticketid], $date, $_GET[opcode]);
} else if ($_GET[action] == 'winner') {
     $b->set_ticket_status(3, $_GET[ticketid], $_GET[viti]."-".$_GET[muaji]."-".$_GET[dita], $_GET[opcode]);
} else if ($_GET[action] == 'loser') {
     $b->set_ticket_status(4, $_GET[ticketid], $_GET[viti]."-".$_GET[muaji]."-".$_GET[dita], $_GET[opcode]);
}

$skedina_anulluar = $b->get_tickets($date, 2);
$skedina_pritje = $b->get_tickets($date, -1);
$skedina_fituese = $b->get_tickets($date, 3);
$skedina_pranuar = $b->get_tickets($date, 1);
$skedina_refuzuar = $b->get_tickets($date, 0);
$skedina_djegur = $b->get_tickets($date, 4);


if (!empty($skedina_pritje)) {
     print "<div id=\"skedina_adm\" style=\"border-color: #55A0FF;\"><p>SKEDINA N&Euml; PRITJE:";
     print "<table id=\"skedina_adm_tab\" width=\"260\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_pritje as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr onmouseover=\"this.style.backgroundColor='#fe7737'; \" onmouseout=\"this.style.backgroundColor='#3a3a3a';\">";
          print "<td width=\"40\">".$v[time]."</td>";
          print "<td width=\"20\"><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."&op=".$v[id_op]."&uid=".$uid."\" target=\"_new\">".$v[nr_bilete]."</a></td>";
          print "<td width=\"20\">".$uinfo[username]."</td>";
          print "<td width=\"40\">".$v[shuma]."</td>";
          print "<td width=\"40\">".$v[total_coe]."</td>";
          print "<td width=\"40\">".$v[fitimi]."</td>";
          print "<td width=\"30\"> [ <a href=\"{$_SERVER[PHP_SELF]}?ticketid={$v[nr_bilete]}&opcode={$v[id_op]}&sum={$v[shuma]}&viti={$_GET[viti]}&muaji={$_GET[muaji]}&dita={$_GET[dita]}&action=accept\">PRANOJE</a> ] </td>";
          print "<td width=\"30\"> [ <a href=\"{$_SERVER[PHP_SELF]}?ticketid={$v[nr_bilete]}&opcode={$v[id_op]}&sum={$v[shuma]}&viti={$_GET[viti]}&muaji={$_GET[muaji]}&dita={$_GET[dita]}&action=refuse\">REFUZOJE</a> ] </td>";
     }
     print "</table></p></div>";
}

if (!empty($skedina_pranuar)) {
     print "<div id=\"skedina_adm\"><p>SKEDINA T&Euml; PRANUARA:";
     print "<table id=\"skedina_adm_tab\" width=\"530\" cellpadding=\"1\" cellspacing=\"1\">";
     $t_shuma = 0;
     $t_fitimi = 0;
     foreach ($skedina_pranuar as $v) {
          $uinfo = $l->user_info($v[id_op]);
//          if ($uinfo[username] == "admin")
//               continue;
          print "<tr onmouseover=\"this.style.backgroundColor='#fe7737'; \" onmouseout=\"this.style.backgroundColor='#3a3a3a';\">";
          print "<td width=\"50\">".$v[time]."</td>";
          print "<td width=\"30\"><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."&op=".$v[id_op]."&uid=".$uid."\" target=\"_new\">".$v[nr_bilete]."</a></td>";
          print "<td width=\"30\">".$uinfo[username]."</td>";
          print "<td width=\"50\">".$v[shuma]."</td>";
          print "<td width=\"50\">".$v[total_coe]."</td>";
          print "<td width=\"50\">".$v[fitimi]."</td>";
          print "<td width=\"125\"> [ <a href=\"{$_SERVER[PHP_SELF]}?ticketid={$v[nr_bilete]}&opcode={$v[id_op]}&viti={$_GET[viti]}&muaji={$_GET[muaji]}&dita={$_GET[dita]}&action=winner\">FITUESE</a> ] </td>";
          print "<td width=\"145\"> [ <a href=\"{$_SERVER[PHP_SELF]}?ticketid={$v[nr_bilete]}&opcode={$v[id_op]}&viti={$_GET[viti]}&muaji={$_GET[muaji]}&dita={$_GET[dita]}&action=loser\">JO FITUESE</a> ] </td>";
          print "</tr>";
          $t_shuma += $v[shuma];
          $t_fitimi += $v[fitimi];
     }
     print "</table>SHUMA TOTAL: ".number_format($t_shuma)."; FITIMI TOTAL I MUNDSHEM: ".number_format($t_fitimi)."</p></div>";
}

if (!empty($skedina_fituese)) {
     print "<div id=\"skedina_adm\" style=\"border-color: #00CC66;\"><p>SKEDINA FITUESE:";
     print "<table id=\"skedina_adm_tab\" width=\"60%\" cellpadding=\"1\" cellspacing=\"1\">";
     $jepen = 0;
     foreach ($skedina_fituese as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."&op=".$v[id_op]."&uid=".$uid."\" target=\"_new\">".$v[nr_bilete]."</a></td>";
          print "<td>".$uinfo[username]."</td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
          print "</tr>";
          $jepen += $v[fitimi];
     }
     print "</table>FITIMI I BILETAVE: {$jepen}</p></div>";
}

if (!empty($skedina_djegur)) {
     print "<div id=\"skedina_adm\" style=\"border-color: #CC3300;\"><p>SKEDINA T&Euml; DJEGURA:";
     print "<table id=\"skedina_adm_tab\" width=\"60%\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_djegur as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."&op=".$v[id_op]."&uid=".$uid."\" target=\"_new\">".$v[nr_bilete]."</a></td>";
          print "<td>".$uinfo[username]."</td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
          print "</tr>";
     }
     print "</table></p></div>";
}

if (!empty($skedina_anulluar)) {
     print "<div id=\"skedina_adm\" style=\"border-color: #000000;\"><p>SKEDINA T&Euml; ANULLUARA:";
     print "<table id=\"skedina_adm_tab\" width=\"60%\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_anulluar as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."&op=".$v[id_op]."&uid=".$uid."\" target=\"_new\">".$v[nr_bilete]."</a></td>";
          print "<td>".$uinfo[username]."</td>";
          print "<td>".$v[shuma]."</td>";
          print "<td>".$v[total_coe]."</td>";
          print "<td>".$v[fitimi]."</td>";
          print "</tr>";
     }
     print "</table></p></div>";
}

if (!empty($skedina_refuzuar)) {
     print "<div id=\"skedina_adm\" style=\"border-color: #dddddd;\"><p>SKEDINA T&Euml; REFUZUARA:";
     print "<table id=\"skedina_adm_tab\" width=\"60%\" cellpadding=\"1\" cellspacing=\"1\">";
     foreach ($skedina_refuzuar as $v) {
          $uinfo = $l->user_info($v[id_op]);
          print "<tr>";
          print "<td>".$v[time]."</td>";
          print "<td><a href=\"shiko_skedine.php?ticket=".$v[nr_bilete]."&viti=".$_GET[viti]."&muaji=".$_GET[muaji]."&dita=".$_GET[dita]."&op=".$v[id_op]."&uid=".$uid."\" target=\"_new\">".$v[nr_bilete]."</a></td>";
          print "<td>".$uinfo[username]."</td>";
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
