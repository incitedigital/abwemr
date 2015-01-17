<?php session_start(); ?>
<?php

$patientpackageID =  $_GET['patientpackageID'];
$patientID = $_GET['patientID'];
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

$colname_rspatient = "-1";
if (isset($_GET['patientID'])) {
  $colname_rspatient = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rspatient = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rspatient, "int"));
$rspatient = mysql_query($query_rspatient, $dbc) or die(mysql_error());
$row_rspatient = mysql_fetch_assoc($rspatient);
$totalRows_rspatient = mysql_num_rows($rspatient);

mysql_select_db($database_dbc, $dbc);
$patientquery = "INSERT INTO tbl_injection (patient_ID, patientpackageID, username) VALUES ('$patientID', '$patientpackageID','$_SESSION[MM_Username]')";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());

$patientquery2 = "INSERT into tbl_activity (username, action, firstname, lastname,  date, category, centerID) VALUES ('$_SESSION[MM_Username]', 'added an injection for', '$row_rspatient[fname]', '$row_rspatient[lname]', CURDATE(), 'injection', '$_SESSION[centerID]')";
$rsRemove2 = mysql_query($patientquery2, $dbc) or die(mysql_error());
?>

<meta http-equiv="refresh" content="1;URL=viewpatient.php?patientID=<?php echo $patientID; ?>">

<?php include('includes/loading.php'); 

mysql_free_result($rspatient);
?>
