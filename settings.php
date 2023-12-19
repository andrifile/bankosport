<?php

DEFINE("HOST", "dbhost");
DEFINE("USER", "dbuser");
DEFINE("PASSWORD", "dbpass");
DEFINE("DATABASE", "bankosport");

//User access levels
DEFINE("L_ADMIN",  1);
DEFINE("L_BOSS",   2);
DEFINE("L_EDITOR", 3);
DEFINE("L_CLIENT", 4);
DEFINE("L_NONE",   5);

DEFINE("COOKIE_PREF", "bankosport");

DEFINE("PERCENT", 0.08); //Percent goes to the operator.
DEFINE("PERCENT_F", 0.08); //Percent goes to the operator.
DEFINE("PERCENT_S", 0.03); //Percent from single-game tickets
DEFINE("W_LIMIT", 250000); //Winning limit, disallow the ticket altogether.
DEFINE("S_LIMIT", 10000); //Sum limit, just put ticket on wait.

//
DEFINE("STA_BROKEN",    -2);
DEFINE("STA_WAITING",   -1);
DEFINE("STA_REFUSED",    0);
DEFINE("STA_ACCEPTED",   1);
DEFINE("STA_REMOVED",    2);
DEFINE("STA_WINNING",    3);
DEFINE("STA_LOSER",      4);

//-2:e parregullt, -1:ne pritje, 0:refuzuar, 1:pranuar, 2:anulluar, 3:fituese

date_default_timezone_set("Europe/Berlin");
?>
