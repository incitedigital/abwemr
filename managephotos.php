<?php require_once('Connections/dbc.php'); ?>
<?php require_once('ScriptLibrary/incPureUpload.php'); ?>
<?php require_once('ScriptLibrary/cGraphicMediator.php'); ?>
<?php 
//*** Pure PHP File Upload 3.0.1
// Process form uploadphoto
$ppu = new pureFileUpload();
$ppu->nameConflict = "uniq";
$ppu->storeType = "file";
$uploadphoto = $ppu->files("photo");
$uploadphoto->path = "uploadedfilesforbw";
$uploadphoto->allowedExtensions = "GIF,JPG,JPEG,BMP,PNG"; // "images"
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
$colname_rsPhotos = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPhotos = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPhotos = sprintf("SELECT patientID, photo FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsPhotos, "int"));
$rsPhotos = mysql_query($query_rsPhotos, $dbc) or die(mysql_error());
$row_rsPhotos = mysql_fetch_assoc($rsPhotos);
$totalRows_rsPhotos = mysql_num_rows($rsPhotos);
?>
<?php
// Smart Image Processor PHP 2.1.0
$sipp2 = new cGraphicMediator("folder", "uploadedfilesforbw" );
$sipp2->setComponent("Auto");
$sipp2->setMatteColor("#FFFFFF");
$sipp2->resizeEx(175, 175, true, false);
$sipp2->overwrite = true;
$sipp2->saveJPEG(80);
$sipp2->process();
?>
<?php
$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "uploadphoto")) {
  $updateSQL = sprintf("UPDATE tbl_patient SET photo=%s WHERE patientID=%s",
                       GetSQLValueString($_POST['photo'], "text"),
                       GetSQLValueString($_POST['patientID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "viewpatient.php?patientID=" . $row_rsPhotos['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
?>

<!DOCTYPE html>
<html>
<head>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link rel="stylesheet" href="http://<?php echo $server; ?>/css/validationEngine.jquery.css" type="text/css"/>
<script type="text/javascript" src="http://<?php echo $server; ?>/js/jquery-ui-1.8.21.custom.min.js"></script>
<script src="http://<?php echo $server; ?>/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="http://<?php echo $server; ?>/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<script type="text/javascript"><?php echo $ppu->generateScriptCode() ?></script>
<script src="ScriptLibrary/incPU3.js" type="text/javascript"></script>
</head>

<body>

<h2>Upload Photo</h2>

<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="uploadphoto" onSubmit="<?php echo $ppu->getSubmitCode() ?>;return document.MM_returnValue">
  <?php echo $ppu->getProgressField() ?>
  <input name="photo" type="file" onChange="<?php echo $uploadphoto->getValidateCode() ?>;return document.MM_returnValue"  />
  <input type="hidden" name="patientID" value="<?php echo $row_rsPhotos['patientID']; ?>" /><br><br>
  <input type="submit" name="Upload Photo" class="buttoncolor" value="Upload Photo" />
  <input type="hidden" name="MM_update" value="uploadphoto" />
</form>


</body>
</html>
<?php
mysql_free_result($rsPhotos);
?>
