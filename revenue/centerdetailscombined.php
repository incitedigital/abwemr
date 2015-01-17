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

$colname_rsCenters = "-1";
if (isset($_GET['centerID'])) {
  $colname_rsCenters = $_GET['centerID'];
}
mysql_select_db($database_dbc, $dbc);
$datestart =  date("Y-m-1");
$dateend = date("Y-m-31");
$centerID = $_GET['centerID'];
$query_rsCenters = sprintf("SELECT sum(hundreds) as hundreds, sum(fiftys)  as fiftys, sum(twentys)  as twentys, sum(tens)  as tens, sum(fives) as fives, sum(ones) as ones, sum(quarters) as quarters, sum(dimes) as dimes, sum(nickels) as nickels, sum(pennys) as pennys, sum(giftcards) as giftcards, sum(groupons) as groupons, sum(credit) as credit, sum(checks) as checks, locationname, tbl_center.centerID FROM tbl_register JOIN tbl_center ON tbl_register.centerID = tbl_center.centerID WHERE date >= '$datestart' AND date <= '$dateend' AND tbl_center.centerID = '$centerID' GROUP BY locationname", GetSQLValueString($colname_rsCenters, "int"));
$rsCenters = mysql_query($query_rsCenters, $dbc) or die(mysql_error());
$row_rsCenters = mysql_fetch_assoc($rsCenters);
$totalRows_rsCenters = mysql_num_rows($rsCenters);$colname_rsCenters = "-1";

?>
<?php include('includes/header.php'); ?>
<div class="container">
<div class="row"><div class="col-md-3"></div><div class="col-md-3"><h2><?php echo $row_rsCenters['locationname']; ?></h2></div><div class="col-md-6 datecontainer"><label for="from">From</label>
<input type="text" id="from" name="from" />
<label for="to">to</label>
<input type="text" id="to" name="to" />
 </div></div>
<div class="row">
  <div class="col-md-3 left_col">
 <?php include('includes/menu.php'); ?>
  </div>
  <div class="col-md-9 right_col">
  
   <?php do { ?>
   <div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"> 
  <div class="row">
  
  <div class="col-md-3"><span class="glyphicon glyphicon-calendar"></span> <?php echo date("m/d/Y", strtotime($row_rsCenters['date'])); ?> </div><div class="col-md-3"><strong>Starting Register:</strong> <?php echo $row_rsCenters['startingregister']; ?></div><div class="col-md-3"><strong>Cashier:</strong>  <?php echo $row_rsCenters['cashier']; ?></div><div class="col-md-3"><strong>Witness:</strong> <?php echo $row_rsCenters['witness']; ?></div>
  </div>
  </div>
  <div class="panel-body">
  <div class="row">
  <div class="col-md-4">
 
  
  <table class="table">
  <tr><td><img src="images/cash.png" alt="cash" width="32" height="20" /> 100's</td><td> <span class="badge pull-right"><?php echo $row_rsCenters['hundreds']; ?></span></td></tr>
  <tr><td><img src="images/cash.png" alt="cash" width="32" height="20" />  50's</td><td><span class="badge pull-right"> <?php echo $row_rsCenters['fiftys']; ?></span></td></tr>
  <tr><td><img src="images/cash.png" alt="cash" width="32" height="20" />  20's</td><td><span class="badge pull-right"> <?php echo $row_rsCenters['twentys']; ?></span></td></tr>
  <tr><td><img src="images/cash.png" alt="cash" width="32" height="20" />  10's</td><td><span class="badge pull-right"> <?php echo $row_rsCenters['tens']; ?></span></td></tr>
  <tr><td><img src="images/cash.png" alt="cash" width="32" height="20" />  5's </td><td><span class="badge pull-right"><?php echo $row_rsCenters['fives']; ?></span></td></tr>
  <tr><td><img src="images/cash.png" alt="cash" width="32" height="20" />  1's </td><td><span class="badge pull-right"><?php echo $row_rsCenters['ones']; ?></span></td></tr>
  </table>
  
  </div>
  <div class="col-md-4">
	<table class="table">
  <tr><td>.25's</td><td><span class="badge pull-right"> <?php echo $row_rsCenters['quarters']; ?></span></td></tr>
  <tr><td>.10's</td><td><span class="badge pull-right"> <?php echo $row_rsCenters['dimes']; ?></span></td></tr>
  <tr><td>.05's</td><td><span class="badge pull-right"> <?php echo $row_rsCenters['nickels']; ?></span></td></tr>
  <tr><td>.01's</td><td><span class="badge pull-right"> <?php echo $row_rsCenters['pennys']; ?></span></td></tr>
  <tr><td>Cash </td><td><?php echo "$ ".number_format($row_rsCenters['hundreds'] * 100 + $row_rsCenters['fiftys'] * 50 + $row_rsCenters['twentys'] *20  + $row_rsCenters['tens'] *10 + $row_rsCenters['fives'] * 5 + $row_rsCenters['ones']  + $row_rsCenters['quarters'] * .25 + $row_rsCenters['dimes'] * .10 + $row_rsCenters['nickels'] * .05 + $row_rsCenters['pennys'] * .01, 2);?></td></tr>
  </table>
  </div>
  
  <div class="col-md-4">
 
	<div class="well">
<h3>Grand Total <?php echo "$ ".number_format($row_rsCenters['hundreds'] * 100 + $row_rsCenters['fiftys'] * 50 + $row_rsCenters['twentys'] *20  + $row_rsCenters['tens'] *10 + $row_rsCenters['fives'] * 5 + $row_rsCenters['ones']  + $row_rsCenters['quarters'] * .25 + $row_rsCenters['dimes'] * .10 + $row_rsCenters['nickels'] * .05 + $row_rsCenters['pennys'] * .01 + $row_rsCenters['credit'] + $row_rsCenters['checks'] + $row_rsCenters['groupons'] + $row_rsCenters['giftcards'], 2); ?></h3>
  </div>
  </table>
  </div>
     </div>
     <div class="row">
     <div class="panel-footer">
		<div class="col-md-3"><img src="images/giftcard.png" alt="giftcard" width="32" height="22" /> <strong>Gift Cards</strong> <?php echo "$ " .number_format( $row_rsCenters['giftcards'], 2); ?></div>
        
        <div class="col-md-3"> <img src="images/groupon.png" width="23" height="22"> <strong> Groupons </strong><?php echo "$ " .number_format($row_rsCenters['groupons'],2); ?></div>
        <div class="col-md-3"> <img src="images/ccard.png" alt="ccard" width="" height="" /> <strong> Credit</strong> <?php echo "$ " .number_format( $row_rsCenters['credit'], 2); ?></div>
       <div class="col-md-3"> <img src="images/check.png" alt="check" width="" height="" /> <strong> Checks</strong>  <?php echo "$ " .number_format( $row_rsCenters['checks'], 2); ?></div>
 </div>
  </div>
       
       
       
      
   
  </div>

  	</div>

        <?php } while ($row_rsCenters = mysql_fetch_assoc($rsCenters)); ?>

  
</div>
</div><!-- end container -->


<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsCenters);
?>
