<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Vendos skedin&euml;</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="pragma" content="no-cache">
<link href="stili.css" rel="stylesheet" type="text/css" />
<!-- per te shfaqur bileten perfundimtare -->
<script language="JavaScript" type="text/JavaScript" src="scripts.js"></script>

<?php
     require_once("settings.php");
     require_once("Skedina.php");
     require_once("Login.php");
     require_once("Bileta.php");

     //Security check
     if (($_COOKIE[COOKIE_PREF."_rights"] > L_CLIENT) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
          die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
     }

     //Instantiate a new child of the base class
     $l = new Login(HOST, USER, PASSWORD, DATABASE);
     //Instantiate a new Bileta class
     $b = new Bileta(HOST, USER, PASSWORD, DATABASE);
     $uid = $l->get_uid($_COOKIE[COOKIE_PREF."_uname"]);
          
     $stat = $b->get_ticket_status($_GET[ticket], date("Y-m-d"), $uid);
     
     if ($stat == -1) {
          echo "<meta http-equiv=\"refresh\" content=\"4;{$_SERVER[PHP_SELF]}?ticket={$_GET[ticket]}\" />";
     } else if ($stat == 1) {
          echo "<script>popUpWindow('shiko_skedine.php?ticket={$_GET[ticket]}&print=this', 100, 100, 550, 400); window.resizeTo(550, 400);</script>";
     }
?>

</head>
<body>
     <?php if ($stat == -1) { echo "<div id=\"mesazhi_prisni\" align=\"center\">JU LUTEM PRISNI P&Euml;R KONFIRMIMIN...</div>"; } ?>
     <?php
     if ($stat == 0)
     {
          echo "<div id=\"mesazhi_prisni\" align=\"center\">BILETA NUK &Euml;SHT&Euml; PRANUAR.</div>";
          echo "<script>window.close();</script>";
     }
     ?>
</body>
</html>
