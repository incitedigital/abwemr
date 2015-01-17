<?php require_once('../ScriptLibrary/incPureUpload.php'); ?>
<?php 
//*** Pure PHP File Upload 3.1.0
// Process form form1
$ppu = new pureFileUpload();
$ppu->nameConflict = "uniq";
$ppu->storeType = "file";
$uploadmedicalhistory = $ppu->files("medicalhistory");
$uploadmedicalhistory->path = "filesup";
$ppu->redirectUrl = "";
$ppu->checkVersion("3.1.0");
$ppu->doUpload();
if ($ppu->done) {
  $_POST["undefined"] = undefined;
  $_POST["undefined"] = undefined;
}
?>
<?php require_once('Connections/dbc.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$patientID = $_POST['patientID'];
	$ssn = $_POST['ssn'];
	$digital = $_POST['digitalsig'];
  $insertSQL = sprintf("INSERT INTO tbl_patient (fname, middleinitial, lname, address1, address2, city, `state`, zip, email, homephone, mobilephone, dob, sex, username, insurance, insuranceco, copay, occupation, emergencycontact, emergencycontactaddress, emergencycontactphone, relationship, medicalhistory, photo, heightft, heightin, weight, howdidyouhear, policyno, groupno, subname, subrel, otherproblems, othersurgeries, refer, refername, curpatient, anxiety, anxietyoften, coumadin, steroids, antidepressants, herbs, med1, med1dose, med1times, med2, med2dose, med2times, med3, med3dose, med3times, password, med4, med4dose, med4times, med5, med5dose, med5times, maritialstatus, alcohol, smoke, drugs, familycancer, familydiabetes, heartdisease, stroke, other, convulsions, migranes, strokes, paralysis, chestpain, palpations, highbloodpressure, heartfailure, heartattack, chroniccough, sleepapnea, asthma, colds, bloodinurine, bladder, kidney, pain, bloodinstool, vomitblood, blackstool, diarrhea, constipation, bloating, nausea, diffswallowing, painswallowing, heartburn, hepatitis, ulcers, pancreatitis, gallstones, diabetes, thyroid, lackofenergy, anemia, easybruising, bloodclots, bloodtransfusions, hiv, blindness, doublevision, contacts, deafness, ringingears, skinrashes, moles, breastlumps, backpain, hippain, kneepain, otherjoint, arthritis, depression, anxiety2, hallucination, fever, chills, sweats, weightgain, pregnant, children, menstrual, menopause, lastperiod, breastfeed, allergies, patientID, ssn, digitalsig) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$patientID', '$ssn', '$digital')",
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
                       GetSQLValueString($_POST['dob'], "date"),
                       GetSQLValueString($_POST['sex'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['insurance'], "text"),
                       GetSQLValueString($_POST['insuranceco'], "text"),
                       GetSQLValueString($_POST['copay'], "int"),
                       GetSQLValueString($_POST['occupation'], "text"),
                       GetSQLValueString($_POST['emergencycontact'], "text"),
                       GetSQLValueString($_POST['emergencycontactaddress'], "text"),
                       GetSQLValueString($_POST['emergencycontactphone'], "text"),
                       GetSQLValueString($_POST['relationship'], "text"),
                       GetSQLValueString($_POST['medicalhistory'], "text"),
                       GetSQLValueString($_POST['photo'], "text"),
                       GetSQLValueString($_POST['heightft'], "text"),
                       GetSQLValueString($_POST['heightin'], "text"),
                       GetSQLValueString($_POST['weight'], "text"),
                       GetSQLValueString($_POST['howdidyouhear'], "text"),
                       GetSQLValueString($_POST['policyno'], "text"),
                       GetSQLValueString($_POST['groupno'], "text"),
                       GetSQLValueString($_POST['subname'], "text"),
                       GetSQLValueString($_POST['subrel'], "text"),
                       GetSQLValueString($_POST['otherproblems'], "text"),
                       GetSQLValueString($_POST['othersurgeries'], "text"),
                       GetSQLValueString($_POST['refer'], "text"),
                       GetSQLValueString($_POST['refername'], "text"),
                       GetSQLValueString($_POST['curpatient'], "text"),
                       GetSQLValueString($_POST['anxiety'], "text"),
                       GetSQLValueString($_POST['anxietyoften'], "text"),
                       GetSQLValueString($_POST['coumadin'], "text"),
                       GetSQLValueString($_POST['steroids'], "text"),
                       GetSQLValueString($_POST['antidepressants'], "text"),
                       GetSQLValueString($_POST['herbs'], "text"),
                       GetSQLValueString($_POST['med1'], "text"),
                       GetSQLValueString($_POST['med1dose'], "text"),
                       GetSQLValueString($_POST['med1times'], "text"),
                       GetSQLValueString($_POST['med2'], "text"),
                       GetSQLValueString($_POST['med2dose'], "text"),
                       GetSQLValueString($_POST['med2times'], "text"),
                       GetSQLValueString($_POST['med3'], "text"),
                       GetSQLValueString($_POST['med3dose'], "text"),
                       GetSQLValueString($_POST['med3times'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['med4'], "text"),
                       GetSQLValueString($_POST['med4dose'], "text"),
                       GetSQLValueString($_POST['med4times'], "text"),
                       GetSQLValueString($_POST['med5'], "text"),
                       GetSQLValueString($_POST['med5dose'], "text"),
                       GetSQLValueString($_POST['med5times'], "text"),
                       GetSQLValueString($_POST['maritialstatus'], "text"),
                       GetSQLValueString($_POST['alcohol'], "text"),
                       GetSQLValueString($_POST['smoke'], "text"),
                       GetSQLValueString($_POST['drugs'], "text"),
                       GetSQLValueString($_POST['familycancer'], "text"),
                       GetSQLValueString($_POST['familydiabetes'], "text"),
                       GetSQLValueString($_POST['heartdisease'], "text"),
                       GetSQLValueString($_POST['stroke'], "text"),
                       GetSQLValueString($_POST['other'], "text"),
                       GetSQLValueString($_POST['convulsions'], "text"),
                       GetSQLValueString($_POST['migranes'], "text"),
                       GetSQLValueString($_POST['strokes'], "text"),
                       GetSQLValueString($_POST['paralysis'], "text"),
                       GetSQLValueString($_POST['chestpain'], "text"),
                       GetSQLValueString($_POST['palpations'], "text"),
                       GetSQLValueString($_POST['highbloodpressure'], "text"),
                       GetSQLValueString($_POST['heartfailure'], "text"),
                       GetSQLValueString($_POST['heartattack'], "text"),
                       GetSQLValueString($_POST['chroniccough'], "text"),
                       GetSQLValueString($_POST['sleepapnea'], "text"),
                       GetSQLValueString($_POST['asthma'], "text"),
                       GetSQLValueString($_POST['colds'], "text"),
                       GetSQLValueString($_POST['bloodinurine'], "text"),
                       GetSQLValueString($_POST['bladder'], "text"),
                       GetSQLValueString($_POST['kidney'], "text"),
                       GetSQLValueString($_POST['pain'], "text"),
                       GetSQLValueString($_POST['bloodinstool'], "text"),
                       GetSQLValueString($_POST['vomitblood'], "text"),
                       GetSQLValueString($_POST['blackstool'], "text"),
                       GetSQLValueString($_POST['diarrhea'], "text"),
                       GetSQLValueString($_POST['constipation'], "text"),
                       GetSQLValueString($_POST['bloating'], "text"),
                       GetSQLValueString($_POST['nausea'], "text"),
                       GetSQLValueString($_POST['diffswallowing'], "text"),
                       GetSQLValueString($_POST['painswallowing'], "text"),
                       GetSQLValueString($_POST['heartburn'], "text"),
                       GetSQLValueString($_POST['hepatitis'], "text"),
                       GetSQLValueString($_POST['ulcers'], "text"),
                       GetSQLValueString($_POST['pancreatitis'], "text"),
                       GetSQLValueString($_POST['gallstones'], "text"),
                       GetSQLValueString($_POST['diabetes'], "text"),
                       GetSQLValueString($_POST['thyroid'], "text"),
                       GetSQLValueString($_POST['lackofenergy'], "text"),
                       GetSQLValueString($_POST['anemia'], "text"),
                       GetSQLValueString($_POST['easybruising'], "text"),
                       GetSQLValueString($_POST['bloodclots'], "text"),
                       GetSQLValueString($_POST['bloodtransfusions'], "text"),
                       GetSQLValueString($_POST['hiv'], "text"),
                       GetSQLValueString($_POST['blindness'], "text"),
                       GetSQLValueString($_POST['doublevision'], "text"),
                       GetSQLValueString($_POST['contacts'], "text"),
                       GetSQLValueString($_POST['deafness'], "text"),
                       GetSQLValueString($_POST['ringingears'], "text"),
                       GetSQLValueString($_POST['skinrashes'], "text"),
                       GetSQLValueString($_POST['moles'], "text"),
                       GetSQLValueString($_POST['breastlumps'], "text"),
                       GetSQLValueString($_POST['backpain'], "text"),
                       GetSQLValueString($_POST['hippain'], "text"),
                       GetSQLValueString($_POST['kneepain'], "text"),
                       GetSQLValueString($_POST['otherjoint'], "text"),
                       GetSQLValueString($_POST['arthritis'], "text"),
                       GetSQLValueString($_POST['depression'], "text"),
                       GetSQLValueString($_POST['anxiety2'], "text"),
                       GetSQLValueString($_POST['hallucination'], "text"),
                       GetSQLValueString($_POST['fever'], "text"),
                       GetSQLValueString($_POST['chills'], "text"),
                       GetSQLValueString($_POST['sweats'], "text"),
                       GetSQLValueString($_POST['weightgain'], "text"),
                       GetSQLValueString($_POST['pregnant'], "text"),
                       GetSQLValueString($_POST['children'], "text"),
                       GetSQLValueString($_POST['menstrual'], "text"),
                       GetSQLValueString($_POST['menopause'], "text"),
                       GetSQLValueString($_POST['lastperiod'], "text"),
                       GetSQLValueString($_POST['breastfeed'], "text"),
                       GetSQLValueString($_POST['allergies'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "index.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8" />
<title>New Patient Registration</title>
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="../css/validationEngine.jquery.css" type="text/css"/>
<script type="text/javascript"><?php echo $ppu->generateScriptCode() ?></script>
<script src="../ScriptLibrary/incPU3.js" type="text/javascript"></script>
</head>
<body>
<div id="wrapper">
<div class="row">
<div class="col-xs-3"><img src="images/logo.png" alt="logo" width="" height="" /></div><div class="col-xs-9"></div>
</div>

<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1" id="formID" onSubmit="<?php echo $ppu->getSubmitCode() ?>;return document.MM_returnValue">
  <?php echo $ppu->getProgressField() ?>
  <h5>Personal Information</h5>
  <div class="row">
    <div class="col-xs-4">
      <div class="form-group">
        <label for="fname">First Name <span class="red">*</span></label>
        <input type="text" class="form-control validate[required]" id="fname" name="fname">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="lname">Last Name <span class="red">*</span></label>
        <input type="text" class="form-control validate[required]" id="lname" name="lname">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="ssn">SSN</label>
        <input type="text" class="form-control validate[required]" id="ssn" name="ssn">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-4">
      <div class="form-group">
        <label for="address1">Address 1<span class="red">*</span></label>
        <input type="text" class="form-control validate[required]" id="address1" name="address1">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="address2">Address 2</label>
        <input type="text" class="form-control" id="address2" name="address2">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="email">D.O.B <span class="red">*</span></label>
        <input type="text" class="form-control validate[required]" id="dob" name="dob" placeholder="mm/dd/yyyy">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-4">
      <div class="form-group">
        <label for="city">City <span class="red">*</span></label>
        <input type="text" class="form-control validate[required]" id="city" name="city">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="state">State<span class="red">*</span></label>
        <select name="state validate[required]" class="form-control">
          <option value="">Select</option>
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
        </select>
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="zip">Zip<span class="red">*</span></label>
        <input type="text" class="form-control validate[required]" id="zip" name="zip">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-4">
      <div class="form-group">
        <label for="homephone">Home Phone</label>
        <input type="text" class="form-control" id="homephone" name="homephone">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="mobilephone">Mobile Phone</label>
        <input type="text" class="form-control" id="mobilephone" name="mobilephone">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="email">Email <span class="red">*</span></label>
        <input type="text" class="form-control validate[required]" id="email" name="email">
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-4">
      <div class="form-group">
        <label for="fname">Height (ft)</label>
        <input type="text" class="form-control" id="heightft" name="heightft">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="middleinitial">Height (in)</label>
        <input type="text" class="form-control" id="heightin" name="heightin">
      </div>
    </div>
    <div class="col-xs-4">
      <div class="form-group">
        <label for="lname">Weight</label>
        <input type="text" class="form-control" id="weight" name="weight">
      </div>
    </div>
    <div class="row">
      <div class="col-xs-12">
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="2">Referrals</th>
          </thead>
          <tr>
            <td>How did you hear about us?</td>
            <td><input type="text" class="form-control" name="howdidyouhear"></td>
          </tr>
          <tr>
            <td>Did someone refer you to us? </td>
            <td><input type="radio" name="refer" value="yes">
              Yes
              <input type="radio" name="refer" value="no">
              No </td>
          </tr>
          <tr>
            <td>If yes, please list their name?</td>
            <td><input type="text" name="refername" class="form-control"></td>
          </tr>
          <tr>
            <td>Is this person currently a patient?</td>
            <td><input type="radio" name="curpatient" value="yes">
              Yes
              <input type="radio" name="curpatient" value="no">
              No</td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="2">Insurance Information</th>
          </thead>
          <tr>
            <td>Insurance</td>
            <td><input type="radio">
              Yes
              <input type="radio">
              No </td>
          </tr>
          <tr>
            <td>Insurance Company</td>
            <td><input type="text" class="form-control" name="insuranceco"></td>
          </tr>
          <tr>
            <td>Co-Pay</td>
            <td><input type="text" class="form-control" name="copay"></td>
          </tr>
          <tr>
            <td>Policy Number</td>
            <td><input type="text" class="form-control" name="policyno"></td>
          </tr>
          <tr>
            <td>Group Name</td>
            <td><input type="text" class="form-control" name="groupno"></td>
          </tr>
          <tr>
            <td>Subscriber Name</td>
            <td><input type="text" class="form-control" name="subname"></td>
          </tr>
          <tr>
            <td>Relation to Subscriber</td>
            <td><input type="text" class="form-control" name="subrel"></td>
          </tr>
        </table>
      </div>
      <div class="col-xs-6">
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="2">Emergency Contact</th>
          </thead>
          <tr>
            <td>Emergency Contact</td>
            <td><input type="text" class="form-control" name="emergencycontact"></td>
          </tr>
          <tr>
            <td>Emergency Contact Phone</td>
            <td><input type="text" class="form-control" name="emergencycontactphone"></td>
          </tr>
          <tr>
            <td>Relationship</td>
            <td><input type="text" class="form-control" name="relationship"></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-xs-6">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Medication</th>
              <th>Yes</th>
              <th>No</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Do you take medication for anxiety?</td>
              <td><input type="radio" name="anxiety" value="yes"></td>
              <td><input type="radio" name="anxiety" value="no"></td>
            </tr>
            <tr>
              <td>Do you take Coumadin?</td>
              <td><input type="radio" name="coumadin" value="yes"></td>
              <td><input type="radio" name="coumadin" value="no"></td>
            </tr>
            <tr>
              <td>Do you take prednisone or steroids</td>
              <td><input type="radio" name="steroids" value="yes"></td>
              <td><input type="radio" name="steroids" value="no"></td>
            </tr>
            <tr>
              <td>Do you take antidepressants?</td>
              <td><input type="radio" name="antidepressants" value="yes"></td>
              <td><input type="radio" name="antidepressants" value="no"></td>
            </tr>
            <tr>
              <td>Do you take herbs, roots, medicinal tea?</td>
              <td><input type="radio" name="herbs" value="yes"></td>
              <td><input type="radio" name="herbs" value="no"></td>
            </tr>
          </tbody>
        </table>
        Please list your other medical problems (such as diabetes, high blood pressure, etc):
        <textarea class="form-control" name="otherproblems"></textarea>
        <br>
        <br>
        List all operations/surgeries you have had in the past and any complications you had:
        <textarea class="form-control" name="othersurgeries"></textarea>
      </div>
      <div class="col-xs-6">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Name of Medication</th>
              <th>Dose</th>
              <th>How many times per day</th>
          </thead>
          <tr>
            <td width="55%"><input type="textbox" name="med1" class="form-control"></td>
            <td width="10%"><input type="textbox" name="med1dose" class="form-control"></td>
            <td><input type="radio" name="med1times" value="1">
             <label> 1 </label>
              <input type="radio" name="med1times" value="2">
             <label> 2 </label>
              <input type="radio" name="med1times" value="3">
             <label> 3 </label>
              <input type="radio" name="med1times" value="4">
             <label> 4 </label>
             </td>
          </tr>
          <tr>
            <td><input type="textbox" name="med2" class="form-control"></td>
            <td><input type="textbox" name="med2dose" class="form-control"></td>
            <td><input type="radio" name="med2times" value="1">
              <label>1</label>
              <input type="radio" name="med2times" value="2">
             <label> 2</label>
              <input type="radio" name="med2times" value="3">
             <label> 3</label>
              <input type="radio" name="med2times" value="4">
             <label> 4</label>
              </td>
          </tr>
          <tr>
            <td><input type="textbox" name="med3" class="form-control"></td>
            <td><input type="textbox" name="med3dose" class="form-control"></td>
            <td><input type="radio" name="med3times" value="1">
               <label>1</label>
              <input type="radio" name="med3times" value="2">
              <label> 2</label>
              <input type="radio" name="med3times" value="3">
               <label>3</label>
              <input type="radio" name="med3times" value="4">
               <label>4</label>
              </td>
          </tr>
          <tr>
            <td><input type="textbox" name="med4" class="form-control"></td>
            <td><input type="textbox" name="med4dose" class="form-control"></td>
            <td><input type="radio" name="med4times" value="1">
              <label>1</label>
              <input type="radio" name="med4times" value="2">
              <label>2</label>
              <input type="radio" name="med4times" value="3">
               <label>3</label>
              <input type="radio" name="med4times" value="4">
              <label>4</label>
             </td>
          </tr>
          <tr>
            <td><input type="textbox" name="med5" class="form-control"></td>
            <td><input type="textbox" name="med5dose" class="form-control"></td>
            <td><input type="radio" name="med5times" value="1">
              <label>1</label>
              <input type="radio" name="med5times" value="2">
              <label>2</label>
              <input type="radio" name="med5times" value="3">
              <label>3</label>
              <input type="radio" name="med5times" value="4">
              <label>4</label>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <!-- end row -->
    <hr>
    <div class="row">
      <div class="col-xs-6">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Family History</th>
              <th>Mother</th>
              <th>Father</th>
              <th>Other</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Cancer</td>
              <td><input type="checkbox" name="familycancer" value="mother"></td>
              <td><input type="checkbox" name="familycancer" value="father"></td>
              <td><input type="checkbox" name="familycancer" value="other"></td>
            </tr>
            <tr>
              <td>Diabetes</td>
              <td><input type="checkbox" name="familydiabetes" value="mother"></td>
              <td><input type="checkbox" name="familydiabetes" value="father"></td>
              <td><input type="checkbox" name="familydiabetes" value="other"></td>
            </tr>
            <tr>
              <td>Heart Disease</td>
              <td><input type="checkbox" name="heartdisease" value="mother"></td>
              <td><input type="checkbox" name="heartdisease" value="father"></td>
              <td><input type="checkbox" name="heartdisease" value="other"></td>
            </tr>
            <tr>
              <td>Stroke</td>
              <td><input type="checkbox" name="stroke" value="mother"></td>
              <td><input type="checkbox" name="stroke" value="father"></td>
              <td><input type="checkbox" name="stroke" value="other"></td>
            </tr>
            <tr>
              <td>Other</td>
              <td><input type="checkbox" name="other" value="mother"></td>
              <td><input type="checkbox" name="other" value="father"></td>
              <td><input type="checkbox" name="stroke" value="other"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-xs-6">
        <table class="table table-striped">
          <thead>
            <tr>
              <th colspan="5">Social History</th>
          </thead>
          <tbody>
            <tr>
              <td>Marital Status</td>
              <td><input type="checkbox" name="maritalstatus" value="single">
                Single</td>
              <td><input type="checkbox" name="maritalstatus" value="married">
                Married</td>
              <td><input type="checkbox" name="maritalstatus" value="divorced">
                Divorced </td>
              <td><input type="checkbox" name="maritalstatus" value="widow">
                Widow</td>
            </tr>
            <tr>
              <td>Drink Alcohol?</td>
              <td><input type="radio" name="alcohol" value="yes">
                Yes </td>
              <td><input type="radio" name="alcohol" value="no">
                No</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Smoke?</td>
              <td><input type="radio" name="smoke" value="yes">
                Yes </td>
              <td><input type="radio" name="smoke" value="no">
                No </td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Recreational Drugs?</td>
              <td><input type="radio" name="drugs" value="yes">
                Yes</td>
              <td><input type="radio" name="drugs" value="no">
                No</td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <!-- end row -->
    <div class="row">
      <div class="col-xs-2">
        <h5>Neuro</h5>
        <ul>
          <li>
            <input type="checkbox" name="convulsions" value="yes">
            Convulsions</li>
          <li>
            <input type="checkbox" name="migranes" value="yes">
            Migrane Headaches</li>
          <li>
            <input type="checkbox" name="strokes" value="yes">
            Strokes</li>
          <li>
            <input type="checkbox" name="paralysis" value="yes">
            Paralysis</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Cardiac</h5>
        <ul>
          <li>
            <input type="checkbox" name="chestpain" value="yes">
            Chest Pain</li>
          <li>
            <input type="checkbox" name="palpations" value="yes">
            Palpatations</li>
          <li>
            <input type="checkbox" name="highbloodpressure" value="yes">
            High Blood Pressure</li>
          <li>
            <input type="checkbox" name="heartfailure" value="yes">
            Heart Failure</li>
          <li>
            <input type="checkbox" name="heartattack" value="yes">
            Heart Attack</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Pulmonary</h5>
        <ul>
          <li>
            <input type="checkbox" name="chroniccough" value="yes">
            Chronic Cough</li>
          <li>
            <input type="checkbox" name="sleepapnea" value="yes">
            Sleep Apnea / Snoring</li>
          <li>
            <input type="checkbox" name="asthma" value="yes">
            Asthma</li>
          <li>
            <input type="checkbox" name="colds" value="yes">
            Recent Colds and Pneumonia</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Renal</h5>
        <ul>
          <li>
            <input type="checkbox" name="bloodinurine" value="yes">
            Blood in urine</li>
          <li>
            <input type="checkbox" name="bladder" value="yes">
            Frequent Bladder Infections</li>
          <li>
            <input type="checkbox" name="kidney" value="yes">
            Kidney Infections/Disorders</li>
          <li>
            <input type="checkbox" name="pain" value="yes">
            Pain when urinating</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Hearing</h5>
        <ul>
          <li>
            <input type="checkbox" name="deafness" value="yes">
            Deafness</li>
          <li>
            <input type="checkbox" name="ringinginears" value="yes">
            Ringing in ears</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Skin/Integument</h5>
        <ul>
          <li>
            <input type="checkbox" name="skinrashes" value="yes">
            Skin Rashes</li>
          <li>
            <input type="checkbox" name="moles" value="yes">
            Unusual Moles</li>
          <li>
            <input type="checkbox" name="breastlumps" value="yes">
            Breast Lumps</li>
        </ul>
      </div>
    </div>
    <!-- end row -->
    <div class="row">
      <div class="col-xs-2">
        <h5>Muscular/Skeletal</h5>
        <ul>
          <li>
            <input type="checkbox" name="backpain" value="yes">
            Back Pain</li>
          <li>
            <input type="checkbox" name="hippain" value="yes">
            Hip Pain</li>
          <li>
            <input type="checkbox" name="kneepain" value="yes">
            Knee Pain</li>
          <li>
            <input type="checkbox" name="otherjoint" value="yes">
            Other Joint</li>
          <li>
            <input type="checkbox" name="arthritis" value="yes">
            Arthritis</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Psychiatric</h5>
        <ul>
          <li>
            <input type="checkbox" name="depression" value="yes">
            Depression</li>
          <li>
            <input type="checkbox" name="anxiety2" value="yes">
            Anxiety</li>
          <li>
            <input type="checkbox" name="hallucination" value="yes">
            Hallucination</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Endocrine</h5>
        <ul>
          <li>
            <input type="checkbox" name="diabetes" value="yes">
            Diabetes</li>
          <li>
            <input type="checkbox" name="thyroid" value="yes">
            Thyroid Problems</li>
          <li>
            <input type="checkbox" name="lackofenergy" value="yes">
            Lack of Energy</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Hematologic</h5>
        <ul>
          <li>
            <input type="checkbox" name="anemia" value="yes">
            Anemia</li>
          <li>
            <input type="checkbox" name="easybruising" value="yes">
            Easy Bruising</li>
          <li>
            <input type="checkbox" name="bloodclots" value="yes">
            Blood Clots in deep veins of arms/legs</li>
          <li>
            <input type="checkbox" name="bloodtransfusions" value="yes">
            Blood Transfusions</li>
          <li>
            <input type="checkbox" name="hiv" value="yes">
            HIV/AIDS</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Vision</h5>
        <ul>
          <li>
            <input type="checkbox" name="blindness" value="yes">
            Blindness</li>
          <li>
            <input type="checkbox" name="doublevision" value="yes">
            Double Vision</li>
          <li>
            <input type="checkbox" name="contacts" value="yes">
            Do you wear glasses or contact lenses?</li>
        </ul>
      </div>
      <div class="col-xs-2">
        <h5>Constitutional</h5>
        <ul>
          <li>
            <input type="checkbox" name="fever" value="yes">
            Fever</li>
          <li>
            <input type="checkbox" name="chills" value="yes">
            Chills</li>
          <li>
            <input type="checkbox" name="sweats" value="yes">
            Sweats</li>
          <li>
            <input type="checkbox" name="weightgain" value="yes">
            Excessive Weight Gain</li>
        </ul>
      </div>
    </div>
    <!-- end row -->
    <div class="row">
      <div class="col-xs-3">
        <h5>Gastrointestinal</h5>
        <ul>
          <li>
            <input type="checkbox" name="bloodinstool" value="yes">
            Blood in Stool</li>
          <li>
            <input type="checkbox" name="vomitblood" value="yes">
            Vomiting Blood</li>
          <li>
            <input type="checkbox" name="blackstool" value="yes">
            Black Stools</li>
          <li>
            <input type="checkbox" name="diarrhea" value="yes">
            Chronic Diarrhea</li>
          <li>
            <input type="checkbox" name="constipation" value="yes">
            Chronic Constipation</li>
          <li>
            <input type="checkbox" name="bloating" value="yes">
            Bloating</li>
          <li>
            <input type="checkbox" name="nausea" value="yes">
            Nausea or Vomiting</li>
          <li>
            <input type="checkbox" name="diffswallowing" value="yes">
            Difficulty Swallowing</li>
          <li>
            <input type="checkbox" name="painswallowing" value="yes">
            Pain when swallowing</li>
          <li>
            <input type="checkbox" name="heartburn" value="yes">
            Chronic Heartburn / Reflux</li>
          <li>
            <input type="checkbox" name="hepatitis" value="yes">
            Hepatitis</li>
          <li>
            <input type="checkbox" name="ulcers" value="yes">
            Ulcers</li>
          <li>
            <input type="checkbox" name="pancreatitis" value="yes">
            Pancreatitis</li>
          <li>
            <input type="checkbox" name="gallstones" value="yes">
            Gallstones</li>
        </ul>
      </div>
      <div class="row">
        <div class="col-xs-3">
          <h5>Gynecology (Ladies Only)</h5>
          <ul>
            <li>
              <input type="checkbox" name="pregnant" value="yes">
              Ever been pregnant?</li>
            <li>
              <input type="checkbox" name="children" value="yes">
              Do you have children?</li>
            <li>
              <input type="checkbox" name="menstrual" value="yes">
              Any changes in menstrual?</li>
            <li>
              <input type="checkbox" name="menopause" value="yes">
              Menopause?</li>
            <li>
              <input type="checkbox" name="breastfeed" value="yes">
              Do you breast feed?</li>
            <li>Date of last menstrual period?
              <input type="text" name="lastperiod">
            </li>
          </ul>
        </div>
        <div class="col-xs-3"></div>
        <div class="col-xs-3">
          <h5>Upload Medical history</h5>
          <input name="medicalhistory" type="file" class="form-control" onChange="<?php echo $uploadmedicalhistory->getValidateCode() ?>;return document.MM_returnValue">
        </div>
        <div class="col-xs-3"></div>
      </div>
      <!-- end row -->
      <div class="row wide">
        <div class="col-xs-10">
          <label for="digitalsig">Digital Signature:<span class="red">*</span></label>
          <input type="text" class="form-control validate[required]" name="digitalsig">
          <span class="help-block">Enter the last four digits of your SSN and the year of your birth. For example, if your SSN is 123-45-6789 and your birthday is 06/10/1978 Your digital signature is <strong>67891978</strong></span> </div>
        <div class="col-xs-2"> <br>
          <input type="submit" name="submit" class="btn btn-success" value="Submit Medical History">
        </div>
      </div>
    </div>
    <!-- end container-->
    <br>
    <br>
    <br>
    <br>
    <br>
  </div>
  <!-- wrapper -->
  <input type="hidden" name="patientID" value="<?php echo rand(); ?>" ?>
  <input type="hidden" name="MM_insert" value="form1" />
</form>

<script src="jquery-1.7.2.min.js"></script>
<script src="languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
<script>
		jQuery(document).ready(function(){
			// binds form submission and fields to the validation engine
			jQuery("#formID").validationEngine();
		});
</script>
</body>
</html>