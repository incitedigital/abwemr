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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "announcements")) {
  $insertSQL = sprintf("INSERT INTO tbl_announcements (subject, message, attachment, adminID) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['subject'], "text"),
                       GetSQLValueString($_POST['message'], "text"),
                       GetSQLValueString($_POST['attachment'], "text"),
                       GetSQLValueString($_POST['adminID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "employeemanager.php?success=announcement";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$maxRows_rsAnnouncements = 10;
$pageNum_rsAnnouncements = 0;
if (isset($_GET['pageNum_rsAnnouncements'])) {
  $pageNum_rsAnnouncements = $_GET['pageNum_rsAnnouncements'];
}
$startRow_rsAnnouncements = $pageNum_rsAnnouncements * $maxRows_rsAnnouncements;

mysql_select_db($database_dbc, $dbc);
$query_rsAnnouncements = "SELECT * FROM tbl_announcements";
$query_limit_rsAnnouncements = sprintf("%s LIMIT %d, %d", $query_rsAnnouncements, $startRow_rsAnnouncements, $maxRows_rsAnnouncements);
$rsAnnouncements = mysql_query($query_limit_rsAnnouncements, $dbc) or die(mysql_error());
$row_rsAnnouncements = mysql_fetch_assoc($rsAnnouncements);

if (isset($_GET['totalRows_rsAnnouncements'])) {
  $totalRows_rsAnnouncements = $_GET['totalRows_rsAnnouncements'];
} else {
  $all_rsAnnouncements = mysql_query($query_rsAnnouncements);
  $totalRows_rsAnnouncements = mysql_num_rows($all_rsAnnouncements);
}
$totalPages_rsAnnouncements = ceil($totalRows_rsAnnouncements/$maxRows_rsAnnouncements = 10);
$pageNum_rsAnnouncements = 0;
if (isset($_GET['pageNum_rsAnnouncements'])) {
  $pageNum_rsAnnouncements = $_GET['pageNum_rsAnnouncements'];
}
$startRow_rsAnnouncements = $pageNum_rsAnnouncements * $maxRows_rsAnnouncements;

mysql_select_db($database_dbc, $dbc);
$query_rsAnnouncements = "SELECT tbl_announcements.*, firstname,lname FROM tbl_announcements JOIN tbl_admin on tbl_announcements.adminID = tbl_admin.adminID";
$query_limit_rsAnnouncements = sprintf("%s LIMIT %d, %d", $query_rsAnnouncements, $startRow_rsAnnouncements, $maxRows_rsAnnouncements);
$rsAnnouncements = mysql_query($query_limit_rsAnnouncements, $dbc) or die(mysql_error());
$row_rsAnnouncements = mysql_fetch_assoc($rsAnnouncements);

if (isset($_GET['totalRows_rsAnnouncements'])) {
  $totalRows_rsAnnouncements = $_GET['totalRows_rsAnnouncements'];
} else {
  $all_rsAnnouncements = mysql_query($query_rsAnnouncements);
  $totalRows_rsAnnouncements = mysql_num_rows($all_rsAnnouncements);
}
$totalPages_rsAnnouncements = ceil($totalRows_rsAnnouncements/$maxRows_rsAnnouncements)-1;

$colname_rsMessage = "-1";
if (isset($_GET['announcementID'])) {
  $colname_rsMessage = $_GET['announcementID'];
}


$announceID = $_GET['announcementID'];
mysql_select_db($database_dbc, $dbc);
$query_rsMessage = "SELECT * FROM tbl_announcements WHERE announcementID = '$announceID'";
$rsMessage = mysql_query($query_rsMessage, $dbc) or die(mysql_error());
$row_rsMessage = mysql_fetch_assoc($rsMessage);
$totalRows_rsMessage = mysql_num_rows($rsMessage);
?>

<?php require_once('ScriptLibrary/incPureUpload.php'); ?>
<?php 
//*** Pure PHP File Upload 3.0.1
// Process form announcements
$ppu = new pureFileUpload();
$ppu->nameConflict = "uniq";
$ppu->storeType = "file";
$uploadattachment = $ppu->files("attachment");
$uploadattachment->path = "messageattachments";
$ppu->redirectUrl = "";
$ppu->checkVersion("3.0.1");
$ppu->doUpload();
?>

<?php include('includes/headeradmin.php'); ?>


<div class="container">

<div class="row">
<div class="col-md-3"></div>
<div class="col-md-9">

</div>
</div>
<br>

<div class="row">

<div class="col-md-12">

<div class="wrapper">
<div class="row">
<div class="col-md-12">
<ol class="breadcrumb">
  <li><a href="employeemanager.php">Dashboard</a></li>
  
  <li class="active"><?php echo $row_rsMessage['subject']; ?></li>
</ol>
</div>

</div>
<h4><strong><?php echo $row_rsMessage['subject']; ?></strong></h4>

<p>

<?php echo $row_rsMessage['message']; ?>

<br><br>
<?php if ($row_rsMessage['attachment'] != NULL || $row_rsMessage['attachment'] != "" ) {echo  "<span class='glyphicon glyphicon-paperclip'></span> <a href=\"messageattachments/$row_rsMessage[attachment]\" target=\"blank\"> $row_rsMessage[attachment]</a>";}?>
</p>

</div>


  </div>
  
</div>




</div> <!-- end container -->

<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsAnnouncements);

mysql_free_result($rsMessage);
?>
