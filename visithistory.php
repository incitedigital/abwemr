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

$center_Recordset1 = "1";
if (isset($_SESSION['centerID'])) {
  $center_Recordset1 = $_SESSION['centerID'];
}
mysql_select_db($database_dbc, $dbc);
$query_Recordset1 = sprintf("SELECT DISTINCT tbl_patient.patientID, fname, lname, email, homephone, lastvisitdate FROM tbl_patient INNER JOIN tbl_queue on tbl_patient.patientID = tbl_queue.patientID WHERE centerID =  %s AND  lastvisitdate < DATE_SUB(NOW(), INTERVAL 45 DAY)", GetSQLValueString($center_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $dbc) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
 

?>

<?php include('includes/header.php'); ?>


<h1>Visit History</h1>
Clients that have not returned in more than 45 days
<br><br>
<?php if ($totalRows_Recordset1 == 0) { // Show if recordset empty ?>
  No Clients Found
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_Recordset1 > 0) { // Show if recordset not empty ?>
  <table class="table table-striped">
    <thead>
      <tr>
        <td>Name</td>
        <td>Email</td>
        <td>Home Phone</td>
        <td>Last Visit Date</td>
        
        </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><a href="viewpatient.php?patientID=<?php echo $row_Recordset1['patientID']; ?>"><?php echo $row_Recordset1['fname']; ?> <?php echo $row_Recordset1['lname']; ?></td>
          <td><?php echo $row_Recordset1['email']; ?></td>
          <td><?php echo $row_Recordset1['homephone']; ?></td>
          <td><?php echo date('m/d/y', strtotime($row_Recordset1['lastvisitdate'])); ?></td>
          
        </tr>
        <?php } while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); ?>
    </tbody>
  </table>
  <?php } // Show if recordset not empty ?>



<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($Recordset1);
?>
