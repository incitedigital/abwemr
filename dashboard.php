<?php session_start(); ?>
<?php include('includes/header.php'); ?>
<?php

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
<?php require_once('ScriptLibrary/dmxPaginator.php'); ?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "add_new_patient")) {
	$dob = date('Y-m-d', strtotime($_POST['dob']));
  $insertSQL = sprintf("INSERT INTO tbl_patient (username, fname, lname, address1, address2, city, `state`, zip, email, homephone, mobilephone, dob, sex) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,'$dob')",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['fname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['address1'], "text"),
                       GetSQLValueString($_POST['address2'], "text"),
                       GetSQLValueString($_POST['city'], "text"),
                       GetSQLValueString($_POST['state'], "text"),
                       GetSQLValueString($_POST['zip'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['homephone'], "text"),
                       GetSQLValueString($_POST['mobilephone'], "text"),
                       GetSQLValueString($_POST['sex'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());


$patientquery = "INSERT into tbl_activity (username, action, firstname, lastname,  date,  category) VALUES ('$_SESSION[MM_Username]', 'added new user', '$_POST[fname]', '$_POST[lname]', CURDATE(), 'new user')";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());

  $insertGoTo = "dashboard.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
	
	
	
	
	
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dbc, $dbc);

$query_rsIncomplete = "SELECT count('queueID') as count FROM tbl_queue WHERE status = 1 AND date= CURDATE() AND tbl_queue.centerID = '$_SESSION[centerID]'";
$rsIncomplete = mysql_query($query_rsIncomplete, $dbc) or die(mysql_error());
$row_rsIncomplete = mysql_fetch_assoc($rsIncomplete);
$totalRows_rsIncomplete = mysql_num_rows($rsIncomplete);


$colname_rsAdmin = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsAdmin = $_SESSION['MM_Username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsAdmin = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsAdmin, "text"));
$rsAdmin = mysql_query($query_rsAdmin, $dbc) or die(mysql_error());
$row_rsAdmin = mysql_fetch_assoc($rsAdmin);
$totalRows_rsAdmin = mysql_num_rows($rsAdmin);

mysql_select_db($database_dbc, $dbc);
$query_rsqueue = "SELECT tbl_queue.patientID,fname,tbl_patient.lname,firstname FROM tbl_queue JOIN tbl_patient on tbl_queue.patientID = tbl_patient.patientID JOIN tbl_admin on tbl_admin.username = tbl_queue.username WHERE status = 1 AND date = CURDATE() AND tbl_queue.centerID = '$_SESSION[centerID]'";
$rsqueue = mysql_query($query_rsqueue, $dbc) or die(mysql_error());
$row_rsqueue = mysql_fetch_assoc($rsqueue);
$totalRows_rsqueue = mysql_num_rows($rsqueue);

$maxRows_rsActivity = 10;
$pageNum_rsActivity = 0;
if (isset($_GET['pageNum_rsActivity'])) {
  $pageNum_rsActivity = $_GET['pageNum_rsActivity'];
}
$startRow_rsActivity = $pageNum_rsActivity * $maxRows_rsActivity;

mysql_select_db($database_dbc, $dbc);
$query_rsActivity = "SELECT activityID, tbl_activity.firstname as 'First Name', tbl_activity.lastname,action,category,date,tbl_admin.firstname FROM tbl_activity LEFT JOIN tbl_admin on tbl_activity.username = tbl_admin.username WHERE centerID = '$_SESSION[centerID]' ORDER BY activityID DESC";
$query_limit_rsActivity = sprintf("%s LIMIT %d, %d", $query_rsActivity, $startRow_rsActivity, $maxRows_rsActivity);
$rsActivity = mysql_query($query_limit_rsActivity, $dbc) or die(mysql_error());
$row_rsActivity = mysql_fetch_assoc($rsActivity);

if (isset($_GET['totalRows_rsActivity'])) {
  $totalRows_rsActivity = $_GET['totalRows_rsActivity'];
} else {
  $all_rsActivity = mysql_query($query_rsActivity);
  $totalRows_rsActivity = mysql_num_rows($all_rsActivity);
}
$totalPages_rsActivity = ceil($totalRows_rsActivity/$maxRows_rsActivity)-1;

mysql_select_db($database_dbc, $dbc);
$query_rsAll = "SELECT count(queueID) as Total FROM tbl_queue WHERE date = Now() AND tbl_queue.centerID = '$_SESSION[centerID]'";
$rsAll = mysql_query($query_rsAll, $dbc) or die(mysql_error());
$row_rsAll = mysql_fetch_assoc($rsAll);
$totalRows_rsAll = mysql_num_rows($rsAll);

mysql_select_db($database_dbc, $dbc);
$query_rsQueueResolved = "SELECT tbl_queue.patientID,fname,tbl_patient.lname,firstname FROM tbl_queue JOIN tbl_patient on tbl_queue.patientID = tbl_patient.patientID JOIN tbl_admin on tbl_admin.username = tbl_queue.username WHERE status = 0 AND date = CURDATE() AND tbl_queue.centerID = '$_SESSION[centerID]'";
$rsQueueResolved = mysql_query($query_rsQueueResolved, $dbc) or die(mysql_error());
$row_rsQueueResolved = mysql_fetch_assoc($rsQueueResolved);
$totalRows_rsQueueResolved = mysql_num_rows($rsQueueResolved);

mysql_select_db($database_dbc, $dbc);
$query_rsResolvedCount = "SELECT count(queueID) as Total FROM tbl_queue WHERE date = CURDATE() AND Status = 0 AND tbl_queue.centerID = '$_SESSION[centerID]'";
$rsResolvedCount = mysql_query($query_rsResolvedCount, $dbc) or die(mysql_error());
$row_rsResolvedCount = mysql_fetch_assoc($rsResolvedCount);
$totalRows_rsResolvedCount = mysql_num_rows($rsResolvedCount);

?>



<div class="col-xs-12">
<h2 class="title">Activity <?php echo $row_rsIncomplete['count']; ?> Patients in Queue</h2>  
<img src="images/peopleicon.jpg" align="top" alt="peopleicon" width="32" height="32" /><span class="queuetitle">Patient Lobby Queue</span>
<br><br>
			<!--	<button id="find-user">Find Patient</button>
					<button id="create-user">Add New Patient</button>	-->
<!-- Nav tabs -->
<ul class="nav nav-tabs" role="tablist">
  <li class="active"><a href="#home" role="tab" data-toggle="tab">Incomplete <span class="badge"><?php echo $row_rsIncomplete['count']; ?></span></a></li>
  <li><a href="#profile" role="tab" data-toggle="tab">Resolved <span class="badge"><?php echo $row_rsResolvedCount['Total']; ?></span></a></li>
</ul>

 
 

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="home"> <?php if ($totalRows_rsqueue == 0) { // Show if recordset empty ?>
                No resolved records.
  <?php } // Show if recordset empty ?>
              <?php if ($totalRows_rsqueue > 0) { // Show if recordset not empty ?>
  <ul class="queue">
    
    <?php do { ?>
      <li><a href="viewpatient.php?patientID=<?php echo $row_rsqueue['patientID']; ?>">
        <span class="fname"><?php echo $row_rsqueue['fname']; ?></span>
        <span class="lname"><?php echo $row_rsqueue['lname']; ?> </span>
        <span class="adminname"><img src="images/icon.png" width="13" height="14" align="left" /><?php echo  $row_rsqueue['firstname']; ?> </span>
        </a>
      </li>
      <?php } while ($row_rsqueue = mysql_fetch_assoc($rsqueue)); ?>
  </ul>
  <?php } // Show if recordset not empty ?></div>
  
  
  <div class="tab-pane" id="profile"> <?php if ($totalRows_rsQueueResolved == 0) { // Show if recordset empty ?>
                No resolved records.
  <?php } // Show if recordset empty ?>
              <?php if ($totalRows_rsQueueResolved > 0) { // Show if recordset not empty ?>
  <ul class="queue">
    
    <?php do { ?>
      <li><a href="viewpatient.php?patientID=<?php echo $row_rsQueueResolved['patientID']; ?>">
        <span class="fname"><?php echo $row_rsQueueResolved['fname']; ?></span>
        <span class="lname"><?php echo $row_rsQueueResolved['lname']; ?></span>
        <span class="adminname"><img src="images/icon.png" width="13" height="14" align="left" /><?php echo $row_rsQueueResolved['firstname']; ?></span>
        </a>
      </li>
      <?php } while ($row_rsQueueResolved = mysql_fetch_assoc($rsQueueResolved)); ?>
  </ul>
  <?php } // Show if recordset not empty ?></div>

</div>





<div class="well well-sm"><span class="glyphicon glyphicon-cog"></span> Activity Across your Queue</div>
<?php if ($totalRows_rsActivity == 0) { // Show if recordset empty ?>
  No Activity found at this location
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsActivity > 0) { // Show if recordset not empty ?>
  <table  width="100%"  class="table table-striped activity">
  <thead>
    <tr>
      <th>ActivityID</th>
      <th>Category</th>
      <th>Activity</th>
      <th>Date</th>
      
      
    </tr>
    </thead>
    <tbody>
    <?php do { ?>
      <tr>
        <td><?php echo $row_rsActivity['activityID']; ?></td>
        <td><?php echo $row_rsActivity['category']; ?></td>
        <td><?php echo $row_rsActivity['firstname']; ?> <?php echo $row_rsActivity['action']; ?> <?php echo $row_rsActivity['First Name']; ?> <?php echo $row_rsActivity['lastname']; ?></td>
        <td><?php echo date('m/d/Y', strtotime($row_rsActivity['date'])); ?></td>
      </tr>
      <?php } while ($row_rsActivity = mysql_fetch_assoc($rsActivity)); ?>
    </tbody>
  </table>
  <div id="paginate">
    <?php
// DMXzone Paginator PHP 1.0.2
$pag1 = new dmxPaginator();
$pag1->recordsetName = "rsActivity";
$pag1->rowsTotal = $totalRows_rsActivity;
$pag1->showNextPrev = true;
$pag1->showFirstLast = true;
$pag1->outerLinks = 1;
$pag1->pageNumSeparator = "...";
$pag1->adjacentLinks = 2;
$pag1->rowsPerPage = $maxRows_rsActivity;
$pag1->prevLabel = "‹";
$pag1->nextLabel = "›";
$pag1->firstLabel = "‹‹";
$pag1->lastLabel = "››";
$pag1->addPagination();
?>
  </div>
  <?php } // Show if recordset not empty ?>
</div>


</div>






<?php include ('includes/footer.php'); ?>
