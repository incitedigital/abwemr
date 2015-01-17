<?php session_start(); 
if($_SESSION['MM_UserGroup'] != 3)
	{
		header("Location: loginfailedmgr.php");	
	} ?>
<?php require_once('Connections/dbc.php'); ?>
<?php include('includes/FusionCharts/dynamic/DWFChart.inc.php'); ?>
<?php
// fusion chart # FusionChart4 data include
 include('includes/FusionCharts/dynamic/data/fc_FusionChart4_data.php');

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

mysql_select_db($database_dbc, $dbc);
$query_rsCenterTotalsbyMonth = "SELECT locationname, date, ((IFNULL(hundreds,0) * 100) + (IFNULL(fiftys,0) * 50) + (IFNULL(twentys,0) *20) + (IFNULL(tens,0) *10) + (IFNULL(fives,0) *5) + (IFNULL(ones,0)*1) + (IFNULL(quarters,0)*.25) + (IFNULL(dimes,0)*.10) + (IFNULL(nickels,0)*.05) + (IFNULL(pennys,0)*.01) + IFNULL(credit,0) + IFNULL(groupons,0) + IFNULL(giftcards,0) + IFNULL(checks,0) ) as Totals FROM tbl_register JOIN tbl_center on tbl_register.centerID = tbl_center.centerID";
$rsCenterTotalsbyMonth = mysql_query($query_rsCenterTotalsbyMonth, $dbc) or die(mysql_error());
$row_rsCenterTotalsbyMonth = mysql_fetch_assoc($rsCenterTotalsbyMonth);
$totalRows_rsCenterTotalsbyMonth = mysql_num_rows($rsCenterTotalsbyMonth);
?>

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
	
  $logoutGoTo = "emrmanager.php";
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

mysql_select_db($database_dbc, $dbc);
$query_rsUsers = "SELECT * FROM tbl_admin WHERE username = '$_SESSION[MM_Username]'";
$rsUsers = mysql_query($query_rsUsers, $dbc) or die(mysql_error());
$row_rsUsers = mysql_fetch_assoc($rsUsers);
$totalRows_rsUsers = mysql_num_rows($rsUsers);

mysql_select_db($database_dbc, $dbc);
$query_rsLocations = "SELECT * FROM tbl_center WHERE centerID = '$_SESSION[centerID]'";
$rsLocations = mysql_query($query_rsLocations, $dbc) or die(mysql_error());
$row_rsLocations = mysql_fetch_assoc($rsLocations);
$totalRows_rsLocations = mysql_num_rows($rsLocations);
 
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

mysql_select_db($database_dbc, $dbc);
$query_Locations = "SELECT * FROM tbl_center";
$Locations = mysql_query($query_Locations, $dbc) or die(mysql_error());
$row_Locations = mysql_fetch_assoc($Locations);
$totalRows_Locations = mysql_num_rows($Locations);

// fusion chart #FusionChart4 dynamic filter data
 

// fusion chart recordset

mysql_select_db($database_dbc, $dbc);
$fcquery_FusionChart4_rs0 = "SELECT sum(totalsbymonth.Totals) AS xtd_value,  SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2)  AS xtd_date, ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) AS xtd_date_part,  totalsbymonth.date  FROM  totalsbymonth WHERE  ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) >= 1 AND  ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) <= 12 AND  totalsbymonth.locationname LIKE '%95%' GROUP BY xtd_date";
$FusionChart4_rs0 = mysql_query($fcquery_FusionChart4_rs0, $dbc) or die(mysql_error());
$row_FusionChart4_rs0 = mysql_fetch_assoc($FusionChart4_rs0);
$totalRows_FusionChart4_rs0 = mysql_num_rows($FusionChart4_rs0);

// fusion chart recordset

mysql_select_db($database_dbc, $dbc);
$fcquery_FusionChart4_rs1 = "SELECT sum(totalsbymonth.Totals) AS xtd_value,  SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2)  AS xtd_date, ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) AS xtd_date_part,  totalsbymonth.date  FROM  totalsbymonth WHERE  ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) >= 1 AND  ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) <= 12 AND  totalsbymonth.locationname LIKE '%Edge%' GROUP BY xtd_date";
$FusionChart4_rs1 = mysql_query($fcquery_FusionChart4_rs1, $dbc) or die(mysql_error());
$row_FusionChart4_rs1 = mysql_fetch_assoc($FusionChart4_rs1);
$totalRows_FusionChart4_rs1 = mysql_num_rows($FusionChart4_rs1);

// fusion chart recordset

mysql_select_db($database_dbc, $dbc);
$fcquery_FusionChart4_rs2 = "SELECT sum(totalsbymonth.Totals) AS xtd_value,  SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2)  AS xtd_date, ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) AS xtd_date_part,  totalsbymonth.date  FROM  totalsbymonth WHERE  ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) >= 1 AND  ( SUBSTRING(CAST(totalsbymonth.date AS char), 6, 2) ) <= 12 AND  totalsbymonth.locationname LIKE '%michigan%' GROUP BY xtd_date";
$FusionChart4_rs2 = mysql_query($fcquery_FusionChart4_rs2, $dbc) or die(mysql_error());
$row_FusionChart4_rs2 = mysql_fetch_assoc($FusionChart4_rs2);
$totalRows_FusionChart4_rs2 = mysql_num_rows($FusionChart4_rs2);

$dFCFusionChart4 = new DWFChart("FusionChart4", "MSLine", "includes/FusionCharts/charts/", 800, 400, "", "", "", "", "", "");
$dFCFusionChart4->setVersion("1.1.1");
$dFCFusionChart4->setConfigXML($FusionChart4_dataXML);
$dFCFusionChart4->setCategory($NoRecordset, "", "useExport=true", "date");
$dFCFusionChart4->addSeries($FusionChart4_rs0,  "totalsbymonth.Totals", "series_date_display=month_lo;series_value_col=totalsbymonth.Totals;series_table=totalsbymonth;full_date=0;series_date_col=totalsbymonth.date;series_calculation=sum;series_group_type=month;min_val=1;series_date_format=yyyy-mm-dd;max_val=12", "color='ff6600' seriesName='95th%20Beverly'", "default", "'totalsbymonth.locationname,contains,OTU='");
$dFCFusionChart4->addSeries($FusionChart4_rs1,  "totalsbymonth.Totals", "series_date_display=month_lo;series_value_col=totalsbymonth.Totals;series_table=totalsbymonth;full_date=0;series_date_col=totalsbymonth.date;series_calculation=sum;series_group_type=month;min_val=1;series_date_format=yyyy-mm-dd;max_val=12", "color='339999' seriesName='Edgewater'", "default", "'totalsbymonth.locationname,contains,RWRnZQ=='");
$dFCFusionChart4->addSeries($FusionChart4_rs2,  "totalsbymonth.Totals", "series_date_display=month_lo;series_value_col=totalsbymonth.Totals;series_table=totalsbymonth;full_date=0;series_date_col=totalsbymonth.date;series_calculation=sum;series_group_type=month;min_val=1;series_date_format=yyyy-mm-dd;max_val=12", "color='cc3366' seriesName='Michigan%20Ave'", "default", "'totalsbymonth.locationname,contains,bWljaGlnYW4='");
$dFCFusionChart4->setOrdering('Category', 'asc');
$dFCFusionChart4->prepareData();
 

mysql_select_db($database_dbc, $dbc);
if(isset($_POST['from'])){$datestart = date("'Y-m-d'", strtotime($_POST['from'])); } else {$datestart = date("'Y-m-d'");}
if(isset($_POST['to'])){$dateend = date("'Y-m-d'", strtotime($_POST['to'])); } else {$dateend = date("'Y-m-d'");}
$query_RSCenterTotal = "SELECT sum(hundreds) * 100 as hundreds, sum(fiftys) * 50 as fiftys, sum(twentys) *20 as twentys, sum(tens) *10 as tens, sum(fives)*5 as fives, sum(ones)*1 as ones, sum(quarters)*.25 as quarters, sum(dimes)*.10 as dimes, sum(nickels)*.05 as nickels, sum(pennys)*.01 as pennys, sum(credit) as credit, sum(checks) as checks FROM tbl_register WHERE date BETWEEN $datestart AND $dateend";
$RSCenterTotal = mysql_query($query_RSCenterTotal, $dbc) or die(mysql_error());
$row_RSCenterTotal = mysql_fetch_assoc($RSCenterTotal);
$totalRows_RSCenterTotal = mysql_num_rows($RSCenterTotal);

$maxRows_rsRevenue = 10;
$pageNum_rsRevenue = 0;
if (isset($_GET['pageNum_rsRevenue'])) {
  $pageNum_rsRevenue = $_GET['pageNum_rsRevenue'];
}
$startRow_rsRevenue = $pageNum_rsRevenue * $maxRows_rsRevenue;

mysql_select_db($database_dbc, $dbc);
if(isset($_POST['from'])){$datestart = date("'Y-m-d'", strtotime($_POST['from'])); } else {$datestart = date("'Y-m-d'");}
if(isset($_POST['to'])){$dateend = date("'Y-m-d'", strtotime($_POST['to'])); } else {$dateend = date("'Y-m-d'");}
$query_rsRevenue = "SELECT sum(hundreds) * 100 as hundreds, sum(fiftys) * 50 as fiftys, sum(twentys) *20 as twentys, sum(tens) *10 as tens, sum(fives)*5 as fives, sum(ones)*1 as ones, sum(quarters)*.25 as quarters, sum(dimes)*.10 as dimes, sum(nickels)*.05 as nickels, sum(pennys)*.01 as pennys, sum(giftcards) as giftcards, sum(groupons) as groupons, sum(credit) as credit, sum(checks) as checks, locationname, tbl_center.centerID FROM tbl_register JOIN tbl_center ON tbl_register.centerID = tbl_center.centerID WHERE date BETWEEN $datestart AND $dateend GROUP BY locationname";
$query_limit_rsRevenue = sprintf("%s LIMIT %d, %d", $query_rsRevenue, $startRow_rsRevenue, $maxRows_rsRevenue);
$rsRevenue = mysql_query($query_limit_rsRevenue, $dbc) or die(mysql_error());
$row_rsRevenue = mysql_fetch_assoc($rsRevenue);

if (isset($_GET['totalRows_rsRevenue'])) {
  $totalRows_rsRevenue = $_GET['totalRows_rsRevenue'];
} else {
  $all_rsRevenue = mysql_query($query_rsRevenue);
  $totalRows_rsRevenue = mysql_num_rows($all_rsRevenue);
}
$totalPages_rsRevenue = ceil($totalRows_rsRevenue/$maxRows_rsRevenue)-1;
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
<link rel="stylesheet" type="text/css" href="style/validationEngine.jquery.css">
<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300' rel='stylesheet' type='text/css'>
<link href='http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/cupertino/jquery-ui.min.css' rel='stylesheet' type="text/css">
<!--jQuery References-->
<script src="http://code.jquery.com/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.min.js" type="text/javascript"></script>
<!--Theme-->
<link href="http://cdn.wijmo.com/themes/aristo/jquery-wijmo.css" rel="stylesheet" type="text/css" />
<!--Wijmo Widgets CSS-->
<link href="http://cdn.wijmo.com/jquery.wijmo-pro.all.3.20132.15.min.css" rel="stylesheet" type="text/css" />
<!--Wijmo Widgets JavaScript-->
<script src="http://cdn.wijmo.com/jquery.wijmo-open.all.3.20132.15.min.js" type="text/javascript"></script>
<script src="http://cdn.wijmo.com/jquery.wijmo-pro.all.3.20132.15.min.js" type="text/javascript"></script>
<script src="http://cdn.wijmo.com/interop/wijmo.data.ajax.3.20132.15.js" type="text/javascript"></script>
<script type="text/javascript" src="includes/FusionCharts/dynamic/js/FusionCharts.js"></script>
<script type="text/javascript" src="includes/FusionCharts/dynamic/js/FusionChartsExportComponent.js"></script>
</head>
<body>


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
<?php
mysql_free_result($rsUsers);

mysql_free_result($rsLocations);
?>

<div class="container">

<div class="row">
<div class="col-md-3"> <?php include('includes/menu.php'); ?></div>



<div class="col-md-9"><h1>

<?php if(isset($_POST['from'])){echo date('M d,Y', strtotime($_POST['from'])) ." - " . date('M d,Y', strtotime($_POST['to']));} else {echo date("F d, Y");} ?>

Center Combined Totals: <span class="green"> <?php echo "$ ".number_format ( $row_RSCenterTotal['hundreds']+ $row_RSCenterTotal['fiftys']+ $row_RSCenterTotal['twentys']+ $row_RSCenterTotal['tens']+ $row_RSCenterTotal['fives']+ $row_RSCenterTotal['ones']+ $row_RSCenterTotal['quarters']+ $row_RSCenterTotal['dimes']+$row_RSCenterTotal['nickels']+ $row_RSCenterTotal['pennys']+ $row_RSCenterTotal['checks']+ $row_RSCenterTotal['credit'], 2); ?></span></h1>  
<form class="form-inline" name="datesearch" id="datesearch" method="post" action="">
<div class="form-group"><label for="from">From</label>
<input type="text" id="from" name="from" class="form-control" placeholder="from" value="" /></div>
<div class="form-group">
<label for="to">To</label>
<input type="text" id="to" name="to" class="form-control" placeholder="to" value="" />
</div>
<input type="submit" name="submit" class="btn btn-default search" value="Search"/>
</form>

 
    <?php if ($totalRows_rsRevenue == 0) { // Show if recordset empty ?>
      <br><br>No data found<br><br>
  <?php } // Show if recordset empty ?>
<?php if ($totalRows_rsRevenue > 0) { // Show if recordset not empty ?>
  <h4>Combined Centers</h4>
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
   <?php } // Show if recordset not empty ?>
  <div id="totals">
  <div id="whitebar"></div>
  <?php
// (FCChart Begin) #FusionChart4
renderFusionChart($dFCFusionChart4, 800, 400);
// #FusionChart4 (FCChart End)
 ?>

</div>  
</div><!-- end container -->


<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($Locations);

mysql_free_result($FusionChart4_rs0);

mysql_free_result($FusionChart4_rs1);

mysql_free_result($FusionChart4_rs2);

mysql_free_result($rsCenterTotalsbyMonth);

mysql_free_result($RSCenterTotal);

mysql_free_result($rsRevenue);
?>
