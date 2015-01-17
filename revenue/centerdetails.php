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
if(isset($_POST['from'])){$datestart = date("'Y-m-d'", strtotime($_POST['from'])); } else {$datestart = date("'Y-m-1'");}
if(isset($_POST['to'])){$dateend = date("'Y-m-d'", strtotime($_POST['to'])); } else {$dateend = date("'Y-m-31'");}


mysql_select_db($database_dbc, $dbc);
$query_Recordset1 = "SELECT tbl_register.date, locationname, tbl_register.cashier,tbl_register.witness,tbl_register.startingregister,tbl_register.giftcards,tbl_register.groupons,tbl_register.credit,tbl_register.checks,tbl_register.hundreds,tbl_register.fiftys,tbl_register.twentys,tbl_register.tens,tbl_register.fives,tbl_register.ones,tbl_register.quarters,tbl_register.dimes,tbl_register.nickels, tbl_register.pennys FROM tbl_register JOIN tbl_center ON tbl_register.centerID = tbl_center.centerID ";
$Recordset1 = mysql_query($query_Recordset1, $dbc) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);

$colname_LocationName = "-1";
if (isset($_GET['centerID'])) {
  $colname_LocationName = $_GET['centerID'];
}
mysql_select_db($database_dbc, $dbc);
$query_LocationName = sprintf("SELECT * FROM tbl_center WHERE centerID = %s", GetSQLValueString($colname_LocationName, "int"));
$LocationName = mysql_query($query_LocationName, $dbc) or die(mysql_error());
$row_LocationName = mysql_fetch_assoc($LocationName);
$totalRows_LocationName = mysql_num_rows($LocationName);

//Export to Excel Server Behavior
if (isset($_POST['export'])&&($_POST['export']=="1")){
	$delim="";
	$delim_replace="";
	if($delim==""){
		$lang=(strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'],",")===false)?$_SERVER['HTTP_ACCEPT_LANGUAGE']:substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'],","));
		$semi_array=array("af","zh-hk","zh-mo","zh-cn","zh-sg","zh-tw","fr-ch","de-li","de-ch","it-ch","ja","ko","es-do","es-sv","es-gt","es-hn","es-mx","es-ni","es-pa","es-pe","es-pr","sw");
		$delim=(in_array($lang,$semi_array) || substr_count($lang,"en")>0)?",":";";
	}
	$output="";
	$include_hdr="1";
	if($include_hdr=="1"){
		$totalColumns_Recordset1=mysql_num_fields($Recordset1);
		for ($x=0; $x<$totalColumns_Recordset1; $x++) {
			if($x==$totalColumns_Recordset1-1){$comma="";}else{$comma=$delim;}
			$output = $output.(str_replace("_", " ",mysql_field_name($Recordset1, $x))).$comma;
		}
		$output = $output."\r\n";
	}

	do{$fixcomma=array();
    		foreach($row_Recordset1 as $r){array_push($fixcomma,str_replace($delim,$delim_replace,$r));}
		$line = join($delim,$fixcomma);
    		$line=str_replace("\r\n", " ",$line);
    		$line=str_replace("\r", " ",$line);
    		$line=str_replace("\n", " ",$line);
    		$line = "$line\n";
    		$output=$output.$line;}while($row_Recordset1 = mysql_fetch_assoc($Recordset1));
	$export_encoding="";
	$export_encoding=($export_encoding=="")?"":("; charset=".$export_encoding);
	header("Content-Type: application/xls".$export_encoding);
	header("Pragma: public");
	header("Content-Disposition: attachment; filename=report.csv");
	header("Content-Type: application/force-download");
	header("Cache-Control: post-check=0, pre-check=0", false);
	echo $output;
	die();
}

$colname_rsCenters = "-1";
if (isset($_GET['centerID'])) {
  $colname_rsCenters = $_GET['centerID'];
}
if(isset($_POST['from'])){$datestart = date('Y-m-d', strtotime($_POST['from'])); } else {$datestart = date("Y-m-1");}

if(isset($_POST['to'])){$dateend = date('Y-m-d', strtotime($_POST['to'])); } else {$dateend = date("Y-m-31");}
mysql_select_db($database_dbc, $dbc);

$query_rsCenters = sprintf("SELECT tbl_register.*, locationname FROM tbl_register JOIN tbl_center ON tbl_register.centerID = tbl_center.centerID WHERE tbl_register.centerID = %s AND date >= '$datestart' AND date <= '$dateend' ORDER BY date DESC", GetSQLValueString($colname_rsCenters, "int"));
$rsCenters = mysql_query($query_rsCenters, $dbc) or die(mysql_error());
$row_rsCenters = mysql_fetch_assoc($rsCenters);
$totalRows_rsCenters = mysql_num_rows($rsCenters);$colname_rsCenters = "-1";

$query_rsName= sprintf("SELECT locationname FROM tbl_center WHERE tbl_center.centerID = %s", GetSQLValueString($colname_rsCenters, "int"));
$rsName = mysql_query($query_rsName, $dbc) or die(mysql_error());
$row_rsName = mysql_fetch_assoc($rsName);

?>
<?php include('includes/headeradmin.php'); ?>
<div class="container">
<div class="row">
  <div class="col-md-3">
    <?php include('includes/menu.php'); ?>
  </div>
  <div class="col-md-9 datecontainer">
    <div class="row">
      <div class="col-md-9">
        <h2><?php echo $row_LocationName['locationname']; ?></h2>
        <form class="form-inline" name="datesearch" id="datesearch" method="post" action="">
          <div class="form-group"><label for="from">From</label>
          <input type="text" id="from" name="from" class="form-control" placeholder="from" value="" /></div>
          <div class="form-group">
            <label for="to">to</label>
            <input type="text" id="to" name="to" class="form-control" placeholder="to" value="" />
          </div>
          <input type="submit" name="submit" class="btn btn-default search" value="Search"/>
        </form>
      </div>
      
      
      <div class="col-sm-3">
        <form name="exportform" action="" method="post">
          <input type="hidden" name="export" value="1">
          <input type="image" src="images/spreadsheets.png" alt="Download Spreadsheet" class="spreadsheet">
        </form>
      </div>
      
    </div><!-- end row --> 
    <div class="row" id="pushdown">
      <div class="col-md-12 right_col">
        <?php if ($totalRows_rsCenters == 0) { // Show if recordset empty ?>
          No history found
          <?php } // Show if recordset empty ?>
        <?php do { ?>
        <?php if ($totalRows_rsCenters > 0) { // Show if recordset not empty ?>
          <div class="panel panel-default">
          <!-- Default panel contents -->
          <div class="panel-heading">
            <div class="row">
              <div class="col-md-2"><span class="glyphicon glyphicon-calendar"></span> <?php echo date("m/d/Y", strtotime($row_rsCenters['date'])); ?></div>
              <div class="col-md-10"><strong>Starting Register:</strong> <?php echo $row_rsCenters['startingregister']; ?> <span class="moveright"><strong>Cashier:</strong> <?php echo $row_rsCenters['cashier']; ?></span> <span class="moveright"> <strong>Witness:</strong> <?php echo $row_rsCenters['witness']; ?></span> <span class=""><strong>Total</strong></span> <?php echo "$ ".number_format($row_rsCenters['hundreds'] * 100 + $row_rsCenters['fiftys'] * 50 + $row_rsCenters['twentys'] *20  + $row_rsCenters['tens'] *10 + $row_rsCenters['fives'] * 5 + $row_rsCenters['ones']  + $row_rsCenters['quarters'] * .25 + $row_rsCenters['dimes'] * .10 + $row_rsCenters['nickels'] * .05 + $row_rsCenters['pennys'] * .01 + $row_rsCenters['credit'] + $row_rsCenters['checks'] + $row_rsCenters['groupons'] + $row_rsCenters['giftcards'], 2); ?></div>
            </div>
          </div>
          <div class="panel-body">
          <div class="row">
            <div class="col-md-4">
              <table class="table">
                <tr>
                  <td><img src="images/cash.png" alt="cash" width="32" height="20" /> 100's</td>
                  <td><span class="badge pull-right"><?php echo $row_rsCenters['hundreds']; ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/cash.png" alt="cash" width="32" height="20" /> 50's</td>
                  <td><span class="badge pull-right"> <?php echo $row_rsCenters['fiftys']; ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/cash.png" alt="cash" width="32" height="20" /> 20's</td>
                  <td><span class="badge pull-right"> <?php echo $row_rsCenters['twentys']; ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/cash.png" alt="cash" width="32" height="20" /> 10's</td>
                  <td><span class="badge pull-right"> <?php echo $row_rsCenters['tens']; ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/cash.png" alt="cash" width="32" height="20" /> 5's </td>
                  <td><span class="badge pull-right"><?php echo $row_rsCenters['fives']; ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/cash.png" alt="cash" width="32" height="20" /> 1's </td>
                  <td><span class="badge pull-right"><?php echo $row_rsCenters['ones']; ?></span></td>
                </tr>
              </table>
            </div>
            <div class="col-md-4">
              <table class="table">
                <tr>
                  <td>.25's</td>
                  <td><span class="badge pull-right"> <?php echo $row_rsCenters['quarters']; ?></span></td>
                </tr>
                <tr>
                  <td>.10's</td>
                  <td><span class="badge pull-right"> <?php echo $row_rsCenters['dimes']; ?></span></td>
                </tr>
                <tr>
                  <td>.05's</td>
                  <td><span class="badge pull-right"> <?php echo $row_rsCenters['nickels']; ?></span></td>
                </tr>
                <tr>
                  <td>.01's</td>
                  <td><span class="badge pull-right"> <?php echo $row_rsCenters['pennys']; ?></span></td>
                </tr>
              </table>
            </div>
            <div class="col-md-4">
              <table class="table">
                <tr>
                  <td><img src="images/cash.png" alt="Cash" width="32" height="22" /> <strong>Cash</strong></td>
                  <td><span class="badge pull-right"> <?php echo "$".number_format($row_rsCenters['hundreds'] * 100 + $row_rsCenters['fiftys'] * 50 + $row_rsCenters['twentys'] *20  + $row_rsCenters['tens'] *10 + $row_rsCenters['fives'] * 5 + $row_rsCenters['ones']  + $row_rsCenters['quarters'] * .25 + $row_rsCenters['dimes'] * .10 + $row_rsCenters['nickels'] * .05 + $row_rsCenters['pennys'] * .01, 2); ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/giftcard.png" alt="giftcard" width="32" height="22" /> <strong>Gift Cards</strong></td>
                  <td><span class="badge pull-right"> <?php echo "$" .number_format( $row_rsCenters['giftcards'], 2); ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/groupon.png" width="23" height="22"> <strong> Groupons </strong></td>
                  <td><span class="badge pull-right"><?php echo "$" .number_format($row_rsCenters['groupons'],2); ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/ccard.png" alt="ccard" width="32" height="22" /> <strong> Credit</strong></td>
                  <td><span class="badge pull-right"><?php echo "$" .number_format( $row_rsCenters['credit'], 2); ?></span></td>
                </tr>
                <tr>
                  <td><img src="images/check.png" alt="check" width="32" height="22" /> <strong> Checks</strong></td>
                  <td><span class="badge pull-right"><?php echo "$" .number_format( $row_rsCenters['checks'], 2); ?></span></td>
                </tr>
              </table>
            </div>
          </div>
          <?php } // Show if recordset not empty ?>
      </div>
    </div>
    <?php } while ($row_rsCenters = mysql_fetch_assoc($rsCenters)); ?>
  </div>
</div>
<!-- end container -->


<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($Recordset1);

mysql_free_result($LocationName);

mysql_free_result($rsCenters);
?>
