<?php

$patientpackageID = $_GET['patientpackageID'];
$patientID = $_GET['patientID'];
$adminID = $_GET['adminID'];

?>

<?php require_once('Connections/dbc.php'); ?>
<?php
mysql_select_db($database_dbc, $dbc);
$patientquery = "UPDATE tbl_patientpackage SET status = 'open' WHERE patientPackageID = '$patientpackageID'";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());


?>

<?php
mysql_select_db($database_dbc, $dbc);
$packagequery = "UPDATE tbl_queue SET status = 0 WHERE patientID = '$patientID'";
$rsPackage = mysql_query($packagequery, $dbc) or die(mysql_error());
?>



<meta http-equiv="refresh" content="1;URL=checkout.php">


<?php include('includes/loading.php'); ?>