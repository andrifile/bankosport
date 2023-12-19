<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="stili.css" rel="stylesheet" type="text/css">
</head>

<body>

<?php

require_once("settings.php");
require_once("Skedina.php");


//Security check
if (($_COOKIE[COOKIE_PREF."_rights"] > L_EDITOR) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
     die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
}


$s = new Skedina(HOST, USER, PASSWORD, DATABASE);
$list = $s->table_vlist(date("Y-m-d"), "01:00:00");

echo "<table>";
echo "<tr><td>KODI</td><td>NDESHJA</td><td>TOTAL</td></tr>";
foreach ($list as $l) {
echo "<tr><td>".$l['kodi']."</td><td>".$l['ndeshja']."</td></tr>";

}
echo "</table>";
?>

</body>
</html>
