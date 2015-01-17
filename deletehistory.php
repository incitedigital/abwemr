<?php

$noteID =  $_GET['noteID'];
$patientID= $_GET['patientID']; 
?>

<?php require_once('Connections/dbc.php'); ?>
<?php
mysql_select_db($database_dbc, $dbc);
$patientquery = "DELETE FROM tbl_notes WHERE noteID = '$noteID'";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());
?>

<meta http-equiv="refresh" content="1;URL=viewpatient.php?patientID=<?php echo $patientID; ?>">

<?php include('includes/loading.php'); ?>