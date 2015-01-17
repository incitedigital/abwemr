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
$query_Locations = "SELECT * FROM tbl_center";
$Locations = mysql_query($query_Locations, $dbc) or die(mysql_error());
$row_Locations = mysql_fetch_assoc($Locations);
$totalRows_Locations = mysql_num_rows($Locations);
?>
<?php include('includes/header.php'); ?>
<div class="container">
<div class="row"><div class="col-md-3"></div><div class="col-md-9"><h1><?php echo date("F Y"); ?> Center Combined Totals: <span class="green"> <?php echo "$ ".number_format ( $row_RSCenterTotal['Total'], 2); ?></span></h1></div></div>
<div class="row">
  <div class="col-md-3 left_col">
 <?php include('includes/menu.php'); ?>
 

  
   
  
  </div>
  <div class="col-md-9 right_col"><h3>Combined Centers</h3>
  <table class=" table  table-bordered">
  <thead>
  <tr>
  <th>Location Name</th>
  <th><img src="images/cash.png" width="28" height="18"> Cash</th>
  <th><img src="images/ccard.png" width="28" height="19"> Credit</th>
  <th><img src="images/check.png" width="28" height="19"> Checks</th>
  <th>Totals</th>
  <th></th>
  </tr>
  </thead>
  <tbody>

  <?php do { ?>
  <tr>
    <td><?php echo $row_rsRevenue['locationname']; ?></td>
    <td><?php echo "$ ".number_format ($row_rsRevenue['hundreds'] + $row_rsRevenue['fiftys'] + $row_rsRevenue['twentys']  + $row_rsRevenue['tens']  + $row_rsRevenue['fives']  + $row_rsRevenue['ones']  + $row_rsRevenue['quarters']  + $row_rsRevenue['dimes']  + $row_rsRevenue['nickels']  + $row_rsRevenue['pennys'], 2); ?></td>
    <td><?php echo "$ ".number_format($row_rsRevenue['credit'], 2); ?></td>
    <td><?php echo "$ ".number_format( $row_rsRevenue['checks'], 2); ?></td>
    <td><?php echo "$ ".number_format($row_rsRevenue['hundreds'] + $row_rsRevenue['fiftys'] + $row_rsRevenue['twentys']  + $row_rsRevenue['tens']  + $row_rsRevenue['fives']  + $row_rsRevenue['ones']  + $row_rsRevenue['quarters']  + $row_rsRevenue['dimes']  + $row_rsRevenue['nickels']  + $row_rsRevenue['pennys'] + $row_rsRevenue['credit'] + $row_rsRevenue['checks'], 2); ?></td>
	 <td><a href="centerdetails.php?centerID=<?php echo $row_rsRevenue['centerID']; ?>"><span class="glyphicon glyphicon-list"></span> Detailed</a></td>
    <?php } while ($row_rsRevenue = mysql_fetch_assoc($rsRevenue)); ?>
 
  </tr>
  </table>
  <div id="totals">

  </div>
  </div>
</div>
</div><!-- end container -->


<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($Locations);

mysql_free_result($RSCenterTotal);

mysql_free_result($rsRevenue);
?>
