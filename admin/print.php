<?php session_start(); 
?>
<?php require_once('../../Connections/dbc.php'); ?>
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

$MM_restrictGoTo = "../loginfailed.php";
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
<?php require_once('../ScriptLibrary/dmxPaginator.php'); ?>
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

  $insertGoTo = "viewpatient.php?patientID=" . $row_rsPatient['patientID'] . "";
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

  $insertGoTo = "viewpatient.php?patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
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
$query_rsVitals = sprintf("SELECT tbl_vitals.vitalID, tbl_vitals.patientID,  tbl_vitals.date, tbl_vitals.heightft, tbl_vitals.heightin, tbl_vitals.weight, tbl_vitals.bmi, tbl_vitals.pulse, tbl_vitals.bloodpressure,tbl_vitals.username, tbl_admin.firstname FROM tbl_vitals JOIN tbl_admin on tbl_vitals.username = tbl_admin.username WHERE patientID = %s ORDER BY Date DESC", GetSQLValueString($colname_rsVitals, "int"));
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
$query_rsNotes = sprintf("SELECT patientID, noteID, notes, date, tbl_notes.username, firstname FROM tbl_notes JOIN tbl_admin on tbl_notes.username = tbl_admin.username WHERE patientID = %s", GetSQLValueString($colname_rsNotes, "int"));
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
?>
<?php include('../includes/header.php'); ?>


<div id="wrapper">



<div id="right_colbig">

<div id="top_container">
  
 <div id="patient_head">
 <ul>
 <li><h1><?php echo $row_rsPatient['fname']; ?> <?php echo $row_rsPatient['lname']; ?></h1> </li>
<li> <?php echo $row_rsPatient['address1']; ?>  <?php echo $row_rsPatient['address2']; ?> </li>
 <li><?php echo $row_rsPatient['city']; ?>, <?php echo $row_rsPatient['state']; ?> <?php echo $row_rsPatient['zip']; ?> </li>
 <li>Home: <?php echo $row_rsPatient['homephone']; ?> </li>
 <li> Mobile: <?php echo $row_rsPatient['mobilephone']; ?> </li>
<li> Email: <?php echo $row_rsPatient['email']; ?> </li>
 <li>D.O.B.: <?php echo date('m/d/Y', strtotime( $row_rsPatient['dob'])); ?></li>
<li><a href="../editprofile.php?patientID=<?php echo $row_rsPatient['patientID']; ?>" rel="facebox">Edit Profile</a></li>
</ul>
 </div>
  
 <div id="currentvitals">
 <ul>

  <li><h2>Current Vitals</h2></li>
   <?php if ($totalRows_rsVitals == 0) { // Show if recordset empty ?>
   <li>No Vitals Entered</li>
   <?php } // Show if recordset empty ?>
 <?php if ($totalRows_rsVitals > 0) { // Show if recordset not empty ?>
  
  <li>Height: <?php echo $row_rsVitals['heightft']; ?>' <?php echo $row_rsVitals['heightin']; ?>"</li>
   <li>Weight: <?php echo $row_rsVitals['weight']; ?></li>
   <li>P: <?php echo $row_rsVitals['pulse']; ?></li>
   <li>BP:<?php echo $row_rsVitals['bloodpressure']; ?></li>
   <li>BMI:<?php echo round( ($row_rsVitals['weight']/ ((($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) * (($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) )* 703), 2);  ?></li>
   <?php } // Show if recordset not empty ?>
   </ul>
</div> 
  
  



</div><!-- End Top Container -->
<div class="clear"></div>
<br/>

<h3><a href="#">Vitals</a></h3>
	<div>
		<?php if ($totalRows_rsVitals == 0) { // Show if recordset empty ?>
  No Records Found
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsVitals > 0) { // Show if recordset not empty ?>
<table width="100%" class="brick">
  <tr>
    
    <th><strong>Date</strong></th>
    <th><strong>Weight</strong></th>
    <th><strong>Pulse</strong></th>
    <th><strong>Blood Pressure</strong></th>
    <th><strong>BMI</strong></th>
    <th><strong>Administrator</strong></th>
    <th><strong>Delete</strong></th>

  </tr>
  <?php do { ?>
    <tr>
     
     
      <td width="100px"><?php echo date('m/d/Y', strtotime( $row_rsVitals['date'])); ?></td>
      <td><?php echo $row_rsVitals['weight']; ?></td>
      <td><?php echo $row_rsVitals['pulse']; ?></td>
      <td><?php echo $row_rsVitals['bloodpressure']; ?></td>
      <td><?php echo round( ($row_rsVitals['weight']/ ((($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) * (($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) )* 703), 2);  ?></td>
      <td/><?php echo $row_rsVitals['firstname']; ?></td>
      <td/><strong><a href="../deletevitals.php?patientID=<?php echo $row_rsPatient['patientID']; ?>&amp;vitalID=<?php echo $row_rsVitals['vitalID']; ?>">X</a></strong>
      </td>
    </tr>
    <?php } while ($row_rsVitals = mysql_fetch_assoc($rsVitals)); ?>
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
    
    <br/>

	
	<h3><a href="#">Diagnosis</a></h3>
	<div>
		<ol>
  <?php if ($totalRows_rsDiagnosis == 0) { // Show if recordset empty ?>
     
        <li>No Diagnosis Assigned</li>
    
      <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsDiagnosis > 0) { // Show if recordset not empty ?>
  <?php do { ?>
    <li><?php echo $row_rsDiagnosis['diagnosisName']; ?> -  <a href="../deletediagnosis.php?patientdiagID=<?php echo $row_rsDiagnosis['patientdiagID']; ?>&amp;patientID=<?php echo $row_rsPatient['patientID']; ?>"></a></li>
    <?php } while ($row_rsDiagnosis = mysql_fetch_assoc($rsDiagnosis)); ?>
  <?php } // Show if recordset not empty ?>
  </ol>
	</div>
	<br/>
	
    
    <h3><a href="#">Notes</a></h3>
	<div>
		  
    <?php if ($totalRows_rsNotes == 0) { // Show if recordset empty ?>
      No records found
      <?php } // Show if recordset empty ?>
  </p>
  <?php if ($totalRows_rsNotes > 0) { // Show if recordset not empty ?>
  <table width="100%" border="0" cellpadding="5" cellspacing="5" class="brick">
  <tr><th width="5%"><strong>Date</strong></th><th width="60%"><strong>Notes</strong></th><th><strong>Administrator</strong></th><th colspan="2"><strong>Actions</strong></th></tr>
 
    <?php do { ?>
      <tr>
   
        <td>
        <strong><?php echo date('m/d/Y',strtotime( $row_rsNotes['date'])); ?></strong>
        </td>
        <td>
		<?php echo $row_rsNotes['notes']; ?></td>
        <td><?php echo $row_rsNotes['firstname']; ?></td>
        <td><a href="../editnotes.php?noteID=<?php echo $row_rsNotes['noteID']; ?>&amp;patientID=<?php echo $row_rsPatient['patientID']; ?>" rel="facebox">Edit</a></td>
        <td><a href="../deletehistory.php?noteID=<?php echo $row_rsNotes['noteID']; ?>&amp;patientID=<?php echo $row_rsNotes['patientID']; ?>">   <img src="../images/delete-item.gif" width="32" height="32" /></a></td>
      </tr>
      <?php } while ($row_rsNotes = mysql_fetch_assoc($rsNotes)); ?>
  </table>
    <?php } // Show if recordset not empty ?>
	</div>
    <br/>
    
       <h3><a href="#">Current Medication</a></h3>
	<div>
		 <ol>
  <?php if ($totalRows_rsMedication == 0) { // Show if recordset empty ?>
     
        <li>No Medication Assigned</li>
    
      <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsMedication > 0) { // Show if recordset not empty ?>
  <?php do { ?>
    <li><?php echo $row_rsMedication['medicationName']; ?> - <a href="../deletemedication.php?patientMedID=<?php echo $row_rsMedication['patientMedID']; ?>&amp;patientID=<?php echo $row_rsPatient['patientID']; ?>"></a></li>
    <?php } while ($row_rsMedication = mysql_fetch_assoc($rsMedication)); ?>
  <?php } // Show if recordset not empty ?>
  </ol>
	</div>
    
    <h3><a href="#">Medical Records</a></h3>
	<div>
		 <ol>
  <?php if ($totalRows_rsMedicalRecords == 0) { // Show if recordset empty ?>
     
        <li>No Medical Records Assigned</li>
    
      <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsMedicalRecords > 0) { // Show if recordset not empty ?>
  <?php do { ?>
    <li><a href="uploadedfilesforbw/<?php echo $row_rsMedicalRecords['filelocation']; ?>" target="_blank"><?php echo $row_rsMedicalRecords['title']; ?> </a><br/> <?php echo $row_rsMedicalRecords['filedescription']; ?> - <a href="../deletemedicalrecords.php?formID=<?php echo $row_rsMedicalRecords['formID']; ?>&amp;patientID=<?php echo $row_rsPatient['patientID']; ?>"></a></li>
    <?php } while ($row_rsMedicalRecords = mysql_fetch_assoc($rsMedicalRecords)); ?>
  <?php } // Show if recordset not empty ?>
  </ol>
	</div>
    
    
</div>
<div class="clear"></div>
<h3 class="newbar">Current Packages</h3>
	<div>
	
<?php
$patientID = $_GET['patientID']; 
mysql_select_db($database_dbc, $dbc);
$query = "SELECT packagename, package_ID, patientPackageID, date, status, patient_ID FROM tbl_patientpackage JOIN tbl_package on tbl_patientpackage.package_ID = tbl_package.packageId WHERE status = 'open' or status = 'active' and patient_ID = '$patientID'";
$result = mysql_query($query) or die(mysql_error());


while ($row = mysql_fetch_assoc($result)){
echo "<div class=\"injectiontitle\">$row[packagename] -  ". date('m/d/Y', strtotime($row[date])) ." <a href=\"updateinjection.php?patientpackageID=$row[patientPackageID]&amp;patientID=$patientID\" class=\"injectionlink\" > <img src=\"images/10-medical.png\" width=\"12\" height=\"12\" align=\"absbottom\"/>Add Injection </a> | <a href=\"closepackage.php?patientpackageID=$row[patientPackageID]&amp;patientID=$patientID\">Close Package </a></div> <div class=\"clear\"></div> ";
	{

		$query2 = "SELECT tbl_injection.patient_ID, patientpackageID, date FROM tbl_injection WHERE patientpackageID = '$row[patientPackageID]' AND tbl_injection.patient_ID = '$patientID' ";
		$result2 = mysql_query($query2) or die(mysql_error());
		while ($row2 = mysql_fetch_assoc($result2))
				{
					echo "<div class=\"administer\">Administered   " . date('m/d/Y', strtotime($row2[date])) . "</div>";	
					
			
				}
			echo "<div class=\"clear\"></div>";
	}
	

}





?>

	</div>




<div class="clear"></div>

    


</div>







<?php include('../includes/footer.php'); ?>

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




?>
