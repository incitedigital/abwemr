<?php session_start(); ?>
<?php
$server = $_SERVER["HTTP_HOST"];
$server = $server."/better";
//echo $server;
$adminID = $_SESSION["MM_Username"];
?>
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

$colname_rsInsurance = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsInsurance = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsInsurance = sprintf("SELECT patientID, insurance FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsInsurance, "int"));
$rsInsurance = mysql_query($query_rsInsurance, $dbc) or die(mysql_error());
$row_rsInsurance = mysql_fetch_assoc($rsInsurance);
$totalRows_rsInsurance = mysql_num_rows($rsInsurance);

$colname_rsPatient = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPatient = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPatient = sprintf("SELECT patientID, fname, lname FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsPatient, "int"));
$rsPatient = mysql_query($query_rsPatient, $dbc) or die(mysql_error());
$row_rsPatient = mysql_fetch_assoc($rsPatient);
$totalRows_rsPatient = mysql_num_rows($rsPatient);
 
 ?>


<?php include('includes/header.php'); ?>


<div id="wrapper">

<div id="left_col">
<?php include('includes/menu.php'); ?>
		
</div>

<div id="right_col">

<h1>Add HCG Package Details for <?php echo $row_rsPatient['fname']; ?> <?php echo $row_rsPatient['lname']; ?></h1>

<form action="$editFormAction" method="POST" name="newpackage">
<table cellpadding="5" cellspacing="5" id="newpage">

<tr><td width="150px">Method:</td><td><select name="method"><option value="-1">Select</option><option value="injection">Injection</option><option value="sublingual">Sublingual</option></select></td></tr>
<tr><td>Orientation Date:</td><td><input type="text" name="orientationdate" id="orientationdate" class="textbox" placeholder="mm/dd/yyyy"/></td></tr>
<tr><td>Orientation:</td><td><select name="orientation"><option value="-1">Select</option><option value="given">Given</option><option value="refused">Refused</option></select></td></tr>
<tr><td>Packet Given:</td><td><select name="packetgiven"><option value="-1">Select</option><option value="yes">Yes</option><option value="no">No</option></select></td></tr>
<tr><td>Injection Demonstrated:</td><td><select name="injectiondemonstrated"><option value="-1">Select</option><option value="yes">Yes</option><option value="no">No</option></select></td></tr>

<?php if ( $row_rsInsurance['insurance'] == 'Yes') { echo 
'<tr><td valign="top">Billing Info</td><td>
<input type="radio" name="billing" value="6 Month Comprehensive">  6 Month Comprehensive</input> <br/>
<input type="radio" name="billing" value="Follow Up Expanded (99213)">  Follow Up Expanded (99213)</input> <br/>
<input type="radio" name="billing" value="Counseling/15min (99401)">  Counseling/15min (99401)</input> <br/>
<input type="radio" name="billing" value="HCG Counseling (99403)">  HCG Counseling (99403)</input> <br/>
</td></tr>


<tr><td valign="top">Injection Type</td><td ><input type="radio" name="injection" value="Injection Only with PA (99212)">  Injection Only with PA (99212)</input> <br/>
<input type="radio" name="injection" value="Injection Only (99211)">  Injection Only (99211)</input> <br/>
<input type="radio" name="injection" value="Follow Up Detailed (99214)">  Follow Up Detailed (99214)</input> <br/>
<input type="radio" name="injection" value="Orientation (99354)">  Orientation (99354)</input></td></tr>
<tr><td>Protein Drinks</td><td><select name="proteindrinks"><option name="-1">Select </option><option name="yes">Yes</option><option name="no">No</option></select></td></tr>
<tr><td>Protein Bars</td><td><select name="proteinbars"><option name="-1">Select </option><option name="yes">Yes</option><option name="no">No</option></select></td></tr>';
}?>

<input type="hidden" name="patientID" value="<?php echo $row_rsPatient['patientID']; ?>"/>
<input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>"/>
<input type="hidden" name="adminID" value=<?php echo "$adminID"; ?>/>
<input type="hidden" name="status" value="active"/>


<tr><td></td><td><input type="submit" name="submit" class="buttoncolor" value="Add Package" /></td></tr>
</table>
<input type="hidden" name="MM_insert" value="newpackage" />
</form>

</div> <!--End Right_col -->
</div> <!--End Wrapper -->

<?php
mysql_free_result($rsInsurance);

mysql_free_result($rsPatient);
?>
