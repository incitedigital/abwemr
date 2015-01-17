<?php require_once('Connections/dbc.php'); ?>
<?php require_once('ScriptLibrary/incPureUpload.php'); ?>
<?php 
//*** Pure PHP File Upload 3.0.1
// Process form registration
$ppu = new pureFileUpload();
$ppu->timeOut = 60;
$ppu->nameConflict = "uniq";
$ppu->storeType = "file";
$ppu->path = "uploadedfilesforbw";
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "registration")) {
	$dob = date("Y-m-d", strtotime( $_POST["dob"]));
  	
  $insertSQL = sprintf("INSERT INTO tbl_patient (patientID, fname,middleinitial, lname, address1, address2, city, `state`, zip, email, homephone, mobilephone, sex, insurance, insuranceco, copay, occupation, emergencycontact, emergencycontactaddress, emergencycontactphone, relationship, dob) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$dob')",
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
                       GetSQLValueString($_POST['copay'], "int"),
                       GetSQLValueString($_POST['occupation'], "text"),
                       GetSQLValueString($_POST['emergencycontact'], "text"),
                       GetSQLValueString($_POST['emergencycontactaddress'], "text"),
                       GetSQLValueString($_POST['emergencycontactphone'], "text"),
                       GetSQLValueString($_POST['relationship'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());
  $patientID = $_POST['patientID'];
  $insertGoTo = "viewpatient.php?patientID=$patientID";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<?php require_once('includes/header.php'); ?>
<script type="text/javascript"><?php echo $ppu->generateScriptCode() ?></script>
<script src="ScriptLibrary/incPU3.js" type="text/javascript"></script>

<h2>Add New Patient</h2>
  <form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="registration" id="registration" onSubmit="<?php echo $ppu->getSubmitCode() ?>;return document.MM_returnValue">
   
    <fieldset  name="Personal">
      <table id="registrationtable" class="table">
        <tr>
          <td colspan="6"><span class="formheader">Personal Information</span></td>
        </tr>
        <tr>
          <td><label for= "fname" class="label">First Name:<span class="required">*</span></label>
          <input type="text" id="fname" name="fname" class="form-control validate[required]"></td>
       		<td><label for= "middleinitial" class="label">Middle Initial:</label>
       		<input type="text" id="middleinitial" name="middleinitial" class="form-control" ></td>
          <td><label for= "lname" class="label">Last Name:<span class="required">*</span></label>
         <input type="text" id="lname" name="lname" class="form-control validate[required]"></td>
        </tr>
               <tr>
          <td><label for = "address1" class="label">Address: <span class="required">*</span></label>
         <input type="text" name="address1" id="address1" class="form-control"></td>
        
          <td><label for ="address2" class="label">Address 2:</label>
          <input type="text" name="address2" id="address2" class="form-control"></td>
           <td><label for ="dob" class="label">D.O.B:<span class="required">*</span></label>
          <input type="text" name="dob" class="form-control validate[required]" placeholder="mm/dd/yyyy"></td>
        </tr>
        <tr>
          <td><label for ="city" class="label">City:<span class="required">*</span></label>
          <input type="text" name="city" id="city" class="form-control validate[required]"></td>
      
          <td><label for ="state" class="label">State:<span class="required">*</span></label>
         <select name="state" class="form-control">
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
         <input type="text" id="zip" name="zip" class="form-control validate[required]"></td>
        </tr>
        <tr>
          <td><label for="homephone" class="label">Home Phone:<span class="required">*</span></label>
         <input type="text" id="homephone" name="homephone" class="form-control validate[required]" placeholder="xxx.xxx.xxxx">
            </td>
      
          <td><label for="mobilephone" class="label">Mobile Phone:</label>
         <input type="text" id="mobilephone" name="mobilephone" class="form-control" placeholder="xxx.xxx.xxxx">
           </td>
      
          <td><label for="email" class="label">Email:</label>
          <input type="text" id="email" name="email" class="form-control validate[custom[email]]" ></td>
        </tr>
        <tr>
         <td><label for="sex" class="label">Sex:<span class="required">*</span></label>
         Male: <input type="radio" class="sex validate[required]" name="sex" value="male ">
          Female: <input type="radio" class="sex validate[required]" name="sex" value="female">
         </td>
          <td><label for="occupation" class="label">Occupation:</label>
          <input type="text" id="occupation" name="occupation" class="form-control"></td>
       </tr>
         
       
      
        <tr>
          <td colspan="6"><span class="formheader">Emergency Contact Information</span></td>
        </tr>
        <tr>
          <td><label for="emergencycontact" class="label">Emergency Contact Name:</label><input type="text" id="emergencycontact" name="emergencycontact" class="form-control" ></td>
        
         <td><label for="relationship" class="label">Relationship:</label><input type="text" id="relationship" name="relationship" class="form-control" ></td>
        </tr>
        <tr>
          <td><label for="emergency" class="label">Emergency Contact Address:</label><input type="text" id="emergencycontactaddress" name="emergencycontactaddress" class="form-control" ></td>
      
          <td><label for="email" class="label">Emergency Contact Phone:</label><input type="text" id="emergencycontactphone" name="emergencycontactphone" class="form-control" ></td>
        </tr>
       
          <td colspan="6"><span class="formheader">Insurance Information</span></td>
        </tr>
        <tr>
          <td><label for="insurance" class="label">Insurance:</label> Yes
            <input type="radio" id="insurance" name="insurance" value="Yes">
            No
            <input type="radio" name="insurance" value="No"></td>
      
          <td><label for="insuranceco" class="label">Insurance Company:</label><select name="insuranceco" id="insuranceco" class="form-control">
            <option value="">Select</option>
            <option value="Blue Cross/ Blue Shield">Blue Cross / Blue Shield</option>
            <option value="other">Other</option>
          </select></td>
    
          <td><label for="copay" class="label">Co-Pay Amount:</label>            <input type="text" id="copay" name="copay" class="form-control validate[custom[number]" placeholder="ex.50.00">
            </td>
        </tr>
      </table>
    </fieldset>

    <input type="hidden" name="patientID" value="<?php echo rand(); ?>"/>
    <input type="submit" name="submit" value="Add Patient" class="buttoncolor">
    <input type="hidden" name="MM_insert" value="registration">
  </form>

<div class="clear"></div>


<?php require_once('includes/footer.php'); ?>
</div>
    

</body>
</html>