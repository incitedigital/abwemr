<?php session_start(); ?>

<?php

$patientID = $_GET['patientID'];
$adminID = $_GET['username'];
$centerID = $_GET['centerID'];

?>

<?php require_once('Connections/dbc.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_rsPatient = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPatient = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPatient = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsPatient, "int"));
$rsPatient = mysql_query($query_rsPatient, $dbc) or die(mysql_error());
$row_rsPatient = mysql_fetch_assoc($rsPatient);
$totalRows_rsPatient = mysql_num_rows($rsPatient);

mysql_select_db($database_dbc, $dbc);
$patientquery = "INSERT into tbl_queue (patientID, status, date, username, centerID) VALUES ('$patientID', '1',  CURDATE(), '$adminID', '$_SESSION[centerID]')";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());

$patientquery2 = "INSERT into tbl_activity (username, action, firstname, lastname,  date, category, centerID) VALUES ('$_SESSION[MM_Username]', 'added $row_rsPatient[fname] $row_rsPatient[lname] to the queue', '', '', CURDATE(), 'queue', '$_SESSION[centerID]' )";
$rsRemove2 = mysql_query($patientquery2, $dbc) or die(mysql_error());

$patientquery3 = "UPDATE tbl_patient set lastvisitdate = CURDATE() WHERE patientID = '$colname_rsPatient'";
$rsRemove3 = mysql_query($patientquery3, $dbc) or die(mysql_error());
?>

<meta http-equiv="refresh" content="1;URL=dashboard.php">


<?php include('includes/loading.php'); 

mysql_free_result($rsPatient);
?>
