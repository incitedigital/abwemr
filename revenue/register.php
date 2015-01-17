<?php require_once('Connections/dbc.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "2,3";
$MM_donotCheckaccess = "false";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && false) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "loginfailed.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
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

$colname_rsProfiles = "-1";
if (isset($_SESSION['MM_username'])) {
  $colname_rsProfiles = $_SESSION['MM_username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsProfiles = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsProfiles, "text"));
$rsProfiles = mysql_query($query_rsProfiles, $dbc) or die(mysql_error());
$row_rsProfiles = mysql_fetch_assoc($rsProfiles);
$totalRows_rsProfiles = mysql_num_rows($rsProfiles);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form")) {

	$result = mysql_query("SELECT * FROM tbl_admin WHERE password = '$_POST[witnessconfirmation]'");
	$num_rows = mysql_num_rows($result);
	
	if ($num_rows > 0) 

{
	 $date = date("Y-m-d", strtotime($_POST['date']));	
  $insertSQL = sprintf("INSERT INTO tbl_register (userID, centerID, cashier, witness, startingregister, giftcards, groupons, credit, checks, hundreds, fiftys, twentys, tens, fives, ones, quarters, dimes, nickels, pennys, date) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '$date')",
                      
                       GetSQLValueString($_POST['userID'], "text"),
                       GetSQLValueString($_POST['centerID'], "int"),
                       GetSQLValueString($_POST['cashier'], "text"),
                       GetSQLValueString($_POST['witnessconfirmation'], "text"),
                       GetSQLValueString($_POST['startingregister'], "double"),
                       GetSQLValueString($_POST['giftcards'], "double"),
                       GetSQLValueString($_POST['groupons'], "double"),
                       GetSQLValueString($_POST['credit'], "double"),
                       GetSQLValueString($_POST['checks'], "double"),
                       GetSQLValueString($_POST['hundreds'], "int"),
                       GetSQLValueString($_POST['fiftys'], "int"),
                       GetSQLValueString($_POST['twentys'], "int"),
                       GetSQLValueString($_POST['tens'], "int"),
                       GetSQLValueString($_POST['fives'], "int"),
                       GetSQLValueString($_POST['ones'], "int"),
                       GetSQLValueString($_POST['quarters'], "int"),
                       GetSQLValueString($_POST['dimes'], "int"),
                       GetSQLValueString($_POST['nickels'], "int"),
                       GetSQLValueString($_POST['pennys'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "register.php?formreg=success";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}
 else
 	{
		echo "<div class=\"container\"><div class=\"alert alert-danger\">
		  <strong>Alert!</strong> The Witness Confirmation code is invalid.
      </div></div>";
	}
}
?>
<?php include('includes/header.php'); ?>

    <div class="container" id="pushdown">
   <!-- 
<div class="alert alert-info alert-dismissable" id="my-alert">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  <strong>Warning!</strong> Best check yo self, you're not looking too good.
</div>
-->
<?php if ($_GET['formreg'] == 'success') { echo 
"<div class=\"row\" id=\"alertcontainer\"><div class=\"alert alert-success alert-dismissable\">
 <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
  <strong>Success!</strong> Your register count has been submitted.
</div></div>";

}

?>
<!-- Main component for a primary marketing message or call to action -->  
    <div class="row">
  <div class="col-md-9">
  <form method="POST" action="<?php echo $editFormAction; ?>" role="form" name="form" id="revenue"  onsubmit="return validateForm();">
  
   <div class="row">
  <div class="col-md-4">
  <div class="form-group">
    <label for="datepicker"><span class="glyphicon glyphicon-calendar"></span> Date</label>
    <input type="text" class="form-control" id="datepicker" name="date" placeholder="Select Date" value="<?php echo $_POST['date'];?>">
  </div>
  </div>
  

  <div class="col-md-4">
  <div class="form-group">
    <label for="startingregister"><span class="glyphicon glyphicon-usd"></span> Starting Register</label>
    <input type="text" class="form-control validate[required,custom[integer],min[0]]" id="startingregister" name="startingregister" placeholder="Starting Register" value="<?php echo $_POST['startingregister'];?>">
  </div>
  </div>
  
 <div class="col-md-4">
 <div id="test"></div>

  </div>
  
  </div> <!-- end row -->
  <div class="row">
  <div class="col-md-12">
  <h1>Cash</h1>
  </div>
  </div>
  <div class="row">
  
   <div class="col-md-2">
  <div class="form-group">
    <label for="hundreds"><span class="glyphicon glyphicon-usd"></span> 100's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="hundreds" id="hundreds" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
   <div class="col-md-2">
  <div class="form-group">
    <label for="fiftys"><span class="glyphicon glyphicon-usd"></span> 50's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="fiftys" id="fiftys" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
   <div class="col-md-2">
  <div class="form-group">
    <label for="twentys"><span class="glyphicon glyphicon-usd"></span> 20's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="twentys" id="twentys" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
   <div class="col-md-2">
  <div class="form-group">
    <label for="tens"><span class="glyphicon glyphicon-usd"></span> 10's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="tens" id="tens" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
   <div class="col-md-2">
  <div class="form-group">
    <label for="fives"><span class="glyphicon glyphicon-usd"></span> 5's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="fives" id="fives" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
   <div class="col-md-2">
  <div class="form-group">
    <label for="ones"><span class="glyphicon glyphicon-usd"></span> 1's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="ones" id="ones" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
  <div class="col-md-2">
  <div class="form-group">
    <label for="quarters">Quarter's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="quarters" id="quarters" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
  <div class="col-md-2">
  <div class="form-group">
    <label for="dimes">Dime's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="dimes" id="dimes" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
  <div class="col-md-2">
  <div class="form-group">
    <label for="nickels">Nickel's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="nickels" id="nickels" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
  <div class="col-md-2">
  <div class="form-group">
    <label for="pennys">Penny's</label>
     <input type="text" class="form-control validate[custom[integer],min[0]]" name="pennys" id="pennys" onChange="updatesum()" placeholder="0" value="">
  </div>
  </div>
  </div> <!-- End row -->
  
  
  <div class="row">
  
 
		<div class="col-md-3">
   <div class="form-group">
    <label for="giftcards"><span class="glyphicon glyphicon-credit-card"></span> Gift Cards</label>
     <input type="text" name="giftcards" class="form-control validate[custom[integer],min[0]]" id="giftcards" onChange="updatesum()" placeholder="0" value="">
  </div></div>

<div class="col-md-3">
   <div class="form-group">
    <label for="groupons"><span class="glyphicon glyphicon-tag"></span> Groupons</label>
     <input type="text" name="groupons"  class="form-control validate[custom[integer],min[0]]" id="groupons" onChange="updatesum()" placeholder="0" value="">
  </div>
</div>

<div class="col-md-3">
  <div class="form-group">
    <label for="credit"><span class="glyphicon glyphicon-credit-card"></span> Credit</label>
     <input type="text" name="credit" class="form-control validate[custom[integer],min[0]]" id="credit" onChange="updatesum()" placeholder="0" value="">
  </div>
</div>

<div class="col-md-3">
  <div class="form-group">
    <label for="checks"><span class="glyphicon glyphicon-leaf"></span> Checks</label>
     <input type="text" name ="checks" class="form-control validate[custom[integer],min[0]]" id="checks" onChange="updatesum()" placeholder="0" value="">
  </div>
</div>




</div> <!-- end row -->

  <div class="row">
  <div class="col-md-6">
  <div class="form-group">
    <label for="cashierconfirmation"><span class="glyphicon glyphicon-user"></span> Cashier Confirmation</label>
    <input type="text" class="form-control validate[required]" name="cashierconfirmation"  onChange="validateForm()" id="cashierconfirmation" disabled value="<?php echo $row_rsUsers['firstname']; ?> <?php echo $row_rsUsers['lname']; ?>">
  </div>
  </div>
  

  <div class="col-md-6">
  <div class="form-group">
    <label for="witnessconfirmation"><span class="glyphicon glyphicon-user"></span> Witness Confirmation</label>
    <input type="password" class="form-control validate[required]" name="witnessconfirmation" onChange="validateForm()" id="witnessconfirmation" placeholder="Witness Confirmation">
  </div>
  </div>
  
 
  
  </div> <!-- end row -->


  <input type="submit" name="submit" class="btn btn-success" value="Submit">


</div> <!-- End col1 -->

<div class="col-md-3">
<div class="panel  panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><span class="glyphicon glyphicon-usd"></span> Today's Total</h3>
  </div>
  <div class="panel-body">
   <div class="bignumber"><input type="text" name="total" id="total" placeholder="$0.00" value="" disabled> </div>
  </div>
</div>
</div>
<input type="hidden" name="userID" value="<?php echo $row_rsUsers['adminID']; ?>">
<input type="hidden" name="cashier" value="<?php echo $row_rsUsers['password']; ?>">
<input type="hidden" name="centerID" value="<?php echo $row_rsLocations['centerID']; ?>">
<input type="hidden" name="MM_insert" value="form">

  </form>

</div> <!-- End row -->
    
    
    
    </div> <!-- /container -->
    
    
    


<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsProfiles);
?>
