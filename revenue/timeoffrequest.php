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

mysql_select_db($database_dbc, $dbc);
$query_rsTimeoff = "SELECT tbl_timeoff.*, firstname,lname FROM tbl_timeoff JOIN tbl_admin ON tbl_timeoff.adminID = tbl_admin.adminID WHERE status = '0'";
$rsTimeoff = mysql_query($query_rsTimeoff, $dbc) or die(mysql_error());
$row_rsTimeoff = mysql_fetch_assoc($rsTimeoff);
$totalRows_rsTimeoff = mysql_num_rows($rsTimeoff);

$colname_rsManager = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsManager = $_SESSION['MM_Username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsManager = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsManager, "text"));
$rsManager = mysql_query($query_rsManager, $dbc) or die(mysql_error());
$row_rsManager = mysql_fetch_assoc($rsManager);
$totalRows_rsManager = mysql_num_rows($rsManager);

?>
<?php include('includes/headeradmin.php'); ?>
<div class="container">
<div class="row">
<div class="col-md-3"><h1>Approve Timeoff</h1></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>

<div class="row">

<div class="col-md-12">
<div class="panel panel-default">
 
  <div class="panel-body">
    <?php if ($totalRows_rsTimeoff == 0) { // Show if recordset empty ?>
      No timeoff requests submitted
  <?php } // Show if recordset empty ?>
    <?php if ($totalRows_rsTimeoff > 0) { // Show if recordset not empty ?>
  <table class="table">
    <thead>
      <tr>
        <td>Name</td>
        <td>Start Date</td>
        <td>End Date</td>
        <td>Status</td>
        
        
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo $row_rsTimeoff['lname']; ?>, <?php echo $row_rsTimeoff['firstname']; ?></td>
          <td><?php echo date("m/d/Y", strtotime($row_rsTimeoff['startdate'])); ?></td>
          <td><?php echo date("m/d/Y",strtotime($row_rsTimeoff['enddate'])); ?></td>
          
          <td><a href="approverequest.php?requestID=<?php echo $row_rsTimeoff['requestID']; ?>&toID=<?php echo $row_rsTimeoff['adminID']; ?>&adminID=<?php echo $row_rsManager['adminID']; ?>" class="btn-info btn-xs">Approve Request</a> | <a href="deleterequest.php?requestID=<?php echo $row_rsTimeoff['requestID']; ?>&toID=<?php echo $row_rsTimeoff['adminID']; ?>&adminID=<?php echo $row_rsManager['adminID']; ?>" class="btn-danger btn-xs confirm">Deny Request</a></td>
        </tr>
        <?php } while ($row_rsTimeoff = mysql_fetch_assoc($rsTimeoff)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>
  </div>
  
</div>
</div>
</div>






</div> <!-- end container -->

<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsTimeoff);

mysql_free_result($rsManager);
?>
