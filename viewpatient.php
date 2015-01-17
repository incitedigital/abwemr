<?php session_start(); ?>
<?php require_once('Connections/dbc.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "loginfailed.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>

<?php $patientID = $_GET['patientID']; ?>
<?php require_once('ScriptLibrary/dmxPaginator.php'); ?>
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

$currentPage = $_SERVER["PHP_SELF"];

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "soapupdate")) {
	
	{
if((isset($_POST['ccnotes'])) && ($_POST['ccnotes'] != "")) {$ccnotes = $_POST['ccnotes'];} else {$ccnotes = $row_rsPatient['ccnotes'];}
if((isset($_POST['subjectivenotes'])) && ($_POST['subjectivenotes'] != "")) {$subjectivenotes = $_POST['subjectivenotes'];} else {$subjectivenotes = $row_rsPatient['subjectivenotes'];}
if((isset($_POST['objectivenotes'])) && ($_POST['objectivenotes'] != "")) {$objectivenotes = $_POST['objectivenotes'];} else {$objectivenotes = $row_rsPatient['objectivenotes'];}
if((isset($_POST['assessmentnotes'])) && ($_POST['assessmentnotes'] != "")) {$assessmentnotes = $_POST['assessmentnotes'];} else {$assessmentnotes = $row_rsPatient['assessmentnotes'];}
if((isset($_POST['plannotes'])) && ($_POST['plannotes'] != "")) {$plannotes = $_POST['plannotes'];} else {$plannotes = $row_rsPatient['plannotes'];} 
if(isset($_POST['cbox']) && ($_POST['cbox'] != ""))
			{ $cbox = implode(', ', $_POST['cbox']);
			}
			else 
			{
				$cbox = "";
			}	
if(isset($_POST['subjective']) && ($_POST['subjective'] != "")) 
			{ $subjective = implode(',', $_POST['subjective']);
			}
			else 
			{
				$subjective = "";
			}	
if(isset($_POST['objective']) && ($_POST['objective'] != "")) 
			{ $objective = implode(',', $_POST['objective']);
			}
			else 
			{
				$objective = "";
			}	
if(isset($_POST['assessment']) && ($_POST['assessment'] != ""))
			{ $assessment = implode(',', $_POST['assessment']);
			}
			else 
			{
				$assessment = "";
			}	
if(isset($_POST['plan']) && ($_POST['plan'] != ""))
			{ $plan = implode(',', $_POST['plan']);
			}
			else 
			{
				$plan = "";
			}	
}

	
	
  $updateSQL = ("UPDATE tbl_patient SET cbox='$cbox', ccnotes='$ccnotes', subjective='$subjective', subjectivenotes='$subjectivenotes', objective='$objective', objectivenotes='$objectivenotes', assessment = '$assessment', assessmentnotes='$assessmentnotes', plan='$plan', plannotes='$plannotes' WHERE patientID='$patientID'");
 
  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "viewpatient.php?message=saved&patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "passwordupdate")) {
  $updateSQL = sprintf("UPDATE tbl_patient SET password=%s WHERE patientID=%s",
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['patientID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "viewpatient.php?message=saved&patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "soapnotes")) {
  $updateSQL = sprintf("UPDATE tbl_patient SET ccnotes=%s, subjectivenotes=%s, objectivenotes=%s, assessmentnotes=%s, plannotes=%s WHERE patientID=%s",
                       GetSQLValueString($_POST['ccnotes'], "text"),
                       GetSQLValueString($_POST['subjectivenotes'], "text"),
                       GetSQLValueString($_POST['objectivenotes'], "text"),
                       GetSQLValueString($_POST['assessmentnotes'], "text"),
                       GetSQLValueString($_POST['plannotes'], "text"),
                       GetSQLValueString(isset($_POST['cbox[]']) ? "true" : "", "defined","1","0"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "viewpatient.php?patientID=" . $row_rsPatient['patientID'] . "&message=success";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addvitals")) {
  $insertSQL = sprintf("INSERT INTO tbl_vitals (patientID, `date`, heightft, heightin, weight, pulse, bloodpressure, bmi, username) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['patientID'], "int"),
                       GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['heightft'], "int"),
                       GetSQLValueString($_POST['heightin'], "int"),
                       GetSQLValueString($_POST['weight'], "text"),
                       GetSQLValueString($_POST['pulse'], "text"),
                       GetSQLValueString($_POST['bloodpressure'], "text"),
                       GetSQLValueString($_POST['bmi'], "text"),
                       GetSQLValueString($_POST['username'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());
  
  $patientquery = "INSERT into tbl_activity (username, action, firstname, lastname,  date,  category) VALUES ('$_SESSION[MM_Username]', 'added vitals for', '$_POST[fname]', '$_POST[lname]', CURDATE(), 'vitals')";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());

  $insertGoTo = "viewpatient.php?message=saved&patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addnote")) {
  $insertSQL = sprintf("INSERT INTO tbl_notes (patientID, notes, `date`, username) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['patientID'], "int"),
                       GetSQLValueString($_POST['notes'], "text"),
                       GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['username'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());
  
  $patientquery = "INSERT into tbl_activity (username, action, firstname, lastname,  date,  category) VALUES ('$_SESSION[MM_Username]', 'added notes for', '$_POST[fname]', '$_POST[lname]', CURDATE(), 'notes')";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());

  $insertGoTo = "viewpatient.php?message=saved&patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
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

$colname_rsPatient = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPatient = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPatient = sprintf("SELECT DISTINCT* FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsPatient, "int"));
$rsPatient = mysql_query($query_rsPatient, $dbc) or die(mysql_error());
$row_rsPatient = mysql_fetch_assoc($rsPatient);
$totalRows_rsPatient = mysql_num_rows($rsPatient);

$maxRows_rsVitals = 10;
$pageNum_rsVitals = 0;
if (isset($_GET['pageNum_rsVitals'])) {
  $pageNum_rsVitals = $_GET['pageNum_rsVitals'];
}
$startRow_rsVitals = $pageNum_rsVitals * $maxRows_rsVitals;

$colname_rsVitals = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsVitals = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsVitals = sprintf("SELECT tbl_vitals.vitalID, tbl_vitals.asian, tbl_vitals.timeentry, tbl_vitals.patientID,  tbl_vitals.date, tbl_vitals.heightft, tbl_vitals.heightin, tbl_vitals.weight, tbl_vitals.bmi, tbl_vitals.pulse, tbl_vitals.systolic, tbl_vitals.diastolic,tbl_vitals.waist, tbl_vitals.hip,tbl_vitals.username, tbl_admin.firstname FROM tbl_vitals JOIN tbl_admin on tbl_vitals.username = tbl_admin.username WHERE patientID = %s ORDER BY date DESC", GetSQLValueString($colname_rsVitals, "int"));
$query_limit_rsVitals = sprintf("%s LIMIT %d, %d", $query_rsVitals, $startRow_rsVitals, $maxRows_rsVitals);
$rsVitals = mysql_query($query_limit_rsVitals, $dbc) or die(mysql_error());
$row_rsVitals = mysql_fetch_assoc($rsVitals);

if (isset($_GET['totalRows_rsVitals'])) {
  $totalRows_rsVitals = $_GET['totalRows_rsVitals'];
} else {
  $all_rsVitals = mysql_query($query_rsVitals);
  $totalRows_rsVitals = mysql_num_rows($all_rsVitals);
}
$totalPages_rsVitals = ceil($totalRows_rsVitals/$maxRows_rsVitals)-1;

$maxRows_rsMedication = 5;
$pageNum_rsMedication = 0;
if (isset($_GET['pageNum_rsMedication'])) {
  $pageNum_rsMedication = $_GET['pageNum_rsMedication'];
}
$startRow_rsMedication = $pageNum_rsMedication * $maxRows_rsMedication;

mysql_select_db($database_dbc, $dbc);
$query_rsMedication = "SELECT tbl_patientMed.patientID, tbl_patientMed.patientMedID,tbl_medication.medID, medicationName FROM tbl_patientMed JOIN tbl_medication on tbl_patientMed.medID = tbl_medication.medID WHERE tbl_patientMed.patientID = '$colname_rsPatient'";
$query_limit_rsMedication = sprintf("%s LIMIT %d, %d", $query_rsMedication, $startRow_rsMedication, $maxRows_rsMedication);
$rsMedication = mysql_query($query_limit_rsMedication, $dbc) or die(mysql_error());
$row_rsMedication = mysql_fetch_assoc($rsMedication);

if (isset($_GET['totalRows_rsMedication'])) {
  $totalRows_rsMedication = $_GET['totalRows_rsMedication'];
} else {
  $all_rsMedication = mysql_query($query_rsMedication);
  $totalRows_rsMedication = mysql_num_rows($all_rsMedication);
}
$totalPages_rsMedication = ceil($totalRows_rsMedication/$maxRows_rsMedication)-1;

mysql_select_db($database_dbc, $dbc);
$query_rsDiagnosis = "SELECT tbl_diagnosis_patient.patientID, tbl_diagnosis_patient.patientdiagID, tbl_diagnosis.diagnosisID,diagnosisName FROM tbl_diagnosis_patient  JOIN tbl_diagnosis on tbl_diagnosis_patient.diagnosisID = tbl_diagnosis.diagnosisID WHERE tbl_diagnosis_patient.patientID = '$colname_rsPatient'";
$rsDiagnosis = mysql_query($query_rsDiagnosis, $dbc) or die(mysql_error());
$row_rsDiagnosis = mysql_fetch_assoc($rsDiagnosis);
$totalRows_rsDiagnosis = mysql_num_rows($rsDiagnosis);

$colname_rsAdmin = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsAdmin = $_SESSION['MM_Username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsAdmin = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsAdmin, "text"));
$rsAdmin = mysql_query($query_rsAdmin, $dbc) or die(mysql_error());
$row_rsAdmin = mysql_fetch_assoc($rsAdmin);
$totalRows_rsAdmin = mysql_num_rows($rsAdmin);

$maxRows_rsNotes = 10;
$pageNum_rsNotes = 0;
if (isset($_GET['pageNum_rsNotes'])) {
  $pageNum_rsNotes = $_GET['pageNum_rsNotes'];
}
$startRow_rsNotes = $pageNum_rsNotes * $maxRows_rsNotes;

$colname_rsNotes = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsNotes = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsNotes = sprintf("SELECT patientID, noteID, notes, date, tbl_notes.username, firstname FROM tbl_notes JOIN tbl_admin on tbl_notes.username = tbl_admin.username WHERE patientID = %s ORDER BY date DESC", GetSQLValueString($colname_rsNotes, "int"));
$query_limit_rsNotes = sprintf("%s LIMIT %d, %d", $query_rsNotes, $startRow_rsNotes, $maxRows_rsNotes);
$rsNotes = mysql_query($query_limit_rsNotes, $dbc) or die(mysql_error());
$row_rsNotes = mysql_fetch_assoc($rsNotes);

if (isset($_GET['totalRows_rsNotes'])) {
  $totalRows_rsNotes = $_GET['totalRows_rsNotes'];
} else {
  $all_rsNotes = mysql_query($query_rsNotes);
  $totalRows_rsNotes = mysql_num_rows($all_rsNotes);
}
$totalPages_rsNotes = ceil($totalRows_rsNotes/$maxRows_rsNotes)-1;

$maxRows_rsPackage = 10;
$pageNum_rsPackage = 0;
if (isset($_GET['pageNum_rsPackage'])) {
  $pageNum_rsPackage = $_GET['pageNum_rsPackage'];
}
$startRow_rsPackage = $pageNum_rsPackage * $maxRows_rsPackage;

$colname_rsPackage = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPackage = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPackage = sprintf("SELECT tbl_patientpackage.*, packagename, date FROM tbl_patientpackage JOIN  tbl_package on tbl_patientpackage.package_ID = tbl_package.packageId WHERE  tbl_patientpackage.patient_ID = %s", GetSQLValueString($colname_rsPackage, "int"));
$query_limit_rsPackage = sprintf("%s LIMIT %d, %d", $query_rsPackage, $startRow_rsPackage, $maxRows_rsPackage);
$rsPackage = mysql_query($query_limit_rsPackage, $dbc) or die(mysql_error());
$row_rsPackage = mysql_fetch_assoc($rsPackage);

if (isset($_GET['totalRows_rsPackage'])) {
  $totalRows_rsPackage = $_GET['totalRows_rsPackage'];
} else {
  $all_rsPackage = mysql_query($query_rsPackage);
  $totalRows_rsPackage = mysql_num_rows($all_rsPackage);
}
$totalPages_rsPackage = ceil($totalRows_rsPackage/$maxRows_rsPackage)-1;

$patientID_rs_patientInjection = "-1";
if (isset($_GET['patientID'])) {
  $patientID_rs_patientInjection = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rs_patientInjection = sprintf("SELECT packagename, tbl_patientpackage.date as 'Package Date', tbl_injection.date as 'Injection Date', tbl_injection.patient_ID, count(tbl_injection.patient_ID) FROM tbl_patientpackage JOIN tbl_injection on tbl_injection.patientpackageID = tbl_patientpackage.patientPackageID JOIN tbl_package on tbl_patientpackage.package_ID = tbl_package.packageID WHERE tbl_patientpackage.patient_ID = %s GROUP BY tbl_injection.patient_ID ,  tbl_injection.date ", GetSQLValueString($patientID_rs_patientInjection, "int"));
$rs_patientInjection = mysql_query($query_rs_patientInjection, $dbc) or die(mysql_error());
$row_rs_patientInjection = mysql_fetch_assoc($rs_patientInjection);
$totalRows_rs_patientInjection = mysql_num_rows($rs_patientInjection);

mysql_select_db($database_dbc, $dbc);
$query_rsdate = "SELECT patientpackageID, date FROM tbl_injection   ORDER BY patientpackageID";
$rsdate = mysql_query($query_rsdate, $dbc) or die(mysql_error());
$row_rsdate = mysql_fetch_assoc($rsdate);
$totalRows_rsdate = mysql_num_rows($rsdate);

$colname_rsinjection = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsinjection = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsinjection = sprintf("SELECT DISTINCT patientpackageID, date FROM tbl_injection WHERE patient_ID = %s GROUP BY patientpackageID , date", GetSQLValueString($colname_rsinjection, "int"));
$rsinjection = mysql_query($query_rsinjection, $dbc) or die(mysql_error());
$row_rsinjection = mysql_fetch_assoc($rsinjection);
$totalRows_rsinjection = mysql_num_rows($rsinjection);

mysql_select_db($database_dbc, $dbc);
$query_rscount = "SELECT packagename, patient_ID, count(patientpackageID) as Injections FROM tbl_injection join tbl_package on patientpackageID = packageID GROUP BY packagename, patient_ID";
$rscount = mysql_query($query_rscount, $dbc) or die(mysql_error());
$row_rscount = mysql_fetch_assoc($rscount);
$totalRows_rscount = mysql_num_rows($rscount);

$colname_rsMedicalRecords = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsMedicalRecords = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsMedicalRecords = sprintf("SELECT * FROM tbl_forms WHERE patientID = %s", GetSQLValueString($colname_rsMedicalRecords, "int"));
$rsMedicalRecords = mysql_query($query_rsMedicalRecords, $dbc) or die(mysql_error());
$row_rsMedicalRecords = mysql_fetch_assoc($rsMedicalRecords);
$totalRows_rsMedicalRecords = mysql_num_rows($rsMedicalRecords);

$colname_rsinject = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsinject = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsinject = sprintf("SELECT * FROM tbl_injection WHERE patient_ID = %s", GetSQLValueString($colname_rsinject, "int"));
$rsinject = mysql_query($query_rsinject, $dbc) or die(mysql_error());
$row_rsinject = mysql_fetch_assoc($rsinject);
$totalRows_rsinject = mysql_num_rows($rsinject);

$colname_rsdiagpatient = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsdiagpatient = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsdiagpatient = sprintf("SELECT * FROM tbl_diagnosis_patient WHERE patientID = %s", GetSQLValueString($colname_rsdiagpatient, "int"));
$rsdiagpatient = mysql_query($query_rsdiagpatient, $dbc) or die(mysql_error());
$row_rsdiagpatient = mysql_fetch_assoc($rsdiagpatient);
$totalRows_rsdiagpatient = mysql_num_rows($rsdiagpatient);

$colname_rsPatientPackage = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPatientPackage = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPatientPackage = sprintf("SELECT status FROM tbl_patientpackage WHERE patient_ID = %s  AND status = 'closed'", GetSQLValueString($colname_rsPatientPackage, "int"));
$rsPatientPackage = mysql_query($query_rsPatientPackage, $dbc) or die(mysql_error());
$row_rsPatientPackage = mysql_fetch_assoc($rsPatientPackage);
$totalRows_rsPatientPackage = mysql_num_rows($rsPatientPackage);

$colname_packagetest = "-1";
if (isset($_GET['patientID'])) {
  $colname_packagetest = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_packagetest = sprintf("SELECT * FROM tbl_patientpackage WHERE patient_ID = %s", GetSQLValueString($colname_packagetest, "int"));
$packagetest = mysql_query($query_packagetest, $dbc) or die(mysql_error());
$row_packagetest = mysql_fetch_assoc($packagetest);
$totalRows_packagetest = mysql_num_rows($packagetest);

$maxRows_rsSubWeight = 2;
$pageNum_rsSubWeight = 0;
if (isset($_GET['pageNum_rsSubWeight'])) {
  $pageNum_rsSubWeight = $_GET['pageNum_rsSubWeight'];
}
$startRow_rsSubWeight = $pageNum_rsSubWeight * $maxRows_rsSubWeight;

$colname_rsSubWeight = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsSubWeight = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsSubWeight = sprintf("SELECT weight, timeentry FROM tbl_vitals WHERE patientID = %s", GetSQLValueString($colname_rsSubWeight, "int"));
$query_limit_rsSubWeight = sprintf("%s LIMIT %d, %d", $query_rsSubWeight, $startRow_rsSubWeight, $maxRows_rsSubWeight);
$rsSubWeight = mysql_query($query_limit_rsSubWeight, $dbc) or die(mysql_error());
$row_rsSubWeight = mysql_fetch_assoc($rsSubWeight);

if (isset($_GET['totalRows_rsSubWeight'])) {
  $totalRows_rsSubWeight = $_GET['totalRows_rsSubWeight'];
} else {
  $all_rsSubWeight = mysql_query($query_rsSubWeight);
  $totalRows_rsSubWeight = mysql_num_rows($all_rsSubWeight);
}
$totalPages_rsSubWeight = ceil($totalRows_rsSubWeight/$maxRows_rsSubWeight)-1;

$queryString_rsMedication = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsMedication") == false && 
        stristr($param, "totalRows_rsMedication") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsMedication = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsMedication = sprintf("&totalRows_rsMedication=%d%s", $totalRows_rsMedication, $queryString_rsMedication);




mysql_select_db($database_dbc, $dbc);
$query_rsPackages = "SELECT * FROM tbl_package";
$rsPackages = mysql_query($query_rsPackages, $dbc) or die(mysql_error());
$row_rsPackages = mysql_fetch_assoc($rsPackages);
$totalRows_rsPackages = mysql_num_rows($rsPackages);



?>

<?php

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "reqForm")) {

$to = 'mccray.ira@gmail.com';
$subject = 'EMR Form Submission';
$headers = "From: " . 'imccray@incitegraphics.com' . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$message = '<html><body>';
$message .= '<table rules="all" width="80%" style="border-color: #666;" cellpadding="10">';
$message .= "<tr><td><strong>Patient ID:</strong> </td><td><a href=\"http://incitegraphics.com/abwemr/viewpatient.php?patientID=$_POST[patientID]\">" . strip_tags($_POST['patientID']) . "</a></td></tr>";
$message .= "<tr><td><strong>Patient Name:</strong> </td><td>" . strip_tags($_POST['fname']) . " " . strip_tags($_POST['lname']) . "</td></tr>";
$message .= "<tr><td><strong>Is patient interested in the hCG program today?:</strong> </td><td>" . strip_tags($_POST['q4']) . "</td></tr>";
$message .= "<tr><td><strong>Recommend Lipo-Ignite Package. Does patient accept?</strong> </td><td>" . strip_tags($_POST['q5']) . "</td></tr>";
$message .= "<tr><td><strong>Recommend Crave-X.  Does patient accept?</strong> </td><td>" . strip_tags($_POST['q6']) . "</td></tr>";
$message .= "<tr><td><strong>Recommend Full-Feel.  Does patient accept?</strong> </td><td>" . strip_tags($_POST['q7']) . "</td></tr>";
$message .= "<tr><td><strong>Recommend Lipo Re-Ignite SL.  Does patient accept?</strong> </td><td>" . strip_tags($_POST['q8']) . "</td></tr>";
$message .= "<tr><td><strong>Recommend Lipo Re-Ignite. Does patient accept? </strong> </td><td>" . strip_tags($_POST['q9']) . "</td></tr>";
$message .= "<tr><td><strong>Recommend Protein supplements, dressings and dips?</strong> </td><td>" . strip_tags($_POST['q10']) . "</td></tr>";
$message .= "</table>";
$message .= "</body></html>";
mail($to, $subject, $message, $headers);

$insertGoTo = "addnewpackage.php?message=hide&patientID=" . $_POST['patientID'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>



<?php

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "kd")) {

$to = 'mccray.ira@gmail.com';
$subject = 'EMR Form Submission';
$headers = "From: " . 'imccray@incitegraphics.com' . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$message = '<html><body>';
$message .= '<table rules="all" width="80%" style="border-color: #666;" cellpadding="10">';
$message .= "<tr><td><strong>Patient ID:</strong> </td><td><a href=\"http://incitegraphics.com/abwemr/viewpatient.php?patientID=$_POST[patientID]\">" . strip_tags($_POST['patientID']) . "</a></td></tr>";
$message .= "<tr><td><strong>Patient Name:</strong> </td><td>" . strip_tags($_POST['fname']) . " " . strip_tags($_POST['lname']) . "</td></tr>";
$message .= "<tr><td><strong>Is patient interested in the hCG program today?:</strong> </td><td>" . strip_tags($_POST['q1']) . "</td></tr>";
$message .= "<tr><td><strong>Recommend Lipo-Ignite Package. Does patient accept?</strong> </td><td>" . strip_tags($_POST['q2']) . "</td></tr>";
$message .= "<tr><td><strong>Recommend Crave-X.  Does patient accept?</strong> </td><td>" . strip_tags($_POST['q3']) . "</td></tr>";
$message .= "</table>";
$message .= "</body></html>";
mail($to, $subject, $message, $headers);

$insertGoTo = "addnewpackage.php?message=hide&patientID=". $_POST['patientID'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));

}
?>



<?php include('includes/header.php'); ?>
<div class="row">
<div class="col-xs-12">

</div>
</div>
<div class="row">
<div class="col-xs-1">
<div class="pic"><?php if ($row_rsPatient['photo'] == ""){echo "<img src=\"images/nophoto.jpg\" alt=\"nophoto\" width=\"75\" height=\"75\" />";}else{echo "<img src=\"uploadedfilesforbw/$row_rsPatient[photo]\" width=\"75\" height=\"75\"  alt=\"Profile Photo\" >";}?>
<a href="managephotos.php?patientID=<?php echo $row_rsPatient['patientID']; ?>" rel="facebox"><br><span class="glyphicon glyphicon-camera"></span> Add Photo</a>
</div></div>


<div class="col-xs-11"><span class="name"><?php echo $row_rsPatient['fname']; ?> <?php echo $row_rsPatient['lname']; ?></span> <span class="age"> <?php echo date('Y') - date('Y', strtotime($row_rsPatient['dob'])); ?> yrs <?php echo $row_rsPatient['sex'];?></span> <span class="date"> <?php echo date('m/d/Y',strtotime( $row_rsPatient['dob'])); ?></span> <span class="phone"> <?php echo $row_rsPatient['homephone']; ?> </span>
<span class="email"><?php echo $row_rsPatient['email']; ?> </span>





</div>
</div>

<div role="tabpanel" class="summary">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs patienttabs" role="tablist">
    <li role="presentation" class="active"><a href="#summary" aria-controls="summary" role="tab" data-toggle="tab">Summary</a>
    <li role="presentation"><a href="#viewpmh" aria-controls="viewpmh" role="tab" data-toggle="tab">View PMH</a>
    <li role="presentation"><a href="#clinic" aria-controls="clinic" role="tab" data-toggle="tab">Clinic Visit History</a>
    <li role="presentation"><a href="#package" aria-controls="package" role="tab" data-toggle="tab">Add Package/Program</a>
     <li role="presentation"><a href="#products" aria-controls="products" role="tab" data-toggle="tab">Add Products</a>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content panelmove">
    <div role="tabpanel" class="tab-pane active" id="summary">
    <div>
<a href="editprofile.php?patientID=<?php echo $row_rsPatient['patientID']; ?>" rel="facebox" class="btn btn-default blueline">Edit Profile</a>
<div class="pull-right">
<a href="addvitals.php?patientID=<?php echo $row_rsPatient['patientID']; ?>"  rel="facebox" class="btn btn-default blueline">Add Vitals</a>
<a href="" data-toggle="modal" class="btn btn-default blueline" data-target="#notesModal"> Add Notes</a>
<a href="medication.php?patientID=<?php echo $row_rsPatient['patientID']; ?>" rel="facebox" class="btn btn-default blueline">Add Medication</a>
<a href="" data-toggle="modal" data-target="#historyModal" class="btn btn-default blueline">Add PMH</a>


<a href="" data-toggle="modal" data-target="#passwordModal" class="btn btn-default blueline"> Assign Password</a>

<a href="removefromqueue.php?patientID=<?php echo $row_rsPatient['patientID']; ?>"  class="btn btn-default orange" title="Add Vitals">Remove from Queue</a></div>
</div>
<div class="row">
<div class="col-xs-3">
<h4>Personal Info</h4>
<?php echo $row_rsPatient['address1']; ?>  <?php echo $row_rsPatient['address2']; ?>
<br>
<?php echo $row_rsPatient['city']; ?>, <?php echo $row_rsPatient['state']; ?> <?php echo $row_rsPatient['zip']; ?>
<br>
<strong>Home:</strong> <?php echo $row_rsPatient['homephone']; ?> <br>
<strong>Mobile:</strong> <?php echo $row_rsPatient['mobilephone']; ?><br>
<strong>Email:</strong> <?php echo $row_rsPatient['email']; ?><br>
<strong>D.O.B.:</strong> <?php echo $row_rsPatient['dob']; ?><br>
<strong>Sex:</strong> <?php echo $row_rsPatient['sex']; ?><br><br>
 <strong>Insurance:</strong> <?php if ($row_rsPatient['insurance'] != "") {echo $row_rsPatient['insurance'];} else {echo "N/A";} ?> <br>
<strong>Insurance Co:</strong> <?php if($row_rsPatient['insuranceco'] != "") {echo $row_rsPatient['insuranceco'];} else {echo "N/A";} ?> 
<br/>

</div>
<div class="col-xs-3">
<h4>Current Vitals</h4>
  <?php if ($totalRows_rsVitals == 0) { // Show if recordset empty ?>
   No Vitals Entered
   <?php } // Show if recordset empty ?>
 <?php if ($totalRows_rsVitals > 0) { // Show if recordset not empty ?>
  
  <strong>Height:</strong> <?php echo $row_rsVitals['heightft']; ?>' <?php echo $row_rsVitals['heightin']; ?><br>
  <strong>Weight:</strong> <?php echo $row_rsVitals['weight']; ?> <br>
  <strong>P:</strong> <?php if ($row_rsVitals['pulse'] >= 110) {echo "<span class='badge redwhite'> $row_rsVitals[pulse] </span>";} else {echo  "$row_rsVitals[pulse]";}  ?><br>
  <strong>BP:</strong> <?php if ($row_rsVitals['systolic'] >= 160) { echo "<span class='badge redwhite'><a href='' data-toggle=\"modal\" data-target=\"#bloodModal\" > $row_rsVitals[systolic] / $row_rsVitals[diastolic]</a></span> | <a href=\"addvitals.php?patientID=$row_rsPatient[patientID]\"  rel=\"facebox\"'>Recheck</a>" ;} else  {
   	echo "$row_rsVitals[systolic] / $row_rsVitals[diastolic]";
   }?>
   <?php $bmi = round( ($row_rsVitals['weight']/ ((($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) * (($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) )* 703), 2);  ?><br>
   <strong>BMI:</strong> <span id="bmi"><?php echo $bmi;  ?></span>
   <?php 
   if (isset($_POST['vitalcheck']) && ($_POST['vitalcheck'] == 1)) {  
   $ratio = $_POST['waist']/$_POST['hip']; 
   $ratio = number_format((float)$ratio,2,'.','');
   echo "<strong>Hip to Waist Ratio:</strong>". $ratio; 
   }
   ?>
   <?php } // Show if recordset not empty ?>
   
   <br>
  
  
   
<?php
$mystring = <<<EOT
      
<script type="text/javascript">
	

    $(window).load(function(){
        $('#ratio').modal('show');
    });
	
	
</script>

EOT;
?>

<h4>Current Progress</h4>

<span class="weightloss"></span>

<br>
<?php

if ($bmi >= 28) {
		echo "
<a href=\"addnewpackage.php?patientID=$row_rsPatient[patientID]\" class=\"btn btn-success btn-sm buttonmovedown\" >
 Approved </a>";
}



if (isset($bmi) && ($bmi < 27) )

{
$bmi = intval(round( ($row_rsVitals['weight']/ ((($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) * (($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) )* 703), 2)); 
$sex = $row_rsPatient['sex']; 
$asian = $_POST['asian'];


if ($row_rsVitals['systolic'] >= 160)

{
	echo "<button class=\"btn btn-danger btn-xs buttonmovedown\" data-toggle=\"modal\" data-target=\"#myModal\">
  Not Approved for Phentermine
</button>";
}




else if (($bmi <= 27 && $sex == 'female' && $ratio > 0.8 ) || ($bmi <= 27 && $sex == 'male' && $ratio > 1.0 ) ) {
	
	echo "
<a href=\"addnewpackage.php?patientID=$row_rsPatient[patientID]\" class=\"btn btn-success btn-sm buttonmovedown\" >
 Approved </a>";
	
}

else if ($bmi > 23 && $asian == 'yes') {
	
	echo "
<a href=\"addnewpackage.php?patientID=$row_rsPatient[patientID]\" class=\"btn btn-success btn-sm buttonmovedown\" >
 Approved </a>";
	
}

else if ($bmi < 27 && $_GET['vitalcheck'] == 1)

{
	echo 
"<button class=\"btn btn-danger btn-xs buttonmovedown\" data-toggle=\"modal\" data-target=\"#myModal\">
 Not Approved for Phentermine
</button>";
}


else if($bmi < 27)
{
	echo $mystring; 
}

}
?>




</div>
<div class="col-xs-3">
<h4>Current Package / Program</h4>
   <?php if ($totalRows_packagetest == 0) { // Show if recordset empty ?>
    No Packages Found
  <?php } // Show if recordset empty ?>
<?php

$patientID = $_GET['patientID']; 
mysql_select_db($database_dbc, $dbc);
$query = "SELECT packagename, package_ID, injectioncount, injectiontype, patientPackageID, date, status, patient_ID,vitamin, cravex, single, administered, muscle,phentermine,phendimetrazine,chronium,htcz,billinginfo,injectiontype,proteindrinks,proteinbars,username,method,orientationdate,orientation,packetgiven,injectiondemonstrated,Phenterminefrequency,Phendimetrazinefrequency, locationname FROM tbl_patientpackage JOIN tbl_package on tbl_patientpackage.package_ID = tbl_package.packageId JOIN tbl_center ON tbl_patientpackage.centerID = tbl_center.centerID WHERE patient_ID = '$patientID' and (status = 'active' or status = 'open')  ORDER by date DESC ";


$result = mysql_query($query) or die(mysql_error());


while ($row = mysql_fetch_assoc($result)){
echo "<strong>". $row[packagename]."</strong><br> <strong>Date: </strong>" . date(("m/d/Y"),strtotime($row["date"])) ."<br><strong>Location:</strong> ".$row[locationname];

if($row['package_ID'] == 1) {echo "<br><strong>Vitamin</strong>: $row[vitamin] <br> <strong>Muscle</strong> : $row[muscle]  <br> <strong>Phentermine</strong>:  $row[phentermine] <br>   <strong>Phentermine Frequency</strong>: $row[Phenterminefrequency] <br> <strong>Phendimetrazine</strong>: $row[phendimetrazine]  <br> <strong>Phendimetrazine Frequency</strong>: $row[Phendimetrazinefrequency]  <br><strong>Chronium</strong>: $row[chronium] <br> <strong>HTCZ</strong>:  $row[htcz]   <br>  <strong>Billing Info:</strong> $row[billinginfo] <br><strong>Injection Type</strong> :$row[injectiontype]";} 

else if($row['package_ID'] == 2) {echo "<strong>Vitamin</strong>: $row[vitamin]<br><strong> Muscle: </strong>$row[muscle]<strong><br>  Phentermine</strong> $row[phentermine]<br> <strong> Phentermine Frequency</strong>: $row[Phenterminefrequency] <br>   <strong>Phendimetrazine</strong>: $row[phendimetrazine]  <br>     <strong>Phendimetrazine Frequency</strong> : $row[Phendimetrazinefrequency] <br>   <strong>Chronium</strong>: $row[chronium] <br>  <strong>HTCZ</strong>: $row[htcz] <br>  <strong>Billing Info</strong>$row[billinginfo]: <br><strong>Injection Type</strong> :$row[injectiontype]";}

else if($row['package_ID'] == 3) {echo "<strong>Vitamin</strong>: $row[vitamin] <br><strong> Muscle</strong>: $row[muscle] <br>  <strong> Phentermine </strong>: $row[phentermine] <br>  <strong>Phentermine Frequency</strong>: $row[Phenterminefrequency]  <br>  <strong>Phendimetrazine</strong>: $row[phendimetrazine]  <br>   <strong>Phendimetrazine Frequency</strong>: $row[Phendimetrazinefrequency] <br>   <strong>Chronium</strong>: $row[chronium] <br> <strong>HTCZ:</strong> $row[htcz] <br>  <strong>Billing Info</strong>:$row[billinginfo]<br><strong>Injection Type</strong> :$row[injectiontype]";}

else if($row['package_ID'] == 4) {echo "<br><strong>Vitamin</strong>: $row[vitamin]<br> <strong>Muscle</strong>: $row[muscle]   <strong>Phentermine</strong>:$row[phentermine]  <strong>Phentermine Frequency</strong>: $row[Phenterminefrequency]  <strong>Phendimetrazine</strong> : $row[phendimetrazine]   <strong>Phendimetrazine Frequency</strong> : $row[Phendimetrazinefrequency] <strong> Chronium</strong> : $row[chronium] <strong>HTCZ </strong>: $row[htcz] <strong>Billing Info</strong>: $row[billinginfo]<br><strong>Injection Type</strong> :$row[injectiontype]";}

else if($row['package_ID'] == 5) {echo "<br><strong>Method</strong>: $row[method] <br><strong> Orientation Date</strong> : $row[orientationdate] <strong>Orientation</strong>:$row[orientation] <strong> Orientation Packet Given</strong>:  $row[packetgiven]  <strong>Injection Demonstrated</strong> :  $row[injectiondemonstrated]<strong> Billing Info</strong> : $row[billinginfo] <strong>Injection Type</strong> : $row[injectiontype] <strong>Protein Drinks</strong>:$row[proteindrinks] <strong>Protein Bars</strong> : $row[proteinbars]";}

else if($row['package_ID'] == 6) {echo "<br><strong>Method</strong>: $row[method] <br><strong>Orientation Date</strong>: $row[orientationdate]  <strong>Orientation</strong>: $row[orientation] <strong>Packet Given: </strong> $row[packetgiven] <strong>Injection Demonstrated</strong>: $row[injectiondemonstrated] <strong>Billing Info</strong> : $row[billinginfo]<strong> Injection Type</strong>: $row[injectiontype] <strong>Protein Drinks</strong>: $row[proteindrinks]  <strong>Protein Bars </strong>: $row[proteinbars]";}

else if($row['package_ID'] == 7) {echo "<br><strong>Crave-X</strong>: $row[cravex] <strong>Administered:</strong>: $row[administered]";}

else if($row['package_ID'] == 13) {echo "<br>strong>Date</strong> : ". date('m/d/Y', strtotime($row[date])) ." <strong>Single</strong> : $row[single] <strong>Muscle</strong> : $row[muscle]  <strong>Administered</strong>: $row[administered]";}

else if($row['package_ID'] == 15) {echo "<br><div class=\"injectiondetails\"><strong>Date</strong> : ". date('m/d/Y', strtotime($row[date])) ." <strong>Muscle</strong> : $row[muscle]  <strong>Administered</strong>: $row[administered]";}


else if($row['package_ID'] == 14) {echo "<div class=\"injectiondetails\"><strong>Phentermine</strong>: $row[phentermine] <strong> Phentermine Frequency </strong> : $row[Phenterminefrequency] <strong>Phendimetrazine</strong>: $row[phendimetrazine] <strong> Phendimetrazine Frequency</strong>: $row[Phendimetrazinefrequency] <strong>Chronium</strong>: $row[chronium]<strong>HTCZ</strong>: $row[htcz]<strong> Billing Info</strong>: $row[billinginfo]<br><strong>Injection Type</strong> :$row[injectiontype]";}

	
};



?>





</div>
<div class="col-xs-3">
<h4>Injections</h4>
<?php

$patientID = $_GET['patientID']; 
mysql_select_db($database_dbc, $dbc);
$query = "SELECT packagename, package_ID, injectioncount, injectiontype, patientPackageID, date, status, patient_ID,vitamin, cravex, single, administered, muscle,phentermine,phendimetrazine,chronium,htcz,billinginfo,injectiontype,proteindrinks,proteinbars,username,method,orientationdate,orientation,packetgiven,injectiondemonstrated,Phenterminefrequency,Phendimetrazinefrequency, locationname FROM tbl_patientpackage JOIN tbl_package on tbl_patientpackage.package_ID = tbl_package.packageId JOIN tbl_center ON tbl_patientpackage.centerID = tbl_center.centerID WHERE patient_ID = '$patientID' and (status = 'active' or status = 'open')  ORDER by date DESC ";

$result = mysql_query($query) or die(mysql_error());

while ($row = mysql_fetch_assoc($result)) 

{

		
	$query2 = "SELECT tbl_injectionID, tbl_injection.patient_ID, patientpackageID, date FROM tbl_injection WHERE patientpackageID = '$row[patientPackageID]' AND tbl_injection.patient_ID = '$patientID' ORDER BY date ASC ";
	$result2 = mysql_query($query2) or die(mysql_error());
		$num_rows = mysql_num_rows($result2);
		$count = 0;
		
		
		while ($row2 = mysql_fetch_assoc($result2))
				{
					if($row[package_ID] == 3 || $row[package_ID] == 4) { echo "<a href=\"deleteinjection.php?tbl_injectionID=$row2[tbl_injectionID]&amp;patientID=$patientID\"><img src=\"images/delete-item.gif\" width=\"16\" height=\"16\" /></a>" . date('m/d/Y', strtotime($row2[date]))."<br>"; } 
				}
		
	}
	
if(($row[package_ID] == 3 || $row[package_ID] == 4) && $num_rows < $row['injectioncount']) 

{echo " &nbsp;<a href=\"updateinjection.php?patientpackageID=$row[patientPackageID]&amp;patientID=$patientID\"  ><br/> <img src=\"images/10-medical.png\" width=\"12\" height=\"12\" align=\"absbottom\"/>  Add Injection </a>  ";}
	
	
if ($num_rows > 0 && $row[package_ID] == 3 or $row[package_ID] == 4 ) {echo  "<br>Injections: " . $num_rows . " of ". $row['injectioncount']. "<br/>"; } 
?>
</div>
<div class="col-xs-12"><hr class="blueborder"></div>



<div class="col-xs-6">
<div class="panel panel-default">
  <div class="panel-heading">
   <div class="col-xs-3 pull-left"><h3 class="panel-title"><span class="glyphicon glyphicon-file"></span>SOAP Note </h3></div>  <div class="col-xs-9"><button class="btn btn-info btn-xs pull-right" data-toggle="modal" data-target="#notesModal"> Add SOAP Note</button></div>
   <div class="clear"></div>
  </div>
  
  <div class="panel-body">
  <div class="col-xs-1"><h3>CC</h3></div><div class="col-xs-11"> <div class="well well-lg"><?php echo $row_rsPatient['cbox']; ?><br/><?php echo $row_rsPatient['ccnotes']; ?></div></div>
  <div class="col-xs-1"><h3>S</h3></div><div class="col-xs-11"><div class="well well-lg"><?php echo $row_rsPatient['subjective']; ?><br/><?php echo $row_rsPatient['subjectivenotes']; ?></div></div>
  <div class="col-xs-1"><h3>O</h3></div><div class="col-xs-11"><div class="well well-lg"><?php echo $row_rsPatient['objective']; ?><br/><?php echo $row_rsPatient['objectivenotes']; ?></div></div>
  <div class="col-xs-1"><h3>A</h3></div><div class="col-xs-11"><div class="well well-lg"><?php echo $row_rsPatient['assessment']; ?><br/><?php echo $row_rsPatient['assessmentnotes']; ?></div></div>
  <div class="col-xs-1"><h3>P</h3></div><div class="col-xs-11"><div class="well well-lg"><?php echo $row_rsPatient['plan']; ?><br/><?php echo $row_rsPatient['plannotes']; ?></div></div>
    </div>
</div>

</div>

<div class="col-xs-6">
<div class="panel panel-default">
  <div class="panel-heading">
   <h3 class="panel-title"><span class="glyphicon glyphicon-screenshot"></span> Current Medication</h3>
  </div>
  <div class="panel-body">
    <ol class="medibullets">
  <?php if ($totalRows_rsMedication == 0) { // Show if recordset empty ?>
     
        <li>No Medication Assigned</li>
    
      <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsMedication > 0) { // Show if recordset not empty ?>
  <?php do { ?>
    <li><?php echo $row_rsMedication['medicationName']; ?> - <a href="deletemedication.php?patientMedID=<?php echo $row_rsMedication['patientMedID']; ?>&amp;patientID=<?php echo $row_rsPatient['patientID']; ?>"><img src="images/217-trash.png" alt="217-trash" width="16" height="19" /></a></li>
    <?php } while ($row_rsMedication = mysql_fetch_assoc($rsMedication)); ?>
  <?php } // Show if recordset not empty ?>
  </ol>
  </div>
</div>
    
    
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><span class="glyphicon glyphicon-th-list"></span>  Medical Records</h3>
  </div>
  <div class="panel-body">
    <ol class="medibullets">
  <?php if ($totalRows_rsMedicalRecords == 0) { // Show if recordset empty ?>
     
        <li>No Medical Records Assigned</li>
    
      <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsMedicalRecords > 0) { // Show if recordset not empty ?>
  <?php do { ?>
    <li><a href="uploadedfilesforbw/<?php echo $row_rsMedicalRecords['filelocation']; ?>" target="_blank"><?php echo $row_rsMedicalRecords['title']; ?> </a><br/> <?php echo $row_rsMedicalRecords['filedescription']; ?> - <a href="deletemedicalrecords.php?formID=<?php echo $row_rsMedicalRecords['formID']; ?>&amp;patientID=<?php echo $row_rsPatient['patientID']; ?>"><img src="images/217-trash.png" alt="217-trash" width="16" height="19" /></a></li>
    <?php } while ($row_rsMedicalRecords = mysql_fetch_assoc($rsMedicalRecords)); ?>
  <?php } // Show if recordset not empty ?>
  </ol>
  </div>
</div>
</div>
    
	


</div>


    
    </div>
    <div role="tabpanel" class="tab-pane" id="viewpmh">
    <!-- Add Medical History -->

        <h4 class="modal-title" id="myModalLabel">Medical History</h4>
     
      <div class="medhistory">
        <form name="medicalhistory"  method="post" name="historyupdate" action="<?php echo $editFormAction; ?>" >
        <div class="row">
          <div class="col-xs-12 ">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th colspan="2">Referrals</th>
              </thead>
              <tr>
                <td>How did you hear about us?</td>
                <td><input type="text" class="form-control" name="howdidyouhear" value="<?php echo $row_rsPatient['howdidyouhear']; ?>"></td>
              </tr>
              <tr>
                <td>Did someone refer you to us? </td>
                <td><input type="radio" name="refer" value="yes"  <?php if (!(strcmp(htmlentities($row_rsPatient['refer'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                  Yes
                  <input type="radio" name="refer" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['refer'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>>
                  No </td>
              </tr>
              <tr>
                <td>If yes, please list their name?</td>
                <td><input type="text" name="refername" class="form-control" value="<?php echo $row_rsPatient['refername']; ?>"></td>
              </tr>
              <tr>
                <td>Is this person currently a patient?</td>
                <td><input type="radio" name="curpatient" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['curpatient'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                  Yes
                  <input type="radio" name="curpatient" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['curpatient'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>>
                  No</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th colspan="2">Insurance Information</th>
              </thead>
              <tr>
                <td>Insurance</td>
                <td><input type="radio" name="insurance" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['insurance'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                  Yes
                  <input type="radio" name="insurance" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['insurance'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>>
                  No </td>
              </tr>
              <tr>
                <td>Insurance Company</td>
                <td><input type="text" class="form-control" name="insuranceco" value="<?php echo $row_rsPatient['insuranceco']; ?>"></td>
              </tr>
              <tr>
                <td>Co-Pay</td>
                <td><input type="text" class="form-control" name="copay" value="<?php echo $row_rsPatient['copay']; ?>"></td>
              </tr>
              <tr>
                <td>Policy Number</td>
                <td><input type="text" class="form-control" name="policyno" value="<?php echo $row_rsPatient['policyno']; ?>"></td>
              </tr>
              <tr>
                <td>Group Name</td>
                <td><input type="text" class="form-control" name="groupno" value="<?php echo $row_rsPatient['groupno']; ?>"></td>
              </tr>
              <tr>
                <td>Subscriber Name</td>
                <td><input type="text" class="form-control" name="subname" value="<?php echo $row_rsPatient['subname']; ?>"></td>
              </tr>
              <tr>
                <td>Relation to Subscriber</td>
                <td><input type="text" class="form-control" name="subrel" value="<?php echo $row_rsPatient['subrel']; ?>"></td>
              </tr>
            </table>
          </div>
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th colspan="2">Emergency Contact</th>
              </thead>
              <tr>
                <td>Emergency Contact</td>
                <td><input type="text" class="form-control" name="emergencycontact" value="<?php echo $row_rsPatient['emergencycontact']; ?>"></td>
              </tr>
              <tr>
                <td>Emergency Contact Phone</td>
                <td><input type="text" class="form-control" name="emergencycontactphone" value="<?php echo $row_rsPatient['emergencycontactphone']; ?>"></td>
              </tr>
              <tr>
                <td>Relationship</td>
                <td><input type="text" class="form-control" name="relationship" value="<?php echo $row_rsPatient['relationship']; ?>"></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Medication</th>
                  <th>Yes</th>
                  <th>No</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Do you take medication for anxiety?</td>
                  <td><input type="radio" name="anxiety" value="yes"  <?php if (!(strcmp(htmlentities($row_rsPatient['allergies'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="radio" name="anxiety" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['allergies'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
                <tr>
                  <td>Do you take Coumadin?</td>
                  <td><input type="radio" name="coumadin" value="yes"  <?php if (!(strcmp(htmlentities($row_rsPatient['coumadin'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="radio" name="coumadin" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['coumadin'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
                <tr>
                  <td>Do you take prednisone or steroids</td>
                  <td><input type="radio" name="steroids" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['steroids'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="radio" name="steroids" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['steroids'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
                <tr>
                  <td>Do you take antidepressants?</td>
                  <td><input type="radio" name="antidepressants" value="yes"  <?php if (!(strcmp(htmlentities($row_rsPatient['antidepressants'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="radio" name="antidepressants" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['antidepressants'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
                <tr>
                  <td>Do you take herbs, roots, medicinal tea?</td>
                  <td><input type="radio" name="herbs" value="yes"  <?php if (!(strcmp(htmlentities($row_rsPatient['herbs'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="radio" name="herbs" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['herbs'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
              </tbody>
            </table>
            Please list your other medical problems (such as diabetes, high blood pressure, etc):
            <textarea class="form-control" name="otherproblems"><?php echo $row_rsPatient['otherproblems']; ?></textarea>
            <br>
            <br>
            List all operations/surgeries you have had in the past and any complications you had:
            <textarea class="form-control" name="othersurgeries"><?php echo $row_rsPatient['othersurgeries']; ?></textarea>
          </div>
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Name of Medication</th>
                  <th>Dose</th>
                  <th>How many times per day</th>
              </thead>
              <tr>
                <td width="55%"><input type="textbox" name="med1" class="form-control" value="<?php echo $row_rsPatient['med1']; ?>"></td>
                <td width="10%"><input type="textbox" name="med1dose" class="form-control" value="<?php echo $row_rsPatient['med1dose']; ?>"></td>
                <td><input type="radio" name="med1times" value="1"  <?php if (!(strcmp(htmlentities($row_rsPatient['med1times'], ENT_COMPAT, 'UTF-8'),"1"))) {echo "checked=\"checked\"";} ?>>
                  <label> 1 </label>
                  <input type="radio" name="med1times" value="2"  <?php if (!(strcmp(htmlentities($row_rsPatient['med1times'], ENT_COMPAT, 'UTF-8'),"2"))) {echo "checked=\"checked\"";} ?>>
                  <label> 2 </label>
                  <input type="radio" name="med1times" value="3" <?php if (!(strcmp(htmlentities($row_rsPatient['med1times'], ENT_COMPAT, 'UTF-8'),"3"))) {echo "checked=\"checked\"";} ?>>
                  <label> 3 </label>
                  <input type="radio" name="med1times" value="4" <?php if (!(strcmp(htmlentities($row_rsPatient['med1times'], ENT_COMPAT, 'UTF-8'),"4"))) {echo "checked=\"checked\"";} ?>>
                  <label> 4 </label></td>
              </tr>
              <tr>
                <td><input type="textbox" name="med2" class="form-control" value="<?php echo $row_rsPatient['med2']; ?>"></td>
                <td><input type="textbox" name="med2dose" class="form-control" value="<?php echo $row_rsPatient['med2dose']; ?>"></td>
                <td><input type="radio" name="med2times" value="1" <?php if (!(strcmp(htmlentities($row_rsPatient['med2times'], ENT_COMPAT, 'UTF-8'),"1"))) {echo "checked=\"checked\"";} ?>>
                  <label>1</label>
                  <input type="radio" name="med2times" value="2" <?php if (!(strcmp(htmlentities($row_rsPatient['med2times'], ENT_COMPAT, 'UTF-8'),"2"))) {echo "checked=\"checked\"";} ?>>
                  <label> 2</label>
                  <input type="radio" name="med2times" value="3" <?php if (!(strcmp(htmlentities($row_rsPatient['med2times'], ENT_COMPAT, 'UTF-8'),"3"))) {echo "checked=\"checked\"";} ?>>
                  <label> 3</label>
                  <input type="radio" name="med2times" value="4" <?php if (!(strcmp(htmlentities($row_rsPatient['med2times'], ENT_COMPAT, 'UTF-8'),"4"))) {echo "checked=\"checked\"";} ?>>
                  <label> 4</label></td>
              </tr>
              <tr>
                <td><input type="textbox" name="med3" class="form-control" value="<?php echo $row_rsPatient['med3']; ?>"></td>
                <td><input type="textbox" name="med3dose" class="form-control" value="<?php echo $row_rsPatient['med3dose']; ?>"></td>
                <td><input type="radio" name="med3times" value="1" <?php if (!(strcmp(htmlentities($row_rsPatient['med3times'], ENT_COMPAT, 'UTF-8'),"1"))) {echo "checked=\"checked\"";} ?>>
                  <label>1</label>
                  <input type="radio" name="med3times" value="2" <?php if (!(strcmp(htmlentities($row_rsPatient['med3times'], ENT_COMPAT, 'UTF-8'),"2"))) {echo "checked=\"checked\"";} ?>>
                  <label> 2</label>
                  <input type="radio" name="med3times" value="3" <?php if (!(strcmp(htmlentities($row_rsPatient['med3times'], ENT_COMPAT, 'UTF-8'),"3"))) {echo "checked=\"checked\"";} ?>>
                  <label> 3</label>
                  <input type="radio" name="med3times" value="4" <?php if (!(strcmp(htmlentities($row_rsPatient['med3times'], ENT_COMPAT, 'UTF-8'),"4"))) {echo "checked=\"checked\"";} ?>>
                  <label> 4</label></td>
              </tr>
              <tr>
                <td><input type="textbox" name="med4" class="form-control" value="<?php echo $row_rsPatient['med4']; ?>"></td>
                <td><input type="textbox" name="med4dose" class="form-control" value="<?php echo $row_rsPatient['med4dose']; ?>"></td>
                <td><input type="radio" name="med4times" value="1" <?php if (!(strcmp(htmlentities($row_rsPatient['med4times'], ENT_COMPAT, 'UTF-8'),"1"))) {echo "checked=\"checked\"";} ?>>
                  <label>1</label>
                  <input type="radio" name="med4times" value="2" <?php if (!(strcmp(htmlentities($row_rsPatient['med4times'], ENT_COMPAT, 'UTF-8'),"2"))) {echo "checked=\"checked\"";} ?>>
                  <label> 2</label>
                  <input type="radio" name="med4times" value="3" <?php if (!(strcmp(htmlentities($row_rsPatient['med4times'], ENT_COMPAT, 'UTF-8'),"3"))) {echo "checked=\"checked\"";} ?>>
                  <label> 3</label>
                  <input type="radio" name="med4times" value="4" <?php if (!(strcmp(htmlentities($row_rsPatient['med4times'], ENT_COMPAT, 'UTF-8'),"4"))) {echo "checked=\"checked\"";} ?>>
                  <label> 4</label></td>
              </tr>
              <tr>
                <td><input type="textbox" name="med5" class="form-control" value="<?php echo $row_rsPatient['med5']; ?>"></td>
                <td><input type="textbox" name="med5dose" class="form-control" value="<?php echo $row_rsPatient['med5dose']; ?>"></td>
                <td><input type="radio" name="med5times" value="1" <?php if (!(strcmp(htmlentities($row_rsPatient['med5times'], ENT_COMPAT, 'UTF-8'),"1"))) {echo "checked=\"checked\"";} ?>>
                  <label>1</label>
                  <input type="radio" name="med5times" value="2" <?php if (!(strcmp(htmlentities($row_rsPatient['med5times'], ENT_COMPAT, 'UTF-8'),"2"))) {echo "checked=\"checked\"";} ?>>
                  <label> 2</label>
                  <input type="radio" name="med5times" value="3" <?php if (!(strcmp(htmlentities($row_rsPatient['med5times'], ENT_COMPAT, 'UTF-8'),"3"))) {echo "checked=\"checked\"";} ?>>
                  <label> 3</label>
                  <input type="radio" name="med5times" value="4" <?php if (!(strcmp(htmlentities($row_rsPatient['med5times'], ENT_COMPAT, 'UTF-8'),"4"))) {echo "checked=\"checked\"";} ?>>
                  <label> 4</label></td>
              </tr>
            </table>
          </div>
        </div>
        <!-- end row -->
        <hr>
        <div class="row">
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Family History</th>
                  <th>Mother</th>
                  <th>Father</th>
                  <th>Other</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Cancer</td>
                  <td><input type="checkbox" name="familycancer" value="mother" <?php if (!(strcmp(htmlentities($row_rsPatient['familycancer'], ENT_COMPAT, 'UTF-8'),"mother"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="familycancer" value="father" <?php if (!(strcmp(htmlentities($row_rsPatient['familycancer'], ENT_COMPAT, 'UTF-8'),"father"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="familycancer" value="other" <?php if (!(strcmp(htmlentities($row_rsPatient['familycancer'], ENT_COMPAT, 'UTF-8'),"other"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
                <tr>
                  <td>Diabetes</td>
                  <td><input type="checkbox" name="familydiabetes" value="mother"<?php if (!(strcmp(htmlentities($row_rsPatient['familydiabetes'], ENT_COMPAT, 'UTF-8'),"mother"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="familydiabetes" value="father" <?php if (!(strcmp(htmlentities($row_rsPatient['familydiabetes'], ENT_COMPAT, 'UTF-8'),"father"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="familydiabetes" value="other" <?php if (!(strcmp(htmlentities($row_rsPatient['familydiabetes'], ENT_COMPAT, 'UTF-8'),"other"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
                <tr>
                  <td>Heart Disease</td>
                  <td><input type="checkbox" name="heartdisease" value="mother" <?php if (!(strcmp(htmlentities($row_rsPatient['heartdisease'], ENT_COMPAT, 'UTF-8'),"mother"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="heartdisease" value="father"  <?php if (!(strcmp(htmlentities($row_rsPatient['heartdisease'], ENT_COMPAT, 'UTF-8'),"father"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="heartdisease" value="other" <?php if (!(strcmp(htmlentities($row_rsPatient['heartdisease'], ENT_COMPAT, 'UTF-8'),"other"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
                <tr>
                  <td>Stroke</td>
                  <td><input type="checkbox" name="stroke" value="mother"  <?php if (!(strcmp(htmlentities($row_rsPatient['stroke'], ENT_COMPAT, 'UTF-8'),"mother"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="stroke" value="father" <?php if (!(strcmp(htmlentities($row_rsPatient['stroke'], ENT_COMPAT, 'UTF-8'),"father"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="stroke" value="other"  <?php if (!(strcmp(htmlentities($row_rsPatient['stroke'], ENT_COMPAT, 'UTF-8'),"other"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
                <tr>
                  <td>Other</td>
                  <td><input type="checkbox" name="other" value="mother" <?php if (!(strcmp(htmlentities($row_rsPatient['other'], ENT_COMPAT, 'UTF-8'),"mother"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="other" value="father" <?php if (!(strcmp(htmlentities($row_rsPatient['other'], ENT_COMPAT, 'UTF-8'),"father"))) {echo "checked=\"checked\"";} ?>></td>
                  <td><input type="checkbox" name="other" value="other" <?php if (!(strcmp(htmlentities($row_rsPatient['stroke'], ENT_COMPAT, 'UTF-8'),"other"))) {echo "checked=\"checked\"";} ?>></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th colspan="5">Social History</th>
              </thead>
              <tbody>
                <tr>
                  <td>Marital Status</td>
                  <td><input type="checkbox" name="maritalstatus" value="single" <?php if (!(strcmp(htmlentities($row_rsPatient['maritalstatus'], ENT_COMPAT, 'UTF-8'),"single"))) {echo "checked=\"checked\"";} ?>>
                    Single</td>
                  <td><input type="checkbox" name="maritalstatus" value="married" <?php if (!(strcmp(htmlentities($row_rsPatient['maritalstatus'], ENT_COMPAT, 'UTF-8'),"married"))) {echo "checked=\"checked\"";} ?>>
                    Married</td>
                  <td><input type="checkbox" name="maritalstatus" value="divorced" <?php if (!(strcmp(htmlentities($row_rsPatient['maritalstatus'], ENT_COMPAT, 'UTF-8'),"divorced"))) {echo "checked=\"checked\"";} ?>>
                    Divorced </td>
                  <td><input type="checkbox" name="maritalstatus" value="widow" <?php if (!(strcmp(htmlentities($row_rsPatient['maritalstatus'], ENT_COMPAT, 'UTF-8'),"widow"))) {echo "checked=\"checked\"";} ?>>
                    Widow</td>
                </tr>
                <tr>
                  <td>Drink Alcohol?</td>
                  <td><input type="radio" name="alcohol" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['alcohol'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                    Yes </td>
                  <td><input type="radio" name="alcohol" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['alcohol'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>>
                    No</td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td>Smoke?</td>
                  <td><input type="radio" name="smoke" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['smoke'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                    Yes </td>
                  <td><input type="radio" name="smoke" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['smoke'], ENT_COMPAT, 'UTF-8'),"no"))) {echo "checked=\"checked\"";} ?>>
                    No </td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td>Recreational Drugs?</td>
                  <td><input type="radio" name="drugs" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['drugs'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                    Yes</td>
                  <td><input type="radio" name="drugs" value="no" <?php if (!(strcmp(htmlentities($row_rsPatient['drugs'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                    No</td>
                  <td></td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- end row -->
        <div class="row">
          <div class="col-xs-2">
            <h5>Neuro</h5>
          
              
                <input type="checkbox" name="convulsions" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['convulsions'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Convulsions <br>
              
                <input type="checkbox" name="migranes" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['migranes'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Migrane Headaches<br>
              
                <input type="checkbox" name="strokes" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['strokes'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Strokes<br>
              
                <input type="checkbox" name="paralysis" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['paralysis'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Paralysis<br>
           
          </div>
          <div class="col-xs-2">
            <h5>Cardiac</h5>
           
              
                <input type="checkbox" name="chestpain" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['chestpain'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Chest Pain<br>
              
                <input type="checkbox" name="palpations" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['palpations'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Palpatations<br>
              
                <input type="checkbox" name="highbloodpressure" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['highbloodpressure'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                High Blood Pressure<br>
              
                <input type="checkbox" name="heartfailure" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['heartfailure'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Heart Failure
              <br>
                <input type="checkbox" name="heartattack" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['heartattack'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Heart Attack<br>
           
          </div>
          <div class="col-xs-2">
            <h5>Pulmonary</h5>
          
              
                <input type="checkbox" name="chroniccough" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['chroniccough'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Chronic Cough<br>
              
                <input type="checkbox" name="sleepapnea" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['sleepapnea'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Sleep Apnea / Snoring<br>
              
                <input type="checkbox" name="asthma" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['asthma'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Asthma<br>
              
                <input type="checkbox" name="colds" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['colds'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Recent Colds and Pneumonia<br>
          
          </div>
          <div class="col-xs-2">
            <h5>Renal</h5>
           
              
                <input type="checkbox" name="bloodinurine" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['bloodinurine'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Blood in urine<br>
              
                <input type="checkbox" name="bladder" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['bladder'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Frequent Bladder Infections<br>
              
                <input type="checkbox" name="kidney" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['kidney'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Kidney Infections/Disorders<br>
              
                <input type="checkbox" name="pain" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['pain'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Pain when urinating
         
          </div>
          <div class="col-xs-2">
            <h5>Hearing</h5>
         
              
                <input type="checkbox" name="deafness" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['deafness'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Deafness<br>
              
                <input type="checkbox" name="ringinginears" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['ringinginears'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Ringing in ears<br>
            
          </div>
          <div class="col-xs-2">
            <h5>Skin/Integument</h5>
         
              
                <input type="checkbox" name="skinrashes" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['alcohol'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Skin Rashes<br>
              
                <input type="checkbox" name="moles" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['alcohol'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Unusual Moles<br>
              
                <input type="checkbox" name="breastlumps" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['alcohol'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Breast Lumps
            
          </div>
        </div>
        <!-- end row -->
        <div class="row">
          <div class="col-xs-2">
            <h5>Muscular/Skeletal</h5>
         
              
                <input type="checkbox" name="backpain" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['backpain'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Back Pain<br>
              
                <input type="checkbox" name="hippain" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['hippain'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Hip Pain<br>
              
                <input type="checkbox" name="kneepain" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['kneeppain'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Knee Pain<br>
              
                <input type="checkbox" name="otherjoin" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['otherjoin'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Other Joint<br>
              
                <input type="checkbox" name="arthritis" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['arthritis'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Arthritis<br>
           
          </div>
          <div class="col-xs-2">
            <h5>Psychiatric</h5>
          
              
                <input type="checkbox" name="depression" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['depression'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Depression<br>
              
                <input type="checkbox" name="anxiety2" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['anxiety2'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Anxiety<br>
              
                <input type="checkbox" name="hallucination" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['hallucination'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Hallucination<br>
          
          </div>
          <div class="col-xs-2">
            <h5>Endocrine</h5>
           
              
                <input type="checkbox" name="diabetes" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['diabetes'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Diabetes<br>
              
                <input type="checkbox" name="thyroid" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['thyroid'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Thyroid Problems<br>
              
                <input type="checkbox" name="lackofenergy" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['lackofenergy'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Lack of Energy<br>
        
          </div>
          <div class="col-xs-2">
            <h5>Hematologic</h5>
            
              
                <input type="checkbox" name="anemia" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['anemia'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Anemia<br>
              
                <input type="checkbox" name="easybruising" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['easybruising'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Easy Bruising<br>
              
                <input type="checkbox" name="bloodclots" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['bloodclots'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Blood Clots in deep veins of arms/legs<br>
              
                <input type="checkbox" name="bloodtransfusions" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['bloodtransfusions'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Blood Transfusions<br>
              
                <input type="checkbox" name="hiv" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['hiv'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                HIV/AIDS<br>
         
          </div>
          <div class="col-xs-2">
            <h5>Vision</h5>
            
              
                <input type="checkbox" name="blindness" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['blindness'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Blindness<br>
              
                <input type="checkbox" name="doublevision" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['doublevision'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Double Vision<br>
              
                <input type="checkbox" name="contacts" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['contacts'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Do you wear glasses or contact lenses?
         
          </div>
          <div class="col-xs-2">
            <h5>Constitutional</h5>
            
              
                <input type="checkbox" name="fever" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['fever'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Fever<br>
              
                <input type="checkbox" name="chills" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['chills'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Chills<br>
              
                <input type="checkbox" name="sweats" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['sweats'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Sweats<br>
              
                <input type="checkbox" name="weightgain" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['weightgain'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Excessive Weight Gain<br>
              
                <input type="checkbox" name="allergies" value="yes"  <?php if (!(strcmp(htmlentities($row_rsPatient['allergies'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Allergies
           
          </div>
        </div>
        <!-- end row -->
        <div class="row">
        <div class="col-xs-3">
          <h5>Gastrointestinal</h5>
         
            
              <input type="checkbox" name="bloodinstool" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['bloodinstool'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Blood in Stool<br>
            
              <input type="checkbox" name="vomitblood" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['vomitblood'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Vomiting Blood<br>
            
              <input type="checkbox" name="blackstool" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['blackstool'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Black Stools<br>
            
              <input type="checkbox" name="diarrhea" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['diarrhea'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Chronic Diarrhea<br>
            
              <input type="checkbox" name="constipation" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['constipation'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Chronic Constipation<br>
            
              <input type="checkbox" name="bloating" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['bloating'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Bloating<br>
            
              <input type="checkbox" name="nausea" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['nausea'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Nausea or Vomiting<br>
            
              <input type="checkbox" name="diffswallowing" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['diffswallowing'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Difficulty Swallowing<br>
            
              <input type="checkbox" name="painswallowing" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['painswallowing'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Pain when swallowing<br>
            
              <input type="checkbox" name="heartburn" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['heartburn'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Chronic Heartburn / Reflux<br>
            
              <input type="checkbox" name="hepatitis" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['hepatitis'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Hepatitis
            
              <input type="checkbox" name="ulcers" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['ulcers'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Ulcers<br>
            
              <input type="checkbox" name="pancreatitis" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['pancreatitis'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Pancreatitis<br>
            
              <input type="checkbox" name="gallstones" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['gallstones'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
              Gallstones<br>
         
        </div>
        <div class="row">
          <div class="col-xs-3">
            <h5>Gynecology (Ladies Only)</h5>
            
              
                <input type="checkbox" name="pregnant" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['pregnant'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Ever been pregnant?<br>
              
                <input type="checkbox" name="children" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['children'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Do you have children?<br>
              
                <input type="checkbox" name="menstrual" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['menstrual'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Any changes in menstrual?<br>
              
                <input type="checkbox" name="menopause" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['menopause'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Menopause?<br>
              
                <input type="checkbox" name="breastfeed" value="yes" <?php if (!(strcmp(htmlentities($row_rsPatient['breatfeed'], ENT_COMPAT, 'UTF-8'),"yes"))) {echo "checked=\"checked\"";} ?>>
                Do you breast feed?<br>
              <br>
              Date of last menstrual period?
                <input type="text" name="lastperiod" class="form-control">
              
          
          </div>
          <div class="col-xs-3"></div>
          <div class="col-xs-3"></div>
          <div class="col-xs-3"></div>
        
        <!-- end row -->
        <input type="hidden" name="patientID" value="<?php echo $row_rsPatient['patientID']; ?>">
        <input type="hidden" name="MM_update" value="form1">
        <p>&nbsp;</p>
     
     
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit"  class="btn btn-success" value="Update record">
        </form>

    <!-- end medical history -->
        </div>

</div></div>
    
    </div>
    <div role="tabpanel" class="tab-pane" id="clinic">
    
    <span class="visitname"><span class="glyphicon glyphicon-folder-close"></span> &nbsp; &nbsp; <?php echo $row_rsPatient['fname']; ?> <?php echo $row_rsPatient['lname']; ?> Visit History</span> 
    
    <?php if ($totalRows_rsVitals == 0) { // Show if recordset empty ?>
  No Records Found
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsVitals > 0) { // Show if recordset not empty ?>
<table width="100%" class="table table-striped" id="newweight">
<thead>
  <tr>
    
    <th>Date</th>
    <th>Weight</th>
    <th>Pulse</th>
    <th>Blood Pressure</th>
    <th>BMI</th>
    <!--<th>Hip</th>
    <th>Waist</th>
    <th>Ratio</th>-->
    <th>Administrator</th>
    <th>Delete</th>

  </tr>
  </thead>
  <tbody>
  <?php do { ?>
    <tr>
     
     
      <td width="100px"><?php echo date('m/d/Y', strtotime( $row_rsVitals['date'])); ?></td>
      <td><span class="weight"><?php echo $row_rsVitals['weight']; ?></span></td>
      <td><?php echo $row_rsVitals['pulse']; ?></td>
      <td><?php echo $row_rsVitals['systolic']; ?> / <?php echo $row_rsVitals['diastolic']; ?> </td>
      <td align="center"><?php echo round( ($row_rsVitals['weight']/ ((($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) * (($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) )* 703), 2);  ?></td>
      <!-- <td align="center"><?php echo $row_rsVitals['waist']?></td>
       <td align="center"><?php echo $row_rsVitals['hip']?></td>
       <td align="center"><?php
    $ratio = $row_rsVitals['waist']/$row_rsVitals['hip']; 
    echo number_format((float)$ratio,2,'.','');
	
	
	?></td>-->
      <td align="center"><?php echo $row_rsVitals['firstname']; ?></td>
     
      <td align="center"><strong><a href="deletevitals.php?patientID=<?php echo $row_rsPatient['patientID']; ?>&amp;vitalID=<?php echo $row_rsVitals['vitalID']; ?>">
      <span class="glyphicon glyphicon-remove"></span>
      </a></strong>
      </td>
    </tr>
    <?php } while ($row_rsVitals = mysql_fetch_assoc($rsVitals)); ?>
    </tbody>
</table>

<?php
// DMXzone Paginator PHP 1.0.2
$pag1 = new dmxPaginator();
$pag1->recordsetName = "rsVitals";
$pag1->rowsTotal = $totalRows_rsVitals;
$pag1->showNextPrev = true;
$pag1->showFirstLast = true;
$pag1->outerLinks = 1;
$pag1->pageNumSeparator = "...";
$pag1->adjacentLinks = 2;
$pag1->rowsPerPage = $maxRows_rsVitals;
$pag1->prevLabel = "‹";
$pag1->nextLabel = "›";
$pag1->firstLabel = "‹‹";
$pag1->lastLabel = "››";
$pag1->addPagination();
?>

 <?php } // Show if recordset not empty ?>

    
    </div>
    <div role="tabpanel" class="tab-pane" id="package">
    
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



    
    </div>
    <div role="tabpanel" class="tab-pane" id="products">...</div>
  </div>
</div>







<!-- SOAP Modal -->
<div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel"> SOAP NOTES</h4>
      </div>
      <div class="modal-body">
	<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="#home" role="tab" data-toggle="tab">CC</a></li>
  <li><a href="#profile" role="tab" data-toggle="tab">S</a></li>
  <li><a href="#messages" role="tab" data-toggle="tab">O</a></li>
  <li><a href="#settings" role="tab" data-toggle="tab">A</a></li>
  <li><a href="#plan" role="tab" data-toggle="tab">P</a></li>
</ul>
   <?php
$as = ($row_rsPatient['cbox']);

?>


<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="home">
  <h3>Chief Complaint </h3>
  <form name="soapnotes" class="soapnotes" method="POST" action="<?php echo $editFormAction; ?>">
  <div class="row">
  <div class="col-xs-4">
  <ul>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Overweight Obesity 278.00"  <?php if (strstr($as,"Overweight Obesity 278.00")) {
	echo "checked=\"checked\"";	}?>> Overweight/Obesity 278.00 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Hypothyroidism 244.9" <?php if (strstr($as,"Hypothyroidism 244.9")) {
	echo "checked=\"checked\"";	}?>> Hypothyroidism 244.9 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Morbid Obesity 278.01" <?php if (strstr($as,"Morbid Obesity 278.01")) {
	echo "checked=\"checked\"";	}?>> Morbid Obesity 278.01 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Sleep Apnea 780.57"  <?php if (strstr($as,"Sleep Apnea 780.57")) {
	echo "checked=\"checked\"";	}?>> Sleep Apnea 780.57</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Eating Disorder 307.50"  <?php if (strstr($as,"Eating Disorder 307.50" )) {
	echo "checked=\"checked\"";	}?>> Eating Disorder 307.50 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Hypertension 401.9"  <?php if (strstr($as,"Hypertension 401.9")) {
	echo "checked=\"checked\"";	}?>> Hypertension 401.9</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Obesity of Endocrine 259.9"  <?php if (strstr($as,"Obesity of Endocrine 259.9")) {
	echo "checked=\"checked\"";	}?>> Obesity of Endocrine 259.9</li>
   <li><input type="checkbox" class="cbox" name="cbox[]" value="Polyphagia 783.6"  <?php if (strstr($as,"Polyphagia 783.6")) {
	echo "checked=\"checked\"";	}?>> Polyphagia 783.6</li>
  </ul>
  </div>
  <div class="col-xs-4">
  <ul>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Hyperlipidemia 272.4"  <?php if (strstr($as,"Hyperlipidemia 272.4")) {
	echo "checked=\"checked\"";	}?> > Hyperlipidemia 272.4 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Dysmetabolic Syndrome 277.7"  <?php if (strstr($as,"Dysmetabolic Syndrome 277.7")) {
	echo "checked=\"checked\"";	}?>> Dysmetabolic Syndrome 277.7 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Hypercholestermia 272.00"  <?php if (strstr($as,"Hypercholestermia 272.00")) {
	echo "checked=\"checked\"";	}?>> Hypercholestermia 272.00 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Fatigue 708.79"  <?php if (strstr($as,"Fatigue 708.79")) {
	echo "checked=\"checked\"";	}?>> Fatigue 708.79</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Diabeties/Controlled 250.0"  <?php if (strstr($as,"Diabeties/Controlled 250.0")) {
	echo "checked=\"checked\"";	}?>> Diabeties/Controlled 250.0</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Weight Gain - Abnormal 783.1"  <?php if (strstr($as,"Weight Gain - Abnormal 783.1"  )) {
	echo "checked=\"checked\"";	}?>> Weight Gain - Abnormal 783.1</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Diabeties/Uncontrolled 250.02"  <?php if (strstr($as,"Diabeties/Uncontrolled 250.02")) {
	echo "checked=\"checked\"";	}?>> Diabeties/Uncontrolled 250.02</li>
  </ul>
  </div>
  <div class="col-xs-4">
   <ul>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Excessive Appetite 783.6"  <?php if (strstr($as,"Excessive Appetite 783.6")) {
	echo "checked=\"checked\"";	}?>> Excessive Appetite 783.6 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Depression 311.0"  <?php if (strstr($as,"Depression 311.0")) {
	echo "checked=\"checked\"";	}?>> Depression 311.0 </li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Overeating D/T Stress 308.3"  <?php if (strstr($as,"Overeating D/T Stress 308.3")) {
	echo "checked=\"checked\"";	}?>> Overeating D/T Stress 308.3</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Gallstones 574.30"  <?php if (strstr($as,"Gallstones 574.30")) {
	echo "checked=\"checked\"";	}?>> Gallstones 574.30</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Fatigue 780.79"  <?php if (strstr($as,"Fatigue 780.79")) {
	echo "checked=\"checked\"";	}?>> Fatigue 780.79</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Arthritis 715.9"  <?php if (strstr($as,"Arthritis 715.9")) {
	echo "checked=\"checked\"";	}?>> Arthritis 715.9</li>
  <li><input type="checkbox" class="cbox" name="cbox[]" value="Headache 784.0"  <?php if (strstr($as,"Headache 784.0")) {
	echo "checked=\"checked\"";	}?>> Headache 784.0</li>
  </ul>
  
  </div>
  
  <div class="col-xs-12">
  <h5>Comments</h5>
  <textarea class="form-control" name="ccnotes">

  <?php $row_rsPatient['ccnotes']; ?>
  

  </textarea>
  </div>
     <?php
$ss = ($row_rsPatient['subjective']);

?>

  </div>
  </div>
  <div class="tab-pane" id="profile">
  <div class="row">
  <div class="col-xs-12">
  <h3>Subjective</h3>
  <ul>
  <li><input type="checkbox" class="cbox" name="subjective[]" value="Patient here for initial weight loss consultation /evaluation for weight management program." <?php if (strstr($ss,"Patient here for initial weight loss consultation /evaluation for weight management program.")) {
	echo "checked=\"checked\"";	}?>> Patient here for initial weight loss consultation /evaluation for weight management program.</li>

<li> <input type="checkbox" class="cbox" name="subjective[]" value="Patient here for follow up visit." <?php if (strstr($ss,"Patient here for follow up visit.")) {
	echo "checked=\"checked\"";	}?>> Patient here for follow up visit.</li>

<li> <input type="checkbox" class="cbox" name="subjective[]" value="Patient here for follow up injection." <?php if (strstr($ss,"Patient here for follow up injection.")) {
	echo "checked=\"checked\"";	}?>> Patient here for follow up injection.</li>
<li> <input type="checkbox" class="cbox" name="subjective[]" value="Patient here for hCG consultation/evaluation." <?php if (strstr($ss,"Patient here for hCG consultation/evaluation.")) {
	echo "checked=\"checked\"";	}?> <?php if (strstr($ss,"Patient here for hCG consultation/evaluation.")) {
	echo "checked=\"checked\"";	}?>> Patient here for hCG consultation/evaluation.</li>

<li><input type="checkbox" class="cbox" name="subjective[]" value="Patient here for hCG follow up visit." <?php if (strstr($ss,"Patient here for hCG follow up visit.")) {
	echo "checked=\"checked\"";	}?>> Patient here for hCG follow up visit.</li>
  
  </ul>
  </div>
    
  <div class="col-xs-12">
  <h5>Comments</h5>
  <textarea class="form-control" name="subjectivenotes">
  <?php $row_rsPatient['subjectivenotes']; ?>
  
  </textarea>
  </div>
  </div>
  </div>
  
   <?php
$os = ($row_rsPatient['objective']);

?>
  <div class="tab-pane" id="messages">
  <h3>Objective</h3>
  <div class="row">
  <div class="col-xs-6">
<strong>General:</strong>
<ul>
<li><input type="checkbox" class="cbox" name="objective[]" value="Well appearing, in no distress. Alert and oriented." <?php if (strstr($os,"Well appearing, in no distress. Alert and oriented.")) {
	echo "checked=\"checked\"";	}?>> Well appearing, in no distress. Alert and oriented.</li>
</ul>

<strong>HEENT:</strong>
<ul>
<li><input type="checkbox" class="cbox" name="objective[]" value="Denies visual loss, H/A, deafness, URI’s, glasses" <?php if (strstr($os,"Denies visual loss, H/A, deafness, URI’s, glasses")) {
	echo "checked=\"checked\"";	}?>> Denies visual loss, H/A, deafness, URI’s, glasses</li>
<li><input type="checkbox" class="cbox" name="objective[]" value="NC/AT, Conjunctiva pink, no petechiae. Oral pharynx w/o lesions" <?php if (strstr($os,"NC/AT, Conjunctiva pink, no petechiae. Oral pharynx w/o lesions")) {
	echo "checked=\"checked\"";	}?>> NC/AT, Conjunctiva pink, no petechiae. Oral pharynx w/o lesions</li>
</ul>

<strong>Neck:</strong>
<ul>
<li><input type="checkbox" class="cbox" name="objective[]" value="Supple, no JVD." <?php if (strstr($os,"Supple, no JVD.")) {
	echo "checked=\"checked\"";	}?> > Supple, no JVD.</li>
</ul>

<strong>Extremities:</strong>
<ul>
<li><input type="checkbox" class="cbox" name="objective[]" value="No cyanosis, clubbing or edema." <?php if (strstr($os,"No cyanosis, clubbing or edema.")) {
	echo "checked=\"checked\"";	}?>> No cyanosis, clubbing or edema.</li>
</ul>

  </div>
  <div class="col-xs-6">
 <strong> Chest/Resp:</strong>
<ul>
<li><input type="checkbox" class="cbox" name="objective[]" value="Clear to auscultation bilaterally. No rales, wheezing or rhonchi." <?php if (strstr($os,"Clear to auscultation bilaterally. No rales, wheezing or rhonchi.")) {
	echo "checked=\"checked\"";	}?>> Clear to auscultation bilaterally. No rales, wheezing or rhonchi.</li>
</ul>
<strong>Heart/CV:</strong>
<ul>
<li><input type="checkbox" class="cbox" name="objective[]" value="Denies palpitations, chest pains" <?php if (strstr($os,"Denies palpitations, chest pains")) {
	echo "checked=\"checked\"";	}?>> Denies palpitations, chest pains</li>

<li><input type="checkbox" class="cbox" name="objective[]" value="Regular S1, S2, without murmur, rubs or gallops." <?php if (strstr($os,"Regular S1, S2, without murmur, rubs or gallops.")) {
	echo "checked=\"checked\"";	}?>> Regular S1, S2, without murmur, rubs or gallops.</li>
</ul>
<strong>Abdomen/GI:</strong>
<ul>
<li><input type="checkbox" class="cbox" name="objective[]" value="Denies heartburn, diarrhea, constipation" <?php if (strstr($os,"Denies heartburn, diarrhea, constipation")) {
	echo "checked=\"checked\"";	}?>> Denies heartburn, diarrhea, constipation </li>

<li><input type="checkbox" class="cbox" name="objective[]" value="NABS. Soft, without tenderness, masses or hernia." <?php if (strstr($os,"NABS. Soft, without tenderness, masses or hernia.")) {
	echo "checked=\"checked\"";	}?>> NABS. Soft, without tenderness, masses or hernia. </li>
</ul>
<strong>LUNGS:</strong>
<ul>
<li><input type="checkbox" class="cbox" name="objective[]" value="Clear to auscultation bilaterally" <?php if (strstr($os,"Clear to auscultation bilaterally. No rales, wheezing or rhonchi.")) {
	echo "checked=\"checked\"";	}?>> Clear to auscultation bilaterally </li>
 
<strong>GU:</strong>

<li><input type="checkbox" class="cbox" name="objective[]" value="Denies polyuria, incontinence, nocturia, UTI’s" <?php if (strstr($os,"Denies polyuria, incontinence, nocturia, UTI’s ")) {
	echo "checked=\"checked\"";	}?>> Denies polyuria, incontinence, nocturia, UTI’s </li>
</ul>  


  
  </div>
  <div class="col-xs-12">
  <h5>Comments</h5>
  <textarea class="form-control" name="objectivenotes">
  <?php $row_rsPatient['objectivenotes']; ?>
  
  </textarea>
  </div>
  </div>
  
  </div>
  
  <?php
$ass = ($row_rsPatient['assessment']);

?>
  <div class="tab-pane" id="settings"><h3>Assessment</h3>
  <div class="row">
  <div class="col-xs-12">
  <ul>
  <li><input type="checkbox" class="cbox" name="assessment[]" value="Underweight" <?php if (strstr($ass,"Underweight")) {
	echo "checked=\"checked\"";	}?>> Underweight </li>
<li><input type="checkbox" class="cbox" name="assessment[]" value="Normal weight" <?php if (strstr($ass,"Normal weight")) {
	echo "checked=\"checked\"";	}?>> Normal weight </li>
<li><input type="checkbox" class="cbox" name="assessment[]" value="Overweight" <?php if (strstr($ass,"Overweight")) {
	echo "checked=\"checked\"";	}?>> Overweight </li>
<li><input type="checkbox" class="cbox" name="assessment[]" value="Obese" <?php if (strstr($ass,"Obese")) {
	echo "checked=\"checked\"";	}?>> Obese </li>
<li><input type="checkbox" class="cbox" name="assessment[]" value="Morbidly obese" <?php if (strstr($ass,"Morbidly obese")) {
	echo "checked=\"checked\"";	}?>> Morbidly obese </li>
<li><input type="checkbox" class="cbox" name="assessment[]" value="Good eating habits" <?php if (strstr($ass,"Good eating habits")) {
	echo "checked=\"checked\"";	}?>> Good eating habits </li>
<li><input type="checkbox" class="cbox" name="assessment[]" value="Poor eating habits" <?php if (strstr($ass,"Poor eating habits")) {
	echo "checked=\"checked\"";	}?>> Poor eating habits </li>
<li><input type="checkbox" class="cbox" name="assessment[]" value="Exercises regularly" <?php if (strstr($ass,"Exercises regularly")) {
	echo "checked=\"checked\"";	}?>> Exercises regularly </li>
<li><input type="checkbox" class="cbox" name="assessment[]" value="Needs to increase activity level" <?php if (strstr($ass,"Needs to increase activity level")) {
	echo "checked=\"checked\"";	}?>> Needs to increase activity level </li>
 </ul> 
    </div>
  <div class="col-xs-12">
  <h5>Comments</h5>
  <textarea class="form-control" name="assessmentnotes">
  
  
  </textarea>
  </div>
  </div>
  </div>
  <div class="tab-pane" id="plan">
  <h3>Plan</h3>
  <?php
$ps = ($row_rsPatient['plan']);

?>
   <div class="row">
  <div class="col-xs-12">
   <ul>
  <li><input type="checkbox" class="cbox" name="plan[]" value="Medication packages, supplements" <?php if (strstr($ps,"Exercises regularly")) {
	echo "checked=\"checked\"";	}?>> Medication packages, supplements </li>
<li><input type="checkbox" class="cbox" name="plan[]" value="Discussed medications, times to take, side effects & need to take medication break after 12 week Rx." <?php if (strstr($ps,"Discussed medications, times to take, side effects & need to take medication break after 12 week Rx.")) {
	echo "checked=\"checked\"";	}?>>  Discussed medications, times to take, side effects & need to take medication break after 12 week Rx.
 </li>
<li><input type="checkbox" class="cbox" name="plan[]" value="Choose lean meats, eat fish at least twice a week, select fat-free, reduced fat products, cut back on beverages and foods with added sugars, choose/prepare foods with little or no salt.
Exercise 3 to 5 days a week as tolerated." <?php if (strstr($ps,"Choose lean meats, eat fish at least twice a week, select fat-free, reduced fat products, cut back on beverages and foods with added sugars, choose/prepare foods with little or no salt.
Exercise 3 to 5 days a week as tolerated.")) {
	echo "checked=\"checked\"";	}?>> Choose lean meats, eat fish at least twice a week, select fat-free, reduced fat products, cut back on beverages and foods with added sugars, choose/prepare foods with little or no salt.
Exercise 3 to 5 days a week as tolerated. </li>  
</ul>
  </div>
  
  <div class="col-xs-12">
  <h5>Comments</h5>
  <textarea class="form-control" name="plannotes">
  <?php $row_rsPatient['plannotes']; ?>
  
  </textarea>
  
  
  </div>
  
  
  </div>
 
  
  
</div>
</div>
      <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      <input type="hidden" name="MM_update" value="soapupdate">
      <input type="submit" class="btn btn-primary" name="submit" value="Submit">
      </div>
     
      
  </form>
    </div>
  </div>
</div>
</div>

<!-- modal for password -->
<div class="modal fade" id="passwordModal" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title"><span class="glyphicon glyphicon glyphicon-lock"></span> Assign Password</h4>
      </div>
   

      <div class="modal-body">
        <form method="post" name="passwordupdate" id="passwordForm" action="<?php echo $editFormAction; ?>">
          <table align="center">
            <tr valign="baseline">
              
              <td>Password:<br><input type="password" name="password" id="password" class="validate[required] form-control" value="" size="32"></td>
            </tr>
            <tr valign="baseline">
              
              <td><br>Confirm Password:<br><input type="password" name="password2" id="password2" value="" class="validate[required,equals[password]] form-control"></td>
            </tr>
          </table>
        
          <input type="hidden" name="MM_update" value="passwordupdate">
        
        <p>&nbsp;</p>
      </div>
      <div class="modal-footer">
      <input type="hidden" name="patientID" value="<?php echo $row_rsPatient['patientID']; ?>">
         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="Set Password">
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<!-- Modal -->
<div class="modal fade bs-example-modal-lg"  id="historyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Medical History</h4>
      </div>
      <div class="modal-body medhistory">
        <form name="form1"  method="post" name="historyupdate" action="<?php echo $editFormAction; ?>" >
        <div class="row">
          <div class="col-xs-12 ">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th colspan="2">Referrals</th>
              </thead>
              <tr>
                <td>How did you hear about us?</td>
                <td><input type="text" class="form-control" name="howdidyouhear" value=""></td>
              </tr>
              <tr>
                <td>Did someone refer you to us? </td>
                <td><input type="radio" name="refer" value="yes"  >
                  Yes
                  <input type="radio" name="refer" value="no" >
                  No </td>
              </tr>
              <tr>
                <td>If yes, please list their name?</td>
                <td><input type="text" name="refername" class="form-control" value=""></td>
              </tr>
              <tr>
                <td>Is this person currently a patient?</td>
                <td><input type="radio" name="curpatient" value="yes" >
                  Yes
                  <input type="radio" name="curpatient" value="no" >
                  No</td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th colspan="2">Insurance Information</th>
              </thead>
              <tr>
                <td>Insurance</td>
                <td><input type="radio" name="insurance" value="yes" >
                  Yes
                  <input type="radio" name="insurance" value="no" >
                  No </td>
              </tr>
              <tr>
                <td>Insurance Company</td>
                <td><input type="text" class="form-control" name="insuranceco" value=""></td>
              </tr>
              <tr>
                <td>Co-Pay</td>
                <td><input type="text" class="form-control" name="copay" value=""></td>
              </tr>
              <tr>
                <td>Policy Number</td>
                <td><input type="text" class="form-control" name="policyno" value=""></td>
              </tr>
              <tr>
                <td>Group Name</td>
                <td><input type="text" class="form-control" name="groupno" value=""></td>
              </tr>
              <tr>
                <td>Subscriber Name</td>
                <td><input type="text" class="form-control" name="subname" value=""></td>
              </tr>
              <tr>
                <td>Relation to Subscriber</td>
                <td><input type="text" class="form-control" name="subrel" value=""></td>
              </tr>
            </table>
          </div>
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th colspan="2">Emergency Contact</th>
              </thead>
              <tr>
                <td>Emergency Contact</td>
                <td><input type="text" class="form-control" name="emergencycontact" value="RULA ALIEH"></td>
              </tr>
              <tr>
                <td>Emergency Contact Phone</td>
                <td><input type="text" class="form-control" name="emergencycontactphone" value="(708)203-7301"></td>
              </tr>
              <tr>
                <td>Relationship</td>
                <td><input type="text" class="form-control" name="relationship" value="SISTER"></td>
              </tr>
            </table>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Medication</th>
                  <th>Yes</th>
                  <th>No</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Do you take medication for anxiety?</td>
                  <td><input type="radio" name="anxiety" value="yes"  ></td>
                  <td><input type="radio" name="anxiety" value="no" ></td>
                </tr>
                <tr>
                  <td>Do you take Coumadin?</td>
                  <td><input type="radio" name="coumadin" value="yes"  ></td>
                  <td><input type="radio" name="coumadin" value="no" ></td>
                </tr>
                <tr>
                  <td>Do you take prednisone or steroids</td>
                  <td><input type="radio" name="steroids" value="yes" ></td>
                  <td><input type="radio" name="steroids" value="no" ></td>
                </tr>
                <tr>
                  <td>Do you take antidepressants?</td>
                  <td><input type="radio" name="antidepressants" value="yes"  ></td>
                  <td><input type="radio" name="antidepressants" value="no" ></td>
                </tr>
                <tr>
                  <td>Do you take herbs, roots, medicinal tea?</td>
                  <td><input type="radio" name="herbs" value="yes"  ></td>
                  <td><input type="radio" name="herbs" value="no" ></td>
                </tr>
              </tbody>
            </table>
            Please list your other medical problems (such as diabetes, high blood pressure, etc):
            <textarea class="form-control" name="otherproblems"></textarea>
            <br>
            <br>
            List all operations/surgeries you have had in the past and any complications you had:
            <textarea class="form-control" name="othersurgeries"></textarea>
          </div>
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Name of Medication</th>
                  <th>Dose</th>
                  <th>How many times per day</th>
              </thead>
              <tr>
                <td width="55%"><input type="textbox" name="med1" class="form-control" value=""></td>
                <td width="10%"><input type="textbox" name="med1dose" class="form-control" value=""></td>
                <td><input type="radio" name="med1times" value="1"  >
                  <label> 1 </label>
                  <input type="radio" name="med1times" value="2"  >
                  <label> 2 </label>
                  <input type="radio" name="med1times" value="3" >
                  <label> 3 </label>
                  <input type="radio" name="med1times" value="4" >
                  <label> 4 </label></td>
              </tr>
              <tr>
                <td><input type="textbox" name="med2" class="form-control" value=""></td>
                <td><input type="textbox" name="med2dose" class="form-control" value=""></td>
                <td><input type="radio" name="med2times" value="1" >
                  <label>1</label>
                  <input type="radio" name="med2times" value="2" >
                  <label> 2</label>
                  <input type="radio" name="med2times" value="3" >
                  <label> 3</label>
                  <input type="radio" name="med2times" value="4" >
                  <label> 4</label></td>
              </tr>
              <tr>
                <td><input type="textbox" name="med3" class="form-control" value=""></td>
                <td><input type="textbox" name="med3dose" class="form-control" value=""></td>
                <td><input type="radio" name="med3times" value="1" >
                  <label>1</label>
                  <input type="radio" name="med3times" value="2" >
                  <label> 2</label>
                  <input type="radio" name="med3times" value="3" >
                  <label> 3</label>
                  <input type="radio" name="med3times" value="4" >
                  <label> 4</label></td>
              </tr>
              <tr>
                <td><input type="textbox" name="med4" class="form-control" value=""></td>
                <td><input type="textbox" name="med4dose" class="form-control" value=""></td>
                <td><input type="radio" name="med4times" value="1" >
                  <label>1</label>
                  <input type="radio" name="med4times" value="2" >
                  <label> 2</label>
                  <input type="radio" name="med4times" value="3" >
                  <label> 3</label>
                  <input type="radio" name="med4times" value="4" >
                  <label> 4</label></td>
              </tr>
              <tr>
                <td><input type="textbox" name="med5" class="form-control" value=""></td>
                <td><input type="textbox" name="med5dose" class="form-control" value=""></td>
                <td><input type="radio" name="med5times" value="1" >
                  <label>1</label>
                  <input type="radio" name="med5times" value="2" >
                  <label> 2</label>
                  <input type="radio" name="med5times" value="3" >
                  <label> 3</label>
                  <input type="radio" name="med5times" value="4" >
                  <label> 4</label></td>
              </tr>
            </table>
          </div>
        </div>
        <!-- end row -->
        <hr>
        <div class="row">
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Family History</th>
                  <th>Mother</th>
                  <th>Father</th>
                  <th>Other</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Cancer</td>
                  <td><input type="checkbox" name="familycancer" value="mother" ></td>
                  <td><input type="checkbox" name="familycancer" value="father" ></td>
                  <td><input type="checkbox" name="familycancer" value="other" ></td>
                </tr>
                <tr>
                  <td>Diabetes</td>
                  <td><input type="checkbox" name="familydiabetes" value="mother"></td>
                  <td><input type="checkbox" name="familydiabetes" value="father" ></td>
                  <td><input type="checkbox" name="familydiabetes" value="other" ></td>
                </tr>
                <tr>
                  <td>Heart Disease</td>
                  <td><input type="checkbox" name="heartdisease" value="mother" ></td>
                  <td><input type="checkbox" name="heartdisease" value="father"  ></td>
                  <td><input type="checkbox" name="heartdisease" value="other" ></td>
                </tr>
                <tr>
                  <td>Stroke</td>
                  <td><input type="checkbox" name="stroke" value="mother"  ></td>
                  <td><input type="checkbox" name="stroke" value="father" ></td>
                  <td><input type="checkbox" name="stroke" value="other"  ></td>
                </tr>
                <tr>
                  <td>Other</td>
                  <td><input type="checkbox" name="other" value="mother" ></td>
                  <td><input type="checkbox" name="other" value="father" ></td>
                  <td><input type="checkbox" name="other" value="other" ></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="col-xs-6">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th colspan="5">Social History</th>
              </thead>
              <tbody>
                <tr>
                  <td>Marital Status</td>
                  <td><input type="checkbox" name="maritalstatus" value="single" >
                    Single</td>
                  <td><input type="checkbox" name="maritalstatus" value="married" >
                    Married</td>
                  <td><input type="checkbox" name="maritalstatus" value="divorced" >
                    Divorced </td>
                  <td><input type="checkbox" name="maritalstatus" value="widow" >
                    Widow</td>
                </tr>
                <tr>
                  <td>Drink Alcohol?</td>
                  <td><input type="radio" name="alcohol" value="yes" >
                    Yes </td>
                  <td><input type="radio" name="alcohol" value="no" >
                    No</td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td>Smoke?</td>
                  <td><input type="radio" name="smoke" value="yes" >
                    Yes </td>
                  <td><input type="radio" name="smoke" value="no" >
                    No </td>
                  <td></td>
                  <td></td>
                </tr>
                <tr>
                  <td>Recreational Drugs?</td>
                  <td><input type="radio" name="drugs" value="yes" >
                    Yes</td>
                  <td><input type="radio" name="drugs" value="no" >
                    No</td>
                  <td></td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <!-- end row -->
        <div class="row">
          <div class="col-xs-2">
            <h5>Neuro</h5>
            <ul>
              <li>
                <input type="checkbox" name="convulsions" value="yes" >
                Convulsions</li>
              <li>
                <input type="checkbox" name="migranes" value="yes" >
                Migrane Headaches</li>
              <li>
                <input type="checkbox" name="strokes" value="yes" >
                Strokes</li>
              <li>
                <input type="checkbox" name="paralysis" value="yes" >
                Paralysis</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Cardiac</h5>
            <ul>
              <li>
                <input type="checkbox" name="chestpain" value="yes" >
                Chest Pain</li>
              <li>
                <input type="checkbox" name="palpations" value="yes" >
                Palpatations</li>
              <li>
                <input type="checkbox" name="highbloodpressure" value="yes" >
                High Blood Pressure</li>
              <li>
                <input type="checkbox" name="heartfailure" value="yes" >
                Heart Failure</li>
              <li>
                <input type="checkbox" name="heartattack" value="yes" >
                Heart Attack</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Pulmonary</h5>
            <ul>
              <li>
                <input type="checkbox" name="chroniccough" value="yes" >
                Chronic Cough</li>
              <li>
                <input type="checkbox" name="sleepapnea" value="yes" >
                Sleep Apnea / Snoring</li>
              <li>
                <input type="checkbox" name="asthma" value="yes" >
                Asthma</li>
              <li>
                <input type="checkbox" name="colds" value="yes" >
                Recent Colds and Pneumonia</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Renal</h5>
            <ul>
              <li>
                <input type="checkbox" name="bloodinurine" value="yes" >
                Blood in urine</li>
              <li>
                <input type="checkbox" name="bladder" value="yes" >
                Frequent Bladder Infections</li>
              <li>
                <input type="checkbox" name="kidney" value="yes" >
                Kidney Infections/Disorders</li>
              <li>
                <input type="checkbox" name="pain" value="yes" >
                Pain when urinating</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Hearing</h5>
            <ul>
              <li>
                <input type="checkbox" name="deafness" value="yes" >
                Deafness</li>
              <li>
                <input type="checkbox" name="ringinginears" value="yes" >
                Ringing in ears</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Skin/Integument</h5>
            <ul>
              <li>
                <input type="checkbox" name="skinrashes" value="yes" >
                Skin Rashes</li>
              <li>
                <input type="checkbox" name="moles" value="yes" >
                Unusual Moles</li>
              <li>
                <input type="checkbox" name="breastlumps" value="yes" >
                Breast Lumps</li>
            </ul>
          </div>
        </div>
        <!-- end row -->
        <div class="row">
          <div class="col-xs-2">
            <h5>Muscular/Skeletal</h5>
            <ul>
              <li>
                <input type="checkbox" name="backpain" value="yes" >
                Back Pain</li>
              <li>
                <input type="checkbox" name="hippain" value="yes" >
                Hip Pain</li>
              <li>
                <input type="checkbox" name="kneepain" value="yes" >
                Knee Pain</li>
              <li>
                <input type="checkbox" name="otherjoin" value="yes" >
                Other Joint</li>
              <li>
                <input type="checkbox" name="arthritis" value="yes" >
                Arthritis</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Psychiatric</h5>
            <ul>
              <li>
                <input type="checkbox" name="depression" value="yes" >
                Depression</li>
              <li>
                <input type="checkbox" name="anxiety2" value="yes" >
                Anxiety</li>
              <li>
                <input type="checkbox" name="hallucination" value="yes" >
                Hallucination</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Endocrine</h5>
            <ul>
              <li>
                <input type="checkbox" name="diabetes" value="yes" >
                Diabetes</li>
              <li>
                <input type="checkbox" name="thyroid" value="yes" >
                Thyroid Problems</li>
              <li>
                <input type="checkbox" name="lackofenergy" value="yes" >
                Lack of Energy</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Hematologic</h5>
            <ul>
              <li>
                <input type="checkbox" name="anemia" value="yes" >
                Anemia</li>
              <li>
                <input type="checkbox" name="easybruising" value="yes" >
                Easy Bruising</li>
              <li>
                <input type="checkbox" name="bloodclots" value="yes" >
                Blood Clots in deep veins of arms/legs</li>
              <li>
                <input type="checkbox" name="bloodtransfusions" value="yes" >
                Blood Transfusions</li>
              <li>
                <input type="checkbox" name="hiv" value="yes" >
                HIV/AIDS</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Vision</h5>
            <ul>
              <li>
                <input type="checkbox" name="blindness" value="yes" >
                Blindness</li>
              <li>
                <input type="checkbox" name="doublevision" value="yes" >
                Double Vision</li>
              <li>
                <input type="checkbox" name="contacts" value="yes" >
                Do you wear glasses or contact lenses?</li>
            </ul>
          </div>
          <div class="col-xs-2">
            <h5>Constitutional</h5>
            <ul>
              <li>
                <input type="checkbox" name="fever" value="yes" >
                Fever</li>
              <li>
                <input type="checkbox" name="chills" value="yes" >
                Chills</li>
              <li>
                <input type="checkbox" name="sweats" value="yes" >
                Sweats</li>
              <li>
                <input type="checkbox" name="weightgain" value="yes" >
                Excessive Weight Gain</li>
              <li>
                <input type="checkbox" name="allergies" value="yes"  >
                Allergies</li>
            </ul>
          </div>
        </div>
        <!-- end row -->
        <div class="row">
        <div class="col-xs-3">
          <h5>Gastrointestinal</h5>
          <ul>
            <li>
              <input type="checkbox" name="bloodinstool" value="yes" >
              Blood in Stool</li>
            <li>
              <input type="checkbox" name="vomitblood" value="yes" >
              Vomiting Blood</li>
            <li>
              <input type="checkbox" name="blackstool" value="yes" >
              Black Stools</li>
            <li>
              <input type="checkbox" name="diarrhea" value="yes" >
              Chronic Diarrhea</li>
            <li>
              <input type="checkbox" name="constipation" value="yes" >
              Chronic Constipation</li>
            <li>
              <input type="checkbox" name="bloating" value="yes" >
              Bloating</li>
            <li>
              <input type="checkbox" name="nausea" value="yes" >
              Nausea or Vomiting</li>
            <li>
              <input type="checkbox" name="diffswallowing" value="yes" >
              Difficulty Swallowing</li>
            <li>
              <input type="checkbox" name="painswallowing" value="yes" >
              Pain when swallowing</li>
            <li>
              <input type="checkbox" name="heartburn" value="yes" >
              Chronic Heartburn / Reflux</li>
            <li>
              <input type="checkbox" name="hepatitis" value="yes" >
              Hepatitis</li>
            <li>
              <input type="checkbox" name="ulcers" value="yes" >
              Ulcers</li>
            <li>
              <input type="checkbox" name="pancreatitis" value="yes" >
              Pancreatitis</li>
            <li>
              <input type="checkbox" name="gallstones" value="yes" >
              Gallstones</li>
          </ul>
        </div>
        <div class="row">
          <div class="col-xs-3">
            <h5>Gynecology (Ladies Only)</h5>
            <ul>
              <li>
                <input type="checkbox" name="pregnant" value="yes" >
                Ever been pregnant?</li>
              <li>
                <input type="checkbox" name="children" value="yes" >
                Do you have children?</li>
              <li>
                <input type="checkbox" name="menstrual" value="yes" >
                Any changes in menstrual?</li>
              <li>
                <input type="checkbox" name="menopause" value="yes" >
                Menopause?</li>
              <li>
                <input type="checkbox" name="breastfeed" value="yes" >
                Do you breast feed?</li>
              <br>
              <li>Date of last menstrual period?
                <input type="text" name="lastperiod" class="form-control">
              </li>
            </ul>
          </div>
          <div class="col-xs-3"></div>
          <div class="col-xs-3"></div>
          <div class="col-xs-3"></div>
        </div>
        <!-- end row -->
        <input type="hidden" name="patientID" value="<?php echo $row_rsPatient['patientID']; ?>">
        <input type="hidden" name="MM_update" value="form1">
        <p>&nbsp;</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit"  class="btn btn-success" value="Update record">
        </form>
      </div>
    </div>
    <!-- end medical history -->



	

<?php include('includes/footer.php'); ?>

<?php
mysql_free_result($rsPatient);

mysql_free_result($rsVitals);

mysql_free_result($rsMedication);

mysql_free_result($rsDiagnosis);

mysql_free_result($rsNotes);

mysql_free_result($rsPackage);

mysql_free_result($rs_patientInjection);

mysql_free_result($rsdate);

mysql_free_result($rsinjection);

mysql_free_result($rscount);

mysql_free_result($rsMedicalRecords);

mysql_free_result($rsinject);

mysql_free_result($rsdiagpatient);

mysql_free_result($rsPatientPackage);

mysql_free_result($packagetest);

mysql_free_result($rsPackages);

mysql_free_result($rsSubWeight);
?>
