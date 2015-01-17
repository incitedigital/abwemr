<?php

$vitalID =  $_GET['vitalID'];
$patientID= $_GET['patientID']; 
?>

<?php require_once('Connections/dbc.php'); ?>
<?php
mysql_select_db($database_dbc, $dbc);
$patientquery = "DELETE FROM tbl_vitals WHERE vitalID = '$vitalID'";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());
?>

<meta http-equiv="refresh" content="1;URL=viewpatient.php?patientID=<?php echo $patientID; ?>">

<?php include('includes/loading.php'); ?>