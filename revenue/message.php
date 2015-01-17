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

$colname_rsMessage = "-1";
if (isset($_GET['messageID'])) {
  $colname_rsMessage = $_GET['messageID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsMessage = sprintf("SELECT subject, message FROM tbl_message WHERE messageID = %s", GetSQLValueString($colname_rsMessage, "int"));
$rsMessage = mysql_query($query_rsMessage, $dbc) or die(mysql_error());
$row_rsMessage = mysql_fetch_assoc($rsMessage);
$totalRows_rsMessage = mysql_num_rows($rsMessage);
?>
<?php include('includes/headeradmin.php'); ?>


<div class="container">

<div class="row">
<div class="col-md-3"></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>
<br>


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



</div>


  </div>
  
</div>




</div> <!-- end container -->

<?php include('includes/footer.php'); ?>
<?php


mysql_free_result($rsMessage);
?>

