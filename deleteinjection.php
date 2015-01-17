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

if ((isset($_GET['tbl_injectionID'])) && ($_GET['tbl_injectionID'] != "")) {
  $deleteSQL = sprintf("DELETE FROM tbl_injection WHERE tbl_injectionID=%s",
                       GetSQLValueString($_GET['tbl_injectionID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($deleteSQL, $dbc) or die(mysql_error());

  $deleteGoTo = "viewpatient.php?patientID=" . $row_patientID['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_patientID = "-1";
if (isset($_GET['patientID'])) {
  $colname_patientID = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_patientID = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_patientID, "int"));
$patientID = mysql_query($query_patientID, $dbc) or die(mysql_error());
$row_patientID = mysql_fetch_assoc($patientID);
$totalRows_patientID = mysql_num_rows($patientID);

mysql_free_result($patientID);
?>
