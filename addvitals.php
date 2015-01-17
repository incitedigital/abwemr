<?php session_start(); ?>
<?php require_once('Connections/dbc.php'); ?>
<?php $patientID= $_GET['patientID']; ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "addvitals")) {
$systolic = $_POST['systolic'];
$diastolic = $_POST['diastolic'];
$waist = $_POST['waist'];
$hip = $_POST['hip'];
$asian = $_POST['asian'];

  $insertSQL = sprintf("INSERT INTO tbl_vitals (patientID, `date`, heightft, heightin, weight, pulse,  bmi, username, systolic, diastolic) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, $systolic, $diastolic)",
                       GetSQLValueString($_POST['patientID'], "int"),
                       GetSQLValueString($_POST['date'], "date"),
                       GetSQLValueString($_POST['heightft'], "int"),
                       GetSQLValueString($_POST['heightin'], "int"),
                       GetSQLValueString($_POST['weight'], "text"),
                       GetSQLValueString($_POST['pulse'], "text"),
                     
                       GetSQLValueString($_POST['bmi'], "text"),
                       GetSQLValueString($_POST['username'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());
  
  $patientquery = "INSERT into tbl_activity (username, action, firstname, lastname,  date,  category, centerID) VALUES ('$_SESSION[MM_Username]', 'added vitals for', '$_POST[fname]', '$_POST[lname]', CURDATE(), 'vitals', '$_SESSION[centerID]' )";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());

  $insertGoTo = "viewpatient.php?patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsPatient = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPatient = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPatient = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsPatient, "int"));
$rsPatient = mysql_query($query_rsPatient, $dbc) or die(mysql_error());
$row_rsPatient = mysql_fetch_assoc($rsPatient);
$totalRows_rsPatient = mysql_num_rows($rsPatient);

$colname_rsVitals = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsVitals = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsVitals = sprintf("SELECT * FROM tbl_vitals WHERE patientID = %s", GetSQLValueString($colname_rsVitals, "int"));
$rsVitals = mysql_query($query_rsVitals, $dbc) or die(mysql_error());
$row_rsVitals = mysql_fetch_assoc($rsVitals);
$totalRows_rsVitals = mysql_num_rows($rsVitals);
 
 ?>
<link rel="stylesheet" type="text/css" href="css/validationEngine.jquery.css"> 
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script language="javascript" src="js/jquery.validationEngine.js"></script>
<script language="javascript" src="js/langes/jquery.validationEngine-en.js"></script>
<div id="modalwindow">

<h4>Add Vitals for <?php echo $row_rsPatient['fname']; ?> <?php echo $row_rsPatient['lname']; ?></h4>
<form method="POST" action="<?php echo $editFormAction; ?>" id="addvitalform" name="addvitals">
<table>

<tr><td>Weight</td><td><input type="text" name="weight" id="weight" class="validate[required,custom[number]] text-input" /> lbs</td></tr>
<tr><td>Pulse</td><td><input type="text" name="pulse" class="text ui-widget-content ui-corner-all validate[required,custom[number]]" /> </td></tr>
<tr><td>Systolic Pressure</td><td><input type="text" name="systolic" class="validate[required,custom[number]]" /> </td></tr>
<tr><td>Diastolic Pressure</td><td><input type="text" name="diastolic" class="validate[required,custom[number]]" /> </td></tr>
<!--<tr><td>Is the patient of Asian decent?</td><td><input type="checkbox" name="asian" id="asian" class="" value="yes" /></td></tr>-->
<tr>
  <td>Height (ft):</td>
  <td><input type="text" name="heightft" value="<?php echo $row_rsVitals['heightft']; ?>" class="validate[required,custom[number]] text-input"/></td></tr>
<tr>
  <td>Height (in.):</td>
  <td><input type="text" name="heightin" value="<?php echo $row_rsVitals['heightin']; ?>"  class="validate[required,custom[number]] text-input" /></td></tr>
<input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>" />
<input type="hidden" name="bmi" value="<?php echo round( ($row_rsVitals['weight']/ ((($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) * (($row_rsVitals['heightft'] * 12) + $row_rsVitals['heightin']) )* 703), 2);  ?>" />

<!--<tr><td>Waist Circumference (in.)</td><td><input type="text" name="waist" id="waistcircumference" class="validate[required,custom[number]] text-input" /> in </td></tr>
<tr><td>Hip Circumference (in.)</td><td><input type="text" name="hip" id="hipcircumference" class="validate[required,custom[number]] text-input" /> in </td></tr>-->
<tr><td></td><td><input type="submit" name="submit" class="buttoncolor" value="Add Vitals" /></td></tr>
<input type="hidden" name="patientID" value="<?php echo $row_rsPatient['patientID']; ?>" />
<input type="hidden" name="username" value="<?php echo $_SESSION['MM_Username']; ?>" />


</table>
<input type="hidden" name="fname" value="<?php echo $row_rsPatient['fname']; ?>"/>
<input type="hidden" name="lname" value="<?php echo $row_rsPatient['lname']; ?>" />
<input type="hidden" name="MM_insert" value="addvitals" />
</form>


<script language="javascript">
 $('#addvitalform').validationEngine();
</script>
</div>

<?php
mysql_free_result($rsPatient);

mysql_free_result($rsVitals);
?>
