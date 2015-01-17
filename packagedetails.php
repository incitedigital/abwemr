<?php session_start(); ?>
<?php
$server = $_SERVER["HTTP_HOST"];
$server = $server."/better";
//echo $server;
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "newpackage")) {
  $orientationdate = date('Y-m-d H:i:s', strtotime($_POST['orientationdate']));
  $billing = $_POST['billing'];
$injection = $_POST['injection'];
$proteindrinks = $_POST['proteindrinks'];
$injectioncount = $_POST['injectioncount'];
$proteinbars = $_POST['proteinbars'];
  $insertSQL = sprintf("INSERT INTO tbl_patientpackage (patient_ID, package_ID, status, username, method, orientation, packetgiven, injectiondemonstrated, orientationdate,billinginfo,injectiontype,proteindrinks,proteinbars, date, centerID, injectioncount) VALUES (%s, %s, %s, %s, %s, %s,%s, %s, '$orientationdate', '$billing', '$injection', '$proteindrinks','$proteinbars', CURDATE(),'$_SESSION[centerID]','$injectioncount')",
                       GetSQLValueString($_POST['patientID'], "int"),
                       GetSQLValueString($_POST['packageId'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['admin'], "text"),
                       GetSQLValueString($_POST['method'], "text"),
                       GetSQLValueString($_POST['orientation'], "text"),
                       GetSQLValueString($_POST['packetgiven'], "text"),
                       GetSQLValueString($_POST['injectiondemonstrated'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());
  
   $patientquery = "INSERT into tbl_activity (username, action, firstname, lastname,  date,  category, centerID) VALUES ('$_SESSION[MM_Username]', 'added new package for', '$_POST[fname]','$_POST[lname]', CURDATE(), 'package', '$_SESSION[centerID]')";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());
  
  
  $insertGoTo = "viewpatient.php?patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addpackage")) {
	$orientationdate = date('Y-m-d H:i:s', strtotime($_POST['orientationdate']));
  $billing = $_POST['billing'];
$injection = $_POST['injection'];
$proteindrinks = $_POST['proteindrinks'];
$injectioncount = $_POST['injectioncount'];
$proteinbars = $_POST['proteinbars'];
$cravex = $_POST['cravex'];
$administered = $_POST['administered'];
$single = $_POST['single'];
$Phenterminefrequency = $_POST['Phenterminefrequency'];
$Phendimetrazinefrequency = $_POST['Phendimetrazinefrequency'];

  $insertSQL = sprintf("INSERT INTO tbl_patientpackage (patient_ID, package_ID, status, vitamin, muscle, phentermine, phendimetrazine, chronium, htcz, billinginfo, injectiontype, proteindrinks, proteinbars, username, date, centerID, injectioncount, cravex, administered,single, Phendimetrazinefrequency, Phenterminefrequency ) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$_SESSION[MM_Username]', CURDATE(),'$_SESSION[centerID]', '$injectioncount', '$cravex', '$administered', '$single', '$Phendimetrazinefrequency', '$Phenterminefrequency')",
                       GetSQLValueString($_POST['patientID'], "int"),
                       GetSQLValueString($_POST['packageId'], "int"),
                       GetSQLValueString($_POST['status'], "text"),
                       GetSQLValueString($_POST['Vitamin'], "text"),
                       GetSQLValueString($_POST['Muscle'], "text"),
                       GetSQLValueString($_POST['Phentermine'], "text"),
                       GetSQLValueString($_POST['Phendimetrazine'], "text"),
                       GetSQLValueString($_POST['Chronium'], "text"),
                       GetSQLValueString($_POST['HTCZ'], "text"),
                       GetSQLValueString($_POST['billing'], "text"),
                       GetSQLValueString($_POST['injection'], "text"),
                       GetSQLValueString($_POST['proteindrinks'], "text"),
                       GetSQLValueString($_POST['proteinbars'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());
  $records = mysql_insert_id();
  
  $patientquery = "INSERT into tbl_activity (username, action, firstname, lastname,  date,  category, centerID) VALUES ('$_SESSION[MM_Username]', 'added new package for', 
  '$_POST[fname]','$_POST[lname]', CURDATE(), 'package', '$_SESSION[centerID]' )";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());


mysql_select_db($database_dbc, $dbc);
$patientinject = "INSERT INTO tbl_injection (patient_ID, patientpackageID, username) VALUES ('$_POST[patientID]', '$records','$_SESSION[MM_Username]')";
$rsRemover = mysql_query($patientinject, $dbc) or die(mysql_error());



  $insertGoTo = "viewpatient.php?patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsPackages = "-1";
if (isset($_GET['packageId'])) {
  $colname_rsPackages = $_GET['packageId'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPackages = sprintf("SELECT * FROM tbl_package WHERE packageId = %s", GetSQLValueString($colname_rsPackages, "int"));
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
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="http://<?php echo $server; ?>/js/jquery-ui-1.8.21.custom.min.js"></script>
	
        <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
		<script type="text/javascript" src="js/jquery-ui-sliderAccess.js"></script>
		<script type="text/javascript">
			$('#orientationdate').datetimepicker({
	ampm: true
});

		</script>       
        
<style type="text/css"> 
			.ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
.ui-timepicker-div dl { text-align: left; }
.ui-timepicker-div dl dt { height: 25px; margin-bottom: -25px; }
.ui-timepicker-div dl dd { margin: 0 10px 10px 65px; }
.ui-timepicker-div td { font-size: 90%; }
.ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }
		</style> 
</head>

<body>

<h2><?php echo $row_rsPackages['packagename']; ?></h2>
<?php
$pack = $_GET['packagename']; 
$string = 'HCG';
$pos = strpos($pack,$string);
?>

<?php if($pos !== false){
echo ("
<form method=\"POST\" action=$editFormAction>
<table cellpadding=\"5\" cellspacing=\"5\" id=\"newpage\">
<tr><td>Method:</td><td><select name=\"method\"><option value=\"\">Select</option><option value=\"injection\">Injection</option><option value=\"sublingual\">Sublingual
</option></select></td></tr>
<tr><td>Orientation Date:</td><td><input type=\"text\" name=\"orientationdate\" id=\"orientationdate\"/></td></tr>
<tr><td>Orientation:</td><td><select name=\"orientation\"><option value=\"\">Select</option><option value=\"given\">Given</option><option value=\"refused\">Refused</option></select></td></tr>
<tr><td>Packet Given:</td><td><select name=\"packetgiven\"><option value=\"\">Select</option><option value=\"yes\">Yes</option><option value=\"no\">No</option></select>
</td></tr>
<tr><td>Injection Demonstrated:</td><td><select name=\"injectiondemonstrated\"><option value=\"\">Select</option><option value=\"yes\">Yes</option><option value=\"no\">No</option></select></td></tr>
");

if ($row_rsPatient[insurance] == "Yes") {echo (
"<tr><td>Billing Info</td><td>
<input type=\"radio\" name=\"billing\" value='6 Month Comprehensive'> 6 Month Comprehensive <br/>
<input type=\"radio\" name=\"billing\" value='Follow Up Expanded (99213)'> Follow Up Expanded (99213)<br/>
<input type=\"radio\" name=\"billing\" value='Counseling/15min (99401)'> Counseling/15min (99401)<br/>
<input type=\"radio\" name=\"billing\" value='HCG Counseling (99403)'> HCG Counseling (99403)<br/>
</td></tr>");}

echo("
<tr><td>Injection Type</td><td><input type=\"radio\" name=\"injection\" value='Injection Only with PA (99212)'> Injection Only with PA (99212)<br/>
<input type=\"radio\" name=\"injection\" value='Injection Only (99211)'> Injection Only (99211)<br/>
<input type=\"radio\" name=\"injection\" value='Follow Up Detailed (99214)'> Follow Up Detailed (99214)<br/>
<input type=\"radio\" name=\"injection\" value='Orientation (99354)'> Orientation (99354)</td></tr>

<tr><td>Protein Drinks</td><td><select name=\"proteindrinks\"><option name=\"\">Select </option><option name=\"yes\">Yes</option><option name=\"no\">No</option></select></td></tr>
<tr><td>Protein Bars</td><td><select name=\"proteinbars\"><option name=\"\">Select </option><option name=\"yes\">Yes</option><option name=\"no\">No</option></select></td></tr>

<tr><td><input type=\"hidden\" name=\"packageId\" value=$row_rsPackages[packageId]></td>
<input type=\"hidden\" name=\"patientID\" value=$row_rsPatient[patientID]>
<input type=\"hidden\" name=\"date\" value=<?php echo date(Y-m-d); ?>>
<input type=\"hidden\" name=\"admin\" value=$_SESSION[MM_Username]>
<input type=\"hidden\" name=\"status\" value=\"active\">
<input type=\"hidden\" name=\"fname\" value=$row_rsPatient[fname]>
<input type=\"hidden\" name=\"lname\" value=$row_rsPatient[lname]>

<tr><td></td><td><input type=\"submit\" name=\"submit\" class=\"buttoncolor\" value=\"Add Package\"></td></tr>
</table>
<input type=\"hidden\" name=\"MM_insert\" value=\"newpackage\">
</form>
");
}

// Standard Package
else if ($row_rsPackages[packageId] == 1) {
echo ("
<form method=\"POST\" action=$editFormAction name=\"addpackage\">
<table cellpadding=\"5\" cellspacing=\"5\" id=\"package\">
<tr><td>Vitamin B12</td><td><select name=\"Vitamin\"><option value=\"\">Select </option><option name=\"left\" value=\"left\">Left</option><option name=\"right\" value=\"right\">Right</option><option name=\"refused\" value=\"refused\">Refused</option></select></td></tr>
<tr><td>Muscle</td><td><select name=\"Muscle\" value=\"muscle\"><option value=\"\" >Select </option><option name=\"deltoid\" value=\"deltoid\">Deltoid</option><option name=\"gluteus\" value=\"gluteus\">Gluteus</option><option name=\"refused\" value=\"refused\">Refused</option></select></td></tr>
<tr><td>Phentermine</td><td><select name=\"Phentermine\"><option value=\"\" >Select </option><option value=\"37.5 mg x30 days\">37.5 mg x30 days</option><option value=\"30 mg x30 days\">30 mg x30 days</option><option value=\"18.75 mg x30 days\">18.75 mg x30 days</option><option value=\"15 mg x30 days\">15 mg x30 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option></select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phenterminefrequency\"><option value=\"\" >Select </option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>

<tr><td>Phendimetrazine</td><td><select name=\"Phendimetrazine\"><option value=\"\" >Select </option><option value=\"35 mg x30 days\">35 mg x30 days</option><option value=\"105 mg x30 days\">105 mg x30 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option</select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phendimetrazinefrequency\"><option value=\"\" >Select </option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>
<tr><td>Chronium</td><td><select name=\"Chronium\"><option value=\"\" >Select </option><option value=\"200 mcg x 30 days by mouth daily\">200 mcg x 30 days by mouth daily</option><option value=\"refused\">refused</option></select></td></tr>
<tr><td>HTCZ</td><td><select name=\"HTCZ\"><option value=\"\" >Select </option><option value=\"25 mg x 30 days\">25 mg x 30 days </option><option value=\"prn\">prn</option><option value=\"refused\">refused</option></select></td></tr>

");

if ($row_rsPatient[insurance] == "Yes") 
{echo ("<tr><td>Billing Info</td><td>
<input type=\"radio\" name=\"billing\" value=\"6 Month Comprehensive\"> 6 Month Comprehensive</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"Follow Up Expanded (99213)\"> Follow Up Expanded (99213)</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"Counseling/15min (99401)\"> Counseling/15min (99401)</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"HCG Counseling (99403)\"> HCG Counseling (99403)</input> <br/>
</td></tr>

<tr><td>Injection Type</td><td><input type=\"radio\" name=\"injection\" value='Injection Only with PA (99212)'> Injection Only with PA (99212)<br/>
<input type=\"radio\" name=\"injection\" value='Injection Only (99211)'> Injection Only (99211)<br/>
<input type=\"radio\" name=\"injection\" value='Follow Up Detailed (99214)'> Follow Up Detailed (99214)<br/>
<input type=\"radio\" name=\"injection\" value='Orientation (99354)'> Orientation (99354)</td></tr>

");}

echo("
<tr><td><input type=\"hidden\" name=\"packageId\" value=$row_rsPackages[packageId]></td>
<input type=\"hidden\" name=\"patientID\" value=$row_rsPatient[patientID]>
<input type=\"hidden\" name=\"date\" value=<?php echo date('Y-m-d'); ?>>
<input type=\"hidden\" name=\"status\" value=\"active\">
<input type=\"hidden\" name=\"fname\" value=$row_rsPatient[fname]>
<input type=\"hidden\" name=\"lname\" value=$row_rsPatient[lname]>
<td><input type=\"submit\" name=\"submit\" class=\"buttoncolor\" value=\"Add Package\"></td>
</tr>
</table>
<input type=\"hidden\" name=\"MM_insert\" value=\"addpackage\">
</form>
");
}
// Stimulus Package

else if ($row_rsPackages[packageId] == 2)  {

echo ("
<form method=\"POST\" action=$editFormAction name=\"addpackage\">
<table cellpadding=\"5\" cellspacing=\"5\" id=\"package\">
<tr><td>Vitamin B12</td><td><select name=\"Vitamin\"><option value=\"\">Select </option><option name=\"left\" value=\"left\">Left</option><option name=\"right\" value=\"right\">Right</option><option name=\"refused\" value=\"refused\">Refused</option></select></td></tr>
<tr><td>Muscle</td><td><select name=\"Muscle\" value=\"muscle\"><option name=\"\" >Select </option><option name=\"deltoid\" value=\"deltoid\">Deltoid</option><option name=\"gluteus\" value=\"gluteus\">Gluteus</option><option name=\"refused\" value=\"refused\">Refused</option></select></td></tr>
<tr><td>Phentermine</td><td><select name=\"Phentermine\"><option name=\"\" >Select </option><option value=\"37.5 mg x 14 days\">37.5 mg x 14 days</option><option value=\"30 mg x 14 days\">30 mg x 14 days</option><option value=\"18.75 mg x 14 days\">18.75 mg x 14 days</option><option value=\"15 mg x 14 days\">15 mg x 14 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option></select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phenterminefrequency\"><option name=\"\" >Select </option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>

<tr><td>Phendimetrazine</td><td><select name=\"Phendimetrazine\"><option name=\"\" >Select </option><option value=\"35 mg x 14 days\">35 mg x 14 days</option><option value=\"105 mg x 14 days\">105 mg x 14 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option</select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phendimetrazinefrequency\"><option name=\"\" >Select </option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>
<tr><td>Chronium</td><td><select name=\"Chronium\"><option name=\"\" >Select </option><option value=\"200 mcg x 14 days by mouth daily\">200 mcg x 14 days by mouth daily</option><option value=\"refused\">refused</option></select></td></tr>
<tr><td>HTCZ</td><td><select name=\"HTCZ\"><option name=\"\" >Select </option><option value=\"25 mg x 14 days\">25 mg x 14 days </option><option value=\"prn\">prn</option><option value=\"refused\">refused</option></select></td></tr>
");

if ($row_rsPatient[insurance] == "Yes") 
{echo ("<tr><td>Billing Info</td><td>
<input type=\"radio\" name=\"billing\" value=\"6 Month Comprehensive\"> 6 Month Comprehensive</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"Follow Up Expanded (99213)\"> Follow Up Expanded (99213)</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"Counseling/15min (99401)\"> Counseling/15min (99401)</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"HCG Counseling (99403)\"> HCG Counseling (99403)</input> <br/>
</td></tr>



<tr><td>Injection Type</td><td><input type=\"radio\" name=\"injection\" value='Injection Only with PA (99212)'> Injection Only with PA (99212)<br/>
<input type=\"radio\" name=\"injection\" value='Injection Only (99211)'> Injection Only (99211)<br/>
<input type=\"radio\" name=\"injection\" value='Follow Up Detailed (99214)'> Follow Up Detailed (99214)<br/>
<input type=\"radio\" name=\"injection\" value='Orientation (99354)'> Orientation (99354)</td></tr>

");}

echo("
<tr><td><input type=\"hidden\" name=\"packageId\" value=$row_rsPackages[packageId]></td>
<input type=\"hidden\" name=\"patientID\" value=$row_rsPatient[patientID]>
<input type=\"hidden\" name=\"date\" value=echo date('Y-m-d')>
<input type=\"hidden\" name=\"status\" value=\"active\">
<input type=\"hidden\" name=\"fname\" value=$row_rsPatient[fname]>
<input type=\"hidden\" name=\"lname\" value=$row_rsPatient[lname]>
<td><input type=\"submit\" name=\"submit\" class=\"buttoncolor\" value=\"Add Package\" ></td>
</tr>
</table>
<input type=\"hidden\" name=\"MM_insert\" value=\"addpackage\" >
</form>");
}

// Lipo Standard


else if ($row_rsPackages[packageId] == 4) {

echo ("<form method=\"POST\" action=$editFormAction name=\"addpackage\">
<table cellpadding=\"5\" cellspacing=\"5\" id=\"package\">

<tr><td>Lipo-Ignite Administered</td><td><select name=\"Vitamin\"><option name=\"\">Select </option><option name=\"left\" value=\"left\">Left</option><option name=\"right\" value=\"right\">Right</option><option name=\"refused\" value=\"refused\">Refused</option></select></td></tr>
<tr><td>Muscle</td><td><select name=\"Muscle\" value=\"muscle\"><option name=\"\" >Select </option><option name=\"deltoid\" value=\"deltoid\">Deltoid</option><option name=\"gluteus\" value=\"gluteus\">Gluteus</option><option name=\"refused\" value=\"refused\">Refused</option></select></td></tr>
<tr><td>Phentermine</td><td><select name=\"Phentermine\"><option name=\"\">Select </option><option value=\"37.5 mg x30 days\">37.5 mg x30 days</option><option value=\"30 mg x30 days\">30 mg x30 days</option><option value=\"18.75 mg x30 days\">18.75 mg x30 days</option><option value=\"15 mg x30 days\">15 mg x30 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option></select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phenterminefrequency\"><option name=\"\">Select </option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>

<tr><td>Phendimetrazine</td><td><select name=\"Phendimetrazine\"><option value=\"\">Select</option><option value=\"35 mg x30 days\">35 mg x30 days</option><option value=\"105 mg x30 days\">105 mg x30 days</option><option value\"on hold\">on hold</option><option value=\"N/A\">N/A</option</select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phendimetrazinefrequency\"><option name=\"\">Select </option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon</option><option value=\"every other day\">every other day</option></select></td></tr>
<tr><td>Chronium</td><td><select name=\"Chronium\"><option name=\"\">Select </option><option value=\"200 mcg x 30 days by mouth daily\">200 mcg x 30 days by mouth daily</option><option value=\"refused\">refused</option></select></td></tr>
<tr><td>HTCZ</td><td><select name=\"HTCZ\"><option name=\"\">Select </option><option value=\"25 mg x 30 days\" >25 mg x 30 days </option><option value=\"prn\">prn</option><option value=\"refused\">refused</option></select></td></tr>");

if ($row_rsPatient[insurance] == "Yes") {echo 
"<tr><td>Billing Info</td><td>
<input type=\"radio\" name=\"billing\" value=\"6 Month Comprehensive\"> 6 Month Comprehensive</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"Follow Up Expanded (99213)\"> Follow Up Expanded (99213)</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"Counseling/15min (99401)\"> Counseling/15min (99401)</input> <br/>
<input type=\"radio\" name=\"billing\" value=\"HCG Counseling (99403)\"> HCG Counseling (99403)</input> <br/>
</td></tr>

<tr><td>Injection Type</td><td><input type=\"radio\" name=\"injection\" value='Injection Only with PA (99212)'> Injection Only with PA (99212)<br/>
<input type=\"radio\" name=\"injection\" value='Injection Only (99211)'> Injection Only (99211)<br/>
<input type=\"radio\" name=\"injection\" value='Follow Up Detailed (99214)'> Follow Up Detailed (99214)<br/>
<input type=\"radio\" name=\"injection\" value='Orientation (99354)'> Orientation (99354)</td></tr>

";}

echo("
<tr><td><input type=\"hidden\" name=\"packageId\" value=$row_rsPackages[packageId]></td>
<input type=\"hidden\" name=\"patientID\" value=$row_rsPatient[patientID]>
<input type=\"hidden\" name=\"date\" value=echo date('Y-m-d')>
<input type=\"hidden\" name=\"status\" value=\"active\">
<input type=\"hidden\" name=\"injectioncount\" value= 4 >
<input type=\"hidden\" name=\"fname\" value=$row_rsPatient[fname] >
<input type=\"hidden\" name=\"lname\" value=$row_rsPatient[lname]>
<td><input type=\"submit\" name=\"submit\" class=\"buttoncolor\" value=\"Add Package\" ></td>
</tr>
</table>
<input type=\"hidden\" name=\"MM_insert\" value=\"addpackage\" >
</form>");

}



// Lipo Stimulus

else if ($row_rsPackages[packageId] == 3)  {

echo ("
<form method=\"POST\" action=$editFormAction name=\"addpackage\">
<table cellpadding=\"5\" cellspacing=\"5\" id=\"package\">

<tr><td>Lipo-Ignite Administered</td><td><select name=\"Vitamin\"><option name=\"\">Select</option><option name=\"left\" value=\"left\">Left</option><option name=\"right\" value=\"right\">Right</option><option name=\"refused\" value=\"refused\">Refused</option></select></td></tr>
<tr><td>Muscle</td><td><select name=\"Muscle\" value=\"muscle\"><option name=\"\" >Select </option><option name=\"deltoid\" value=\"deltoid\">Deltoid</option><option name=\"gluteus\" value=\"gluteus\">Gluteus</option><option name=\"refused\" value=\"refused\">Refused</option></select></td></tr>
<tr><td>Phentermine</td><td><select name=\"Phentermine\"><option name=\"\">Select </option><option value=\"37.5 mg x 14 days\">37.5 mg x 14 days</option><option value=\"30 mg x 14 days\">30 mg x 14 days</option><option value=\"18.75 mg x 14 days\">18.75 mg x 14 days</option><option value=\"15 mg x 14 days\">15 mg x 14 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option></select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phenterminefrequency\"><option name=\"\">Select </option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>

<tr><td>Phendimetrazine</td><td><select name=\"Phendimetrazine\"><option name=\"\">Select </option><option value=\"35 mg x 14 days\">35 mg x 14 days</option><option value=\"105 mg x 14 days\">105 mg x 14 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option</select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phendimetrazinefrequency\"><option name=\"\">Select </option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>
<tr><td>Chronium</td><td><select name=\"Chronium\"><option name=\"\">Select </option><option value=\"200 mcg x 14 days by mouth daily\">200 mcg x 14 days by mouth daily</option><option value=\"refused\">refused</option></select></td></tr>
<tr><td>HTCZ</td><td><select name=\"HTCZ\"><option name=\"\">Select</option><option value=\"25 mg x 14 days\">25 mg x 14 days </option><option value=\"prn\">prn</option><option value=\"refused\">refused</option></select></td></tr>");

if ($row_rsPatient[insurance] == "Yes") {echo 
"<tr><td>Billing Info</td><td>
<input type=\"radio\" name=\"billing\" value='6 Month Comprehensive'> 6 Month Comprehensive</input> <br/>
<input type=\"radio\" name=\"billing\" value='Follow Up Expanded (99213)'> Follow Up Expanded (99213)</input> <br/>
<input type=\"radio\" name=\"billing\" value='Counseling/15min (99401)'> Counseling/15min (99401)</input> <br/>
<input type=\"radio\" name=\"billing\" value='HCG Counseling (99403)'> HCG Counseling (99403)</input> <br/>
</td></tr>

<tr><td>Injection Type</td><td><input type=\"radio\" name=\"injection\" value='Injection Only with PA (99212)'> Injection Only with PA (99212)<br/>
<input type=\"radio\" name=\"injection\" value='Injection Only (99211)'> Injection Only (99211)<br/>
<input type=\"radio\" name=\"injection\" value='Follow Up Detailed (99214)'> Follow Up Detailed (99214)<br/>
<input type=\"radio\" name=\"injection\" value='Orientation (99354)'> Orientation (99354)</td></tr>

";}
echo("
<tr><td><input type=\"hidden\" name=\"packageId\" value=$row_rsPackages[packageId] ></td>
<input type=\"hidden\" name=\"patientID\" value=$row_rsPatient[patientID]>
<input type=\"hidden\" name=\"date\" value=echo date('Y-m-d')>
<input type=\"hidden\" name=\"status\" value=\"active\">
<input type=\"hidden\" name=\"injectioncount\" value= 2 >
<input type=\"hidden\" name=\"fname\" value=$row_rsPatient[fname]>
<input type=\"hidden\" name=\"lname\" value=$row_rsPatient[lname]>
<td><input type=\"submit\" name=\"submit\" class=\"buttoncolor\" value=\"Add Package\"></td>
</tr>
</table>
<input type=\"hidden\" name=\"MM_insert\" value=\"addpackage\">
</form>
");
}

// Crave X

else if ($row_rsPackages[packageId] == 7)  {

echo ("<form method=\"POST\" action=$editFormAction name=\"addpackage\">
<table cellpadding=\"5\" cellspacing=\"5\" id=\"package\">

<tr><td>Crave-X</td><td><select name=\"cravex\"><option name=\"\">Select </option><option value=\"Crave X- Strength 1:  5mg  carbidopa  &  20mg of 5HTP\">Crave X- Strength 1:  5mg  carbidopa  &  20mg of 5HTP</option><option value=\"Crave X- Strength 1:  5mg  carbidopa & 30mg of 5HTP\">Crave X- Strength 1:  5mg  carbidopa & 30mg of 5HTP</option><option value=\"Crave X- Strength 1:  5mg  carbidopa  & 40mg of 5HTP\">Crave X- Strength 1:  5mg  carbidopa  & 40mg of 5HTP</option><option value=\"Crave X- Strength 1:  5mg  carbidopa  & 50mg of 5HTP\">Crave X- Strength 1:  5mg  carbidopa  & 50mg of 5HTP</option></select></td></tr>
<tr><td>Administered</td><td><select name=\"administered\"><option name=\"\">Select </option><option value=\"daily/30 min before each meal\">daily/30 min before each meal</option><option value=\"twice daily/30 min before each meal\">twice daily/30 min before each meal</option><option value=\"three times daily/30 min before each meal\">three times daily/30 min before each meal</option><option value=\"P. R. N. /as needed\">P. R. N. /as needed</option></select></td></tr>
");
if ($row_rsPatient[insurance] == "Yes") {echo 
"<tr><td>Billing Info</td><td>
<input type=\"radio\" name=\"billing\" value='6 Month Comprehensive'> 6 Month Comprehensive</input> <br/>
<input type=\"radio\" name=\"billing\" value='Follow Up Expanded (99213)'> Follow Up Expanded (99213)</input> <br/>
<input type=\"radio\" name=\"billing\" value='Counseling/15min (99401)'> Counseling/15min (99401)</input> <br/>
<input type=\"radio\" name=\"billing\" value='HCG Counseling (99403)'> HCG Counseling (99403)</input> <br/>
</td></tr>


<tr><td>Injection Type</td><td><input type=\"radio\" name=\"injection\" value='Injection Only with PA (99212)'> Injection Only with PA (99212)<br/>
<input type=\"radio\" name=\"injection\" value='Injection Only (99211)'> Injection Only (99211)<br/>
<input type=\"radio\" name=\"injection\" value='Follow Up Detailed (99214)'> Follow Up Detailed (99214)<br/>
<input type=\"radio\" name=\"injection\" value='Orientation (99354)'> Orientation (99354)</td></tr>

";}

echo ("
<tr><td><input type=\"hidden\" name=\"packageId\" value=$row_rsPackages[packageId] ></td>
<input type=\"hidden\" name=\"patientID\" value=$row_rsPatient[patientID]>
<input type=\"hidden\" name=\"date\" value=echo date('Y-m-d')>
<input type=\"hidden\" name=\"status\" value=\"active\">
<input type=\"hidden\" name=\"fname\" value=$row_rsPatient[fname]>
<input type=\"hidden\" name=\"lname\" value=$row_rsPatient[lname]>
<td><input type=\"submit\" name=\"submit\" class=\"buttoncolor\" value=\"Add Package\" ></td>
</tr>
</table>
<input type=\"hidden\" name=\"MM_insert\" value=\"addpackage\" >
</form>
");
}

// Single Injections

else if ($row_rsPackages[packageId] == 9)  {

echo ('
<form method="POST" action="$editFormAction" name="addpackage">
<table cellpadding="5" cellspacing="5" id="package">
<tr><td colspan="2"><h3>Single Injections</h3></td></tr>
<tr><td><input type="radio" name="single" value="Single Lipo-Ignite Injection"/></td><td>Single Lipo-Ignite Injection</td></tr>
<tr><td><input type="radio" name="single" value="Single B12 Injection"/></td><td>Single B 12 Injection</td></tr>
<tr><td>Administered</td><td><select name="administered"><option value="">Select</option><option value="right">right </option><option value="left">left</option><option value="refused">refused</option></select></td></tr>
<tr><td>Muscle</td><td><select name="muscle"><option value="">Select</option><option value="deltoid ">deltoid </option><option value="glute">glute</option><option value=" lateral thigh"> lateral thigh</option></option></select></td></tr>
<tr><td colspan="2"><h3>Single Medications</h3></td></tr>
<tr><td>Phentermine</td><td><select name="Phentermine"><option value="">Select</option><option value="37.5 mg x 14 days" selected>37.5 mg x 14 days</option><option value="30 mg x 14 days">30 mg x 14 days</option><option value="18.75 mg x 14 days">18.75 mg x 14 days</option><option value="15 mg x 14 days">15 mg x 14 days</option><option value="on hold">on hold</option><option value="N/A">N/A</option></select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name="Phenterminefrequency"><option value="">Select</option><option value="daily " selected>daily </option><option value="twice daily ">twice daily </option><option value="every evening ">every evening </option><option value="every afternoon ">every afternoon </option><option value="every other day">every other day</option></select></td></tr>

<tr><td>Phendimetrazine</td><td><select name="Phendimetrazine"><option value="">Select</option><option value="35 mg x 14 days" selected>35 mg x 14 days</option><option value="105 mg x 14 days">105 mg x 14 days</option><option value="on hold">on hold</option><option value="N/A">N/A</option</select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name="Phendimetrazinefrequency"><option value="">Select</option><option value="daily " selected>daily </option><option value="twice daily ">twice daily </option><option value="every evening ">every evening </option><option value="every afternoon ">every afternoon </option><option value="every other day">every other day</option></select></td></tr>
<tr><td>Chronium</td><td><select name="Chronium"><option value="">Select</option><option value="200 mcg x 14 days by mouth daily">200 mcg x 14 days by mouth daily</option><option value="refused">refused</option></select></td></tr>
<tr><td>HTCZ</td><td><select name="HTCZ"><option value="">Select</option><option value="25 mg x 14 days " selected>25 mg x 14 days </option><option value="prn">prn</option><option value="refused">refused</option></select></td></tr>');

if ($row_rsPatient[insurance] == "Yes") {echo 
"<tr><td>Billing Info</td><td>
<input type=\"radio\" name=\"billing\" value='6 Month Comprehensive'> 6 Month Comprehensive</input> <br/>
<input type=\"radio\" name=\"billing\" value='Follow Up Expanded (99213)'> Follow Up Expanded (99213)</input> <br/>
<input type=\"radio\" name=\"billing\" value='Counseling/15min (99401)'> Counseling/15min (99401)</input> <br/>
<input type=\"radio\" name=\"billing\" value='HCG Counseling (99403)'> HCG Counseling (99403)</input> <br/>
</td></tr>



<tr><td>Injection Type</td><td><input type=\"radio\" name=\"injection\" value='Injection Only with PA (99212)'> Injection Only with PA (99212)<br/>
<input type=\"radio\" name=\"injection\" value='Injection Only (99211)'> Injection Only (99211)<br/>
<input type=\"radio\" name=\"injection\" value='Follow Up Detailed (99214)'> Follow Up Detailed (99214)<br/>
<input type=\"radio\" name=\"injection\" value='Orientation (99354)'> Orientation (99354)</td></tr>

";}

echo ('
<tr><td><input type="hidden" name="packageId" value="$row_rsPackages[packageId]"></td>
<input type="hidden" name="patientID" value="$row_rsPatient[patientID]">
<input type="hidden" name="date" value=echo date("Y-m-d")>
<input type="hidden" name="status" value="active">
<input type="hidden" name="fname" value=$row_rsPatient[fname]>
<input type="hidden" name="lname" value=$row_rsPatient[lname]>
<td><input type="submit" name="submit" class="buttoncolor" value="Add Package"></td>
</tr>
</table>
<input type="hidden" name="MM_insert" value="addpackage">
</form>
');
}


else if ($row_rsPackages[packageId] == 12)  {

echo <<<EOT
<form id="form1" name="form1" method="POST" action="$editFormAction" name="addpackage">
  <table width="300" id="productid" border="0" cellpadding="10" cellspacing="10">
    <tr>
      <td>Protein Shakes</td>
      <td><label for="proteinshake"></label>
        <select name="proteinshake" id="proteinshake">
          <option value="yes">yes</option>
          <option value="no">no</option>
      </select></td>
    </tr>
    <tr>
      <td>Quantity</td>
      <td><label for="pquantity"></label>
        <select name="pquantity" id="pquantity">
          <option value="0">0</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
      </select></td>
    </tr>
    <tr>
      <td>Dressings</td>
      <td><select name="dressings" id="dressings">
        <option value="yes">yes</option>
        <option value="no">no</option>
      </select></td>
    </tr>
    <tr>
      <td>Quantity</td>
      <td><select name="pquantity2" id="pquantity2">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
      </select></td>
    </tr>
    <tr>
      <td>Protein Bars</td>
      <td><select name="proteinbar" id="proteinbar">
        <option value="yes">yes</option>
        <option value="no">no</option>
      </select></td>
    </tr>
    <tr>
      <td>Quantity</td>
      <td><select name="pquantity3" id="pquantity3">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
      </select></td>
    </tr>
    <tr><td><input type="hidden" name="packageId" value="$row_rsPackages[packageId]" /></td>
<input type="hidden" name="patientID" value="$row_rsPatient[patientID]"/>
<input type="hidden" name="date" value="echo date('Y-m-d')"/>
<input type="hidden" name="status" value="active"/>
<input type="hidden" name="fname" value="$row_rsPatient[fname]" />
<input type="hidden" name="lname" value="$row_rsPatient[lname]"/>
<td><input type="submit" name="submit" class="buttoncolor" value="Add Product" /></td>
</tr>
</table>
<input type="hidden" name="MM_insert" value="addpackage" />

</form>

EOT;
}

// Single Injection


else if ($row_rsPackages[packageId] == 13) {

echo ("
<form method=\"POST\" action=$editFormAction name=\"addpackage\">
<table cellpadding=\"5\" cellspacing=\"5\" id=\"package\">
<tr><td><input type=\"radio\" name=\"single\" value=\"Single Lipo-Ignite Injection\"/></td><td>Single Lipo-Ignite Injection</td></tr>
<tr><td><input type=\"radio\" name=\"single\" value=\"Single B12 Injection\"/></td><td>Single B 12 Injection</td></tr>
<tr><td>Administered</td><td><select name=\"administered\"><option value=\"\">Select</option><option value=\"right\">right </option><option value=\"left\">left</option><option value=\"refused\">refused</option></select></td></tr>
<tr><td>Muscle</td><td><select name=\"muscle\"><option value=\"\">Select</option><option value=\"deltoid\">deltoid </option><option value=\"glute\">glute</option><option value=\"lateral thigh\"> lateral thigh</option></option></select></td></tr><br/>

<tr><td><input type=\"hidden\" name=\"packageId\" value=$row_rsPackages[packageId]/></td>
<input type=\"hidden\" name=\"patientID\" value=$row_rsPatient[patientID]/>
<input type=\"hidden\" name=\"date\" value=echo date('Y-m-d')>
<input type=\"hidden\" name=\"status\" value=\"active\">
<input type=\"hidden\" name=\"fname\" value=$row_rsPatient[fname] >
<input type=\"hidden\" name=\"lname\" value=$row_rsPatient[lname]>
<td><input type=\"submit\" name=\"submit\" class=\"buttoncolor\" value=\"Add Package\"  ></td>
</tr>
</table>
<input type=\"hidden\" name=\"MM_insert\" value=\"addpackage\" >
</form>
");
}

// Single Medication


else if ($row_rsPackages[packageId] == 14) {

echo ("
<form method=\"POST\" action=$editFormAction name=\"addpackage\">
<table cellpadding=\"5\" cellspacing=\"5\" id=\"package\">
<tr><td>Phentermine</td><td><select name=\"Phentermine\"><option value=\"\">Select</option><option value=\"37.5 mg x 14 days\">37.5 mg x 14 days</option><option value=\"30 mg x 14 days\">30 mg x 14 days</option><option value=\"18.75 mg x 14 days\">18.75 mg x 14 days</option><option value=\"15 mg x 14 days\">15 mg x 14 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option></select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phenterminefrequency\"><option value=\"\">Select</option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>

<tr><td>Phendimetrazine</td><td><select name=\"Phendimetrazine\"><option value=\"\">Select</option><option value=\"35 mg x 14 days\">35 mg x 14 days</option><option value=\"105 mg x 14 days\">105 mg x 14 days</option><option value=\"on hold\">on hold</option><option value=\"N/A\">N/A</option</select></td></tr>
<tr><td>Phentermine - frequency</td><td><select name=\"Phendimetrazinefrequency\"><option value=\"\">Select</option><option value=\"daily\">daily </option><option value=\"twice daily\">twice daily </option><option value=\"every evening\">every evening </option><option value=\"every afternoon\">every afternoon </option><option value=\"every other day\">every other day</option></select></td></tr>
<tr><td>Chronium</td><td><select name=\"Chronium\"><option value=\"\">Select</option><option value=\"200 mcg x 30 days by mouth daily\">200 mcg x 30 days by mouth daily</option><option value=\"refused\">refused</option></select></td></tr>
<tr><td>HTCZ</td><td><select name=\"HTCZ\"><option value=\"\">Select</option><option value=\"25 mg x 30 days \">25 mg x 14 days </option><option value=\"prn\">prn</option><option value=\"refused\">refused</option></select></td></tr>");

if ($row_rsPatient[insurance] == "Yes") {echo 
"<tr><td>Billing Info</td><td>
<input type=\"radio\" name=\"billing\" value='6 Month Comprehensive'> 6 Month Comprehensive</input> <br/>
<input type=\"radio\" name=\"billing\" value='Follow Up Expanded (99213)'> Follow Up Expanded (99213)</input> <br/>
<input type=\"radio\" name=\"billing\" value='Counseling/15min (99401)'> Counseling/15min (99401)</input> <br/>
<input type=\"radio\" name=\"billing\" value='HCG Counseling (99403)'> HCG Counseling (99403)</input> <br/>
</td></tr>



<tr><td>Injection Type</td><td><input type=\"radio\" name=\"injection\" value='Injection Only with PA (99212)'> Injection Only with PA (99212)<br/>
<input type=\"radio\" name=\"injection\" value='Injection Only (99211)'> Injection Only (99211)<br/>
<input type=\"radio\" name=\"injection\" value='Follow Up Detailed (99214)'> Follow Up Detailed (99214)<br/>
<input type=\"radio\" name=\"injection\" value='Orientation (99354)'> Orientation (99354)</td></tr>

";}

echo ("
<tr><td><input type=\"hidden\" name=\"packageId\" value=$row_rsPackages[packageId]></td>
<input type=\"hidden\" name=\"patientID\" value=$row_rsPatient[patientID]>
<input type=\"hidden\" name=\"date\" value=echo date('Y-m-d')>
<input type=\"hidden\" name=\"status\" value=\"active\">
<input type=\"hidden\" name=\"fname\" value=$row_rsPatient[fname]>
<input type=\"hidden\" name=\"lname\" value=$row_rsPatient[lname]>
<td><input type=\"submit\" name=\"submit\" class=\"buttoncolor\" value=\"Add Package\" ></td>
</tr>
</table>
<input type=\"hidden\" name=\"MM_insert\" value=\"addpackage\">
</form>
");
}
?>


</body>
</html>
<?php
mysql_free_result($rsPackages);

mysql_free_result($rsPatient);
?>
