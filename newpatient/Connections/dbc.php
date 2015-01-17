<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_dbc = "localhost";
$database_dbc = "nacprodt_emr";
$username_dbc = "nacprodt_emr";
$password_dbc = "diamondpony8";
$dbc = mysql_pconnect($hostname_dbc, $username_dbc, $password_dbc) or trigger_error(mysql_error(),E_USER_ERROR); 
?>