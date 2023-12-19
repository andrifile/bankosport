<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Untitled Document</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="stili.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
@require_once("settings.php");
@require_once("Skedina.php");

//Security check
if (($_COOKIE[COOKIE_PREF."_rights"] > L_CLIENT) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
     die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
}

$s = new Skedina(HOST, USER, PASSWORD, DATABASE);

if (isset($_POST[vendos])) {
     foreach ($_POST as $k=>$v) {
          if (strstr($k, "kuota_")) {
               if (strlen($v) > 0) {
                    if (is_numeric($v)) {
                         var_dump($s->set_quota($_GET[id], substr($k, 6), $v));
                    }
               }
          }
     }
}

if (isset($_GET[act])) {
     if ($_GET[act] == 'view') {
          $id = mysql_real_escape_string($_GET[id]);
          $list = $s->select_game_quotes($id);
          echo "<table>";
          foreach ($list as $k=>$v) {
               echo "<tr>";
               echo "<td bgcolor=\"#777777\"><strong>".$k."</strong></td>";
               echo "<td bgcolor=\"#999999\">".$v."</td>";
               echo "</tr>";
          }
          echo "</table>";
     } else if ($_GET[act] == 'edit') {
          $id = mysql_real_escape_string($_GET[id]);
          $list = $s->select_game_quotes($id);
          echo "<form action=\"{$_SERVER[PHP_SELF]}?id={$_GET[id]}&act=edit\" method=\"POST\"><table>";
          $i = 0;
          foreach ($list as $k=>$v) {
               if ($i % 9 == 0)
                    echo "<tr>\n";
               echo "<td bgcolor=\"#777777\"><strong>".$k."</strong></td>\n";
               echo "<td bgcolor=\"#006699\">".$v."</td>\n";
               echo "<td bgcolor=\"#AAAAAA\"><input type=\"text\" name=\"kuota_{$k}\" id=\"kuota_{$k}\" value=\"\" size=\"6\" ></td>\n";
               if ($i % 6 == 0)
                    echo "</tr><tr>\n";
               $i+=3;
          }
          echo "</table>";
          echo "<input type=\"submit\" name=\"vendos\" value=\"vendos\"><br>";
          echo "[<a href=\"{$_SERVER[PHP_SELF]}?act=block&id={$_GET[id]}\"> MBYLL NDESHJEN </a>]<br>";
          echo "[<a href=\"{$_SERVER[PHP_SELF]}?act=allow_sin&id={$_GET[id]}\"> LEJO TEKE </a>]<br>";
          echo "[<a href=\"{$_SERVER[PHP_SELF]}?act=unblock&id={$_GET[id]}\"> AKTIVIZO NDESHJEN </a>]<br>";
          echo "[<a href=\"{$_SERVER[PHP_SELF]}?act=disallow_sin&id={$_GET[id]}\"> MOS LEJO TEKE </a>]<br>";
          echo "[<a href=\"{$_SERVER[PHP_SELF]}?act=remove&id={$_GET[id]}\"> SHUAJ NDESHJEN </a>]<br>";
          echo "</form>";
     } else if ($_GET[act] == 'block') {
          if ($s->update_game_status($_GET[id], 1, 0))
               echo "Ndeshja u bllokua.\n";
     } else if ($_GET[act] == 'allow_sin') {
          if ($s->update_game_status($_GET[id], 2, 1))
               echo "Ndeshja do t&euml; lejohet teke.\n";
     } else if ($_GET[act] == 'disallow_sin') {
          if ($s->update_game_status($_GET[id], 2, 0))
               echo "Ndeshja nuk do t&euml; lejohet m&euml; teke.\n";
     } else if ($_GET[act] == 'unblock') {
          if ($s->update_game_status($_GET[id], 1, 1))
               echo "Ndeshja u aktivizua.\n";
     } else if ($_GET[act] == 'remove') {
          if ($s->remove_game_id($_GET[id]))
               echo "Ndeshja u hoq.\n";
     }


}
?>
</body>
</html>
