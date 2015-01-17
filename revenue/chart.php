<?php require_once('Connections/dbc.php'); ?><?php include('includes/FusionCharts/dynamic/DWFChart.inc.php'); ?><?php
// fusion chart # FusionChart3 data include
 include('includes/FusionCharts/dynamic/data/fc_FusionChart3_data.php');

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

// fusion chart #FusionChart3 dynamic filter data
 

// fusion chart recordset

mysql_select_db($database_dbc, $dbc);
$fcquery_FusionChart3_rs0 = "SELECT sum(tbl_register.checks) AS xtd_value,  SUBSTRING(CAST(tbl_register.date AS char), 6, 2)  AS xtd_date, ( SUBSTRING(CAST(tbl_register.date AS char), 6, 2) ) AS xtd_date_part,  tbl_register.date  FROM  tbl_register WHERE  ( SUBSTRING(CAST(tbl_register.date AS char), 6, 2) ) >= 1 AND  ( SUBSTRING(CAST(tbl_register.date AS char), 6, 2) ) <= 12 AND   1  GROUP BY xtd_date";
$FusionChart3_rs0 = mysql_query($fcquery_FusionChart3_rs0, $dbc) or die(mysql_error());
$row_FusionChart3_rs0 = mysql_fetch_assoc($FusionChart3_rs0);
$totalRows_FusionChart3_rs0 = mysql_num_rows($FusionChart3_rs0);

$dFCFusionChart3 = new DWFChart("FusionChart3", "MSLine", "includes/FusionCharts/charts/", 400, 400, "", "", "", "", "", "");
$dFCFusionChart3->setVersion("1.1.1");
$dFCFusionChart3->setConfigXML($FusionChart3_dataXML);
$dFCFusionChart3->setCategory($NoRecordset, "", "", "date");
$dFCFusionChart3->addSeries($FusionChart3_rs0,  "tbl_register.checks", "series_date_display=month_no;series_value_col=tbl_register.checks;series_table=tbl_register;full_date=0;series_date_col=tbl_register.date;series_calculation=sum;series_group_type=month;min_val=1;series_date_format=yyyy-mm-dd;max_val=12", "color='e7e1d5' seriesName='tbl_register.checks'", "default", "");
$dFCFusionChart3->setOrdering('None', 'asc');
$dFCFusionChart3->prepareData();
 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<script type="text/javascript" src="includes/FusionCharts/dynamic/js/FusionCharts.js"></script>
</head><?php
// (FCChart Begin) #FusionChart3
renderFusionChart($dFCFusionChart3, 400, 400);
// #FusionChart3 (FCChart End)
 ?>

<body>
</body>
</html><?php
mysql_free_result($FusionChart3_rs0);
?>