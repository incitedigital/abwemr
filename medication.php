<?php session_start(); ?>
<?php
$server = $_SERVER["HTTP_HOST"];
$server = $server."/better";

?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form")) {
  $insertSQL = sprintf("INSERT INTO tbl_patientMed (medID, patientID) VALUES (%s, %s)",
                       GetSQLValueString($_POST['medID'], "int"),
                       GetSQLValueString($_POST['patientID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "viewpatient.php?patientID=$patientID";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dbc, $dbc);
$query_rsMedication = "SELECT * FROM tbl_medication";
$rsMedication = mysql_query($query_rsMedication, $dbc) or die(mysql_error());
$row_rsMedication = mysql_fetch_assoc($rsMedication);
$totalRows_rsMedication = mysql_num_rows($rsMedication);

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
<h1>Add Medication</h1>

<form method="POST" action="<?php echo $editFormAction; ?>" name="form">




<input name="autocomplete1" id="autocomplete1" type="text" /><script type='text/javascript'>
jQuery(document).ready(
function() {
jQuery('#autocomplete1').autocomplete('autocomplete-medication-php-1.php',
{
	opacity : .7,
	delay : 100,
	autoFill : true,
	minChars : 1,
	searchAll : true,
	idField : 'medID',
	hiddenIdField : 'medID',
	fxShow : { type:'slide' },
	fxHide : { type:'slide' }

})});
</script>
<input type="hidden" name="patientID" value="<?php echo $patientID; ?>" />
<input type="hidden" name="medID" />
<input type="submit" name="add" value="Add" class="buttoncolor" />
<input type="hidden" name="MM_insert" value="form" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rsMedication);
?>
