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
$totalPages_rsAnnouncements = ceil($totalRows_rsAnnouncements/$maxRows_rsAnnouncements)-1;
?>
<?php include('includes/headeradmin.php'); ?>
<?php include('includes/headermenu.php'); ?>

<div class="row">

<div class="col-md-3">
<div class="panel panel-default">
 
  <div class="panel-body">
   <div class="media">
  <a class="pull-left" href="#">
    <?php if ($row_rsUsers['photo'] != NULL) {echo "<img src=\"$row_rsUsers[photo]\" class=\"media-object\" alt=\"user\" width=\"64\" height=\"64\">";} else {echo "<img class=\"media-object\" src=\"images/user.jpg\" alt=\"user\" width=\"64\" height=\"64\" />";}?></a>
  <div class="media-body">
    <h4 class="media-heading"><?php echo $row_rsUsers['firstname']; ?> <?php echo $row_rsUsers['lname']; ?></h4>
    
  </div>
</div>
  </div>
  
</div>


<div class="panel panel-default">
  <div class="panel-heading">Who's out?</div>
  <div class="panel-body">
   
 
</div>
 
   
</div>


<div class="panel panel-default">
  <div class="panel-heading">Company Information</div>
  <div class="panel-body">
   
 
</div>
 
   
</div>


</div>
<div class="col-md-9">
<?php if($_GET['success']=='announcement'){echo "<div class=\"alert alert-dismissable alert-success\"> <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\true\">&times;</button>Your announcement has been posted.</div>";} ?>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><strong>Company Announcements</strong></h3>
  </div>
 
  <div class="panel-body">
    <table class="table">
    
      <tbody>
      <?php do { ?>
        <tr>
         <td><a href="announcement.php?announcementID=<?php echo $row_rsAnnouncements['announcementID']; ?>"><?php echo $row_rsAnnouncements['subject']; ?></a></td>
         <td align="right"><?php echo date('m/d/Y', strtotime($row_rsAnnouncements['date'])); ?></td>
        </tr>
        <?php } while ($row_rsAnnouncements = mysql_fetch_assoc($rsAnnouncements)); ?>
        </tbody>
    </table>
<div class="row">
  <div class="col-md-9">
  </div>
  
  <div class="col-md-3">
    
    
  </div>
</div>
    </div>
    
    </div>
   

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><strong>Your Inbox</strong></h3>
  </div>
 
  <div class="panel-body">
    Panel content
  </div>
 
</div>


<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><strong><?php echo date('F'); ?> Work Schedule</strong></h3>
  </div>
 
  <div class="panel-body">
    <div id="calendar"></div>
  </div>
 
</div>


</div> <!-- end row -->


</div> <!-- end container -->

<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsAnnouncements);
?>
