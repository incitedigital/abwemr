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

if ((isset($_GET['disciplineID'])) && ($_GET['disciplineID'] != "")) {
  $deleteSQL = sprintf("DELETE FROM tbl_discipline WHERE disciplineID=%s",
                       GetSQLValueString($_GET['disciplineID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($deleteSQL, $dbc) or die(mysql_error());

  $deleteGoTo = "employeedetails.php?adminID=" . $row_rsadmin['adminID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $deleteGoTo .= (strpos($deleteGoTo, '?')) ? "&" : "?";
    $deleteGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $deleteGoTo));
}

$colname_rsadmin = "-1";
if (isset($_GET['adminID'])) {
  $colname_rsadmin = $_GET['adminID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsadmin = sprintf("SELECT * FROM tbl_admin WHERE adminID = %s", GetSQLValueString($colname_rsadmin, "int"));
$rsadmin = mysql_query($query_rsadmin, $dbc) or die(mysql_error());
$row_rsadmin = mysql_fetch_assoc($rsadmin);
$totalRows_rsadmin = mysql_num_rows($rsadmin);

mysql_free_result($rsadmin);
?>