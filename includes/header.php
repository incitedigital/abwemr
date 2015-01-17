<?php session_start(); ?>
<?php require_once('Connections/dbc.php'); ?>

<?php
$server = $_SERVER["HTTP_HOST"];
$server = $server."";
//echo $server;
?>

<?php
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "https://www.abwemr.com/";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
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

$colname_rsAdmin = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsAdmin = $_SESSION['MM_Username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsAdmin = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsAdmin, "text"));
$rsAdmin = mysql_query($query_rsAdmin, $dbc) or die(mysql_error());
$row_rsAdmin = mysql_fetch_assoc($rsAdmin);
$totalRows_rsAdmin = mysql_num_rows($rsAdmin);

$colname_rsCenter = "-1";
if (isset($_SESSION['centerID'])) {
  $colname_rsCenter = $_SESSION['centerID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsCenter = sprintf("SELECT * FROM tbl_center WHERE centerID = %s", GetSQLValueString($colname_rsCenter, "int"));
$rsCenter = mysql_query($query_rsCenter, $dbc) or die(mysql_error());
$row_rsCenter = mysql_fetch_assoc($rsCenter);
$totalRows_rsCenter = mysql_num_rows($rsCenter);
?>


<!DOCTYPE html">
<html>
<head>
<meta charset="UTF-8">
<title>Better Weigh Medical EMR System</title>
<script src="js/jquery-1.7.2.min.js"></script>
<script src="js/jquery-ui-1.8.21.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="style/style.css" />
<link href='http://fonts.googleapis.com/css?family=Roboto:400,300italic,300,500,700' rel='stylesheet' type='text/css'>
<link type="text/css" href="css/custom-theme/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
<link rel="stylesheet" href="css/template.css" type="text/css"/>
<link href="css/facebox.css" media="screen" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/print.css" type="text/css" media="print" />

<script type="text/javascript" src="ScriptLibrary/jquery.autocomplete.js"></script>
<script type="text/javascript" src="ScriptLibrary/jquery.bgiframe.min.js"></script>
<link rel="stylesheet" type="text/css" href="ScriptLibrary/autocomplete.css" />
</head>

<body>
<div class="container-fluid">
<div class="row">
		<div class="col-xs-1 blackmenu sidebar">	
			<?php include('includes/menu.php'); ?>
		</div>

<div class="col-xs-11" id="maincontent">

<div id="logo">
<a href="dashboard.php"><img src="images/logo.png" width="125" height="70" /></a></div><!-- End Logo -->
<div id="logout"> 
<?php echo $row_rsAdmin['firstname']; ?> <?php echo $row_rsAdmin['lname']; ?>  &nbsp; &nbsp;| &nbsp; &nbsp;<?php echo $row_rsCenter['locationname']; ?>  &nbsp; | &nbsp;   <a href="<?php echo $logoutAction ?>">
 Log out</a>
</div><!-- End Logout -->



<?php
mysql_free_result($rsAdmin);

mysql_free_result($rsCenter);
?>
