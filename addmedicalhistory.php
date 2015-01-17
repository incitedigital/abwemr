<?php session_start(); ?>
<?php require_once('Connections/dbc.php'); ?>
<?php require_once('ScriptLibrary/incPureUpload.php'); ?>
<?php 
//*** Pure PHP File Upload 3.0.1
// Process form upload
$ppu = new pureFileUpload();
$ppu->nameConflict = "uniq";
$ppu->storeType = "file";
$uploadfilename = $ppu->files("filename");
$uploadfilename->path = "uploadedfilesforbw";
$ppu->redirectUrl = "";
$ppu->checkVersion("3.0.1");
$ppu->doUpload();
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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "upload")) {
  $insertSQL = sprintf("INSERT INTO tbl_forms (title, filedescription, filelocation, patientID) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['description'], "text"),
                       GetSQLValueString($_POST['filename'], "text"),
                       GetSQLValueString($_POST['patientID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<script type="text/javascript"><?php echo $ppu->generateScriptCode() ?></script>
<script src="ScriptLibrary/incPU3.js" type="text/javascript"></script>
</head>

<body>

<h1>Upload Medical Record </h1>

<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="upload" onsubmit="<?php echo $ppu->getSubmitCode() ?>;return document.MM_returnValue" >
  <?php echo $ppu->getProgressField() ?> File Name <br/>
  <input type="text" name="title" size="30"/>
  <br/>
  <br/>
Description <br/>
<textarea name="description" rows="6" cols ="40"> </textarea>
<br/>
<input name="filename" type="file" onchange="<?php echo $uploadfilename->getValidateCode() ?>;return document.MM_returnValue"  />
<br/>
<br/>
<input type="submit" name="submit" value="Upload" class="buttoncolor" />
<input type="hidden" name="patientID" value="<?php echo $row_rsPatient['patientID']; ?>" />
<input type="hidden" name="MM_insert" value="upload" />
</form>

</body>
</html>
<?php
mysql_free_result($rsPatient);
?>
