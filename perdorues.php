<?php
require_once("settings.php");
require_once("Login.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="pragma" content="no-cache" />
<title>P&euml;rdorues</title>
<link href="stili.css" rel="stylesheet" type="text/css" />
</head>

<body>

<?php
if (($_COOKIE[COOKIE_PREF."_rights"] > L_ADMIN) OR (!isset($_COOKIE[COOKIE_PREF."_rights"]))) {
     die ("Nuk keni të drejtë të vizitoni këtë faqe.<br></body></html>");
}
?>

<table width="100%" border="0" cellpadding="1" cellspacing="1">
  <tr>
    <td width="261" align="center" valign="top" bgcolor="#575757">

<!-- User list -->
    <table width="260" border="0" cellspacing="1" cellpadding="1">
  <tr>
    <td width="20" bgcolor="#666666">ID</td>
    <td width="120" bgcolor="#999999">Emri i p&euml;rdoruesit</td>
    <td width="140" bgcolor="#999999">Emri i plot&euml;</td>
  </tr>
  <?php
     $l = new Login(HOST, USER, PASSWORD, DATABASE);
     $users = $l->user_list();

     foreach ($users as $user) {
          echo '    <tr>';
          echo '    <td width="20" bgcolor="#888888">'.$user[id].'</td>';
          echo '    <td width="110" bgcolor="#AAAAAA"><a href="perdorues.php?act=edit&uid='.$user[id].'" target="_self"><font color="#FFFF00">'.$user[username].'</font></a></td>';
          echo '    <td width="130" bgcolor="#AAAAAA">'.$user[name].'</td>';
          echo '    </tr>';
     }
  ?>
</table>
    <br />
    [ <a href="<?php echo $_SERVER[PHP_SELF]."?act=new"; ?>">P&euml;rdorues i ri</a> ]<!-- End user list -->    </td>
    <td width="775" align="left" valign="top" bgcolor="#CCCCCC">
    <!-- User info -->
        <?php
                $st_userid = 0;
                    if ($_GET[act] == "new") {
                         if ($_GET[value]==1) {
                              $uname = $_POST[newUname];
                              $fname = $_POST[newName];
                              $active = $_POST[newChkActive];
                              if ($_POST[txtNewpass] === $_POST[txtNewpass2]) {
                                   $pass = $_POST[txtNewpass];
                              } else {
                                   echo "<script>alert('Fjalëkalimet duhet të jenë njëlloj.');</script>";
                                   return ;
                              }
                              $krediti = $_POST[newCredit];
                              $rights = $_POST[listRights];
                         }
          ?>
          <form name="edit_user" id="edit_user" method="post" action="<?php echo $_SERVER[PHP_SELF]."?act=new&value=1"; ?>">
                  <table width="673" border="0" cellpadding="1" cellspacing="1">
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                         <td width="150">Emri i p&euml;rdoruesit :</td>
                     <td width="240"><input name="newUname" type="text" id="newUname" /></td>
                     <td width="160">T&euml; drejtat e p&euml;rdoruesit:</td>
                     <td width="100"><select name="listRights" id="listRights">
                       <option value="1">Admin</option>
                       <option value="2">Boss</option>
                       <option value="3">Editor</option>
                       <option value="4">Klient</option>
                       <option value="5" selected>Asnje</option>
                     </select></td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Emri i plot&euml;: </td>
                      <td width="240"><input name="newName" type="text" id="newName" /></td>
                      <td width="160">Aktiv:                      </td>
                      <td width="100"><input name="newChkActive" type="checkbox" id="newChkActive" value="checkbox" checked="checked" <?php if ($user[active]) echo "checked=\"checked\""; ?>  /></td>
                    </tr>
                    <tr id="row_off">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">&nbsp;</td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Fjal&euml;kalimi: </td>
                      <td width="240"><input name="txtNewpass" type="password" id="txtNewpass" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Fjal&euml;kalimi p&euml;rs&euml;ri: </td>
                      <td width="240"><input name="txtNewpass2" type="password" id="txtNewpass2" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_off">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">&nbsp;</td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Krediti fillestar : </td>
                      <td width="240"><input name="newCredit" type="text" id="newCredit" size="7" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">
                      <input name="Pranoje" type="submit" id="Pranoje" value="Pranoje" />
                      </td>
                      <td width="160">&nbsp;</td>
                      <td width="100"><input name="st_userid" type="hidden" id="st_userid" value="<?php echo $user[id] ?>" /></td>
                    </tr>
                  </table>
      </form>          
         <?php
                } else if ($_GET[act] == "edit")
                        if (isset($_GET[uid])) {
                                $user = $l->user_info($_GET[uid]);
                                $st_userid = $user[id];
        ?>
      <form name="edit_user" id="edit_user" method="post" action="<?php echo $_SERVER[PHP_SELF]."?act=edit&uid=$st_userid"; ?>">
                  <table width="673" border="0" cellpadding="1" cellspacing="1">
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                         <td width="150">Emri i p&euml;rdoruesit :</td>
                     <td width="240"><input name="txtUsername" type="text" id="txtUsername" value="<?php echo $user[username]; ?>" /></td>
                     <td width="160">T&euml; drejtat e p&euml;rdoruesit:</td>
                     <td width="100"><select name="listRights" id="listRights">
                     </select></td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Emri i plot&euml;: </td>
                      <td width="240"><input name="txtName" type="text" id="txtName" value="<?php echo $user[name]; ?>" /></td>
                      <td width="160">Aktiv:                      </td>
                      <td width="100"><input name="chkActive" type="checkbox" id="chkActive" value="checkbox" <?php if ($user[active]) echo "checked=\"checked\""; ?>  /></td>
                    </tr>
                    <tr id="row_off">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">&nbsp;</td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Ndrysho fjal&euml;kalimin: </td>
                      <td width="240"><input name="txtNewpass" type="password" id="txtNewpass" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Fjal&euml;kalimi p&euml;rs&euml;ri: </td>
                      <td width="240"><input name="txtNewpass2" type="password" id="txtNewpass2" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_off">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">&nbsp;</td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Rivendos kreditin: </td>
                      <td width="240"><input name="txtNewcredit" type="text" id="txtNewcredit" size="7" value="<?php echo $user[kredit]; ?>" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">
                      <input name="Pranoje" type="submit" id="Pranoje" value="Pranoje" />
                      <?php if ($l->get_uid($_COOKIE[COOKIE_PREF."_uname"])==1) echo ' <input name="Shuaje" type="submit" id="Shuaje" value="Shuaje" />' ; ?>
                      </td>
                      <td width="160">&nbsp;</td>
                      <td width="100"><input name="st_userid" type="hidden" id="st_userid" value="<?php echo $user[id] ?>" /></td>
                    </tr>
                  </table>
      </form>
        <?php
                        } else if ($_GET[act] == "new") {
          ?>
                <form name="edit_user" id="edit_user" method="post" action="<?php echo $_SERVER[PHP_SELF]."?act=edit&uid=$st_userid"; ?>">
                  <table width="673" border="0" cellpadding="1" cellspacing="1">
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                         <td width="150">Emri i p&euml;rdoruesit :</td>
                     <td width="240"><input name="txtUsername" type="text" id="txtUsername" /></td>
                     <td width="160">T&euml; drejtat e p&euml;rdoruesit:</td>
                     <td width="100"><select name="listRights" id="listRights">
                     </select></td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Emri i plot&euml;: </td>
                      <td width="240"><input name="txtName" type="text" id="txtName" /></td>
                      <td width="160">Aktiv:                      </td>
                      <td width="100"><input name="chkActive" type="checkbox" id="chkActive" value="checkbox"  /></td>
                    </tr>
                    <tr id="row_off">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">&nbsp;</td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Ndrysho fjal&euml;kalimin: </td>
                      <td width="240"><input name="txtNewpass" type="password" id="txtNewpass" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Fjal&euml;kalimi p&euml;rs&euml;ri: </td>
                      <td width="240"><input name="txtNewpass2" type="password" id="txtNewpass2" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_off">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">&nbsp;</td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">Krediti fillestar: </td>
                      <td width="240"><input name="txtNewcredit" type="text" id="txtNewcredit" size="7" /></td>
                      <td width="160">&nbsp;</td>
                      <td width="100">&nbsp;</td>
                    </tr>
                    <tr id="row_on">
                      <td width="7">&nbsp;</td>
                      <td width="150">&nbsp;</td>
                      <td width="240">
                      <input name="Pranoje" type="submit" id="Pranoje" value="Pranoje" />
                      </td>
                      <td width="160">&nbsp;</td>
                      <td width="100"></td>
                    </tr>
                  </table>
      </form>
          <?php
                              }
        ?>
    <!-- End user info -->    </td>
        <td><div id="spacer"></div></td>
  </tr>
</table>

<?php
     if (isset($_POST[Pranoje])) {
          $olduser = $l->user_info($_POST[st_userid]);
          if ($_POST[txtUsername] != $olduser[username]) {
               $username = stripcslashes($_POST[txtUsername]);
               $sql_uname = "`username` = '{$username}',";
          }
          if ($_POST[txtName] != $olduser[name]) {
               $name = stripcslashes($_POST[txtName]);
               $sql_name = "`name` = '{$name}',";
          }

          if (strlen($_POST[txtNewpass]) > 4 ) {
               if ($_POST[txtNewpass] == $_POST[txtNewpass2]) {
                    $pass = $_POST[txtNewpass];
                    $sql_pass = "`password` = MD5( '{$pass}' ) ,";
               } else {
                    die("Fjalëkalimet nuk janë njësoj.\n");
               }
          } else if (strlen($_POST[txtNewpass]) > 0) {
               die("Fjalëkalimi shumë i shkurtër.\n");
          }
          if ($_POST[txtNewcredit] != $l->get_user_credit($_POST[st_userid])) {
                    $credit = $_POST[txtNewcredit];
                    $sql_cre = "`kredit` = '{$credit}',";
          }
          if ($_POST[chkActive]) {
               $sql_active = "`active` = '1'";
          } else {
               $sql_active = "`active` = '0'";
          }
               
          $sql  = "UPDATE `".DATABASE."`.`perdorues` SET ";
          $sql .= $sql_name;
          $sql .= $sql_uname;
          $sql .= $sql_pass;
          $sql .= $sql_cre;
          $sql .= $sql_active;
          $sql .= " WHERE `perdorues`.`id` ={$_POST[st_userid]} LIMIT 1 ;";
          
          $link = mysql_connect(HOST, USER, PASSWORD);
          mysql_select_db(DATABASE);
          
          $result = mysql_query($sql, $link);
          if (isset($sql_name))
               echo "Emri u ndryshua.<br />";
          if (isset($sql_uname))
               echo "Emri i p&euml;rdoruesit u ndryshua.<br />";
          if (isset($sql_pass))
               echo "Fjal&euml;kalimi u ndryshua.<br />";
          if (isset($sql_cre))
               echo "Krediti u rivendos n&euml; vler&euml;n <strong>{$credit}</strong><br />";
     }
?>
</body>
</html>

