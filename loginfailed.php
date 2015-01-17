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

mysql_select_db($database_dbc, $dbc);
$query_rsCenters = "SELECT * FROM tbl_center";
$rsCenters = mysql_query($query_rsCenters, $dbc) or die(mysql_error());
$row_rsCenters = mysql_fetch_assoc($rsCenters);
$totalRows_rsCenters = mysql_num_rows($rsCenters);

$colname_rsAdmin = "-1";
if (isset($_POST['username'])) {
  $colname_rsAdmin = $_POST['username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsAdmin = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsAdmin, "text"));
$rsAdmin = mysql_query($query_rsAdmin, $dbc) or die(mysql_error());
$row_rsAdmin = mysql_fetch_assoc($rsAdmin);
$totalRows_rsAdmin = mysql_num_rows($rsAdmin);
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['login2'])) {
  $loginUsername=$_POST['login2'];
  $password=$_POST['password'];
  $center = $_POST['centerID'];

  $MM_fldUserAuthorization = "accesslevel";
  $MM_redirectLoginSuccess = "dashboard.php";
  $MM_redirectLoginFailed = "loginfailed.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_dbc, $dbc);
  	
  $LoginRS__query=sprintf("SELECT username, password, adminID, accesslevel FROM tbl_admin WHERE username=%s AND password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $dbc) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'accesslevel');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	
	$_SESSION['centerID'] = $center;      
	if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Untitled Document</title>
<script src="SpryAssets/SpryValidationTextField.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationPassword.js" type="text/javascript"></script>
<script src="SpryAssets/SpryValidationSelect.js" type="text/javascript"></script>
<link href="SpryAssets/SpryValidationTextField.css" rel="stylesheet" type="text/css">
<link href="SpryAssets/SpryValidationPassword.css" rel="stylesheet" type="text/css">
<link href="SpryAssets/SpryValidationSelect.css" rel="stylesheet" type="text/css">
</head>

<body>

<div id="container">

<div id="logo"><img src="images/logo.png" width="130" height="73"></div>
<div class="login">
  
  <form ACTION="<?php echo $loginFormAction; ?>" METHOD="POST" name = "login">
  <span id="sprytextfield1">
  <label for = "login2">Username:</label><br/><input type="text" name="login2" class="loginform"><br/>
  <span class="textfieldRequiredMsg">A value is required.</span></span>
  <span id="sprypassword1">
  <label for = "password">Password: </label><br/><input type="password" name="password" class="password" /><br/>
  <span class="passwordRequiredMsg">A value is required.</span></span><br/>
  <span id="spryselect1">
  <select name="centerID">
    <option value="-1">Select Location</option>
    <?php
do {  
?>
    <option value="<?php echo $row_rsCenters['centerID']?>"><?php echo $row_rsCenters['locationname']?></option>
    <?php
} while ($row_rsCenters = mysql_fetch_assoc($rsCenters));
  $rows = mysql_num_rows($rsCenters);
  if($rows > 0) {
      mysql_data_seek($rsCenters, 0);
	  $row_rsCenters = mysql_fetch_assoc($rsCenters);
  }
?>
  </select>
  <span class="selectInvalidMsg">Please select a location.</span><span class="selectRequiredMsg">Please select an item.</span></span><br/>
  <input type="submit" name="submit" class="submit" value="Login"/>

</form>
</div>
</div>
<div id="shadow">
 Copyright &copy; <?php echo date('Y'); ?> A Better Weigh Inc. - All Rights Reserved.  Powered by Nac Media Group.
</div>
<script type="text/javascript">
var sprytextfield1 = new Spry.Widget.ValidationTextField("sprytextfield1");
var sprypassword1 = new Spry.Widget.ValidationPassword("sprypassword1");
var spryselect1 = new Spry.Widget.ValidationSelect("spryselect1", {invalidValue:"-1"});
</script>
</body>
</html>
<?php
mysql_free_result($rsCenters);

mysql_free_result($rsAdmin);
?>
