<?php require_once('../../Connections/dbc.php'); ?>
<?php require_once('../../ScriptLibrary/incPureUpload.php'); ?>
<?php 
//*** Pure PHP File Upload 3.0.1
// Process form greenregister
$ppu = new pureFileUpload();
$ppu->nameConflict = "over";
$ppu->storeType = "file";
$uploadphoto = $ppu->files("photo");
$uploadphoto->path = "../uploadedfilesforbw";
$uploadphoto->allowedExtensions = "GIF,JPG,JPEG,BMP,PNG"; // "images"
$ppu->redirectUrl = "";
$ppu->checkVersion("3.0.1");
$ppu->doUpload();
?>
<?php require_once('../../ScriptLibrary/cGraphicMediator.php'); ?>
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

// *** Redirect if username exists
$MM_flag="MM_insert";
if (isset($_POST[$MM_flag])) {
  $MM_dupKeyRedirect="alreadyregistered.php";
  $loginUsername = $_POST['email'];
  $LoginRS__query = sprintf("SELECT email FROM tbl_patient WHERE email=%s", GetSQLValueString($loginUsername, "text"));
  mysql_select_db($database_dbc, $dbc);
  $LoginRS=mysql_query($LoginRS__query, $dbc) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);

  //if there is a row in the database, the username was found - can not add the requested username
  if($loginFoundUser){
    $MM_qsChar = "?";
    //append the username to the redirect page
    if (substr_count($MM_dupKeyRedirect,"?") >=1) $MM_qsChar = "&";
    $MM_dupKeyRedirect = $MM_dupKeyRedirect . $MM_qsChar ."requsername=".$loginUsername;
    header ("Location: $MM_dupKeyRedirect");
    exit;
  }
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "greenregister")) {
$dob = date("Y-m-d", strtotime( $_POST["dob"]));
  $insertSQL = sprintf("INSERT INTO tbl_patient (patientID, fname, middleinitial, lname, address1, address2, city, `state`, zip, email, homephone, mobilephone, sex, insurance, insuranceco, occupation, emergencycontact, emergencycontactaddress, emergencycontactphone, relationship, photo, dob) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$dob')",
                       GetSQLValueString($_POST['patientID'], "int"),
                       GetSQLValueString($_POST['fname'], "text"),
                       GetSQLValueString($_POST['middleinitial'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['address1'], "text"),
                       GetSQLValueString($_POST['address2'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['homephone'], "text"),
                       GetSQLValueString($_POST['mobilephone'], "text"),
                       GetSQLValueString($_POST['sex'], "text"),
                       GetSQLValueString($_POST['insurance'], "text"),
                       GetSQLValueString($_POST['insuranceco'], "text"),
                       GetSQLValueString($_POST['occupation'], "text"),
                       GetSQLValueString($_POST['emergencycontact'], "text"),
                       GetSQLValueString($_POST['emergencycontactaddress'], "text"),
                       GetSQLValueString($_POST['emergencycontactphone'], "text"),
                       GetSQLValueString($_POST['relationship'], "text"),
                       GetSQLValueString($_POST['photo'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

   $insertGoTo = "coupon.php?patientID=" . $_POST['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dbc, $dbc);
$query_rsUsers = "SELECT * FROM tbl_patient";
$rsUsers = mysql_query($query_rsUsers, $dbc) or die(mysql_error());
$row_rsUsers = mysql_fetch_assoc($rsUsers);
$totalRows_rsUsers = mysql_num_rows($rsUsers);
?>

<?php
// Smart Image Processor PHP 2.1.1
if (isset($_GET['GP_upload'])) {
$sipp2 = new cGraphicMediator("upload", $ppu, "photo");
$sipp2->setComponent("Auto");
$sipp2->setMatteColor("#FFFFFF");
$sipp2->resizeEx(150, 150, true, false);
$sipp2->overwrite = true;
$sipp2->saveJPEG(80);
$sipp2->process();
}
?>
<!doctype html>
<html>
<head>
<title>Better Weigh Medical Upload</title>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" href="../../css/validationEngine.jquery.css" type="text/css"/>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script><script src="../../js/jquery.validate.js" type="text/javascript"></script>
<script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
<script src="../../js/languages/jquery.validationEngine-en.js" type="text/javascript"></script>

<script>
		jQuery(document).ready(function(){
			// binds form submission and fields to the validation engine
			jQuery("#greenregister").validationEngine();

		});


	</script>
<script type="text/javascript"><?php echo $ppu->generateScriptCode() ?></script>
<script src="../../ScriptLibrary/incPU3.js" type="text/javascript"></script>
</head>
<body>
<div id="masthead">
<img src="images/header.jpg" width="830" height="241">
</div>	
<div id="wrapper">


<div id="right_col">

  <form action="<?php echo $GP_uploadAction; ?>" method="post" enctype="multipart/form-data" name="greenregister" id="greenregister" onSubmit="<?php echo $ppu->getSubmitCode() ?>;return document.MM_returnValue">
    <?php echo $ppu->getProgressField() ?>
    <fieldset  name="Personal">
      <table id="registrationtable">
        <tr>
          <td colspan="6"><span class="formheader">Personal Information</span></td>
        </tr>
        <tr>
          <td><label for= "fname" class="label">First Name:<span class="required">*</span></label>
            <input type="text" id="fname" name="fname" class="textbox validate[required]"></td>
          <td><label for= "middleinitial" class="label">Middle Initial:</label>
            <input type="text" id="middleinitial" name="middleinitial" class="shorttextbox" ></td>
          <td><label for= "lname" class="label">Last Name:<span class="required">*</span></label>
            <input type="text" id="lname" name="lname" class="textbox validate[required]"></td>
        </tr>
        <tr>
          <td><label for = "address1" class="label">Address: <span class="required">*</span></label>
            <input type="text" name="address1" id="address1" class="textbox"></td>
          <td><label for ="address2" class="label">Address 2:</label>
            <input type="text" name="address2" id="address2" class="textbox"></td>
          <td><label for ="dob" class="label">D.O.B:<span class="required">*</span></label>
            <input type="text" name="dob" class="textbox validate[required]" placeholder="mm/dd/yyyy"></td>
        </tr>
        <tr>
          <td><label for ="city" class="label">City:<span class="required">*</span></label>
            <input type="text" name="city" id="city" class="shorttextbox validate[required]"></td>
          <td><label for ="state" class="label">State:<span class="required">*</span></label>
            <select name="state">
              <option value="select">Select</option>
              <option value="AL">AL</option>
              <option value="AK">AK</option>
              <option value="AZ">AZ</option>
              <option value="AR">AR</option>
              <option value="CA">CA</option>
              <option value="CO">CO</option>
              <option value="CT">CT</option>
              <option value="DE">DE</option>
              <option value="DC">DC</option>
              <option value="FL">FL</option>
              <option value="GA">GA</option>
              <option value="HI">HI</option>
              <option value="ID">ID</option>
              <option value="IL">IL</option>
              <option value="IN">IN</option>
              <option value="IA">IA</option>
              <option value="KS">KS</option>
              <option value="KY">KY</option>
              <option value="LA">LA</option>
              <option value="ME">ME</option>
              <option value="MD">MD</option>
              <option value="MA">MA</option>
              <option value="MI">MI</option>
              <option value="MN">MN</option>
              <option value="MS">MS</option>
              <option value="MO">MO</option>
              <option value="MT">MT</option>
              <option value="NE">NE</option>
              <option value="NV">NV</option>
              <option value="NH">NH</option>
              <option value="NJ">NJ</option>
              <option value="NM">NM</option>
              <option value="NY">NY</option>
              <option value="NC">NC</option>
              <option value="ND">ND</option>
              <option value="OH">OH</option>
              <option value="OK">OK</option>
              <option value="OR">OR</option>
              <option value="PA">PA</option>
              <option value="RI">RI</option>
              <option value="SC">SC</option>
              <option value="SD">SD</option>
              <option value="TN">TN</option>
              <option value="TX">TX</option>
              <option value="UT">UT</option>
              <option value="VT">VT</option>
              <option value="VA">VA</option>
              <option value="WA">WA</option>
              <option value="WV">WV</option>
              <option value="WI">WI</option>
              <option value="WY">WY</option>
            </select></td>
          <td><label for="zip" class="label">Zip:<span class="required">*</span></label>
            <input type="text" id="zip" name="zip" class="shorttextbox validate[required]"></td>
        </tr>
        <tr>
          <td><label for="homephone" class="label">Home Phone:<span class="required">*</span></label>
            <input type="text" id="homephone" name="homephone" class="shorttextbox validate[required]" placeholder="xxx.xxx.xxxx"></td>
          <td><label for="mobilephone" class="label">Mobile Phone:</label>
            <input type="text" id="mobilephone" name="mobilephone" class="shorttextbox" placeholder="xxx.xxx.xxxx"></td>
          <td><label for="email" class="label">Email:*</label>
            <input type="text" id="email" name="email" class="shorttextbox validate[required,custom[email]]" ></td>
        </tr>
        <tr>
          <td><label for="sex" class="label">Sex:<span class="required">*</span></label>
            Male:
            <input type="radio" class="sex validate[required]" name="sex" value="male ">
            Female:
            <input type="radio" class="sex validate[required]" name="sex" value="female"></td>
          <td><label for="occupation" class="label">Occupation:</label>
            <input type="text" id="occupation" name="occupation" class="textbox"></td>
          <td><label for="Upload Photo" class="label">Upload Photo:
            <input name="photo" type="file" onChange="<?php echo $uploadphoto->getValidateCode() ?>;return document.MM_returnValue">
          </label></td>
        </tr>
        <tr>
          <td colspan="6"><span class="formheader">Emergency Contact Information</span></td>
        </tr>
        <tr>
          <td><label for="emergencycontact" class="label">Emergency Contact Name:</label>
            <input type="text" id="emergencycontact" name="emergencycontact" class="textbox" ></td>
          <td><label for="relationship" class="label">Relationship:</label>
            <input type="text" id="relationship" name="relationship" class="textbox" ></td>
        </tr>
        <tr>
          <td><label for="emergency" class="label">Emergency Contact Address:</label>
            <input type="text" id="emergencycontactaddress" name="emergencycontactaddress" class="textbox" ></td>
          <td><label for="email" class="label">Emergency Contact Phone:</label>
            <input type="text" id="emergencycontactphone" name="emergencycontactphone" class="textbox" ></td>
        </tr>
        <td colspan="6"><span class="formheader">Insurance Information</span></td>
        </tr>
        <tr>
          <td><label for="insurance" class="label">Insurance:</label>
            Yes
            <input type="radio" id="insurance" name="insurance" value="Yes" class="validate[required]"> 
            No
            <input type="radio" name="insurance" value="No" class="validate[required]"></td>
          <td><label for="insuranceco" class="label">Insurance Company:</label>
            <select name="insuranceco" id="insuranceco">
              <option value="">Select</option>
              <option value="Blue Cross/ Blue Shield">Blue Cross / Blue Shield</option>
              <option value="other">Other</option>
            </select></td>
        </tr>
      </table>
      <input type="hidden" name="patientID" value="<?php echo rand(); ?>"/>
      <input type="image" name="submit" src="images/submit.jpg">
      <input type="hidden" name="MM_insert" value="registration">
    </fieldset>
    <input type="hidden" name="MM_insert" value="greenregister">
  </form>
</div>
</div><!-- End Wrapper -->

<div class="clear"></div>

</body>
<div class="footercontainer">
<div class="footerleft">&copy; Copyright 2013 A Better Weigh, Inc. All rights reserved.</div> <div class="footerright">Designed & Powered by Nac Media Group</div>
</div>
<?php
mysql_free_result($rsUsers);
?>
