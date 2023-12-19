<?php
@require_once("settings.php");
@require_once("Login.php");

$l = new Login(HOST, USER, PASSWORD, DATABASE);
$uid = $l->get_uid($_COOKIE[COOKIE_PREF."_uname"]);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Info Frame</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="refresh" content="12;info.php" />
<link href="stili.css" rel="stylesheet" type="text/css" />
</head>

<body id="hdr_body">
<?php
if (!isset($_COOKIE[COOKIE_PREF."_uname"]) OR !isset($_COOKIE[COOKIE_PREF."_rights"]) OR $_COOKIE[COOKIE_PREF."_rights"] == 5)
     die("<body></html>");
?>
<div id="hdr_info">
<table width="100%"  border="0" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF">
  <tr>
    <td width="50%" align="right" bgcolor="#888888">Operatori:</td>
    <td align="right" bgcolor="#CCCCCC"><font color="#FF0000"><strong><?php echo $l->get_fullname($_COOKIE[COOKIE_PREF."_uname"]);?></strong></font></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#888888">Kredit:</td>
    <td align="right" bgcolor="#CCCCCC"><font color="#000000"><?php echo $l->get_user_credit($uid); ?></font></td>
  </tr>
  <tr>
    <td align="right" bgcolor="#888888"> </td>
    <td align="right" bgcolor="#CCCCCC"><a href="changepass.php" target="mainFrame"><font color="#000000">[ Ndrysho fjal&euml;kalim ]</font></a></td>
  </tr>
</table>
</div>
</body>
</html>
