<?php
@require_once("settings.php");
@require_once("Login.php");

function user_panel($level) {
echo '<div align="right"><table border="0" cellspacing="0" cellpadding="0">
  <tr>
  <td><a href="leftframe.php" target="leftFrame"><img src="images/panel/lista_e_ndeshjeve.png" border="0"></a></td>';
  $tablestr = "";
     switch($level) {
     case L_ADMIN:
          $tablestr .= '    <td><a href="admin_skedina.php" target="mainFrame"><img src="images/panel/sqaro.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="inventari.php" target="mainFrame"><img src="images/panel/inventari.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="klient_skedina.php" target="mainFrame"><img src="images/panel/bileta_ditore.png" border="0"></a></td>';
          $tablestr .= '    </tr><tr><td><a href="ditore.php" target="mainFrame"><img src="images/panel/skedina_ditore.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="perdorues.php" target="mainFrame"><img src="images/panel/perdoruesit.png" border="0"></td>';
          $tablestr .= '    <td><a href="stats.php" target="mainFrame"><img src="images/panel/statistikat.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="skedine_ere2.php" target="mainFrame"><img src="images/panel/skedine_e_re.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="index.php?logout=1" target="_parent"><img src="images/panel/cidentifikohu.png" border="0"></a></td>';
          break;
     case L_BOSS:
          $tablestr .= '    <td><a href="admin_skedina.php" target="mainFrame"><img src="images/panel/sqaro.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="inventari.php" target="mainFrame"><img src="images/panel/inventari.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="klient_skedina.php" target="mainFrame"><img src="images/panel/bileta_ditore.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="stats.php" target="mainFrame"><img src="images/panel/statistikat.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="index.php?logout=1" target="_parent"><img src="images/panel/cidentifikohu.png" border="0"></a></td>';
          break;
     case L_EDITOR:
          $tablestr .= '    <td><a href="admin_skedina.php" target="mainFrame"><img src="images/panel/sqaro.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="inventari.php" target="mainFrame"><img src="images/panel/inventari.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="klient_skedina.php" target="mainFrame"><img src="images/panel/bileta_ditore.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="ditore.php" target="mainFrame"><img src="images/panel/skedina_ditore.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="index.php?logout=1" target="_parent"><img src="images/panel/cidentifikohu.png" border="0"></a></td>';
          break;
     case L_CLIENT:
          $tablestr .= '    <td><a href="inventari.php" target="mainFrame"><img src="images/panel/inventari.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="klient_skedina.php" target="mainFrame"><img src="images/panel/bileta_ditore.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="skedine_ere2.php" target="mainFrame"><img src="images/panel/skedine_e_re.png" border="0"></a></td>';
          $tablestr .= '    <td><a href="index.php?logout=1" target="_parent"><img src="images/panel/cidentifikohu.png" border="0"></a></td>';
          break;
     default:
          $tablestr .= '    <td><a href="index.php?logout=1" target="_parent"><img src="images/panel/cidentifikohu.png" border="0"></a></td>';
          break;
     case L_NONE:
          break;
     }
  echo $tablestr;
echo '  </tr>
</table></div>';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Menuja</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="pragma" content="no-cache" />
<link href="stili.css" rel="stylesheet" type="text/css" />
</head>

<body id="hdr_body">
<?php
if (!isset($_COOKIE[COOKIE_PREF."_uname"]) OR !isset($_COOKIE[COOKIE_PREF."_rights"]) OR $_COOKIE[COOKIE_PREF."_rights"] == 5) {
     die("</body></html>");
} else {
     user_panel($_COOKIE[tiranabet_rights]);
}
?>

</body>
</html>
