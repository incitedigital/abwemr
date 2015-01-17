<?php

$patientID = $_GET['patientID'];

?>

<?php require_once('Connections/dbc.php'); ?>
<?php
mysql_select_db($database_dbc, $dbc);
$patientquery = "UPDATE tbl_queue SET status = 0 WHERE patientID = '$patientID'";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());
?>

<meta http-equiv="refresh" content="1;URL=dashboard.php">

<?php include('includes/loading.php'); ?>