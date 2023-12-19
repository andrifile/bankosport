<?php
function login_show() {
echo '<form name="login_form" method="post" action="index.php" target=_parent>
<div align="left">
  <table width="300"  border="0" cellpadding="0" cellspacing="0" class="login_tabela">
    <tr>
      <td width="130">Emri i p&euml;rdoruesit: </td>
      <td width="176"><input name="txtUsername" type="text" id="txtUsername" size="20"></td>
    </tr>
    <tr>
      <td>Fjal&euml;kalimi:</td>
      <td><input name="txtPassword" type="password" id="txtPassword" size="20"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="login" type="submit" id="login" value="Identifikohu"></td>
    </tr>
  </table>
</div>
</form>
';
}

if (!isset($_POST[login]))
     login_show();
?>

