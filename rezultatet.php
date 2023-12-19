<?php
require_once("settings.php");
require_once("Skedina.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Rezultatet</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="stili.css" rel="stylesheet" type="text/css">
</head>

<body>

<form name="formKerko" id="formKerko" method="get" action="rezultatet.php" target="_self">
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
     
if (isset($_GET['Shikoje']) | isset($_GET['showres'])) {
     $date = $_GET[viti]."-".$_GET[muaji]."-".$_GET[dita];
} else {
     $date = date("Y-m-d");
}

$s = new Skedina(HOST, USER, PASSWORD, DATABASE);
$list = $s->table_vlist($date, "00:00:01");

if (isset($_POST[submit])) {
     foreach ($list as $l) {
          if (($_POST[$l['kodi']."_r45"] != "") & ($_POST[$l['kodi']."_r90"] != "")) {
                echo "Ndeshjes {$l[kodi]} ju vendos rezultati.<br>\n";
                $s->write_results($l['kodi'], $l['ndeshja'], $date, $_POST[$l['kodi']."_r45"], $_POST[$l['kodi']."_r90"]);
               }
     }
}

echo "<form name=\"rezultatet\" action=\"{$_SERVER[PHP_REFERER]}\" method=\"POST\">";
echo "<table cellpadding=\"1\" cellspacing=\"1\">";
echo "<tr>
      <td align=\"center\" bgcolor=\"#000000\">ORA</td>
      <td align=\"center\" bgcolor=\"#000000\">KODI</td>
      <td align=\"center\" bgcolor=\"#000000\">NDESHJA</td>
      <td align=\"center\" bgcolor=\"#000000\">Rez. 45</td>
      <td align=\"center\" bgcolor=\"#000000\">Rez. 90</td>
      </tr>";
foreach ($list as $t) {
     if ($s->read_results($t['kodi'], $date) === NULL) {
             echo "<tr>";
             echo "<td bgcolor=\"#C0C0C0\"><font color=\"#000000\">{$t[ora]}</font></td>
                   <td bgcolor=\"#999999\">{$t[kodi]}</td>
                   <td bgcolor=\"#C0C0C0\"><font color=\"#000000\">{$t[ndeshja]}</font></td>
                   <td bgcolor=\"#CFBA94\"><input type=\"text\" name=\"{$t[kodi]}_r45\" size=\"3\"></td>
                   <td bgcolor=\"#CFBA94\"><input type=\"text\" name=\"{$t[kodi]}_r90\" size=\"3\"></td>";
             echo "</tr>";
     }
}
echo "</table>";
echo "<input type=\"submit\" name=\"submit\" id=\"submit\" value=\"vendos\">";
echo "</form>";
?>
<p>
<form name="hh" id="hh" action="<?php echo $_SERVER[PHP_SELF]; ?>" method="GET">
<input name="dita" type="text" id="dita" value="<?php echo date("d"); ?>" size="2" maxlength="2">
<input name="muaji" type="text" id="muaji" value="<?php echo date("m"); ?>" size="2" maxlength="2">
<input name="viti" type="text" id="viti" value="<?php echo date("Y"); ?>" size="4" maxlength="4">
<input type="submit" name="showres" id="showres" value="showres">
</form>
</p>
<?php
if (isset($_GET['showres'])) {
//print "<table border=\"1\" cellspacing=\"1\">";
foreach ($list as $l) {
$r = $s->read_results($l['kodi'], $date);
$s->burn($l['kodi'], $date, $r);
//print "<tr>";
//print "<td>{$l[kodi]}</td><td>$r[0]</td><td>$r[0]</td><td>$r[1][0]</td><td>$r[1][1]</td>";
//print "</tr>";
print "<pre>";
print_r($r);
print "</pre>";
}
//print "</table>";
}
?>
</body>
</html>
