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

<div id="modalwindow">
<h2>Enter Notes</td></h2>
<form action="<?php echo $editFormAction; ?>" id="noteform" name="addnote" method="POST" >
<table>

<tr><td><textarea name="notes" class="validate[required]" rows="5" cols="40"  /> </textarea></td></tr>
<tr><td><input type="submit" class="buttoncolor" name="submit" value="Add Notes"/></td></tr>
</table>
<input type="hidden" name="patientID" value="<?php echo $row_rsPatient['patientID']; ?>"  />
<input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>" />
<input type="hidden" name="MM_insert" value="addnote" />
<input type="hidden" name="username" value="<?php echo $_SESSION['MM_Username']; ?>" />
<input type="hidden" name="fname" value="<?php echo $row_rsPatient['fname']; ?>"/>
<input type="hidden" name="lname" value="<?php echo $row_rsPatient['lname']; ?>" />
</form>
</div>
<?php
mysql_free_result($rsPatient);
?>
