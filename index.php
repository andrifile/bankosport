<?php
require_once("Login.php");
require_once("settings.php");

ob_start();

$l = new Login(HOST, USER, PASSWORD, DATABASE);

if (isset($_POST[login])) {
     if ($l->testUname(stripcslashes($_POST[txtUsername]), stripcslashes($_POST[txtPassword]))) {
          $l->setcookies(3600*24);
     } else {
          echo "<script>alert('{$_POST[txtUsername]} nuk ekziston ose fjalekalimi eshte i pasakte.');</script>";
     }
} else if (isset($_GET[logout])) {
     $l->destroycookies();
}

ob_flush();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>BankoSport</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="pragma" content="no-cache" />
<?php
if(isset($_POST[login])) echo '<meta http-equiv="refresh" content="2;index.php" />';
if(isset($_GET[logout])) echo '<meta http-equiv="refresh" content="2;index.php" />';
?>
<link href="stili.css" rel="stylesheet" type="text/css" />
</head>

<?php
if (isset($_COOKIE[COOKIE_PREF."_uname"])) {
     unset($_POST);

     if (!isset($_GET[logout])) {
          echo '
               <frameset rows="80,430*" cols="300,*" framespacing="0" frameborder="NO" border="0">
                 <frame src="info.php" />
                 <frame src="menuja.php" />
                 <frame src="leftframe.php" name="leftFrame" scrolling="auto" noresize="1" />
                 <frame src="skedine_ere2.php" name="mainFrame" />
               </frameset>';
     } else {
          echo "<noframes><body>\n
               <span style=\"margin-top: 200px; margin-left: 200px;\"><strong>Ju lutem prisni sa të ridrejtoheni në faqen e duhur...</strong></span>\n
               </body></noframes>\n";
     }
} else {
     if (isset($_POST[login])) {
          die("<span style=\"margin-top: 200px; margin-left: 200px;\"><strong>Ju lutem prisni sa të ridrejtoheni në faqen e duhur...</strong></span>\n");
     }
     echo "<body onload=\"document.login_form.txtUsername.focus()\">";
     require("login_panel.php");
     die("<br /><font color=#FFFF00>Për të vazhduar, ju duhet të identifikoheni më parë.</font></body></html>\n");
}
?>
</html>

