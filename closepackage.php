<?php

$patientpackageID =  $_GET['patientpackageID'];
$patientID = $_GET['patientID'];
?>

<?php require_once('Connections/dbc.php'); ?>
<?php
mysql_select_db($database_dbc, $dbc);
$patientquery = "UPDATE tbl_patientpackage set status = 'closed' where patientPackageID = '$patientpackageID'";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());
?>

<meta http-equiv="refresh" content="1;URL=viewpatient.php?patientID=<?php echo $patientID; ?>">

<?php include('includes/loading.php'); ?>