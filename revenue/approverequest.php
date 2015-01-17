<?php session_start(); ?>
<?php require_once('Connections/dbc.php'); ?>
<?php 
	$toID = $_GET['toID'];
	$adminID = $_GET['adminID'];
	$subject =  "Your timeoff request was approved.";
	$message = "Your timeoff request was approved. Please contact your manager for more information.";	
 	mysql_select_db($database_dbc, $dbc);
 
  $sql = "INSERT INTO tbl_message (adminID, toID, subject, message) VALUES ('$adminID', '$toID', '$subject', '$message')";
  $Result1 = mysql_query($sql, $dbc) or die(mysql_error());

?>
<?php

$requestID = $_GET['requestID']; 

?>
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

$colname_rsUpdate = "-1";
if (isset($_GET['requestID'])) {
  $colname_rsUpdate = $_GET['requestID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsUpdate = sprintf("SELECT * FROM tbl_timeoff WHERE requestID = %s", GetSQLValueString($colname_rsUpdate, "int"));
$rsUpdate = mysql_query($query_rsUpdate, $dbc) or die(mysql_error());
$row_rsUpdate = mysql_fetch_assoc($rsUpdate);
$totalRows_rsUpdate = mysql_num_rows($rsUpdate);

mysql_select_db($database_dbc, $dbc);
$query_rsUpdate2 = "UPDATE tbl_timeoff SET status = 1 WHERE requestID = '$requestID'";
$rsUpdate2 = mysql_query($query_rsUpdate2, $dbc) or die(mysql_error());
header('Location: http://www.abwemr.com/revenue/timeoffrequest.php');



mysql_free_result($rsUpdate);
?>

