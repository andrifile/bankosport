<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Vendos skedin&euml;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="pragma" content="no-cache">
   <!-- Main stylesheet on top -->
   <link rel="stylesheet" type="text/css" href="stili.css" media="screen" />
   <!-- Print only, on bottom -->
   <link rel="stylesheet" type="text/css" href="print.css" media="print" />
<script language="JavaScript" type="text/JavaScript" src="scripts.js"></script>
</head>

<body>
<?php //BEGIN HEADER BLOCK
     require_once("settings.php");
     require_once("Login.php");
     require_once("Bileta.php");

     //Security check
     if (($_COOKIE[COOKIE_PREF."_rights"] > L_CLIENT) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
          die ("Nuk keni të drejtë të vizitoni këtë faqe.<br>");
     }

     //Instantiate a new child of the base class
     $l = new Login(HOST, USER, PASSWORD, DATABASE);
     $uid = $l->get_uid($_COOKIE[COOKIE_PREF."_uname"]);
     $b = new Bileta(HOST, USER, PASSWORD, DATABASE);

               if (isset($_GET[dita]) & isset($_GET[viti]))
                    $date = $_GET[viti]."-".$_GET[muaji]."-".$_GET[dita];
               else
                    $date = date("Y-m-d");

     if (isset($_GET[op]) & isset($_GET[uid])) {
          if ($_COOKIE[COOKIE_PREF."_rights"] <= L_ADMIN) {

               $flag = true;
               $list = $b->select_ticket($_GET[ticket], $date, $_GET[op]);
          }
     } else {
          $flag = false;
          $list = $b->select_ticket($_GET[ticket], $date, $uid);
     }
     
     if ($list == "#!")
          die("Nuk ka asnjë biletë të përfunduar me këtë numër.");

     if (isset($_GET['reset']) & ($flag == true)) {
          $b->set_ticket_sum($_POST[txtNewSum], $_GET[ticket], $date, $_GET[op]);
          $b->set_ticket_coe($_POST[txtNewCoe], $_GET[ticket], $date, $_GET[op]);
          $b->set_ticket_winning($_POST[txtNewWin], $_GET[ticket], $date, $_GET[op]);
          echo 'Shuma e re: '.$_POST[txtNewSum].'<br>';
          echo 'Koeficenti i ri: '.$_POST[txtNewCoe].'<br>';
          echo 'Fitimi i ri: '.$_POST[txtNewWin].'<br>';
     }


?>

<table width="100%" border="0" cellspacing="1" cellpadding="1">
  <p><br></p>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
    <?php
          if ($flag==false) {
    ?>
     <table id="tickethead" width="300" border="0" cellspacing="1" cellpadding="0">
          <tr><td>Nr. bilete: </td><td><?php echo $_GET[ticket]; ?></td></tr>
          <tr><td>Operatori:</td><td><?php echo $_COOKIE[COOKIE_PREF."_uname"]; ?></td></tr>
          <tr><td>Data:</td><td><?php echo $b->ticket_date($_GET[ticket], $date, $uid); ?></td></tr>
     </table>
     <?php
     } else if ($flag==true) {
     ?>
     <table id="tickethead" width="300" border="0" cellspacing="1" cellpadding="0">
          <tr><td>Nr. bilete: </td><td><?php echo $_GET[ticket]; ?></td></tr>
          <tr><td>Operatori:</td><td><?php echo $_GET[op]; ?></td></tr>
          <tr><td>Data:</td><td><?php echo $b->ticket_date($_GET[ticket], $date, $_GET[op]); ?></td></tr>
     </table>

     <?php
     }
     ?>
    </td>
  </tr>
  <tr>
    <td>
    <!-- ENTRIES TABLE //-->
    <table id="ticketbody" width="500" border="0" cellspacing="1" cellpadding="1">
  <tr><td colspan="6"><br><hr><br></td></tr>
  <tr id="tickethdr">
    <td><center>NR.</center></td>
    <td><center>ORA</center></td>
    <td><center>KODI</center></td>
    <td><center>NDESHJA</center></td>
    <td><center>LOJA</center></td>
    <td><div align="right">KOEF</div></td>
  </tr>
  <?php
     $total_coe = 1;
     $i = 1;

     if (count($list) > 0) {
     foreach ($list as $n) {
     echo "<tr id=\"ticketrow\">\n";
     echo " <td width=\"40\"><center>".$i++."</center></td>";
     echo " <td width=\"60\"><center>".substr($n[entry_time], 0, 5) ."</center></td>\n";
     echo " <td width=\"40\"><center>".$n[entry_code]."</center></td>\n";
     echo " <td><center>".$n[entry_name]."</center></td>\n";
     echo " <td width=\"30\"><center>".$l->get_col_name($n[game_type])."</center></td>\n";
     echo " <td><div align=\"right\">".$n[game_coe]."</div></td>\n";
     echo "</tr>\n";
     $total_coe *= $n[game_coe];
     }
     }
  ?>
  <tr><td colspan="6"><br><hr></td></tr>
  <?php
     if ($flag == false) {
  ?>
  <tr>
    <td colspan="5"><div align="right">Koeficenti total: </div></td><td><div align="right"><?php echo $b->ticket_coe($_GET[ticket], $date, $uid); ?></div></td>
  </tr>
  <tr>
    <td colspan="5"><div align="right">Shuma e luajtur: </div></td><td><div align="right"><?php echo number_format($b->ticket_sum($_GET[ticket], $date, $uid)); ?></div></td>
  </tr>
  <tr>
    <td colspan="5"><div align="right">Fitimi i mundshem: </div></td><td><div align="right"><?php echo number_format($b->ticket_winning($_GET[ticket], $date, $uid)); ?></div></td>
  </tr>
  
  <?php
  } else if ($flag == true) {
  ?>
  <tr>
    <td colspan="5"><div align="right">Koeficenti total: </div></td><td><div align="right"><?php echo $b->ticket_coe($_GET[ticket], $date, $_GET[op]); ?></div></td>
  </tr>
  <tr>
    <td colspan="5"><div align="right">Shuma e luajtur: </div></td><td><div align="right"><?php echo number_format($b->ticket_sum($_GET[ticket], $date, $_GET[op])); ?></div></td>
  </tr>
  <tr>
    <td colspan="5"><div align="right">Fitimi i mundshem: </div></td><td><div align="right"><?php echo number_format($b->ticket_winning($_GET[ticket], $date, $_GET[op])); ?></div></td>
  </tr>

  <?php
  }
  ?>
</table>
</td>
  </tr>
  <tr>
  <td>
<br>
<br>


<?php
if ((isset($_GET[dita]) & isset($_GET[muaji]) & isset($_GET[viti]))
     | (isset($_GET[op]) & isset($_GET[uid]))
     & ($_COOKIE[COOKIE_PREF."_rights"] <= L_EDITOR))  {
     
     $date = $_GET[viti]."-".$_GET[muaji]."-".$_GET[dita];
     $status = $b->get_ticket_status($_GET[ticket], $date, $uid);
     if ($flag == true) {
     echo '<form id="newsum" name="newsum" method="post" action="'.$_SERVER['PHP_SELF'].'?reset=this&ticket='.$_GET[ticket].'&op='.$_GET[op].'&viti='.$_GET[viti].'&muaji='.$_GET[muaji].'&dita='.$_GET[dita].'&uid='.$uid.'">
            Shuma <input name="txtNewSum" type="text" value="'.$b->ticket_sum($_GET[ticket], $date, $_GET[op]).'" id="txtNewSum" size="5" />
            Koe.  <input name="txtNewCoe" type="text" value="'.$b->ticket_coe($_GET[ticket], $date, $_GET[op]).'" id="txtNewCoe" size="5" />
            Fit.  <input name="txtNewWin" type="text" value="'.$b->ticket_winning($_GET[ticket], $date, $_GET[op]).'" id="txtNewSum" size="10" />
            <input name="btnRivendos" type="submit" id="btnRivendos" value="Rivendos vlerat" />
           </form>';
     }
          if ($status == STA_BROKEN)
               echo "<div id=\"mesazhe_skedine\">Me sa duket ka nj&eum; problem me k&euml;t&euml; bilet&euml;. Duhet t&euml; kontaktoni me administratorin.</div>";
//          if ($status == STA_WAITING)
//               echo "<div id=\"mesazhe_skedine\">Prisni deri sa bileta të pranohet.<br>Kontrollojeni m&euml; von&euml; k&euml;t&euml; bilet&euml; nga lista e biletave ditore.</div>";
          if ($status == STA_REFUSED)
               echo "<div id=\"mesazhe_skedine\">Bileta është refuzuar.</div>";
          if ($status == STA_REMOVED)
               echo "<div id=\"mesazhe_skedine\">Bileta është e anulluar.</div>";
          if ($status == STA_WINNING)
               echo "<div id=\"mesazhe_skedine\"><strong>Bileta është fituese.</div>";
          if ($status == STA_LOSER)
               echo "<div id=\"mesazhe_skedine\"><strong>Bileta është e djegur.</div>";
          if (($status == STA_ACCEPTED) & (count($list) == 1)) {
               //echo "<script>printPage(0)</script>";
               echo "<div id=\"mesazhe_skedine\"><strong>Bileta &euml;sht&euml; pranuar.</strong></div>";
          }

} else if (isset($_POST['Po'])) {
     echo $b->cancel_ticket($_GET[ticket], $uid);
} else if (isset($_GET['print']) && ($_GET['print'] == "this")) {
     echo '<script language="JavaScript" type="text/JavaScript">printPage(0);</script>';
} else if (isset($_GET['anullo']) && $_GET['anullo'] == "Anulloje") {
     if ($b->is_canceled($_GET[ticket], $uid))
          die("<div id=\"mesazhe_skedine\">Kjo bilet&euml; &euml;sht&euml; e anulluar.<br></div>");
?>
<table width="300" border="1" cellpadding="1" cellspacing="1" bordercolor="#FFFFFF">
  <tr>
    <td><form name="form1" method="post" action="<?php echo $_SERVER[PHP_SELF]."?ticket=".$_GET[ticket]; ?>">
      <table width="500" border="0" cellspacing="1" cellpadding="1">
        <tr>
          <td colspan="2">Doni ta anulloni k&euml;t&euml; bilet&euml;? </td>
        </tr>
        <tr>
          <td align="center"><input name="Po" type="submit" id="Po" value="Po"></td>
          <td align="center"><input name="Jo" type="submit" id="Jo" value="Jo" onClick="this.Close();"></td>
        </tr>
      </table>
    </form></td>
  </tr>
</table>
<?php
}
?>
</body>
</html>
