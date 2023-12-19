<?php
require_once("settings.php");
require_once("Skedina.php");
require_once("Login.php");
/*

datenow() - returns mySql-formatted date
nr_b($uid) - returns next or current ticket number by uid

check_not_finished($uid) - checks if current uid has an unfinished ticket, and do cleanup if there are no entries
select_not_finished($uid) - selects entries for current unfinished ticket

calc_total_coe($uid) - calculates total coefficent for this uid's unfinished ticket

time_f_code($code) - from a game code, get time of the game
name_f_code($code) - from a game code, get the name of the game

ticket_sum($ticketno) - get saved ticket sum for this ticketnumber
ticket_coe($ticketno) - get saved ticket coe for this ticketnumber
ticket_winning($ticketno) - get saved ticket winning for this ticketnumber


*/
class Bileta {
     protected $link = NULL;
     private $datenow;
     private $oranow;


     function __construct($host, $user, $password, $db) {
          $this->link = $this->conn_mysql($host, $user, $password, $db);
          $this->datenow = date("Y-m-d");
          $this->oranow = date("H:i:s", time()-(60*10));
     }

     function __destruct() {
          $this->close_mysql($this->link);
     }

     protected function conn_mysql($host, $user, $password, $db) {
          $link = mysql_connect($host, $user, $password);
          if (!$link) {
               die("Lidhja me serverin nuk u krye: ".mysql_error());
          }

          if (!mysql_select_db($db, $link)) {
               die("Nuk ka mundësi të zgjidhet databaza: ".mysql_error());
          }
          return $link;
     }

     protected function close_mysql($link) {
          mysql_close($link);
     }
////////////////////////////////////////////////////////////////////////////////////

     //Get mySQL formatted date_time
     public function datenow() {
          //2008-12-07 17:01:09
          return date("d-m-Y H:i:s", time());
     }

     //Get next ticket id, or this id if there is an unfinished ticket
     public function nr_b($uid) {
          $sql = "SELECT * FROM `bileta_shumat` WHERE `id_op`='{$uid}' ;";
          $result = mysql_query($sql, $this->link);
          while ($row = mysql_fetch_assoc($result)) {
               $nr[] = $row[nr_bilete];
               if ($row[finished] == 1) {
                    continue;
               } else if ($row[finished] == 0) {
                    return $row[nr_bilete];
               }
          }
          if (empty($nr))
               return 1;
          return max($nr)+1;
     }

     //Check if there is an unfinished ticket and do cleanup if necessary
     public function check_not_finished($uid) {
          $sql = "SELECT * FROM `bileta_shumat` WHERE `id_op`='{$uid}' AND `finished`='0' ;";
          $result = mysql_query($sql, $this->link);
          if (mysql_num_rows($result) > 0) {
               $unfinished_ticket = true;
               $row = mysql_fetch_assoc($result);
          } else {
               $unfinished_ticket = false;
          }

          $sql = "SELECT * FROM `bileta` WHERE `id_op`='{$uid}' AND `nr_bilete`='{$row[nr_bilete]}' AND `date`='".$this->datenow."' ;";
          $result = mysql_query($sql, $this->link);
          if (mysql_num_rows($result) < 1) {
               $sql = "DELETE FROM `bileta_shumat` WHERE `id_op`='{$uid}' AND `finished`='0' LIMIT 1 ;";
               mysql_query($sql, $this->link);
               return false;
          } else {
               return $unfinished_ticket;
          }
     }

     //Select unfinished entry/ticket
     public function select_not_finished($uid) {
          $sql = "SELECT * FROM `bileta_shumat` WHERE `id_op`='{$uid}' AND `finished`='0' AND `date`='".$this->datenow."' ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          $ticketno = $row[nr_bilete];

          $sql = "SELECT * FROM `bileta` WHERE `id_op`='{$uid}' AND `nr_bilete`='{$ticketno}' AND `date`='".$this->datenow."' ;";
          $result = mysql_query($sql, $this->link);
          while ($row = mysql_fetch_assoc($result)) {
               $n[] = $row;
          }

          return $n;
     }

     function select_ticket($ticketid, $date, $uid) {
          $sql = "SELECT * FROM `bileta_shumat` WHERE `id_op`='{$uid}' AND `nr_bilete`='{$ticketid}' AND `date`='{$date}' AND `finished`='1' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          if (@mysql_num_rows($result) < 1) {
               return "#!";
          }
          $sql = "SELECT * FROM `bileta` WHERE `id_op`='{$uid}' AND `nr_bilete`='{$ticketid}' AND `date`='".$date."' ;";
          $result = mysql_query($sql, $this->link);
          while ($row = mysql_fetch_assoc($result)) {
               $n[] = $row;
          }

          return $n;
     }

     //Calculate total ticket coefficent, to be used with ticket info updater
     public function calc_total_coe($uid) {
          $notfinished = $this->select_not_finished($uid);
          $totalcoe = 1;
          if (count($notfinished) > 0)
               foreach ($notfinished as $v)
                    $totalcoe *= $v[game_coe];

          return $totalcoe;
     }

     // Time string from entry code
     public function time_f_code($code) {
          $sql = "SELECT `ora` FROM `skedina_ditore` WHERE `kodi`='{$code}' AND `data`='".$this->datenow."' AND `ora`>='".$this->oranow."'  LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          return $row[ora];
     }

     //Name string from entry code
     public function name_f_code($code) {
          $sql = "SELECT `ndeshja` FROM `skedina_ditore` WHERE `kodi`='{$code}' AND `data`='".$this->datenow."' AND `ora`>='".$this->oranow."'  LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          return $row[ndeshja];
     }

     public function is_single($code) {
          $sql = "SELECT * FROM `skedina_ditore` WHERE `kodi`='{$code}' AND `data`='".$this->datenow."' LIMIT 1 ;";
          $result  = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          return $row[teke];
     }

     public function ticket_sum($ticketno, $date, $uid) {
          $sql = "SELECT `shuma` from `bileta_shumat` WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".$date."' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          return $row[shuma];
     }

     public function ticket_coe($ticketno, $date, $uid) {
          $sql = "SELECT `total_coe` FROM `bileta_shumat` WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".$date."' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          return $row[total_coe];
     }

     public function ticket_winning($ticketno, $date, $uid) {
          $sql = "SELECT `fitimi` FROM `bileta_shumat` WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".$date."' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          return $row[fitimi];
     }

     public function set_ticket_sum($newval, $ticketno, $date, $uid) {
          $sql = "UPDATE `bileta_shumat` SET `shuma`='".$newval."' WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".$date."' LIMIT 1 ;";
          mysql_query($sql, $this->link);
     }
     
     public function set_ticket_coe($newval, $ticketno, $date, $uid) {
          $sql = "UPDATE `bileta_shumat` SET `total_coe`='".$newval."' WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".$date."' LIMIT 1 ;";
          mysql_query($sql, $this->link);
     }
     
     public function set_ticket_winning($newval, $ticketno, $date, $uid) {
          $sql = "UPDATE `bileta_shumat` SET `fitimi`='".$newval."' WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".$date."' LIMIT 1 ;";
          mysql_query($sql, $this->link);
     }

     public function ticket_date($ticketno, $date, $uid) {
          $sql = "SELECT `date`,`time` FROM `bileta_shumat` WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".$date."' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          return $row[date]." ".$row[time];
     }

     ////////////////// SANITY FUNCTION ////////////////////
     public function check_code($kodi) {
          $datenow = date("Y-m-d");
          $oranow = date("H:i:s", time()+(60*10));
          $sql = "SELECT `kodi` FROM `skedina_ditore` WHERE `kodi`='{$kodi}' AND `data`='{$datenow}' AND `ora`>='{$oranow}' AND `aktive`='1' ;";
          $result = mysql_query($sql, $this->link);
          if (@mysql_num_rows($result) > 0)
               return TRUE;
          else
               return FALSE;
     }

     ////////////////// SANITY FUNCTION ////////////////////
     public function check_gametype($loja) {
          $sql = "SELECT `short` FROM `lojrat` WHERE `short`='{$loja}' AND `aktive`=FALSE ;";
          $result = mysql_query($sql, $this->link);
          if (@mysql_num_rows($result) > 0)
               return TRUE;
          else
               return FALSE;
     }

     ////////////////// SANITY FUNCTION ////////////////////
     public function check_dup($kodi, $uid) {
          if (count($this->select_not_finished($uid)) > 0)
          foreach ($this->select_not_finished($uid) as $v) {
               if ($kodi == $v[entry_code])
                    return FALSE;
          }

          return TRUE;
     }

     ///////////////// FORM FUNCTION //////////////////////
     public function drop_entry($id, $uid) {
          $sql = "DELETE FROM `bileta` WHERE `id`={$id} LIMIT 1 ;";
          mysql_query($sql, $this->link);

          $ticketsum = round($this->ticket_sum($this->nr_b($uid), $this->datenow, $uid), 2);
          $ticketcoe = round($this->calc_total_coe($uid), 2);
          $ticketwinning = round($ticketsum * $ticketcoe, 2);

          //Update ticket info
          $sqlu  = "UPDATE `bileta_shumat` SET ";
          $sqlu .= "`shuma` = '".$ticketsum."', ";
          $sqlu .= "`total_coe` = '".$ticketcoe."', ";
          $sqlu .= "`fitimi` = '".$ticketwinning."' ";
          $sqlu .= "WHERE `nr_bilete` = '".$this->nr_b($uid)."' LIMIT 1 ;";

          mysql_query($sqlu, $this->link);
     }

     ///////////////// FORM FUNCTION //////////////////////
     public function add_entry($uid, $n_kodi, $n_ora, $n_emri, $loja, $koe) {

          if (!$this->check_code($n_kodi)) {
               echo "<script>alert('Kodi nuk është i saktë ose ndeshja nuk mund të vihet.');</script>";
               unset($_GET[kodi]);
               return ;
          }

          if (!$this->check_gametype($loja)) {
               echo "<script>document.add_entry.loja.focus(); /* alert('Kjo lloj loje nuk ekziston. Shikoni tabelën për lojrat e vlefshme.'); */</script>";
               return ;
          }

          if ($koe < 1) {
               echo "<script>alert('Ky koeficent për këtë ndeshje është i bllokuar.');</script>";
               return ;
          }

          if (!$this->check_dup($n_kodi, $uid)) {
               echo "<script>alert('E njëjta ndeshje nuk mund të luhet dy herë në të njëjtën skedinë.');</script>";
               return ;
          }

          $count = count($this->select_not_finished($uid));

          if ($count == 10) {
               echo "<script>alert('Nuk mund të vihen më shumë se 10 ndeshje në të njëjtën skedinë.');</script>";
               return ;
          }

          $sqli  = "INSERT INTO `bileta` ( `id` , `nr_bilete` , `id_op` , `date` , `time` , `entry_code` , `entry_time`, `entry_name`, `game_type` , `game_coe` )";
          $sqli .= " VALUES (";
          $sqli .= " NULL , '".$this->nr_b($uid)."', '".$uid."', '".date("Y-m-d")."' , '".date("H:i:s")."' , '".$n_kodi."', '".$n_ora."', '".$n_emri."', '".$loja."', '".$koe."' ";
          $sqli .= ");";

          $ticketsum = round($this->ticket_sum($this->nr_b($uid), $this->datenow, $uid), 2);
          $ticketcoe = round($this->calc_total_coe($uid) * $koe, 2);
          $ticketwinning = round($ticketsum * $ticketcoe, 2);

//          $sqlc  = "SET AUTOCOMMIT=0; START TRANSACTION; ";
          $sqlc = "INSERT INTO `bileta_shumat` ( `id` , `date` , `time` , `id_op` , `status` , `finished` , `nr_bilete` , `shuma` , `total_coe` , `fitimi` )";
          $sqlc .= " VALUES (";
          $sqlc .= " NULL, '".date("Y-m-d")."' , '".date("H:i:s")."' , '{$uid}', '-1', '0', '".$this->nr_b($uid)."', '{$ticketsum}', '{$ticketcoe}', '{$ticketwinning}' ";
          $sqlc .= "); ";
//          $sqlc .= "COMMIT;";

          $sqlu  = "UPDATE `bileta_shumat` SET ";
          $sqlu .= "`shuma` = '{$ticketsum}', ";
          $sqlu .= "`total_coe` = '{$ticketcoe}', ";
          $sqlu .= "`fitimi` = '{$ticketwinning}' ";
          $sqlu .= "WHERE `nr_bilete` = '".$this->nr_b($uid)."' AND `id_op`='{$uid}' AND `date`='".$this->datenow."' LIMIT 1 ;";

          $n = $this->check_not_finished($uid);
          if ($n == true) { // Just add a new row to `bileta`, update main info in `bileta_shumat`
               mysql_query($sqli, $this->link);
               mysql_query($sqlu, $this->link);
          } else { //Create new row in `bileta_shumat` and add this row to `bileta`
               mysql_query($sqli, $this->link);
               mysql_query($sqlc, $this->link);
          }

     }

     public function update_sum($sum, $uid) {
          if (empty($sum))
               $sum = 100;
          if ($sum < 100) {
               echo "<script>alert('Minimumi i shumës së lejuar është 100.');</script>";
               return ;
          }

          $totalcoe = $this->ticket_coe($this->nr_b($uid), $this->datenow, $uid);
          $winning = $sum * $totalcoe;
          if ($winning > W_LIMIT) {
               echo "<script>alert('Maksimumi i fitimit për një skedinë është ".W_LIMIT.".');</script>";
               return ;
          }

          $sql = "SELECT `kredit` FROM `perdorues` WHERE `id`='{$uid}' ;";
          $result = mysql_query($sql, $this->link);
          $row = mysql_fetch_assoc($result);
          $usercredit = $row[kredit];
          if (($usercredit - $sum) < 0) {
               echo "<script>alert('Kërkoni kredit përpara se të vendosni bileta të tjera.');</script>";
               return;
          }

          $sqlu  = "UPDATE `bileta_shumat` SET `shuma`='".$sum."', `total_coe`='".$totalcoe."', `fitimi`='".$winning."' ";
          $sqlu .= "WHERE `nr_bilete`='".$this->nr_b($uid)."' AND `id_op`='".$uid."' AND `finished`='0' LIMIT 1 ;";
          mysql_query($sqlu, $this->link);
     }

     public function finish_ticket($uid) {
          $count = count($this->select_not_finished($uid));
          $ticketno = $this->nr_b($uid);
          $st = array(1, $ticketno);
          if ($count < 3) {
               $sql = "SELECT `entry_code` FROM `bileta` WHERE `id_op`='{$uid}' AND `nr_bilete`='{$ticketno}' AND `date`='".$this->datenow."' ;";
               $result = mysql_query($sql, $this->link);
               while ($row = mysql_fetch_assoc($result)) {
                    if ($this->is_single($row[entry_code])) {
                         $st[0] = -1;
                         continue;
                    } else {
                         echo "<script>alert('Nuk mund të vihen më pak se 3 ndeshje jo-teke në skedinë.');</script>";
                         $st[0] = -2;
                         return $st;
                    }
               }
          }
          if ($this->ticket_sum($ticketno, $this->datenow, $uid) < 100) {
               echo "<script>alert('Shuma nuk mund të jetë nën 100.');</script>";
               return ;

          }

          if ($this->ticket_sum($ticketno, $this->datenow, $uid) > S_LIMIT) {
               $st[0] = -1;
          }
          $sql = "SELECT count(*) as cnt FROM `bileta_shumat` WHERE `total_coe` LIKE '".$this->ticket_coe($ticketno, $this->datenow, $uid)."' AND `date`='".$this->datenow."' AND `id_op`='{$uid}' ;";
          $result = mysql_query($sql, $this->link);
          $row = @mysql_fetch_assoc($result);
          if ($uid == 1) {
//                print_r(array("row"=>$row,"coe"=>$this->ticket_coe($ticketno, $this->datenow, $uid)));
                }
          if ($row['cnt'] > 3) {
               $st[0] = -1;
          }

          $sql = "UPDATE `bileta_shumat` SET `finished`='1', `status`='".$st[0]."' WHERE `finished`='0' AND `id_op`='{$uid}' LIMIT 1 ;";
          mysql_query($sql, $this->link);

          return $st;
     }

      public function cancel_ticket($ticketno, $uid) {
          $sql = "SELECT * FROM `bileta_shumat` WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".date("Y-m-d")."' AND `time`>='".date("H:i:s", time()-60*20)."' ;";
          $result = mysql_query($sql, $this->link);
          if (mysql_num_rows($result) < 1) {
               return "<p>Koha p&euml;r anullimin e k&euml;saj bilete ka kaluar.</p>";
          } else {
               $row = mysql_fetch_assoc($result);
               $shuma = $row[shuma];

               $sql = "SELECT `kredit` FROM `perdorues` WHERE `id`='{$uid}' ;";
               $result = mysql_query($sql, $this->link);
               $row = mysql_fetch_assoc($result);
               $kredit = $row[kredit];

               $sql = "UPDATE `perdorues` SET `kredit`='".$kredit+$shuma."' WHERE `id`='{$uid}' ;";
               $result = mysql_query($sql, $this->link);

               $sql2 = "UPDATE `bileta_shumat` SET `status`='2' WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".date("Y-m-d")."' ;";
               mysql_query($sql2, $this->link);
               return "<p>Bileta u anullua.</p>";
          }
      }

      public function is_canceled($ticketno, $uid) {
           $sql = "SELECT * FROM `bileta_shumat` WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='".date("Y-m-d")."' AND `time`>='".date("H:i:s", time()-60*20)."' AND `status`='2' ;";
          $result = mysql_query($sql, $this->link);
          if (@mysql_num_rows($result) > 0) {
               return true;
          } else {
               return false;
          }
      }

/*      public function is_winning($ticketno, $date, $uid) {
          $sql = "SELECT * FROM `bileta_shumat` WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='{$date}' AND `status`='3' ;";
          $result = mysql_query($sql, $this->link);
          if (@mysql_num_rows($result) > 0) {
               return TRUE;
          } else {
               return FALSE;
          }
          return FALSE;
      }
*/

      public function get_ticket_status($ticketno, $date, $uid) {
          $ticketno = mysql_real_escape_string($ticketno);
          $date = mysql_real_escape_string($date);
          $uid = mysql_real_escape_string($uid);

          $sql = "SELECT `status` FROM `bileta_shumat` WHERE `nr_bilete`='{$ticketno}' AND `id_op`='{$uid}' AND `date`='{$date}' ;";
          $result = mysql_query($sql, $this->link);
          if (mysql_num_rows($result) > 0) {
               $row = mysql_fetch_assoc($result);
               return $row[status];
          } else {
               return -16;

      }

          return -16;

      }

      

      public function set_ticket_status($status, $ticketno, $date, $uid) {
           $ticketno = mysql_real_escape_string($ticketno);
           $date = mysql_real_escape_string($date);
           $uid = mysql_real_escape_string($uid);

           if (!is_numeric($status)) {
               return ;
           } else {
               $status = mysql_real_escape_string($status);
           }

           $sql = "UPDATE `bileta_shumat` SET `status`='".$status."' WHERE `nr_bilete`='".$ticketno."' AND `date`='".$date."' AND `id_op`='".$uid."' LIMIT 1;";
           if (mysql_query($sql, $this->link)) {
                return $status;
           } else {
               return "Error updating row.";
           }
      }

      public function get_tickets($date, $status) {
          $sql = "SELECT `time`, `nr_bilete`, `id_op`, `shuma`, `total_coe`, `fitimi` FROM `bileta_shumat` WHERE `date`='".$date."' AND `status`='".$status."' AND `finished`='1' ORDER BY `time` ASC ;";
          $result = mysql_query($sql, $this->link);

          while ($row = mysql_fetch_assoc($result)) {
               $ticket[] = $row;
          }
          return $ticket;
      }

      public function get_tickets_K($date, $status, $uid) {
          $sql = "SELECT `time`, `nr_bilete`, `shuma`, `total_coe`, `fitimi` FROM `bileta_shumat` WHERE `id_op`='".$uid."' AND `date`='".$date."' AND `status`='".$status."' AND `finished`='1' ORDER BY `time` ASC ;";
          $result = mysql_query($sql, $this->link);

          while ($row = @mysql_fetch_assoc($result)) {
               $ticket[] = $row;
          }
          return $ticket;
      }

     function rez_list($date) {

     $sql = "SELECT `kodi` as k, `data` as d FROM `skedina_ditore` WHERE `data`='".$date."' JOIN SELECT `kodi` as rk FROM `rezultatet` WHERE `data`='".$date."' ;";
     $result = mysql_query($sql, $this->link);

          while ($row = mysql_fetch_assoc($result)) {
               print_r($row);
          }


     /*
     <form name="check" method="post" action="">

       <table width="500" border="0" cellspacing="1" cellpadding="1">
          foreach
         <tr>
           <td>KODI: {$v[kodi]}</td>
           <td>NDESHJA: {$v[ndeshja}</td>
           <td>REZ_45:
           <input name="rez45_" type="text" size="2" maxlength="2"></td>
           <td>REZ_90:
           <input name="rez_90" type="text" size="2" maxlength="2"></td>
         </tr>

       </table>
     </form>
     */
     }

}

?>

