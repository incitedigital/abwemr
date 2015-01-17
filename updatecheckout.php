<?php require_once('Connections/dbc.php'); ?>
<?php

$productID = $_GET['productID']; 

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

$colname_rsProducts = "-1";
if (isset($_GET['productID'])) {
  $colname_rsProducts = $_GET['productID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsProducts = sprintf("SELECT * FROM tbl_products WHERE productID = %s", GetSQLValueString($colname_rsProducts, "int"));
$rsProducts = mysql_query($query_rsProducts, $dbc) or die(mysql_error());
$row_rsProducts = mysql_fetch_assoc($rsProducts);
$totalRows_rsProducts = mysql_num_rows($rsProducts);


mysql_select_db($database_dbc, $dbc);
$queryupdate = "UPDATE tbl_products SET status = 0 WHERE productID = '$productID'";
$rsproducts = mysql_query($queryupdate, $dbc) or die(mysql_error());
header('Location: checkout.php');


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>
<?php
mysql_free_result($rsProducts);
?>
