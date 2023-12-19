<?php
     require_once("settings.php");
     require_once("Skedina.php");
     require_once("Login.php");
     
     //Instantiate the object
     $l = new Login(HOST, USER, PASSWORD, DATABASE);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Vendos Skedinë</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="stili.css" rel="stylesheet" type="text/css" />

<script language="JavaScript" type="text/javascript">
<?php
     echo $l->table_to_array("skedina_ditore");
?>
function changeChildValue(sender, childname, index) {
     if (document.getElementById(sender+index).value != '') {
          document.getElementById(childname+index).value = ndeshjet[document.getElementById(sender+index).value];
     }
}

document.getElementById('nothere').value = 'value1';
</script>

</head>

<?php
//Security check
if (($_COOKIE[COOKIE_PREF."_rights"] > L_CLIENT) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
     die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
}

//Get the uid for the user who is submitting
if (isset($_COOKIE[COOKIE_PREF."_uname"]))
$uid = $l->getUserinfo($_COOKIE[COOKIE_PREF."_uname"]); //Get the uid for the current user

//Get the serial of the current receipt
$serno = $l->next_serial();

//Get the current datetime
$datetime = date('d/m/Y H:i:s');
$sql_datetime = date('Y/m/d H:i:s');
?>

<body>
<table width="500"  border="0" cellpadding="0" cellspacing="1" class="koka">
  <tr>
    <td width="100">Pika</td>
    <td><?php echo "<strong>".$uid."</strong> ({$_COOKIE[COOKIE_PREF.'_uname']})"; ?></td>
  </tr>
  <tr>
    <td width="100">SKEDINA_NR. </td>
    <td><?php echo $serno ?></td>
  </tr>
  <tr>
    <td width="100">DATA:</td>
    <td><font color="#FFFF00"><?php echo $datetime; ?></font></td>
  </tr>
</table>
<br /><br />
<div id="nothere">value0</div>
<?php
if (!$_POST[Shikoje] == 'Skedinë e re') {
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left">
     <form action="<?php echo $_SERVER[PHP_SELF]; ?>" method="post" name="vendos_skedinen" id="vendos_skedinen">
      <table width="400" border="0" cellspacing="0" cellpadding="0">
    <tr class="shenim">
      <td>Kodi</td>
      <td>Ndeshja</td>
      <td>Loja</td>
      <td>Koef.</td>
    </tr>
    <?php
          for ($i=0;$i<=9;$i++) {
               echo '
     <tr>
      <td><input name="txtKodi'.$i.'" type="text" id="txtKodi'.$i.'" size="4"  onBlur="changeChildValue(\'txtKodi\', \'txtNdeshja\', \''.$i.'\')"></td>
      <td><input name="txtNdeshja'.$i.'" type="text" id="txtNdeshja'.$i.'" readonly="true"></td>
      <td><input name="txtLoja'.$i.'" type="text" id="txtLoja'.$i.'" size="4"></td>
      <td><input name="txtKoef'.$i.'" type="text" id="txtKoef'.$i.'" size="4" readonly="true"></td>
    </tr>';
          }
    ?>
  </table>
  
      <br />
      <input name="Shikoje" type="submit" id="Shikoje" value="Skedinë e re" />
      <input name="Clear" type="reset" id="Clear" value="Pastro" />
     </form>
    </td>
    <td align="right">
    
    <!-- Tabela e llojeve te lojrave //-->
     <table width="150"  border="0" cellpadding="2" cellspacing="1">
     <?php
      $list = $l->get_game_shortcuts();
     for ($i=0; $i<count($list); $i++) {
          echo "<tr>\n";
            echo "<td class=\"tabela_lojrave1\"><strong>{$list[$i][short]}</strong></td>\n";
            echo "<td class=\"tabela_lojrave2\">{$list[$i][name]}</td>\n";
          echo "</tr>\n";
     }
     ?>
     </table>

    </td>
  </tr>
</table>

<?php
}
?>

<?php

     if ($_POST[Shikoje] == 'Skedinë e re') {

          $i = 0;
          for ($i=0;$i<10;$i++) {
               foreach ($_POST as $k=>$v) {
                    if (strstr($k, "txt")) {
                         if (substr($k, strlen($k)-1) == $i) {
                              $row[$i][] = $v;
                         }
                    }
               }
//               $row[$i][3] = $l->get_val($row[$i][0], $row[$i][2]);
          }

          //Trim the array empty end
          for ($i=0;$i<10;$i++) {
               if ($row[$i][0] == "") {
                    unset($row[$i]);
               } else {
                    $row[$i][3] = $l->get_val($row[$i][0], $row[$i][2]);
               }
          }

          print "<pre>";
          print_r($row);
          print "</pre>";
     }
?>
</body>
</html>
