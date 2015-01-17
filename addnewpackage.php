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
$query_rsPackages = "SELECT * FROM tbl_package";
$rsPackages = mysql_query($query_rsPackages, $dbc) or die(mysql_error());
$row_rsPackages = mysql_fetch_assoc($rsPackages);
$totalRows_rsPackages = mysql_num_rows($rsPackages);

$colname_rsPatient = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPatient = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPatient = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsPatient, "int"));
$rsPatient = mysql_query($query_rsPatient, $dbc) or die(mysql_error());
$row_rsPatient = mysql_fetch_assoc($rsPatient);
$totalRows_rsPatient = mysql_num_rows($rsPatient);
 
session_start(); 
?>
<?php include('includes/header.php'); ?>




<h2>Add New Package for <a href="viewpatient.php?patientID=<?php echo $colname_rsPatient; ?>"><?php echo $row_rsPatient['fname']; ?> <?php echo $row_rsPatient['lname']; ?></a></h2>
<br/>
<ul id="packages">
  <?php do { ?>
    <li>
      <a href="packagedetails.php?packagename=<?php echo $row_rsPackages['packagename']; ?> &packageId=<?php echo $row_rsPackages['packageId']; ?>&patientID=<?php echo $colname_rsPatient; ?>"
	  <?php if($_GET['message']=="hide"){ echo "class=\"$row_rsPackages[class]\"";}?>  rel="facebox"><?php echo $row_rsPackages['packagename']; ?><br>
	  <?php 
	   if ($row_rsPackages['packageId'] == 5) 
	  {echo 'Injection$495 <br> Sublingual$595'; } 
	  else if ($row_rsPackages['packageId'] == 6) 
	   {echo 'Injection$695 <br> Sublingual$795'; } 
	  else if ($row_rsPatient['insurance'] == "Yes")
	  { echo "$" .$row_rsPackages['insuranceprice']. ".00";} 
	  else if ($row_rsPatient['insurance'] != "Yes") {echo "$". $row_rsPackages['price']. ".00";}
	 
	  ?></a>
    </li>
    <?php } while ($row_rsPackages = mysql_fetch_assoc($rsPackages)); ?>
</ul>







<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsPackages);

mysql_free_result($rsPatient);
?>
