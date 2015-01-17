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
$query_rsLocations = "SELECT * FROM tbl_center";
$rsLocations = mysql_query($query_rsLocations, $dbc) or die(mysql_error());
$row_rsLocations = mysql_fetch_assoc($rsLocations);
$totalRows_rsLocations = mysql_num_rows($rsLocations);
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

if (isset($_POST['userID'])) {
  $loginUsername=$_POST['userID'];
  $password=$_POST['password'];
  $locationID = $_POST['centerID'];
  $MM_fldUserAuthorization = "accesslevel";
  $MM_redirectLoginSuccess = "register.php";
  $MM_redirectLoginFailed = "loginfailed.php";
  $MM_redirecttoReferrer = false;
  mysql_select_db($database_dbc, $dbc);
  	
  $LoginRS__query=sprintf("SELECT username, password, accesslevel FROM tbl_admin WHERE username=%s AND password=%s",
  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
   
  $LoginRS = mysql_query($LoginRS__query, $dbc) or die(mysql_error());
  $loginFoundUser = mysql_num_rows($LoginRS);
  if ($loginFoundUser) {
    
    $loginStrGroup  = mysql_result($LoginRS,0,'accesslevel');
    
	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;	    
	$_SESSION['centerID'] = $locationID;	   

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
<!DOCTYPE html>
<html>
<head>
<title></title>
<meta charset="UTF-8">
<!--[if lt IE 9]>
<script src=”http://html5shiv.googlecode.com/svn/trunk/html5.js”></script>
<![endif]-->
<link rel="stylesheet" href="style/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="style/style.css">
<link rel="stylesheet" type="text/css" href="style/validationEngine.jquery.css">
<script src="http://code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js" type="text/javascript"></script>
<script src="js/script.js"></script>
</head>
<body>
 <div class="container">

      <form METHOD="POST" id="loginform" class="form-signin validate[required]" action="<?php echo $loginFormAction; ?>">
        <h2 class="form-signin-heading"><img src="images/logo.png" width="163" height="90"></h2>
    	<h4 class="center">Revenue Management System</h4>
        <input type="text" class="form-control" name="userID" placeholder="Username" autofocus>
        <input type="password" class="form-control" name="password" placeholder="Password">
        <label for="location">Choose Location</label>
       <select class="form-control input-lg validate[required]" name="centerID">
       <option value="">Select</option>

                <?php
				
do {  
?>
         <option value="<?php echo $row_rsLocations['centerID']?>"><?php echo $row_rsLocations['locationname']?></option>
         <?php
} while ($row_rsLocations = mysql_fetch_assoc($rsLocations));
  $rows = mysql_num_rows($rsLocations);
  if($rows > 0) {
      mysql_data_seek($rsLocations, 0);
	  $row_rsLocations = mysql_fetch_assoc($rsLocations);
  }
?>
       </select>
<br>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      </form>

    </div> <!-- /container -->
<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsLocations);
?>
