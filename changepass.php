<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Ndrysho fjal&euml;kalimin</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="stili.css" rel="stylesheet" type="text/css" />
</head>

<body>
<?php
@require_once("settings.php");
@require_once("Login.php");

//Security check
if (($_COOKIE[COOKIE_PREF."_rights"] > L_CLIENT) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
     die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
}

if (isset($_POST['Ndryshoje'])) {
     $l = new Login(HOST, USER, PASSWORD, DATABASE);
     $uid = $l->get_uid($_COOKIE[COOKIE_PREF."_uname"]);
     $oldpass = mysql_real_escape_string($_POST['oldpass']);
     $newpass1 = mysql_real_escape_string($_POST['newpass1']);
     $newpass2 = mysql_real_escape_string($_POST['newpass2']);

     if ($newpass1 == $newpass2) {
          if ($l->set_passwd($oldpass, $newpass1, $uid)) {
               die("<p>Fjal&euml;kalimi u ndryshua me sukses.</p>");
          }
     } else {
          echo "<p>Fjal&euml;kalimet nuk jan&euml; nj&euml;lloj.</p>";
     }
}
?>
<form action="<?php echo $_SERVER[PHP_SELF]; ?>" method="post" name="ndryshoPass" target="_self" id="ndryshoPass">
  <table width="500" border="0" cellspacing="1" cellpadding="1">
    <tr>
      <td>Fjal&euml;kalimi i vjet&euml;r: </td>
      <td><input name="oldpass" type="text" id="oldpass" /></td>
    </tr>
    <tr>
      <td>Fjal&euml;kalimi i ri </td>
      <td><input name="newpass1" type="text" id="newpass1" /></td>
    </tr>
    <tr>
      <td>Fjal&euml;kalimi i ri p&euml;rs&euml;ri: </td>
      <td><input name="newpass2" type="text" id="newpass2" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="Ndryshoje" type="submit" id="Ndryshoje" value="Ndryshoje" /></td>
    </tr>
  </table>
</form>
<p>&nbsp;</p>
</body>
</html>
