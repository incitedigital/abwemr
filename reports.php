<?php require_once('Connections/dbc.php'); ?>
<?php if( isset($_POST['submitted'])){
$fromdate = date('Y-m-d', strtotime($_POST['fromdate']));
$todate = date('Y-m-d', strtotime($_POST['todate']));
		
}; 

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

mysql_select_db($database_dbc, $dbc);
$location = $_POST['centerID'];
$query_rsReport = "SELECT fname, lname, prim, secondary, address1, address2, packagename, billinginfo, tbl_patient.city,tbl_patient.state, insurance, tbl_patient.zip,tbl_queue.date, locationname FROM tbl_queue  JOIN tbl_center on tbl_queue.centerID = tbl_center.centerID JOIN tbl_patient on tbl_queue.patientID = tbl_patient.patientID JOIN tbl_patientpackage on tbl_queue.patientID = tbl_patientpackage.patient_ID JOIN tbl_package on tbl_patientpackage.package_ID = tbl_package.packageId JOIN tbl_diagnosis_patient on tbl_queue.patientID = tbl_diagnosis_patient.patientID WHERE tbl_queue.date >= '$fromdate'  AND tbl_queue.date <= '$todate' ORDER BY tbl_queue.date DESC, tbl_queue.centerID ASC ";
$rsReport = mysql_query($query_rsReport, $dbc) or die(mysql_error());
$row_rsReport = mysql_fetch_assoc($rsReport);
$totalRows_rsReport = mysql_num_rows($rsReport);

mysql_select_db($database_dbc, $dbc);
$query_rsCenters = "SELECT * FROM tbl_center";
$rsCenters = mysql_query($query_rsCenters, $dbc) or die(mysql_error());
$row_rsCenters = mysql_fetch_assoc($rsCenters);
$totalRows_rsCenters = mysql_num_rows($rsCenters);
 
session_start(); 
?>
<?php include('includes/header.php'); ?>


<h1>Customer Reports</h1>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
<table id="report" width="100%">
<tr><td>From Date:</td><td><input type="text" class="form-control datepicker" name="fromdate" id="fromdate"/></td><td>To Date:</td><td><input type="text" name="todate" class="form-control datepicker" id="todate"/>
</td><td><input type="submit" class="btn btn-info" name="submitted" value="Search"/></td></tr>
</table>
 <br/>

</form>

<div id="results">
  <?php if ($totalRows_rsReport == 0) { // Show if recordset empty ?>
    No Results Found
  <?php } // Show if recordset empty ?>
  <?php if ($totalRows_rsReport > 0) { // Show if recordset not empty ?>
  <table class="table">
  <thead>
    <tr>
    <th>Date</th>
      <th>Patient Name</th>
      <th>Address</th>
            <th>Location Name</th>
            <th>Billing Info</th>
            <th>Package</th>
           
            <th>Insurance</th>
        
    </tr>
    </thead>
    <tbody>
    <?php do { ?>
      <tr <?php if( $row_rsReport['insurance'] == 'Y'){  echo 'class="blue"';} ?>>
       <td><?php echo date('m/d/Y', strtotime($row_rsReport['date'])); ?></td>
        <td><?php echo $row_rsReport['lname']; ?>, <?php echo $row_rsReport['fname']; ?></td>
       <td><?php echo $row_rsReport['address1']; ?> <?php echo $row_rsReport['city']; ?>,<?php echo $row_rsReport['state']; ?> <?php echo $row_rsReport['zip']; ?> </td>
        <td><?php echo $row_rsReport['locationname']; ?></td>
        <td><?php echo $row_rsReport['billinginfo']; ?></td>
        <td><?php echo $row_rsReport['packagename']; ?></td>
      
        <td><?php if($row_rsReport['insurance'] == "" or NULL){echo "Self Pay";} else {echo $row_rsReport['insurance'];}?></td>
       
      </tr>
      <?php } while ($row_rsReport = mysql_fetch_assoc($rsReport)); ?>
      </tbody>
  </table>
  <?php } // Show if recordset not empty ?>


<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsReport);

mysql_free_result($rsCenters);
?>
