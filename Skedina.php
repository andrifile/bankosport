<?php

/*
 * class Skedina
 *  private methods: resource conn_mysql(string $host, string $user, string $password, string $db);
 *                   close_mysql(resource $link);
 *                   array is_ok($postarray);
 *
 *  public methods:  to_db($array);
 *                   printout();
 *
 *
 *
 *
 */

require_once("settings.php");

class Skedina {

     protected $link = NULL;
     private $ndeshjet;

     function __construct($host, $user, $password, $db) {
          $this->link = $this->conn_mysql($host, $user, $password, $db);
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
          @mysql_close($link);
     }

     protected function is_ok($postarray) {
          if (isset($postarray) && isset($postarray['Submit'])) {
               foreach ($postarray as $k => $v) {
                    if (strstr($k, "txt")) {
                         $is_good[$k] = $v;
                    }
               }
          }
          return array($is_good);
     }

     public function to_db($array) {
          $txtarray = $this->is_ok($array);
          $txtarray = $txtarray[0];
		  $allvals = explode("\r\n", $txtarray['txtkoeList']);
		  
		  //data stuff
		  if (strlen($txtarray['txtdata']) < 1) {
			   return ;
		  } else if (strlen($txtarray['txtdata']) < 10) {
			   echo("<script>alert('Data nuk eshte e saktë për ndeshjen me kodin {$txtarray[1]}.'); this.refresh</script>");
			   return ;
		  }

		  //security
		  $txtarray = array_map("mysql_real_escape_string", $txtarray);
		  $txtarray = array_map("trim", $txtarray);

		  //fix datetime
		  $txtarray['txtdata'] = preg_replace('/(\d{2})-(\d{2})-(\d{4})/i', '$3-$1-$2', $txtarray['txtdata']);

		  $v = $txtarray;
		  unset($txtarray);

		  foreach($allvals as $n) {
			  $vals = preg_split('/[,]+/i', $n, -1, PREG_SPLIT_NO_EMPTY);
			  $vals = array_map("trim", $vals);
			  $vals = array_pad($vals, 30, 0);

			  $sql  = "INSERT INTO `skedina_ditore` ( `id` , `data` , `kodi` , `ndeshja` , `ora` , `kampionati` , `RF_1` , `RF_X` , `RF_2` , `DSH_1X` , `DSH_12` , `DSH_X2` , `RF45_1` , `RF45_X` , `RF45_2` , `4590_1-1` , `4590_1-X` , `4590_1-2` , `4590_X-1` , `4590_X-X` , `4590_X-2` , `4590_2-1` , `4590_2-X` , `4590_2-2` , `SG45_1p` , `SG45_2p` , `SGP_0-1` , `SGP_0-2` , `SGP_2-3` , `SGP_3p` , `SGP_4p` , `SGP_4-6` , `SGP_7p` , `aktive` ) ";
			  $sql .= "VALUES (NULL, '".$v['txtdata']."',  '".$vals[0]."',  '".$vals[1]."',  '".$vals[2]."', '".$v['txtkampionati']."', '{$vals[3]}', '{$vals[4]}', '{$vals[5]}', '{$vals[6]}', '{$vals[7]}', '{$vals[8]}', '{$vals[9]}', '{$vals[10]}', '{$vals[11]}', '{$vals[12]}', '{$vals[13]}', '{$vals[14]}', '{$vals[15]}', '{$vals[16]}', '{$vals[17]}', '{$vals[18]}', '{$vals[19]}', '{$vals[20]}', '{$vals[21]}', '{$vals[22]}', '{$vals[23]}', '{$vals[24]}', '{$vals[25]}', '{$vals[26]}', '{$vals[27]}', '{$vals[28]}', '{$vals[29]}', '1') ;";
			  if (!mysql_query($sql, $this->link))
				   die("Nuk mund të shkruhen vlerat në databazë: " . mysql_error());
			}
     }

     /* @returns: nothing */

     public function printout() {
          foreach($this->ndeshjet as $v)
               echo $v."<br>";
     }

     /* @returns: Next receipt serial no. from the database */
     public function next_serial() {
          $sql = "SELECT `nr_sked` FROM `skedinat`";
          $result = mysql_query($sql, $this->link);
          $ids = mysql_fetch_array($result);
          $cid = array_pop($ids);
          return $cid+1;
     }

     /* @returns: a Javascript-formatted array of values */
     public function table_to_array($table) {
          $sql = "SELECT * FROM `{$table}` WHERE data>='".date("Y-m-d")."' AND aktive='1' ;";
     //   $sql = "SELECT * FROM `{$table}` WHERE aktive=1 ;";
          $result = mysql_query($sql, $this->link);

          $i = 1;
          $return = "var ndeshjet = new Array();\n";
          while ($row = mysql_fetch_array($result)) {
               $return .= "ndeshjet['{$row[kodi]}'] = '{$row[ndeshja]}';\n";
          }

          return $return;
     }

     //This is to be LOOKED INTO
     public function table_vlist($datenow, $oranow) {
//          $datenow = date("Y-m-d");
//          $oranow = date("H:i:s", time()+(60*10));
          $sql = "SELECT * FROM `skedina_ditore` WHERE (`data`='{$datenow}' AND `data`>='{$datenow}') AND `ora`>='{$oranow}' ORDER BY `ora` ASC ;";
          $result = mysql_query($sql, $this->link);
          while ($row = mysql_fetch_assoc($result)) {
               $n[] = $row;
          }

          if (count($n) < 1)
               $n[0][ndeshja] = "Ska ndeshje";
          return $n;
     }

     public function is_valid($id) {
     }

     public function get_game_shortcuts() {
          $sql = "SELECT * FROM `lojrat` WHERE `aktive`=FALSE ;";
          $result = mysql_query($sql, $this->link);
          $i=0;
          while ($row = mysql_fetch_array($result)) {
               $return[$i][short] = $row[short];
               $return[$i][name] = $row[grupi]." ".$row[lloji];
               $i++;
          }

          return $return;
     }

     //Get column name from shortcut
     public function get_col_name($short) {
          $sql = "SELECT `grupi` FROM `lojrat` WHERE `short`='{$short}' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $grupi = mysql_fetch_row($result);

          $sql = "SELECT `lloji` FROM `lojrat` WHERE `short`='{$short}' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $loja = mysql_fetch_row($result);

          $grupi=preg_replace("/\\//", "", $grupi);
          $loja=preg_replace("/\\+/", "p", $loja);

          return $grupi[0]."_".$loja[0];

     }

     //Get value name for $kodi cross $colname
     public function get_val($kodi, $short) {
          $col = $this->get_col_name($short);
          $sql = "SELECT `{$col}` FROM `skedina_ditore` WHERE `kodi`='{$kodi}' AND `data`>='".date("Y-m-d")."' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          if (@mysql_num_rows($result) > 0)
               $out = mysql_fetch_row($result);
          else
               return ;
          return $out[0];
    }

     public function select_game_quotes($id) {
          $sql = "SELECT * FROM `skedina_ditore` WHERE `id`='{$id}' ;";
          $result = mysql_query($sql, $this->link);
          $row = @mysql_fetch_assoc($result);
          unset($row['id'], $row['data'], $row['kodi'], $row['ora'], $row['ndeshja'], $row['kampionati'], $row['teke'], $row['aktive']);
          return $row;
     }
     
     public function select_game_quotes_W($kodi, $data) {
          $sql = "SELECT * FROM `skedina_ditore` WHERE `kodi`={$kodi} AND `data`='{$data}' LIMIT 1 ;";
          $result = mysql_query($sql, $this->link);
          $row = @mysql_fetch_array($result);
          unset($row['id'], $row['data'], $row['kodi'], $row['ora'], $row['ndeshja'], $row['kampionati'], $row['teke'], $row['aktive']);
          unset($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7]);
          return $row;
     }
     
     //Change game quota by ID and column name
     public function set_quota($id, $game, $value) {
          $sql = "UPDATE `skedina_ditore` SET `{$game}` = '{$value}' WHERE `id`='{$id}' LIMIT 1 ;";
          if (mysql_query($sql, $this->link))
               return true;
          return false;
     }
     
     //Update game status by ID
     public function update_game_status($id, $type, $status) {
          if ($type == 1) {
               $sql = "UPDATE `skedina_ditore` SET `aktive`='{$status}' WHERE `id`='{$id}' LIMIT 1 ;";
               if (mysql_query($sql, $this->link))
                    return true;
               return false;
          } else if ($type == 2) {
               $sql = "UPDATE `skedina_ditore` SET `teke`='{$status}' WHERE `id`='{$id}' LIMIT 1 ;";
               if (mysql_query($sql, $this->link))
                    return true;
               return false;
          }
     }
     
     //Remove a game by ID
     public function remove_game_id($id) {
          $id = mysql_real_escape_string($id);
          $sql = "DELETE FROM `skedina_ditore` WHERE `skedina_ditore`.`id`={$id} LIMIT 1 ;";
          if (mysql_query($sql, $this->link))
               return true;
     }

     // Get the last message from db: for showing MOTD
     public function get_last_message() {
          $sql = "SELECT `text` FROM `messages` WHERE `active`='1' ;";
          $result = mysql_query($sql, $this->link);
          while ($row = @mysql_fetch_assoc($result)) {
               $text = $row[text];
          }
          return $text;
     }
     
     public function write_results($code, $name, $date, $r45, $r90) {
          $sql = "INSERT INTO `rezultatet` ( `id` , `data` , `kodi` , `emri` , `rez_45` , `rez_90` ) VALUES ( NULL , '{$date}', '{$code}', ' ', '{$r45}', '{$r90}' );";
          print_r($sql);
          mysql_query($sql, $this->link);
     }
     
     public function read_results($code, $date) {
          $sql = "SELECT `rez_45` as `r45`, `rez_90` as `r90` FROM `rezultatet` WHERE `kodi`='{$code}' and `data`='{$date}';";
          $result = mysql_query($sql, $this->link);
          $v = mysql_fetch_assoc($result);
          $r45 = preg_split("/,/i", $v['r45'], 2, PREG_SPLIT_NO_EMPTY);
          $r90 = preg_split("/,/i", $v['r90'], 2, PREG_SPLIT_NO_EMPTY);
          if ($r45[0] ==  "-1") {
                // call postponed
          }
          if (($r45[0] == NULL) | ($r90[0] == NULL) | ($r45[0] == -1) | ($r90[0] == -1))
                return NULL;
          if ($r90[0] > $r90[1])
                $tail[] = 11; //rf 1
          if ($r90[0] == $r90[1])
                $tail[] = 12; //rf x
          if ($r90[0] < $r90[1])
                $tail[] = 13; //rf 2
          if ($r90[0] >= $r90[1])
                $tail[] = 21; //dsh 1x
          if (($r90[0] > $r90[1]) | ($r90[0] < $r90[1]))
                $tail[] = 22; //dsh 12
          if ($r90[0] <= $r90[1])
                $tail[] = 23; //dsh x2
          if ($r45[0] > $r45[1])
                $tail[] = 31; //rf45 1
          if ($r45[0] == $r45[1])
                $tail[] = 32; //rf45 x
          if ($r45[0] < $r45[1])
                $tail[] = 33; //rf45 2
          if (($r45[0] > $r45[1]) & ($r90[0] > $r90[1]))
                $tail[] = 41; //45/90_1-1
          if (($r45[0] > $r45[1]) & ($r90[0] == $r90[1]))
                $tail[] = 42; //45/90_1-X
          if (($r45[0] > $r45[1]) & ($r90[0] > $r90[1]))
                $tail[] = 43; //45/90_1-2
          if (($r45[0] == $r45[1]) & ($r90[0] > $r90[1]))
                $tail[] = 44; //45/90_X-1
          if (($r45[0] == $r45[1]) & ($r90[0] == $r90[1]))
                $tail[] = 45; //45/90_X-X
          if (($r45[0] == $r45[1]) & ($r90[0] < $r90[1]))
                $tail[] = 46; //45/90_X-2
          if (($r45[0] < $r45[1]) & ($r90[0] > $r90[1]))
                $tail[] = 47; //45/90_2-1
          if (($r45[0] < $r45[1]) & ($r90[0] == $r90[1]))
                $tail[] = 48; //45/90_2-X
          if (($r45[0] < $r45[1]) & ($r90[0] < $r90[1]))
                $tail[] = 49; //45/90_2-2
          if (($r45[0]+$r45[1]) >= 1)
                $tail[] = 51; //SG45_1p
          if (($r45[0]+$r45[1]) >= 2)
                $tail[] = 52; //SG45_2p
          if (($r90[0]+$r90[1]) <= 1)
                $tail[] = 53; //SGP_0-1
          if (($r90[0]+$r90[1]) <= 2)
                $tail[] = 54; //SGP_0-2
          if ((($r90[0]+$r90[1]) >= 2) & (($r90[0]+$r90[1]) <= 3))
                $tail[] = 55; //SGP_2-3
          if (($r90[0]+$r90[1]) >= 3)
                $tail[] = 56; //SGP_3p
          if (($r90[0]+$r90[1]) >= 4)
                $tail[] = 57; //SGP_4p
          if ((($r90[0]+$r90[1]) >= 4) & (($r90[0]+$r90[1]) <= 6))
                $tail[] = 58; //SGP_4-6
          if (($r90[0]+$r90[1]) >= 7)
                $tail[] = 59; //SGP_7p
          return $tail;
     }
     
     public function burn($code, $date, $tail) {
        if ($tail === NULL)
                return ;
        $tail = array_map("intval", $tail);
        echo "burn(): code={$code}, date={$date}<br>";
        $sql = "SELECT `date`, `id_op`, `nr_bilete`, `entry_code`, `game_type` FROM `bileta` WHERE `entry_code`='{$code}' AND `date`='{$date}'";
        $result = mysql_query($sql, $this->link);
        while ($k = mysql_fetch_assoc($result)) {
                $t = array_search(intval($k['game_type']), $tail, TRUE);
                if ($t === FALSE) {
                     echo "ticket: {$k[nr_bilete]} ; idop: {$k[id_op]} ; gametype: {$k[game_type]}; tail: {$tail[$t]}; key: {$t}<br>";
                     $sqlx = "UPDATE `bileta_shumat` SET `status` = '4' WHERE `nr_bilete`='{$k[nr_bilete]}' AND `status`='1' AND `id_op`='{$k[id_op]}' AND `date`='{$date}' LIMIT 1 ;";
                     mysql_query($sqlx, $this->link);
                }


        }
        $lastq = "UPDATE `bileta_shumat` SET `status`='3' WHERE `date`='{$date}' AND `status`='1' ;";
       // mysql_query($lastq, $this->link);
     }
}
?>
