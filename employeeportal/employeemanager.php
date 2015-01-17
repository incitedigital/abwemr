<?php session_start(); ?>
<?php require_once('../Connections/dbc.php'); ?>
<?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
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
	
  $logoutGoTo = "index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

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
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php?message=error";
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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsUsers = 1;
$pageNum_rsUsers = 0;
if (isset($_GET['pageNum_rsUsers'])) {
  $pageNum_rsUsers = $_GET['pageNum_rsUsers'];
}
$startRow_rsUsers = $pageNum_rsUsers * $maxRows_rsUsers;

mysql_select_db($database_dbc, $dbc);
$query_rsUsers = "SELECT * FROM tbl_admin WHERE username = '$_SESSION[MM_Username]'";
$query_limit_rsUsers = sprintf("%s LIMIT %d, %d", $query_rsUsers, $startRow_rsUsers, $maxRows_rsUsers);
$rsUsers = mysql_query($query_limit_rsUsers, $dbc) or die(mysql_error());
$row_rsUsers = mysql_fetch_assoc($rsUsers);

if (isset($_GET['totalRows_rsUsers'])) {
  $totalRows_rsUsers = $_GET['totalRows_rsUsers'];
} else {
  $all_rsUsers = mysql_query($query_rsUsers);
  $totalRows_rsUsers = mysql_num_rows($all_rsUsers);
}
$totalPages_rsUsers = ceil($totalRows_rsUsers/$maxRows_rsUsers)-1;

mysql_select_db($database_dbc, $dbc);
$query_rsLocations = "SELECT * FROM tbl_center WHERE centerID = '$_SESSION[centerID]'";
$rsLocations = mysql_query($query_rsLocations, $dbc) or die(mysql_error());
$row_rsLocations = mysql_fetch_assoc($rsLocations);
$totalRows_rsLocations = mysql_num_rows($rsLocations);

$colname_rsAdminID = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsAdminID = $_SESSION['MM_Username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsAdminID = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsAdminID, "text"));
$rsAdminID = mysql_query($query_rsAdminID, $dbc) or die(mysql_error());
$row_rsAdminID = mysql_fetch_assoc($rsAdminID);
$totalRows_rsAdminID = mysql_num_rows($rsAdminID);

$maxRows_rsInbox = 5;
$pageNum_rsInbox = 0;
if (isset($_GET['pageNum_rsInbox'])) {
  $pageNum_rsInbox = $_GET['pageNum_rsInbox'];
}
$startRow_rsInbox = $pageNum_rsInbox * $maxRows_rsInbox;

mysql_select_db($database_dbc, $dbc);
$query_rsInbox = "SELECT tbl_message.*, firstname,lname FROM tbl_message JOIN tbl_admin on tbl_message.adminID = tbl_admin.adminID WHERE tbl_message.toID = '$row_rsAdminID[adminID]' ORDER BY date DESC";
$query_limit_rsInbox = sprintf("%s LIMIT %d, %d", $query_rsInbox, $startRow_rsInbox, $maxRows_rsInbox);
$rsInbox = mysql_query($query_limit_rsInbox, $dbc) or die(mysql_error());
$row_rsInbox = mysql_fetch_assoc($rsInbox);

if (isset($_GET['totalRows_rsInbox'])) {
  $totalRows_rsInbox = $_GET['totalRows_rsInbox'];
} else {
  $all_rsInbox = mysql_query($query_rsInbox);
  $totalRows_rsInbox = mysql_num_rows($all_rsInbox);
}
$totalPages_rsInbox = ceil($totalRows_rsInbox/$maxRows_rsInbox)-1;

mysql_select_db($database_dbc, $dbc);
$query_allemployees = "SELECT * FROM tbl_admin";
$allemployees = mysql_query($query_allemployees, $dbc) or die(mysql_error());
$row_allemployees = mysql_fetch_assoc($allemployees);
$totalRows_allemployees = mysql_num_rows($allemployees);

mysql_select_db($database_dbc, $dbc);
$query_rsAnnouncements = "SELECT tbl_announcements.*, firstname, lname FROM tbl_announcements JOIN tbl_admin ON tbl_announcements.adminID = tbl_admin.adminID ORDER BY `date` DESC";
$rsAnnouncements = mysql_query($query_rsAnnouncements, $dbc) or die(mysql_error());
$row_rsAnnouncements = mysql_fetch_assoc($rsAnnouncements);
$totalRows_rsAnnouncements = mysql_num_rows($rsAnnouncements);

mysql_select_db($database_dbc, $dbc);
$query_rsTimeoff = "SELECT tbl_timeoff.*, firstname, lname FROM tbl_timeoff JOIN tbl_admin on tbl_timeoff.adminID = tbl_admin.adminID WHERE status = 1";
$rsTimeoff = mysql_query($query_rsTimeoff, $dbc) or die(mysql_error());
$row_rsTimeoff = mysql_fetch_assoc($rsTimeoff);
$totalRows_rsTimeoff = mysql_num_rows($rsTimeoff);

mysql_select_db($database_dbc, $dbc);
$query_rsCompInfo = "SELECT * FROM tbl_companyinfo";
$rsCompInfo = mysql_query($query_rsCompInfo, $dbc) or die(mysql_error());
$row_rsCompInfo = mysql_fetch_assoc($rsCompInfo);
$totalRows_rsCompInfo = mysql_num_rows($rsCompInfo);

$queryString_rsInbox = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsInbox") == false && 
        stristr($param, "totalRows_rsInbox") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsInbox = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsInbox = sprintf("&totalRows_rsInbox=%d%s", $totalRows_rsInbox, $queryString_rsInbox);
 
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "announcements")) {
  $insertSQL = sprintf("INSERT INTO tbl_announcements (subject, message, adminID) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['message'], "text"),
                       GetSQLValueString($_POST['adminID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "employeemanager.php?message=announcement";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "sendmessage")) {
  $insertSQL = sprintf("INSERT INTO tbl_message (adminID, toID, subject, message) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['adminID'], "int"),
                       GetSQLValueString($_POST['toID'], "int"),
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['message'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "employeemanager.php?success=message";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "inventoryrequest")) {
  $insertSQL = sprintf("INSERT INTO tbl_inventory (adminID, request) VALUES (%s, %s)",
                       GetSQLValueString($_POST['adminID'], "int"),
                       GetSQLValueString($_POST['message'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "employeemanager.php?success=inventory";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "timeoffrequest")) {
	$start = date('Y-m-d', strtotime($_POST['start']));
$end = date('Y-m-d', strtotime($_POST['end']));
$adminID = $_POST['adminID']; 
 $insertSQL = "INSERT INTO tbl_timeoff (startdate, enddate, adminID) VALUES ('$start', '$end', '$adminID')";
                     
  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "employeemanager.php?success=timeoff";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}



if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "eventscalendar")) {
	$start = date('Y-m-d', strtotime($_POST['start']));
$end = date('Y-m-d', strtotime($_POST['end']));
$timeoff = $_POST['timeoff'];
  $insertSQL = sprintf("INSERT INTO evenement (title, `start`, `end`,timeoff) VALUES (%s, '$start', '$end','$timeoff')",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['start'], "date"),
                       GetSQLValueString($_POST['end'], "date"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "employeemanager.php?message=addevent";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Better Weigh Revenue Tracking System</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--[if lt IE 9]>
<script src=”js/html5shiv.js”></script>
<![endif]-->
<!--[if gt IE 8]>
    <meta http-equiv="x-ua-compatible" content="IE=8">
<![endif]-->

<link rel="stylesheet" href="style/bootstrap.css">
<link rel="stylesheet" type="text/css" href="style/normalize.css">
<link rel="stylesheet" type="text/css" href="style/style.css">
<link rel="stylesheet" type="text/css" href="style/jquerycalendar.css">
<link rel="stylesheet" type="text/css" href="style/validationEngine.jquery.css">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
<link href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/cupertino/jquery-ui.min.css' rel='stylesheet' type="text/css">

<!--jQuery References--> 
<script src="http://code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js" type="text/javascript"></script>

<script src="js/fullcalendar.min.js"></script>
<link rel="stylesheet" href="style/fullcalendar.css"/>
<link rel="stylesheet" href="style/fullcalendar.print.css"/>

<script>
$(document).ready(function() {
  var date = new Date();
  var d = date.getDate();
  var m = date.getMonth();
  var y = date.getFullYear();

  var calendar = $('#calendar').fullCalendar({
   editable: false,
   header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,agendaWeek,agendaDay'
   },
   
   events: "http://abwemr.com/revenue/events.php",
   
   // Convert the allDay from string to boolean
   eventRender: function(event, element, view) {
    if (event.allDay === 'true') {
     event.allDay = true;
    } else {
     event.allDay = false;
    }
   },
   selectable: false,
   selectHelper: false,
   select: function(start, end, allDay) {
   var title = prompt('Event Title:');
   var url = prompt('Type Event url, if exits:');
   if (title) {
   var start = $.fullCalendar.formatDate(start, "yyyy-MM-dd HH:mm:ss");
   var end = $.fullCalendar.formatDate(end, "yyyy-MM-dd HH:mm:ss");
   $.ajax({
   url: 'http://abwemr.com/revenue/add_events.php',
   data: 'title='+ title+'&start='+ start +'&end='+ end +'&url='+ url ,
   type: "POST",
   success: function(json) {
   alert('Added Successfully');
   }
   });
   calendar.fullCalendar('renderEvent',
   {
   title: title,
   start: start,
   end: end,
   allDay: allDay
   },
   true // make the event "stick"
   );
   }
   calendar.fullCalendar('unselect');
   },
   
   editable: false,
   eventDrop: function(event, delta) {
   var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
   var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
   $.ajax({
   url: 'http://abwemr.com/revenue/update_events.php',
   data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
   type: "POST",
   success: function(json) {
    alert("Updated Successfully");
   }
   });
   },
   eventResize: function(event) {
   var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
   var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
   $.ajax({
    url: 'http://abwemr.com/revenue/update_events.php',
    data: 'title='+ event.title+'&start='+ start +'&end='+ end +'&id='+ event.id ,
    type: "POST",
    success: function(json) {
     alert("Updated Successfully");
    }
   });

}
   
  });
  
 });

	
</script>

<div id="topbar" class="navbar  navbar-fixed-top" >
    <div class="container">  
        <div class="navbar-header ">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
           <a href="#"><img src="../images/logo.png" alt="logo" width="109" height="60" /></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
          <!--  <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#">Action</a></li>
                <li><a href="#">Another action</a></li>
                <li><a href="#">Something else here</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Nav header</li>
                <li><a href="#">Separated link</a></li>
                <li><a href="#">One more separated link</a></li>-->
              </ul>
            </li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="">Welcome: <?php echo $row_rsUsers['firstname']; ?> <?php echo $row_rsUsers['lname']; ?></a></li>
           
             <li><a href="<?php echo $logoutAction ?>"><span class="glyphicon glyphicon-lock"></span> Logout</a></li>
            <li>
			<!--<div class="btn-group">
  <button type="button" class="btn btn-info">Action</button>
  <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu">
   
   <li><a href="register.php">Revenue Tracker</a></li>
			//add php opening tag here if (isset($_SESSION['MM_UserGroup'])) 
			{
   if ($_SESSION['MM_UserGroup'] == 3) {
     echo " <li><a href=\"revenuemanager.php\">Revenue Manager</a></li>";
   }}?>
    <li class="divider"></li>
    <li><a href="<?php echo $logoutAction ?>"><span class="glyphicon glyphicon-lock"></span> Logout</a></li>
  </ul>-->
</div>
			
			</li>
           
          </ul>
        </div><!--/.nav-collapse -->
     </div>
</div>
<div class="yellowbg">
<div class="container">
<div class="row">
<div class="col-md-3">
    <strong><?php echo $row_rsAdminID['firstname']; ?> <?php echo $row_rsAdminID['lname']; ?></strong> <?php echo $row_rsAdminID['password']; ?>
  </div>
  <div class="col-md-3">
    <strong>Job Title:</strong> <?php echo $row_rsAdminID['jobtitle']; ?>
  </div>
  <div class="col-md-3">
    <strong>Employment Status:</strong> <?php echo $row_rsAdminID['employmentstatus']; ?>
  </div>
  <div class="col-md-3">
    <strong>Hire Date: </strong><?php echo date('m/d/Y', strtotime($row_rsAdminID['hiredate'])); ?>
  </div>


</div><!-- end row -->
</div>


</div>


<div class="container">
<div class="row">
  <div class="col-md-3">
    <h1>Dashboard</h1>
  </div>
  <div class="col-md-9">
   
  </div>
</div>
<div class="row">
  <div class="col-md-3">
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="media"> <a class="pull-left" href="#">
          <?php if ($row_rsUsers['photo'] != NULL) {echo "<img src=\"http://abwemr.com/employeeportal/thumbnail/$row_rsUsers[photo]\" class=\"media-object\" \"img-circle\" alt=\"user\" width=\"64\">";} else {echo "<img class=\"media-object img-circle\" src=\"images/user.jpg\" alt=\"user\" width=\"64\" height=\"64\" />";}?>
          </a><div class="media-body">
            <h5 class="media-heading"><?php echo $row_rsUsers['firstname']; ?> <?php echo $row_rsUsers['lname']; ?> </h5>
                   <button type="button"  data-toggle="modal" data-target="#timeoffrequest" class="btn btn-success btn-xs">Request Time Off</button>
              <br>
              <button type="button"  data-toggle="modal" data-target="#inventoryrequest" class="btn btn-info btn-xs">Submit Inventory Request</button>
          </div>
          <br><small><a href="updateemployee.php?adminID=<?php echo $row_rsUsers['adminID']; ?>">Edit Profile</a></small>
        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">Who's out?</div>
      <div class="panel-body">
        <table border="0" class="table">
        
          <?php do { ?>
            <tr>
              <td><small><?php echo $row_rsTimeoff['firstname']; ?> <?php echo $row_rsTimeoff['lname']; ?></small></td>
              <td align="right"><small><?php echo date('M-d',strtotime($row_rsTimeoff['startdate'])); ?> - <?php echo date('M-d',strtotime($row_rsTimeoff['enddate'])); ?></small></td>
              
            
             
              
             
            </tr>
            <?php } while ($row_rsTimeoff = mysql_fetch_assoc($rsTimeoff)); ?>
        </table>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">Company Information</div>
      <div class="panel-body">
        <ul class="company">
        
          <?php do { ?>
            
               <li><a href="http://abwemr.com/revenue/docs/<?php echo $row_rsCompInfo['filelocation']; ?>"><?php echo $row_rsCompInfo['title']; ?></a></li>
           
            <?php } while ($row_rsCompInfo = mysql_fetch_assoc($rsCompInfo)); ?>
        </ul>
      </div>
    </div>
  </div>
  <div class="col-md-9">
    <?php if($_GET['message']=='announcement'){echo "<div class=\"alert alert-dismissable alert-success\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\true\">&times;</button>Your announcement has been posted.</div>";} ?>
    <?php if($_GET['message']=='deleteannouncement'){echo "<div class=\"alert alert-dismissable alert-success\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\true\">&times;</button>The announcement has been deleted.</div>";} ?>
     <?php if($_GET['message']=='addevent'){echo "<div class=\"alert alert-dismissable alert-success\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\true\">&times;</button>The event has been added</div>";} ?>
    <?php if($_GET['success']=='message'){echo "<div class=\"alert alert-dismissable alert-success\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\true\">&times;</button>Your message has been sent.</div>";} ?>
    <?php if($_GET['success']=='timeoff'){echo "<div class=\"alert alert-dismissable alert-success\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\true\">&times;</button>Your timeoff request has been submitted.</div>";} ?>
    <?php if($_GET['success']=='inventory'){echo "<div class=\"alert alert-dismissable alert-success\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\true\">&times;</button>Your inventory request has been submitted.</div>";} ?>
    <?php if($_GET['success']=='deletemessage'){echo "<div class=\"alert alert-dismissable alert-success\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\true\">&times;</button>Your message has been deleted.</div>";} ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-md-10">
            <h3 class="panel-title"><strong>Company Announcements</strong></h3>
          </div>
          <div class="col-md-2">
         
          </div>
        </div>
      </div>
      <div class="panel-body scroll">
        <table class="table">
          <tbody>
            <?php do { ?>
              <tr>
                <td><a href="announcement.php?announcementID=<?php echo $row_rsAnnouncements['announcementID']; ?>"><?php echo $row_rsAnnouncements['subject']; ?></a><br>
                  <small>Posted By: <?php echo $row_rsAnnouncements['firstname']; ?> <?php echo $row_rsAnnouncements['lname']; ?></small></td>
                <td align="right"><?php echo date('m/d/Y', strtotime($row_rsAnnouncements['date'])); ?></td>
              </tr>
              <?php } while ($row_rsAnnouncements = mysql_fetch_assoc($rsAnnouncements)); ?>
          </tbody>
        </table>
        <div class="row">
          <div class="col-md-9"></div>
          <div class="col-md-3"></div>
        </div>
      </div>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <div class="row">
          <div class="col-md-10">
            <h3 class="panel-title"><strong>Your Inbox</strong></h3>
          </div>
          <div class="col-md-2">
             <button type="button" data-toggle="modal" data-target="#sendmessage" class="btn btn-info btn-xs pull-right">Send Message</button>
          </div>
        </div>
      </div>
      <div class="panel-body scroll">
        <?php if ($totalRows_rsInbox == 0) { // Show if recordset empty ?>
          No Messages in your inbox
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsInbox > 0) { // Show if recordset not empty ?>
  <table class="table">
    <?php do { ?>
      <tr>
        <td><a href="message.php?messageID=<?php echo $row_rsInbox['messageID'];?>"><?php echo $row_rsInbox['subject']; ?></a><br/>
          <small>From: <?php echo $row_rsInbox['firstname']; ?> <?php echo $row_rsInbox['lname']; ?> </small></td>
        <td align="right"><?php echo date("m/d/Y", strtotime($row_rsInbox['date'])); ?> <a href="deletemessage.php?messageID=<?php echo $row_rsInbox['messageID']; ?>"><span class="glyphicon glyphicon-remove"></span></a></td>
      </tr>
      <?php } while ($row_rsInbox = mysql_fetch_assoc($rsInbox)); ?>
  </table>
  <?php } // Show if recordset not empty ?>
      </div>
    </div>
    <!-- calendar 
    <div class="panel panel-default">
      <div class="panel-heading">
      <div class="row">
        <div class="col-md-8"><h3 class="panel-title"><strong>Work Schedule</strong></h3></div>
         <div class="col-md-4">
         </div>
        </div>
      </div>
      <div class="panel-body">
        <div id="calendar"></div>
      </div>
    </div>
  </div>
  -->
 <!-- Modal Windows -->
<?php include('includes/footer.php'); ?>



<!-- Modal -->
<div class="modal fade" id="sendmessage" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Send Message</h4>
      </div>
      <div class="modal-body">
      <form method="POST" action="<?php echo $editFormAction; ?>" role="form" name="sendmessage">
  <div class="form-group">
    <label for="to">To:</label>
    <select class="form-control" name="toID" id="toID">
    <option value="">Select Employee</option>
      <?php
do {  
?>
      <option value="<?php echo $row_allemployees['adminID']?>"><?php echo $row_allemployees['lname']?>, <?php echo $row_allemployees['firstname']?> </option>
      <?php
} while ($row_allemployees = mysql_fetch_assoc($allemployees));
  $rows = mysql_num_rows($allemployees);
  if($rows > 0) {
      mysql_data_seek($allemployees, 0);
	  $row_allemployees = mysql_fetch_assoc($allemployees);
  }
?>
    </select>
  </div>
  <div class="form-group">
            <label for="subject2">Subject</label>
            <input type="text" class="form-control" id="subject2" name="subject" placeholder="Subject">
            <p class="help-block">Enter the subject of your message here</p>
        </div>
  <div class="form-group">
    <label for="message">Message</label>
    <textarea class="form-control" id="message" name="message"></textarea>
  </div>
 
 
  <input type="hidden" name="adminID" value="<?php echo $row_rsAdminID['adminID']; ?>" />
  
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Send Message</button>
        <input type="hidden" name="MM_insert" value="sendmessage" />
      </form>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal -->
<div class="modal fade" id="postannouncement" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Post Announcement</h4>
      </div>
      <div class="modal-body">
        <form action="<?php echo $editFormAction; ?>" method="POST" enctype="multipart/form-data" name="announcements" role="form">
       
          <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject">
            <p class="help-block">Enter the subject of your message here</p>
          </div>
          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" class="form-control" rows="4" cols="10">
          </textarea>
          </div>
         
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="hidden" name="adminID" value="<?php echo $row_rsUsers['adminID']; ?>">
            <input type="submit" class="btn btn-primary" value="Post Announcement">
            <input type="hidden" name="MM_insert" value="announcements">
          </div>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal -->
<div class="modal fade" id="addevent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add Event</h4>
      </div>
      <div class="modal-body">
      <form method="POST" action="<?php echo $editFormAction; ?>" name="eventscalendar">
       <div class="form-group">
            <label for="subject">Title</label>
            <input type="text" class="form-control" id="subject" name="title" placeholder="Title">
            
        </div>
           <div class="form-group"> 
            <label for="start">Start Date</label>
             <input type="text" class="form-control" id="startdate"  name="start">
        </div>
            
            <div class="form-group"> 
            <label for="end">End Date</label>
             <input type="text" class="form-control" id="enddate"  name="end">
            </div>
            
              <div class="form-group"> 
            
             <input type="checkbox"  id="timeoff"  name="timeoff" value="1"> Check for Time Off Request
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="Save changes"/>
        <input type="hidden" name="MM_insert" value="eventscalendar">
      </form>
    </div>
     
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal -->
<div class="modal fade" id="timeoffrequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Timeoff Request</h4>
      </div>
      <div class="modal-body">
      <form method="POST" action="<?php echo $editFormAction; ?>" name="timeoffrequest">
    
           <div class="form-group"> 
            <label for="start">Start Date</label>
             <input type="text" class="form-control" id="startdate2"  name="start">
            </div>
            
            <div class="form-group"> 
            <label for="end">End Date</label>
             <input type="text" class="form-control" id="enddate2"  name="end">
            </div>
            
              <div class="form-group"> 
            
             <input type="hidden"  id="adminID"  name="adminID" value="<?php echo $row_rsUsers['adminID']; ?>"> 
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="Submit Request"/>
        <input type="hidden" name="MM_insert" value="timeoffrequest">
      </form>
      </div>
     
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal -->
<div class="modal fade" id="inventoryrequest" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Inventory Request</h4>
      </div>
      <div class="modal-body">
      <form method="POST" action="<?php echo $editFormAction; ?>" name="inventoryrequest">
    
           <div class="form-group"> 
            <label for="message">What do you need?</label>
             <textarea class="form-control" id="message"  name="message" rows="5" cols="6">
             
             </textarea>
        </div>
            

              <div class="form-group"> 
            
             <input type="hidden"  id="adminID"  name="adminID" value="<?php echo $row_rsUsers['adminID']; ?>"> 
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="Submit Request"/>
        <input type="hidden" name="MM_insert" value="inventoryrequest">
      </form>
      </div>
     
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->





<script language="javascript">

$('#startdate2').datepicker();
$('#enddate2').datepicker();

</script>


</div>


<!-- end container -->
<?php
mysql_free_result($rsAnnouncements);

mysql_free_result($rsTimeoff);

mysql_free_result($rsCompInfo);

mysql_free_result($rsInbox);

mysql_free_result($allemployees);

mysql_free_result($rsUsers);

mysql_free_result($rsAdminID);
?>
