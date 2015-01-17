<?php
require_once('../Connections/dbc.php');
mysql_select_db($database_dbc, $dbc);
$json = array( );
$result = mysql_query("SELECT * FROM tbl_register",$dbc) or die (mysql_error());
while( $row = mysql_fetch_assoc( $result ) ) {
    $json[] = $row;
}
echo json_encode( $json );


?>