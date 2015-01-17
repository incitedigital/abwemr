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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "adddiscipline")) {
  $insertSQL = sprintf("INSERT INTO tbl_discipline (empID, adminID, message) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['empID'], "int"),
                       GetSQLValueString($_POST['adminID'], "int"),
                       GetSQLValueString($_POST['message'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "employeedetails.php?adminID=" . $row_rsAdmin['adminID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$maxRows_rsAdmin = 10;
$pageNum_rsAdmin = 0;
if (isset($_GET['pageNum_rsAdmin'])) {
  $pageNum_rsAdmin = $_GET['pageNum_rsAdmin'];
}
$startRow_rsAdmin = $pageNum_rsAdmin * $maxRows_rsAdmin;

$colname_rsAdmin = "-1";
if (isset($_GET['adminID'])) {
  $colname_rsAdmin = $_GET['adminID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsAdmin = sprintf("SELECT * FROM tbl_admin WHERE adminID = %s", GetSQLValueString($colname_rsAdmin, "int"));
$query_limit_rsAdmin = sprintf("%s LIMIT %d, %d", $query_rsAdmin, $startRow_rsAdmin, $maxRows_rsAdmin);
$rsAdmin = mysql_query($query_limit_rsAdmin, $dbc) or die(mysql_error());
$row_rsAdmin = mysql_fetch_assoc($rsAdmin);

if (isset($_GET['totalRows_rsAdmin'])) {
  $totalRows_rsAdmin = $_GET['totalRows_rsAdmin'];
} else {
  $all_rsAdmin = mysql_query($query_rsAdmin);
  $totalRows_rsAdmin = mysql_num_rows($all_rsAdmin);
}
$totalPages_rsAdmin = ceil($totalRows_rsAdmin/$maxRows_rsAdmin)-1;

$colname_rsdiscipline = "-1";
if (isset($_GET['adminID'])) {
  $colname_rsdiscipline = $_GET['adminID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsdiscipline = sprintf("SELECT * FROM tbl_discipline WHERE empID = %s", GetSQLValueString($colname_rsdiscipline, "int"));
$rsdiscipline = mysql_query($query_rsdiscipline, $dbc) or die(mysql_error());
$row_rsdiscipline = mysql_fetch_assoc($rsdiscipline);
$totalRows_rsdiscipline = mysql_num_rows($rsdiscipline);

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
<div class="col-md-3"><h1>Employees</h1></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>
</div>

<div class="container">
<div class="col-md-2">
 <img src="http://abwemr.com/employeeportal/thumbnail/<?php echo $row_rsAdmin['photo']; ?>" width="150px">
</div>
<div class="col-md-10">
<h3><?php echo $row_rsAdmin['firstname']; ?> <?php echo $row_rsAdmin['lname']; ?> <button type="button" data-toggle="modal" data-target="#adddiscipline" class="btn btn-danger btn-xs pushdown">Add Disciplinary Action</button></h3>
<?php echo $row_rsAdmin['address']; ?><br>
<?php echo $row_rsAdmin['city']; ?> <?php echo $row_rsAdmin['state']; ?> <?php echo $row_rsAdmin['zip']; ?> <br>
Phone: <?php echo $row_rsAdmin['phone']; ?> <br><br>
<strong>Emergency Contact:</strong> <?php echo $row_rsAdmin['emergencycontact']; ?> <br>
<strong>Emergency Contact Phone:</strong>  <?php echo $row_rsAdmin['emergencycontactphone']; ?><br>
<strong>Job Title:</strong>  <?php echo $row_rsAdmin['jobtitle']; ?> <br>
<strong>Employment Status:</strong>  <?php echo $row_rsAdmin['employmentstatus']; ?> <br>
<strong>Hire Date:</strong>  <?php echo date("m/d/Y", strtotime($row_rsAdmin['hiredate'])); ?>

<h4>Disciplinary Actions</h4>
<?php if ($totalRows_rsdiscipline == 0) { // Show if recordset empty ?>
  No disciplinary actions
  <?php } // Show if recordset empty ?>
  <?php if ($totalRows_rsdiscipline > 0) { // Show if recordset not empty ?>
  <table border="0" class="table">
  <thead>
    <tr>
      
      <td colspan="3">Action</td>
    </tr>
    </thead>
    <tbody>
    <?php do { ?>
      <tr>
        <td width="5%"><?php echo date("m/d/Y", strtotime($row_rsdiscipline['date'])); ?></td>
        <td width="90%"><?php echo $row_rsdiscipline['message']; ?></td>
        <td width="5%"><a href="deletediscipline.php?disciplineID=<?php echo $row_rsdiscipline['disciplineID']; ?>&adminID=<?php echo $row_rsdiscipline['empID']; ?>" class="confirm">Delete</a></td>
      </tr>
      <?php } while ($row_rsdiscipline = mysql_fetch_assoc($rsdiscipline)); ?>
      </tbody>
  </table>
  <?php } // Show if recordset not empty ?>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="adddiscipline" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add Disciplinary Action</h4>
      </div>
      <div class="modal-body">
      <form method="POST" action="<?php echo $editFormAction; ?>" name="adddiscipline">
     
        
      	<div class="form-group">
          <label for="message">Enter Message</label>
         <textarea class="form-control" name="message" id="message">
         
         
         </textarea>
        </div>
        
           <input type="hidden" name="empID" value="<?php echo $row_rsAdmin['adminID']; ?>">
          <input type="hidden" name="adminID" value="<?php echo $row_rsManager['adminID']; ?>">
       
        
      <input type="submit" name="submit" class="btn btn-danger" value="Submit Record">
      <input type="hidden" name="MM_insert" value="adddiscipline">
      </form>
     
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->




<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsAdmin);

mysql_free_result($rsdiscipline);

mysql_free_result($rsManager);
?>
