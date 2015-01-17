<?php require_once('../../Connections/dbc.php'); ?>
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

$colname_Recordset1 = "-1";
if (isset($_GET['patientID'])) {
  $colname_Recordset1 = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_Recordset1 = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $dbc) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

mysql_select_db($database_dbc, $dbc);
$query_rsUser = "SELECT fname, lname FROM tbl_patient";
$rsUser = mysql_query($query_rsUser, $dbc) or die(mysql_error());
$row_rsUser = mysql_fetch_assoc($rsUser);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Better Weigh Coupon</title>
<link rel="stylesheet" type="text/css" href="style.css" />
<link rel="stylesheet" type="text/css" href="print.css" media="print" />
<script>
function printpage()
  {
  window.print()
  }
</script>
</head>

<body>

<div id="masthead">
<img src="images/header.jpg" width="830" height="241">
</div>	
<div id="print"><input type="button" value="Print this Coupon" onclick="printpage()"></div>

<div id="coupon">

<div id="name"><?php echo $row_Recordset1['fname']; ?> <?php echo $row_Recordset1['lname']; ?></div>
<img src="images/coupon.jpg" alt="coupon" width="800" height="504" />

</div>

</body>
</html>
<?php
mysql_free_result($Recordset1);
?>
