<?php session_start(); ?>
<?php require_once('Connections/dbc.php'); ?>
<?php require_once('ScriptLibrary/incPureUpload.php'); ?>
<?php require_once('ScriptLibrary/cGraphicMediator.php'); ?>
<?php 
//*** Pure PHP File Upload 3.0.1
// Process form form1
$ppu = new pureFileUpload();
$ppu->nameConflict = "uniq";
$ppu->storeType = "file";
$uploadphoto = $ppu->files("photo");
$uploadphoto->path = "../employeeportal/thumbnail/";
$uploadphoto->allowedExtensions = "GIF,JPG,JPEG,BMP,PNG"; // "images"
$ppu->redirectUrl = "";
$ppu->checkVersion("3.0.1");
$ppu->doUpload();
?>
<?php require_once('ScriptLibrary/incPUAddOn.php'); ?>
<?php
// Smart Image Processor PHP 2.1.1
{
$sipp2 = new cGraphicMediator("upload", $ppu, "photo");
$sipp2->setComponent("Auto");
$sipp2->setMatteColor("#FFFFFF");
$sipp2->resizeEx(150, 150, true, false);
$sipp2->overwrite = false;
$sipp2->setMask("##path##thumb_##name##.##ext##");
$sipp2->save();
$sipp2->process();
}
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

// Rename Uploaded Files Addon 1.0.10
if (isset($_GET['GP_upload'])) {
  $ruf = new renameUploadedFiles($ppu);
  $ruf->renameMask = "myFile_##name##.##ext##";
  $ruf->checkVersion("1.0.10");
  $ruf->doRename();
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

$colname_rsEmployee = "-1";
if (isset($_GET['adminID'])) {
  $colname_rsEmployee = $_GET['adminID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsEmployee = sprintf("SELECT * FROM tbl_admin WHERE adminID = %s", GetSQLValueString($colname_rsEmployee, "int"));
$rsEmployee = mysql_query($query_rsEmployee, $dbc) or die(mysql_error());
$row_rsEmployee = mysql_fetch_assoc($rsEmployee);
$totalRows_rsEmployee = mysql_num_rows($rsEmployee);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $hiredate = date('Y-m-d', strtotime($_POST['hiredate']));	
  if((isset($_POST['photo'])) && ($_POST['photo'] != "")) {$photo = $_POST['photo'];} else {$photo = $row_rsEmployee['photo'];}
  $address = $_POST['address'];
  $city = $_POST['city'];
  $state = $_POST['state'];
  $zip= $_POST['zip'];
  $phone = $_POST['phone'];
  $emergencycontact = $_POST['emergencycontact'];
  $emergencycontactphone = $_POST['emergencycontactphone'];
  $updateSQL = sprintf("UPDATE tbl_admin SET username=%s, password=%s, firstname=%s, lname=%s, accesslevel=%s, jobtitle=%s, employmentstatus=%s, hiredate='$hiredate', photo='$photo', address='$address', city='$city', state='$state', zip = '$zip', phone='$phone', emergencycontact='$emergencycontact', emergencycontactphone = '$emergencycontactphone' WHERE adminID=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['accesslevel'], "int"),
                       GetSQLValueString($_POST['jobtitle'], "text"),
                       GetSQLValueString($_POST['employmentstatus'], "text"),
                       GetSQLValueString($_POST['adminID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "employees.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


?>
<?php include('includes/headeradmin.php'); ?>
<script type="text/javascript"><?php echo $ppu->generateScriptCode() ?></script>
<script src="ScriptLibrary/incPU3.js" type="text/javascript"></script>
<div class="container">
<div class="row">
<div class="col-md-3"><h1>Edit Employee</h1></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>

<div class="row">

<div class="col-md-12">
<div class="panel panel-default">
 
  <div class="panel-body">
    <form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1" onSubmit="<?php echo $ppu->getSubmitCode() ?>;return document.MM_returnValue">
      <?php echo $ppu->getProgressField() ?>
      <table align="center" class="table">
        <tr valign="baseline">
          <td nowrap align="right">Username:</td>
          <td><input type="text" name="username" value="<?php echo htmlentities($row_rsEmployee['username'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">Password:</td>
          <td><input type="text" name="password" value="<?php echo htmlentities($row_rsEmployee['password'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">First Name:</td>
          <td><input type="text" name="firstname" value="<?php echo htmlentities($row_rsEmployee['firstname'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">Last Name:</td>
          <td><input type="text" name="lname" value="<?php echo htmlentities($row_rsEmployee['lname'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
          <tr valign="baseline">
          <td nowrap align="right">Address:</td>
          <td><input type="text" name="address" value="<?php echo htmlentities($row_rsEmployee['address'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
          <tr valign="baseline">
          <td nowrap align="right">City:</td>
          <td><input type="text" name="city" value="<?php echo htmlentities($row_rsEmployee['city'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
          <tr valign="baseline">
          <td nowrap align="right">State:</td>
          <td><input type="text" name="state" value="<?php echo htmlentities($row_rsEmployee['state'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
          <tr valign="baseline">
          <td nowrap align="right">Zip:</td>
          <td><input type="text" name="zip" value="<?php echo htmlentities($row_rsEmployee['zip'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
          <tr valign="baseline">
          <td nowrap align="right">Phone:</td>
          <td><input type="text" name="phone" value="<?php echo htmlentities($row_rsEmployee['phone'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
          <tr valign="baseline">
          <td nowrap align="right">Emergency Contact:</td>
          <td><input type="text" name="emergencycontact" value="<?php echo htmlentities($row_rsEmployee['emergencycontact'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">Emergency Contact Phone:</td>
          <td><input type="text" name="emergencycontactphone" value="<?php echo htmlentities($row_rsEmployee['emergencycontactphone'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">Photo:</td>
          <td><input name="photo" type="file" value="<?php echo htmlentities($row_rsEmployee['photo'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">Access level:</td>
          <td><select name="accesslevel">
            <option value="3" <?php if (!(strcmp(3, htmlentities($row_rsEmployee['accesslevel'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Adminstrator</option>
            <option value="2" <?php if (!(strcmp(2, htmlentities($row_rsEmployee['accesslevel'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>General User</option>
          </select></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">Job Title:</td>
          <td><input type="text" name="jobtitle" value="<?php echo htmlentities($row_rsEmployee['jobtitle'], ENT_COMPAT, ''); ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">Employment Status:</td>
          <td><select name="employmentstatus">
            <option value="Full-Time" <?php if (!(strcmp("Full-Time", htmlentities($row_rsEmployee['employmentstatus'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Full-Time</option>
            <option value="Part-Time" <?php if (!(strcmp("Part-Time", htmlentities($row_rsEmployee['employmentstatus'], ENT_COMPAT, '')))) {echo "SELECTED";} ?>>Part-Time</option>
          </select></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">Hire Date:</td>
          <td><input type="text" name="hiredate" id="hiredate" value="<?php echo date('m/d/Y', strtotime(htmlentities($row_rsEmployee['hiredate'], ENT_COMPAT, ''))); ?>" size="32"></td>
        </tr>
        <tr valign="baseline">
          <td nowrap align="right">&nbsp;</td>
          <td><input type="submit" class="btn btn-info" value="Update record"></td>
        </tr>
      </table>
      <input type="hidden" name="MM_update" value="form1">
      <input type="hidden" name="adminID" value="<?php echo $row_rsEmployee['adminID']; ?>">
    </form>
    <p>&nbsp;</p>
  </div>
  
</div>
</div>
</div>


<script language="javascript">

$('#hiredate').datepicker();


</script>






</div> <!-- end container -->

<?php include('includes/footer.php'); ?>

<?php
mysql_free_result($rsEmployee);

?>
