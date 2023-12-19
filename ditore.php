<?php
require_once("Skedina.php");
require_once("settings.php");

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="pragma" content="no-cache" />';

if (array_key_exists("Submit", $_POST)) {
     if ($_POST['choice'] == 'new') {
//          echo '<meta http-equiv="refresh" content="0;ditore.php" />';
     }
     else if ($_POST['choice'] == 'back') {
//          echo '<meta http-equiv="refresh" content="0;mainframe.php" />';
     }

     //Shkruaj vlerat ne databaze
     $s = new Skedina(HOST, USER, PASSWORD, DATABASE);
     $s->to_db($_POST);
//     $s->printout();
}

echo '<title>Skedina Kryesore</title>
<link href="stili.css" rel="stylesheet" type="text/css" />
</head>';

//Security check
if (($_COOKIE[COOKIE_PREF."_rights"] > L_EDITOR) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
     die ("<body>Nuk keni të drejtë të vizitoni këtë faqe.<br /></body></html>");
}

if (!isset($_COOKIE[COOKIE_PREF."_uname"]) OR !isset($_COOKIE[COOKIE_PREF."_rights"]) OR $_COOKIE[COOKIE_PREF."_rights"] == 5)
     die("<body></html>");

?>

<body onLoad="document.ndeshjet.txtkoeList.focus()">
<?php if($_COOKIE[COOKIE_PREF."_rights"] > L_EDITOR) die("Nuk keni te drejta per te vizituar kete faqe."); ?>
<form id="ndeshjet" name="ndeshjet" method="post" action="<?php echo $_SERVER[PHP_SELF] ?>">
  <table width="100%" border="0" cellpadding="1" cellspacing="1" align="center">
    <tr align="center" valign="middle" class="koka">
      <td bgcolor="#CCCCCC">DATA</td>
      <td bgcolor="#CCCCCC">KAMPIONATI</td>
      <td bgcolor="#CC9966">KODI</td>
      <td bgcolor="#CC9966">NDESHJA</td>
      <td bgcolor="#CC9966">ORA</td>
      <td colspan="3" bgcolor="#99CC99">RF</td>
      <td colspan="3" bgcolor="#33CCFF">DSH</td>
      <td colspan="3" bgcolor="#99CC99">RF45</td>
      <td colspan="9" bgcolor="#33CCFF">45/90</td>
      <td colspan="2" bgcolor="#99CC99">SG45</td>
      <td colspan="7" bgcolor="#33CCFF">SGP</td>
    </tr>
    <tr align="center" valign="middle" class="nenkoka">
      <td bgcolor="#CCCCCC"></td>
      <td bgcolor="#CC9966"></td>
      <td bgcolor="#CC9966"></td>
      <td bgcolor="#CC9966"></td>
      <td bgcolor="#CCCCCC"></td>
      <td bgcolor="#99CC99">1</td>
      <td bgcolor="#99CC99">X</td>
      <td bgcolor="#99CC99">2</td>
      <td bgcolor="#33CCFF">1X</td>
      <td bgcolor="#33CCFF">12</td>
      <td bgcolor="#33CCFF">X2</td>
      <td bgcolor="#99CC99">1</td>
      <td bgcolor="#99CC99">X</td>
      <td bgcolor="#99CC99">2</td>
      <td bgcolor="#33CCFF">1-1</td>
      <td bgcolor="#33CCFF">1-X</td>
      <td bgcolor="#33CCFF">1-2</td>
      <td bgcolor="#33CCFF">X-1</td>
      <td bgcolor="#33CCFF">X-X</td>
      <td bgcolor="#33CCFF">X-2</td>
      <td bgcolor="#33CCFF">2-1</td>
      <td bgcolor="#33CCFF">2-X</td>
      <td bgcolor="#33CCFF">2-2</td>
      <td bgcolor="#99CC99">1+</td>
      <td bgcolor="#99CC99">2+</td>
      <td bgcolor="#33CCFF">0-1</td>
      <td bgcolor="#33CCFF">0-2</td>
      <td bgcolor="#33CCFF">2-3</td>
      <td bgcolor="#33CCFF">3+</td>
      <td bgcolor="#33CCFF">4+</td>
      <td bgcolor="#33CCFF">4-6</td>
      <td bgcolor="#33CCFF">7+</td>
    </tr>

<?php
     echo '    <tr align="center" valign="middle">
      <td bgcolor="#CCCCCC"  valign="top"><input name="txtdata" type="text" id="data" value="'.date('m-d-Y').'" size="10" tabindex="1" /></td>
      <td bgcolor="#CCCCCC"  valign="top"><input name="txtkampionati" type="text" id="kampionati" tabindex="5" /></td>
      <td colspan="30" bgcolor="#99CC99" valign="top">
       <!-- <input name="txtkoeList" type="text" id="txtkoeList" value="" size="135" tabindex="6" /> -->
       <textarea name="txtkoeList" cols="70" rows="10" wrap="off"></textarea>
       </td>
      </tr>
';

/*
for ($i=0;$i<10;$i++) {

      echo ' <tr align="center" valign="middle">
      <td bgcolor="#CCCCCC"><input name="txtdata'.$i.'" type="text" id="data'.$i.'" value="'.date('m-d-Y').'" size="10" tabindex="1'.$i.'" /></td>
      <td bgcolor="#CC9966"><input name="txtkodi'.$i.'" type="text" id="kodi'.$i.'" size="4" tabindex="2'.$i.'" /></td>
      <td bgcolor="#CC9966"><input name="txtndeshja'.$i.'" type="text" id="ndeshja'.$i.'" size="15" tabindex="3'.$i.'" /></td>
      <td bgcolor="#CC9966"><input name="txtora'.$i.'" type="text" id="ora'.$i.'" size="5" tabindex="4'.$i.'" /></td>
      <td bgcolor="#CCCCCC"><input name="txtkampionati'.$i.'" type="text" id="kampionati'.$i.'" /></td>
      <td bgcolor="#99CC99"><input name="txtRF1'.$i.'" type="text" id="RF1'.$i.'" value="0.00" size="4" tabindex="5'.$i.'" /></td>
      <td bgcolor="#99CC99"><input name="txtRFX'.$i.'" type="text" id="RFX'.$i.'" value="0.00" size="4" tabindex="6'.$i.'" /></td>
      <td bgcolor="#99CC99"><input name="txtRF2'.$i.'" type="text" id="RF2'.$i.'" value="0.00" size="4" tabindex="7'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtDSH1X'.$i.'" type="text" id="DSH1X'.$i.'" value="0.00" size="4" tabindex="8'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtDSH12'.$i.'" type="text" id="DSH12'.$i.'" value="0.00" size="4" tabindex="9'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtDSHX2'.$i.'" type="text" id="DSHX2'.$i.'" value="0.00" size="4" tabindex="10'.$i.'" /></td>
      <td bgcolor="#99CC99"><input name="txtRF451'.$i.'" type="text" id="RF451'.$i.'" value="0.00" size="4" tabindex="11'.$i.'" /></td>
      <td bgcolor="#99CC99"><input name="txtRF45X'.$i.'" type="text" id="RF45X'.$i.'" value="0.00" size="4" tabindex="12'.$i.'" /></td>
      <td bgcolor="#99CC99"><input name="txtRF452'.$i.'" type="text" id="RF452'.$i.'" value="0.00" size="4" tabindex="13'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt459011'.$i.'" type="text" id="459011'.$i.'" value="0.00" size="4" tabindex="14'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt45901X'.$i.' type="text" id="45901X'.$i.'" value="0.00" size="4" tabindex="15'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt459012'.$i.'" type="text" id="459012'.$i.'" value="0.00" size="4" tabindex="16'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt4590X1'.$i.'" type="text" id="4590X1'.$i.'" value="0.00" size="4" tabindex="17'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt4590XX'.$i.'" type="text" id="4590XX'.$i.'" value="0.00" size="4" tabindex="18'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt4590X2'.$i.'" type="text" id="4590X2'.$i.'" value="0.00" size="4" tabindex="19'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt459021'.$i.'" type="text" id="459021'.$i.'" value="0.00" size="4" tabindex="20'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt45902X'.$i.'" type="text" id="45902X'.$i.'" value="0.00" size="4" tabindex="21'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txt459022'.$i.'" type="text" id="459022'.$i.'" value="0.00" size="4" tabindex="22'.$i.'" /></td>
      <td bgcolor="#99CC99"><input name="txtSG451p'.$i.'" type="text" id="SG451p'.$i.'" value="0.00" size="4" tabindex="23'.$i.'" /></td>
      <td bgcolor="#99CC99"><input name="txtSG452p'.$i.'" type="text" id="SG452p'.$i.'" value="0.00" size="4" tabindex="24'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtSGP01'.$i.'" type="text" id="SGP01'.$i.'" value="0.00" size="4" tabindex="25'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtSGP02'.$i.'" type="text" id="SGP02'.$i.'" value="0.00" size="4" tabindex="26'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtSGP23'.$i.'" type="text" id="SGP23'.$i.'" value="0.00" size="4" tabindex="27'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtSGP3p'.$i.'" type="text" id="SGP3p'.$i.'" value="0.00" size="4" tabindex="28'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtSGP4p'.$i.'" type="text" id="SGP4p'.$i.'" value="0.00" size="4" tabindex="29'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtSGP46'.$i.'" type="text" id="SGP46'.$i.'" value="0.00" size="4" tabindex="30'.$i.'" /></td>
      <td bgcolor="#33CCFF"><input name="txtSGP7p'.$i.'" type="text" id="SGP7p'.$i.'" value="0.00" size="4" tabindex="31'.$i.'" /></td>
    </tr>
     ';
}
*/

?>
  </table>
  <span class="shenim">  *T&euml; gjith&euml; rreshtat me kod, ndeshje ose dat&euml; t&euml; pash&euml;nuar, jan&euml; t&euml; pavlefsh&euml;m</span><br />
  <br />
  <table width="250"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="85"><input type="submit" name="Submit" value="Ruaj listën" /></td>
      <td width="30"> dhe </td>
      <td width="135"><select name="choice">
        <option value="new" selected="selected">Hap nj&euml; list&euml; t&euml; re</option>
        <option value="back">Kthehu prapa</option>
      </select></td>
    </tr>
  </table>
</form>
</body>
</html>

