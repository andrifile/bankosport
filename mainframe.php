<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Indeksi</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="stili.css" rel="stylesheet" type="text/css">
</head>

<body>
<?php
@require_once("settings.php");
@require_once("Skedina.php");

$s = new Skedina(HOST, USER, PASSWORD, DATABASE);

?>
<div id="daily_message">
<?php echo $s->get_last_message(); ?>
</div>
</body>
</html>
