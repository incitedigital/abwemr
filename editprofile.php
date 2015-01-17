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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
 $dob= date('Y-m-d',strtotime($_POST['dob']));
  $updateSQL = sprintf("UPDATE tbl_patient SET fname=%s, middleinitial=%s, lname=%s, address1=%s, address2=%s, city=%s, `state`=%s, zip=%s, email=%s, homephone=%s, mobilephone=%s, insurance=%s, insuranceco=%s, copay=%s, dob='$dob' WHERE patientID=%s",
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
                       GetSQLValueString($_POST['insurance'], "text"),
                       GetSQLValueString($_POST['insuranceco'], "text"),
                       GetSQLValueString($_POST['copay'], "int"),
                       GetSQLValueString($_POST['patientID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "viewpatient.php?patientID=" . $row_rsUsers['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}


$colname_rsUsers = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsUsers = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsUsers = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsUsers, "int"));
$rsUsers = mysql_query($query_rsUsers, $dbc) or die(mysql_error());
$row_rsUsers = mysql_fetch_assoc($rsUsers);
$totalRows_rsUsers = mysql_num_rows($rsUsers);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<link rel="stylesheet" href="http://<?php echo $server; ?>/css/validationEngine.jquery.css" type="text/css"/>
<script type="text/javascript" src="http://<?php echo $server; ?>/js/jquery-ui-1.8.21.custom.min.js"></script>
<script src="http://<?php echo $server; ?>/js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="http://<?php echo $server; ?>/js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>

<script>
		jQuery(document).ready(function(){
			// binds form submission and fields to the validation engine
			jQuery("#addpatient").validationEngine();
			jQuery("#noteform").validationEngine();
			jQuery("#addvitalform").validationEngine();
		});

		/**
		*
		* @param {jqObject} the field where the validation applies
		* @param {Array[String]} validation rules for this field
		* @param {int} rule index
		* @param {Map} form options
		* @return an error string if validation failed
		*/
		function checkHELLO(field, rules, i, options){
			if (field.val() != "HELLO") {
				// this allows to use i18 for the error msgs
				return options.allrules.validate2fields.alertText;
			}
		}
		
		$('#dob').datepicker();
	</script>

</head>
<body>
<form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form1" id="form1" return document.MM_returnValue">
 
  <h1>Edit Profile</h1>
  <table align="center" cellpadding="5" cellspacing="5" id="spacer">

    <tr valign="baseline">
      <td nowrap="nowrap" align="right">First Name:</td>
      <td><input type="text" name="fname" value="<?php echo htmlentities($row_rsUsers['fname'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Middle Initial:</td>
      <td><input type="text" name="middleinitial" value="<?php echo htmlentities($row_rsUsers['middleinitial'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Last Name:</td>
      <td><input type="text" name="lname" value="<?php echo htmlentities($row_rsUsers['lname'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Address 1:</td>
      <td><input type="text" name="address1" value="<?php echo htmlentities($row_rsUsers['address1'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Address 2:</td>
      <td><input type="text" name="address2" value="<?php echo htmlentities($row_rsUsers['address2'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">City:</td>
      <td><input type="text" name="city" value="<?php echo htmlentities($row_rsUsers['city'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">State:</td>
      <td><input type="text" name="state" value="<?php echo htmlentities($row_rsUsers['state'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Zip:</td>
      <td><input type="text" name="zip" value="<?php echo htmlentities($row_rsUsers['zip'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Email:</td>
      <td><input type="text" name="email" class="validate[custom[email]]" value="<?php echo htmlentities($row_rsUsers['email'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Homephone:</td>
      <td><input type="text" name="homephone" value="<?php echo htmlentities($row_rsUsers['homephone'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Mobilephone:</td>
      <td><input type="text" name="mobilephone" value="<?php echo htmlentities($row_rsUsers['mobilephone'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Dob:</td>
      <td><input type="text" name="dob" value="<?php echo date('m/d/Y', strtotime($row_rsUsers['dob'])); ?>" size="32" placeholder="mm/dd/yyyy" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Insurance:</td>
      <td><select name="insurance">
        <option value="<?php echo $row_rsUsers['insurance']; ?>"><?php echo $row_rsUsers['insurance']; ?></option>
    
        <option value="Yes">Yes</option>
        <option value="No">No</option>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Insurance Company:</td>
      <td><input type="text" name="insuranceco" id="insuranceco" value="<?php echo $row_rsUsers['insuranceco']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Co-Pay Amount:</td>
      <td><input type="text" name="copay" id="copay" class="validate[custom[number]" value="<?php echo $row_rsUsers['copay']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" class="buttoncolor"/></td>
    </tr>
  </table>
  <input type="hidden" name="patientID" value="<?php echo $row_rsUsers['patientID']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
</form>

</body>
</html>
<?php
mysql_free_result($rsUsers);
?>
