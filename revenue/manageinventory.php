<?php session_start(); ?>
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "eventscalendar")) {
	$start = date('Y-m-d', strtotime($_POST['start']));
$end = date('Y-m-d', strtotime($_POST['end']));
$timeoff = $_POST['timeoff'];
  $insertSQL = sprintf("INSERT INTO evenement (title, `start`, `end`,timeoff) VALUES (%s, '$start', '$end', '$timeoff')",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['start'], "date"),
                       GetSQLValueString($_POST['end'], "date"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "manageevents.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dbc, $dbc);
$query_rsEvents = "SELECT * FROM evenement ORDER BY id DESC";
$rsEvents = mysql_query($query_rsEvents, $dbc) or die(mysql_error());
$row_rsEvents = mysql_fetch_assoc($rsEvents);
$totalRows_rsEvents = mysql_num_rows($rsEvents);

$maxRows_rsInventory = 10;
$pageNum_rsInventory = 0;
if (isset($_GET['pageNum_rsInventory'])) {
  $pageNum_rsInventory = $_GET['pageNum_rsInventory'];
}
$startRow_rsInventory = $pageNum_rsInventory * $maxRows_rsInventory;

mysql_select_db($database_dbc, $dbc);
$query_rsInventory = "SELECT * FROM tbl_inventory";
$query_limit_rsInventory = sprintf("%s LIMIT %d, %d", $query_rsInventory, $startRow_rsInventory, $maxRows_rsInventory);
$rsInventory = mysql_query($query_limit_rsInventory, $dbc) or die(mysql_error());
$row_rsInventory = mysql_fetch_assoc($rsInventory);

if (isset($_GET['totalRows_rsInventory'])) {
  $totalRows_rsInventory = $_GET['totalRows_rsInventory'];
} else {
  $all_rsInventory = mysql_query($query_rsInventory);
  $totalRows_rsInventory = mysql_num_rows($all_rsInventory);
}
$totalPages_rsInventory = ceil($totalRows_rsInventory/$maxRows_rsInventory)-1;

$colname_rsEmpID = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsEmpID = $_SESSION['MM_Username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsEmpID = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsEmpID, "text"));
$rsEmpID = mysql_query($query_rsEmpID, $dbc) or die(mysql_error());
$row_rsEmpID = mysql_fetch_assoc($rsEmpID);
$totalRows_rsEmpID = mysql_num_rows($rsEmpID);

mysql_select_db($database_dbc, $dbc);
$query_rsInventoryMgmt = "SELECT tbl_inventory.*, firstname, lname FROM tbl_inventory JOIN tbl_admin on tbl_inventory.adminID = tbl_admin.adminID ";
$rsInventoryMgmt = mysql_query($query_rsInventoryMgmt, $dbc) or die(mysql_error());
$row_rsInventoryMgmt = mysql_fetch_assoc($rsInventoryMgmt);
$totalRows_rsInventoryMgmt = mysql_num_rows($rsInventoryMgmt);
?>
<?php include('includes/headeradmin.php'); ?>
<div class="container">
<div class="row">
<div class="col-md-3"><h1>Manage Inventory Requests</h1></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>

<div class="row">

<div class="col-md-12">
<div class="panel panel-default">
 
  <div class="panel-body">
    <?php if ($totalRows_rsInventoryMgmt == 0) { // Show if recordset empty ?>
      No Inventory Requests Submitted
  <?php } // Show if recordset empty ?>
  <?php if ($totalRows_rsInventoryMgmt > 0) { // Show if recordset not empty ?>
  <table border="0" class="table">
    <tr>
      
      
      <td>Date</td>
      <td>Request</td>
      <td>Submitted by</td>
      <td>Delete</td>
    </tr>
    <?php do { ?>
      <tr>
        
        
        <td><?php echo date('m/d/Y', strtotime($row_rsInventoryMgmt['date'])); ?></td>
        <td><?php echo $row_rsInventoryMgmt['request']; ?></td>
        <td><?php echo $row_rsInventoryMgmt['lname']; ?>, <?php echo $row_rsInventoryMgmt['firstname']; ?> </td>
        <td><a href="deleteinventory.php?inventoryID=<?php echo $row_rsInventoryMgmt['inventoryID']; ?>"  class="confirm">Delete</a></td>
      </tr>
      <?php } while ($row_rsInventoryMgmt = mysql_fetch_assoc($rsInventoryMgmt)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
  </div>
  
</div>
</div>
</div>




</div> <!-- end container -->

<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsEvents);

mysql_free_result($rsEmpID);

mysql_free_result($rsInventoryMgmt);
?>
