<?php session_start(); ?>
<?php
$server = $_SERVER["HTTP_HOST"];
$server = $server."/better";

?>
<?php require_once('Connections/dbc.php'); ?>
<?php $patientID = $_GET['patientID']; ?>
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
$query_rsPatient = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsPatient, "int"));
$rsPatient = mysql_query($query_rsPatient, $dbc) or die(mysql_error());
$row_rsPatient = mysql_fetch_assoc($rsPatient);
$totalRows_rsPatient = mysql_num_rows($rsPatient);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}


	if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form"))
		{
			
			if(isset($_POST['prim'])) 
			{ $strprimary = implode('<br/>', $_POST['prim']);
			}
			else 
			{
				$strprimary = "";
			}
	
			
		}
		
		if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form"))
		{
			
			if(isset($_POST['secondary'])) 
			{ $strsecondary = implode('<br/>', $_POST['secondary']);
			}
			else 
			{
				$strsecondary = "";
			}
	
			
		}


if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form")) {
 
  $insertSQL = sprintf("INSERT INTO tbl_diagnosis_patient (patientID, diagnosisID, username, prim, secondary) VALUES ('$_POST[patientID]', '$_POST[diagnosisID]', '$_SESSION[MM_Username]', '$strprimary', '$strsecondary')",
                       GetSQLValueString($_POST['patientID'], "int"),
                       GetSQLValueString($_POST['diagnosisID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());
  
  $patientquery = "INSERT into tbl_activity (username, action, firstname, lastname,  date,  category, centerID) VALUES ('$_SESSION[MM_Username]', 'added a diagnosis for', '$_POST[fname]', '$_POST[lname]', CURDATE(), 'diagnosis', '$_SESSION[centerID]' )";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());



  $insertGoTo = "viewpatient.php?patientID=$patientID";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="ScriptLibrary/jquery-latest.pack.js"></script>
<script type="text/javascript" src="ScriptLibrary/jquery.autocomplete.js"></script>

<script type="text/javascript" src="ScriptLibrary/jquery.bgiframe.min.js"></script>
<link rel="stylesheet" type="text/css" href="ScriptLibrary/autocomplete.css" />
 <link type="text/css" href="http://<?php echo $server; ?>/css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />


</head>

<body>

<div id="windowframe">
<h1>Add Diagnosis</h1>

<form method="POST" action="<?php echo $editFormAction; ?>" name="form">
 
<script type='text/javascript'>
jQuery(document).ready(
function() {
jQuery('#autocomplete1').autocomplete('autocomplete-diagnosis-php-1.php',
{
	opacity : .7,
	delay : 100,
	autoFill : true,
	minChars : 1,
	idField : 'diagnosisID',
	hiddenIdField : 'diagnosisID',
	fxShow : { type:'slide' },
	fxHide : { type:'slide' }

})});
</script>


<h3>PRIMARY DIAGNOSIS</h3>
<table width="100%" cellpadding="5" cellspacing="5">
<tr><td><input type="checkbox" name="prim[]" value="Overweight Obesity 278.00"> Overweight/Obesity 278.00</td><td><input type="checkbox" name="prim[]" value="Hypothyroidism 244.9"> Hypothyroidism 244.9</td></tr>
<tr><td><input type="checkbox" name="prim[]" value="Morbid Obesity 278.01"> Morbid Obesity 278.01</td><td><input type="checkbox" name="prim[]" value="Sleep Apnea 780.57"> Sleep Apnea 780.57</td></tr>
<tr><td><input type="checkbox" name="prim[]" value="Eating Disorder 307.50"> Eating Disorder 307.50</td><td><input type="checkbox" name="prim[]" value="Hypertension 401.9">Hypertension 401.9</td></tr>
<tr><td><input type="checkbox" name="prim[]" value="Obesity of Endocrine 259.9"> Obesity of Endocrine 259.9</td><td><input type="checkbox" name="prim[]" value="Hyperlipidemia 272.4">Hyperlipidemia 272.4</td></tr>
<tr><td><input type="checkbox" name="prim[]" value="Dysmetabolic Syndrome  77.7"> Dysmetabolic Syndrome 277.7</td><td><input type="checkbox" name="prim[]" value="Hypercholestermia 272.00"> Hypercholestermia 272.00</td></tr>
<tr><td><input type="checkbox" name="prim[]" value="Fatigue  708.79"> Fatigue 708.79</td><td><input type="checkbox" name="prim[]" value="Family HX Hyperlipid V18.1"> Family HX Hyperlipid V18.1</td></tr>
</table>

<h3>SECONDARY DIAGNOSIS / SYMPTOMS</h3>
<table width="100%" cellpadding="5" cellspacing="5">
<tr><td><input type="checkbox" name="secondary[]" value="Diabeties/Controlled 250.0"> Diabeties/Controlled 250.0</td><td><input type="checkbox" name="secondary[]" value="Weight Gain Abnormal 783.1"> Weight Gain - Abnormal 783.1</td></tr>
<tr><td><input type="checkbox" name="secondary[]" value="Diabeties/Uncontrolled 250.02"> Diabeties/Uncontrolled 250.02</td><td><input type="checkbox" name="secondary[]" value="Excessive Appetite 783.6"> Excessive Appetite 783.6</td></tr>
<tr><td><input type="checkbox" name="secondary[]" value="Depression 311.0"> Depression 311.0</td><td><input type="checkbox" name="secondary[]" value="Overeating D T Stress 308.3">Overeating D/T Stress 308.3</td></tr>
<tr><td><input type="checkbox" name="secondary[]" value="Gallstones 574.30"> Gallstones 574.30</td><td><input type="checkbox" name="secondary[]" value="Fatigue 780.79">Fatigue 780.79</td></tr>
<tr><td><input type="checkbox" name="secondary[]" value="Arthritis 715.9"> Arthritis 715.9</td><td><input type="checkbox" name="secondary[]" value="Headache 784.0"> Headache 784.0</td></tr>
<tr><td><input type="checkbox" name="secondary[]" value="Polyphagia 783.6"> Polyphagia 783.6</td><td></td></tr>
</table>


Other: <input name="autocomplete1" id="autocomplete1" type="text" />

<input type="hidden" name="patientID" value="<?php echo $patientID; ?>" />
<input type="hidden" name="fname" value="<?php echo $row_rsPatient['fname']; ?>" />
<input type="hidden" name="lname" value="<?php echo $row_rsPatient['lname']; ?>" />
<input type="hidden" name="diagnosisID" value=""/>
<input type="hidden" name="MM_insert" value="form" />
<br/><br/>
<input type="submit" name="add" value="Add Diagnosis" class="buttoncolor" />

</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsPatient);
?>
