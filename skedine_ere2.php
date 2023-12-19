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
     $s = new Skedina(HOST, USER, PASSWORD, DATABASE);
     $uid = $l->get_uid($_COOKIE[COOKIE_PREF."_uname"]);
     
     function u_name() {
          return $_COOKIE[COOKIE_PREF."_uname"];
     }
     
     if ($_GET[act] == 'rm_entry') {
          $b->drop_entry(stripslashes($_GET[id]), $uid);
     } else if ($_GET[act] == 'shtoje') {
          $kodi = stripslashes(abs($_GET[kodi]));
          $koe = $l->get_val($kodi, stripslashes(abs($_GET[loja])));
          
          $n_ora = substr($b->time_f_code($kodi), 0, 5);
          $n_emri = $b->name_f_code($kodi);
          
          $b->add_entry($uid, $kodi, $n_ora, $n_emri, stripslashes(abs($_GET[loja])), $koe); //Add a row to the table
     } else if ($_GET[act] == 'vendose') {
          $b->update_sum(stripslashes(abs($_GET[shuma])), $uid);
     } else if ($_GET[act] == 'submit') {
          $b->update_sum(stripslashes(abs($_GET[shuma])), $uid);
          $st = $b->finish_ticket($uid);
          if ($st[0] == -1) {
               echo "<script>popUpWindow('waitstate.php?ticket={$st[1]}', 100, 100, 550, 400);</script>";
//               $actualcredit = $l->get_user_credit($uid);
//               $ticketsum = $b->ticket_sum($st[1], date("Y-m-d"), $uid);
//               $l->set_user_credit($actualcredit-$ticketsum, $uid);
          } else if ($st[0] == 1) {
               echo "<script>popUpWindow('shiko_skedine.php?ticket={$st[1]}&print=this', 100, 100, 550, 400);</script>";
               $actualcredit = $l->get_user_credit($uid);
               $ticketsum = $b->ticket_sum($st[1], date("Y-m-d"), $uid);
               $l->set_user_credit($actualcredit-$ticketsum, $uid);
          }
     }
     
?>

<script>
function dealer(evt) {
     
     if (evt.keyCode == 107) {
          document.put_sum.shuma.focus();
          document.put_sum.shuma.value = '';
          document.add_entry.kodi.value = '';
     }

}
</script>
</head>

<body
onload="(document.add_entry.kodi.value.length > 3) ? document.add_entry.loja.focus() : document.add_entry.kodi.focus()"
onkeyup="dealer(event)">

<!-- @@@ Start of big table -->
<table width="100%" border="1">
<tr>
<td valign="top">

<!-- @@@ Start of top info table //-->
<table id="ticketinfotable" width="300" border="0" cellspacing="1" cellpadding="0">
  <tr>
    <td width="100" bgcolor="#333333">Data/Ora:</td>
    <td bgcolor="#4E4E4E"><?php echo $b->datenow(); ?></td>
  </tr>
  <tr>
    <td bgcolor="#333333">Operatori: </td>
    <td bgcolor="#4E4E4E"><?php echo u_name(); ?></td>
  </tr>
  <tr>
    <td bgcolor="#333333">Nr. Bilete: </td>
    <td bgcolor="#4E4E4E"><?php echo $b->nr_b($uid); ?></td>
  </tr>
</table>
<!-- @@@ End of top info table //-->
<br><br>

<form name="add_entry" id="add_entry" method="get" action="<?php echo $_SERVER[PHP_SELF]; ?>">
  <table width="80%"  border="1" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top">Kodi:
          <input name="kodi" id="kodi" type="text" size="5" value="<?php if (($_GET[act] == 'shtoje') & (strlen($_GET['loja']) > 0)) echo ''; else echo $_GET[kodi]; ?>"></td>
      <td valign="top">Loja:
          <?php
               $list= $l->get_game_shortcuts();
               $quotas = $s->select_game_quotes_W($_GET[kodi], date("Y-m-d"));
               echo "<select name=\"loja\" id=\"loja\" onkeydown=\"(event.keyCode == 13) ? document.add_entry.act.focus() : document.add_entry.loja.focus() \">";
               echo "<option value=\"\" selected>    </option>";
               for ($i=0; $i<count($list); $i++) {
                    echo "<option value=\"{$list[$i][short]}\">{$list[$i][short]}___{$list[$i][name]}(".$quotas[str_replace(array(" ", "+", "/"), array("_","p",""), $list[$i][name])].")</option>";
               }
               echo "</select>";
          ?>
      <td valign="top"><input name="act" type="submit" id="act" value="shtoje" onclick="window.reload()"></td>
    </tr>
  </table>
</form>
<br><br>
<?php
//////////////  START IF UNFINISHED
if ($b->check_not_finished($uid) == true) {
?>
<div id="unfinished_ticket">
<table id="ticketbody" width="100%"  border="0" cellspacing="1" cellpadding="0">
  <tr id="tickethdr">
    <td>NR.</td>
    <td>ORA</td>
    <td>KODI</td>
    <td>NDESHJA</td>
    <td>LOJA</td>
    <td>KOEFICENTI</td>
    <td>&nbsp;</td>
  </tr>
  <tr>
  <?php
     $total_coe = 1;
     $i = 1;
     foreach ($b->select_not_finished($uid) as $n) {
     echo "<tr id=\"ticketrow\">\n";
     echo " <td>".$i++."</td>";
     echo " <td>".substr($b->time_f_code($n[entry_code]), 0, 5)."</td>\n";
     echo " <td>".$n[entry_code]."</td>\n";
     echo " <td>".$b->name_f_code($n[entry_code])."</td>\n";
     echo " <td>".$l->get_col_name($n[game_type])."</td>\n";
     echo " <td>".$n[game_coe]."</td>\n";
     echo " <td>[ <a href=\"{$_SERVER[PHP_SELF]}?act=rm_entry&id=".$n[id]."\">Shuaj</a> ]</td>\n";
     echo "</tr>\n";
     $total_coe *= $n[game_coe];
     }
  ?>
  </tr>
</table>
<br><br><hr>
<table id="ticketsettings" width="200"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100">Shuma</td>
    <td><div id="somme"><?php echo $b->ticket_sum($b->nr_b($uid), date("Y-m-d"), $uid); ?></div></td>
  </tr>
  <tr>
    <td>Koeficenti total </td>
    <td><?php echo $b->ticket_coe($b->nr_b($uid), date("Y-m-d"), $uid); ?></td>
  </tr>
  <tr>
    <td>Fitimi total </td>
    <td><?php echo $b->ticket_winning($b->nr_b($uid), date("Y-m-d"), $uid); ?></td>
  </tr>
</table>
<br>
<form action="<?php echo $_SERVER[PHP_SELF];?>?act=updatesum" method="get" name="put_sum" id="put_sum">
  [ Shuma: <input name="shuma" type="text" id="shuma" size="10" onfocus="this.value=''">
  <input name="act" type="submit" id="act" value="submit">
  ]
</form>
<br>
[ <a href="#" onClick="refresh()">Rifresko</a> ] [ <a href="<?php echo $_SERVER[PHP_SELF]; ?>?act=submit">Aprovoje skedin&euml;n</a> ] [ <a href="<?php echo $_SERVER[PHP_SELF]; ?>?act=erase">Shuaje skedin&euml;n</a> ]
</div>
<?php
/////////////////  END IF UNFINISHED
}
?>
<br><br>
</td>

<td valign="top" align="right" width="200">
<table width="200">
<tr><td valign="top">
     <div id="kerkimi">
          <form name="formKerko" id="formKerko" method="get" action="shiko_skedine.php" target="_self">
               <p>Shiko bilet&euml;n me num&euml;r
               <input name="ticket" type="text" size="4" maxlength="4">
               <br>
               Data:
               <input name="dita" type="text" id="dita" value="<?php echo date("d"); ?>" size="2" maxlength="2">
               <input name="muaji" type="text" id="muaji" value="<?php echo date("m"); ?>" size="2" maxlength="2">
               <input name="viti" type="text" id="viti" value="<?php echo date("Y"); ?>" size="4" maxlength="4">
               <br>
               <input name="Shikoje" type="submit" id="Shikoje" value="Shikoje">
               </p>
          </form>
     </div>
</td></tr>

<tr><td valign="top">
     <div id="anullimi">
          <form name="formAnullo" method="get" action="shiko_skedine.php?anullo=this" target="_self">
               <p>  Anullo bilet&euml;n:
               <input name="ticket" type="text" id="nrBilete" size="4">
               <input name="anullo" type="submit" id="Anulloje" value="Anulloje">
               </p>
          </form>
     </div>
</td></tr>
</table>
</td>

<td valign="top" width="150">
    <!-- Tabela e llojeve te lojrave //-->
     <div id="table_gametypes" align="right">
     <table width="150"  border="0" cellpadding="2" cellspacing="1">
     <?php
      $list = $l->get_game_shortcuts();
      $k = 0;
      for ($i=0; $i<count($list); $i++) {
          if ($k % 2 == 0) echo "<tr>\n";
            echo "<td class=\"tabela_lojrave1\"><strong>{$list[$i][short]}</strong></td>\n";
            echo "<td class=\"tabela_lojrave2\">{$list[$i][name]}</td>\n";
          if ($k % 2 == 0) echo "</tr>\n";
          $k++;
      }
     ?>

     </table>
     </div>
</td></tr></table>
<div id="daily_message">
<?php echo $s->get_last_message(); ?>
</div>

</body>
</html>
