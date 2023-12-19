<?php
require_once("Skedina.php");
require_once("settings.php");

class Login extends Skedina {

     public $username;
     public $isLogin;
     public $accesslevel;
     public $userid;

     function __construct($host, $user, $pass, $db) {
          if ($this->link == NULL)
               $this->link = $this->conn_mysql($host, $user, $pass, $db);
     }

     function __destruct() {
          $this->close_mysql($this->link);
     }

    /* bool testUname(string $user, string $pass)
     * @params: $user, $pass
     * @return: TRUE if $user with $pass exists in the database, FALSE if not
     */
     public function testUname($user, $pass) {
          $sql = "SELECT * FROM `perdorues` WHERE username='".$user."' ;";
          $result = mysql_query($sql, $this->link);
          $userinfo = mysql_fetch_array($result);
          $pass = md5($pass);
          if ($userinfo[username] == $user && $userinfo[password] == $pass) {
               if ($userinfo[active] > 0) {
                    $this->isLogin = true;
                    $this->accesslevel = $userinfo[accesslvl];
                    $this->username = $userinfo[username];
                    $this->userid = $userinfo[id];
                    $sql = "UPDATE `perdorues` SET `last_acc_time` = '".date("Y-m-d H:i:s")."', `last_acc_ip`='".$_SERVER['REMOTE_ADDR']."' WHERE `id` ='".$this->userid."' LIMIT 1 ;";
                    mysql_query($sql, $this->link);
                    return true;
               } else {
                    $this->isLogin = false;
                    $this->accesslevel = L_NONE;
                    $this->username = "guest";
                    $this->userid = 0;
                    return false;
               }
          } else {
               $this->isLogin = false;
               $this->accesslevel = L_NONE;
               return false;
          }
     }

     /* getUserinfo(string $user)
      * @params: $user
      * @return: userid of the specified username
      */
     public function getUserinfo($user) {
          $sql = "SELECT * FROM `perdorues` WHERE username='".$user."' ;";
          $result = mysql_query($sql, $this->link);
          $userinfo = mysql_fetch_array($result);
          return $userinfo[id];
     }

    /* enum_users()
     * @params: none
     * @return: array uid()
     */
     public function enum_users() {
          $sql = "SELECT id as uid, username as uname FROM `perdorues` WHERE `active`=1 AND `accesslvl`>=".L_CLIENT." ;";
          $res = mysql_query($sql, $this->link);
          while ($row = mysql_fetch_assoc($res)) {
               $out[] = $row;
          }
          
          return $out;
     }
     
    /* setcookies(int $time)
     * @params: $time
     * @return: nothing
     */
     public function setcookies($time) {
          setcookie(COOKIE_PREF."_uname", $this->username, time()+$time, "/");
          setcookie(COOKIE_PREF."_rights", $this->accesslevel, time()+$time, "/");
     }

    /* destroycookies()
     * @params: none
     * @return: nothing
     */
     public function destroycookies() {
          setcookie(COOKIE_PREF."_uname", "guest", time()-3600, "/");
          setcookie(COOKIE_PREF."_rights", L_NONE, time()-3600, "/");
     }

    /* bool testLevel(string $level)
     * @params: $level
     * @return: TRUE or FALSE
     */
     public function testLevel($level) {
          if ($level == $this->accesslevel)
               return true;
          else
               return false;
     }

    /* string user_exists(string $username, string $password, string $fullname[, string $accesslvl[, bool $active]]);
     * @params: $username, $password, $fullname, $accesslvl, $active
     * @return: string
     */
     public function writeUser($username, $password, $fullname, $credit = 0, $accesslvl = L_NONE, $active = FALSE) {
          $user = stripcslashes($username);
          $pass = md5($password);
          $full = stripcslashes($fullname);

          if ($this->user_exists($user))
               return "User exists already";

          $sql = "INSERT INTO `perdorues` (`id`, `name`, `username`, `password`, `kredit`, `accesslvl`, `active`) ";
          $sql.= "VALUES (NULL, '{$full}', '{$user}', '{$pass}', {$credit}, '{$accesslvl}', '{$active}') ;";
          if (!mysql_query($sql, $this->link))
               die(mysql_error());
          else
               return "Wrote successfully 1 user with username {$user}.";
     }

     public function set_passwd($oldpasswd, $newpasswd, $uid) {
          $sql = "SELECT `password` FROM `perdorues` WHERE `id`='{$uid}' ;";
          $result = mysql_query($sql, $this->link);
          $row = @mysql_fetch_assoc($result);
          $pass = $row[password];
          if (md5($oldpasswd) == md5($pass)) {
               $sql = "UPDATE `perdorues` SET `password`='".md5($newpasswd)."' WHERE `id`='{$uid}' ;";
               mysql_query($sql, $this->link);
               return TRUE;
          } else {
               return FALSE;
          }
          return FALSE;
     }

    /* bool user_exists(string $username);
     * @params: $username
     * @return: TRUE if user exists, else FALSE
     */
     private function user_exists($username) {
          $sql = "SELECT * FROM `perdorues` WHERE `username`='$username' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          if (mysql_num_rows($result) > 0)
               return true;
          else
               return false;
     }

     public function user_list() {
          $sql = "SELECT `id`,`name`,`username` FROM `perdorues` ;";
          $result = mysql_query($sql, $this->link);
          if (@mysql_num_rows($result) > 0) {
               while ($row = mysql_fetch_assoc($result)) {
                    $userlist[] = $row;
               }
               return $userlist;
          } else {
               return "No users found.";
          }
     }

     public function user_info($uid) {
        $sql = "SELECT * FROM `perdorues` WHERE `id`='".$uid."' LIMIT 1;";
        $result = mysql_query($sql, $this->link);
        if (@mysql_num_rows($result) > 0) {
                $row = mysql_fetch_assoc($result);
        }
        return $row;
     }

     public function get_user_credit($uid) {
          $sql = "SELECT `kredit` FROM `perdorues` WHERE `perdorues`.`id`='{$uid}' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);

          return $row[kredit];
     }

     public function set_user_credit($credit, $uid) {
          $sql = "UPDATE `perdorues` SET `kredit` = '{$credit}' WHERE `id` = '{$uid}' LIMIT 1 ;";
          mysql_query($sql, $this->link);

          return $credit;
     }

      public function get_amnt($date, $uid) {
          $sql = "SELECT * FROM `bileta_shumat` WHERE `date`='{$date}' AND `id_op`='{$uid}' AND `status`='1' ;";
          $result = mysql_query($sql, $this->link);
          if (@mysql_num_rows($result) < 1) {
               $amnt = 0;
               $xhiro = 0;
          } else {
               while ($row = @mysql_fetch_assoc($result)) {
                    $amnt += $row[shuma];
                    $xhiro += $row[shuma];
               }
          }

          $sql = "SELECT `fitimi`,`shuma` FROM `bileta_shumat` WHERE `date`='{$date}' AND `id_op`='{$uid}' AND `status`='3' ;";
          $result = mysql_query ($sql, $this->link);
          if (@mysql_num_rows($result) < 1) {
               //Do nothing
          } else {
               while ($row = @mysql_fetch_assoc($result)) {
                    $amnt -= $row[fitimi];
                    $amnt += $row[shuma];
                    $xhiro += $row[shuma];
               }
          }
          return array($amnt, $xhiro);
      }

      public function get_amnt2($date_s, $date_e, $uid) {
        $sql = "select `nr_bilete` from `bileta_shumat` where `id_op`='{$uid}' and `date`>='".$date_s."' and `date`<='".$date_e."' and (`status`='4' or `status`='3' or `status`='1');";
        $result = mysql_query($sql, $this->link);
        $fitimi = 0;
        $xhiro = 0;
        $xhiro_tek = 0;
        $xhiro_cift = 0;

        if (mysql_num_rows($result) > 0) {
                while ($row = mysql_fetch_assoc($result)) {
                        $biletat[] = $row[nr_bilete];
                }

                foreach ($biletat as $v) {
                        $sql = "select `id`,count(*) as `cnt` from `bileta` where `nr_bilete`='{$v}' and `id_op`='{$uid}' and `date`>='".$date_s."' and `date`<='".$date_e."' ;"; //count entries
                        $result1 = mysql_query($sql, $this->link);
                        $row = mysql_fetch_assoc($result1);
                        if ($row[cnt] == 1) { //if 1 entry
                                $sqln = "select `shuma` from `bileta_shumat` where `nr_bilete`='{$v}' and `id_op`='{$uid}'".$date_s."' and `date`<='".$date_e."' and (`status`='4' or `status`='3' or `status`='1');";
                                $result2 = mysql_query($sqln, $this->link);
                                $row_t = mysql_fetch_assoc($result2);
                                $xhiro_tek += $row_t['shuma'];
                        } else if ($row[cnt] != 1) {
                                $sqlv = "select `shuma` from `bileta_shumat` where `nr_bilete`='{$v}' and `id_op`='{$uid}' and `date`>='".$date_s."' and `date`<='".$date_e."' and (`status`='4' or `status`='3' or `status`='1');";
                                $result2 = mysql_query($sqlv, $this->link);
                                $row_t = mysql_fetch_assoc($result2);
                                $xhiro_cift += $row_t['shuma'];
                        }
                }

        } else {
                $xhiro = 0;
                $xhiro_tek = 0;
                $xhiro_cift = 0;
        }
          print $xhiro_tek."<br>";
          print $xhiro_cift."<br>";
        // Fitimi
        $sqlx = "select sum(`fitimi`) from `bileta_shumat` where `id_op`='{$uid}' and `date`>='".$date_s."' and `date`<='".$date_e."' and `status`='3' ;";
        $result3 = mysql_query($sqlx, $this->link);
        $rowx = mysql_fetch_row($result3);
        $fitimi = $rowx[0];

        // Xhiro
        $sqlxh = "select sum(`shuma`) from `bileta_shumat` where `id_op`='{$uid}' and `date`>='".$date_s."' and `date`<='".$date_e."' and (`status`='4' or `status`='3' or `status`='1');";
        $result3 = mysql_query($sqlxh, $this->link);
        $rowxh =  mysql_fetch_row($result3);
        $xhiro = $rowxh[0];

//        $xhiro = $xhiro_tek + $xhiro_cift;
//          print_r($xhiro_tek); echo "."; print_r($xhiro_cift);
        $perqind = ($xhiro_tek*PERCENT_S)+($xhiro_cift*PERCENT_F);
//        $perqind = $xhiro * PERCENT_F;
        $out = array("perqind"=>$perqind, "xhiro"=>$xhiro, "fitimi"=>$fitimi);
        return $out;
      }

     public function get_uid($uname) {
          $sql = "SELECT `id` FROM `perdorues` WHERE `username`='{$uname}' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);

          return $row[id];
     }

     public function get_fullname($uname) {
          $sql = "SELECT name FROM `perdorues` WHERE username='{$uname}' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);

          return $row[name];
     }


}
?>
