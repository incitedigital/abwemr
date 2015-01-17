<?php require_once('../Connections/dbc.php'); ?>
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

mysql_select_db($database_dbc, $dbc);
$query_rsTimeoffCount = "SELECT count(requestID) FROM tbl_timeoff WHERE status = '0'";
$rsTimeoffCount = mysql_query($query_rsTimeoffCount, $dbc) or die(mysql_error());
$row_rsTimeoffCount = mysql_fetch_assoc($rsTimeoffCount);
$totalRows_rsTimeoffCount = mysql_num_rows($rsTimeoffCount);

mysql_select_db($database_dbc, $dbc);
$query_rsInventory = "SELECT count(inventoryID) as count FROM tbl_inventory";
$rsInventory = mysql_query($query_rsInventory, $dbc) or die(mysql_error());
$row_rsInventory = mysql_fetch_assoc($rsInventory);
$totalRows_rsInventory = mysql_num_rows($rsInventory);
?>

<div class="btn-group pull-right">
<a href="addfiles.php" class="btn btn-default pull-right">Manage Documents</a>
<a href="timeoffrequest.php" class="btn btn-default pull-right">Timeoff Requests <span class="badge"><?php echo $row_rsTimeoffCount['count(requestID)']; ?></span></a>
<a href="manageinventory.php" class="btn btn-default pull-right">Inventory Requests <span class="badge"><?php echo $row_rsInventory['count']; ?></span></a>
<!--<button type="button" data-toggle="modal" data-target="#sendmessage" class="btn btn-default pull-right pushdown">Send Message</button> -->
<a href="employees.php" class="btn btn-default pull-right">Employees</a>
<a href="employeemanager.php" class="btn btn-default pull-right">Dashboard</a>


</div>

<?php
mysql_free_result($rsTimeoffCount);

mysql_free_result($rsInventory);
?>
