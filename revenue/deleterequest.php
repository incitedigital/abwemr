<?php require_once('Connections/dbc.php'); ?>

<?php 
	$toID = $_GET['toID'];
	$adminID = $_GET['adminID'];
	$subject =  "Your timeoff request was not approved.";
	$message = "Your timeoff request was not approved. Please contact your manager for more information.";	
 	mysql_select_db($database_dbc, $dbc);
 
  $sql = "INSERT INTO tbl_message (adminID, toID, subject, message) VALUES ('$adminID', '$toID', '$subject', '$message')";
  $Result1 = mysql_query($sql, $dbc) or die(mysql_error());

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

if ((isset($_GET['requestID'])) && ($_GET['requestID'] != "")) {
  $deleteSQL = sprintf("DELETE FROM tbl_timeoff WHERE requestID=%s",
                       GetSQLValueString($_GET['requestID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($deleteSQL, $dbc) or die(mysql_error());

  $deleteGoTo = "timeoffrequest.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}
?>
