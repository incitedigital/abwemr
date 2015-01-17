<?php require_once('../Connections/dbc.php'); ?>
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

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_rsClients = 25;
$pageNum_rsClients = 0;
if (isset($_GET['pageNum_rsClients'])) {
  $pageNum_rsClients = $_GET['pageNum_rsClients'];
}
$startRow_rsClients = $pageNum_rsClients * $maxRows_rsClients;

mysql_select_db($database_dbc, $dbc);
$query_rsClients = "SELECT * FROM tbl_patient order by lname asc";
$query_limit_rsClients = sprintf("%s LIMIT %d, %d", $query_rsClients, $startRow_rsClients, $maxRows_rsClients);
$rsClients = mysql_query($query_limit_rsClients, $dbc) or die(mysql_error());
$row_rsClients = mysql_fetch_assoc($rsClients);

if (isset($_GET['totalRows_rsClients'])) {
  $totalRows_rsClients = $_GET['totalRows_rsClients'];
} else {
  $all_rsClients = mysql_query($query_rsClients);
  $totalRows_rsClients = mysql_num_rows($all_rsClients);
}
$totalPages_rsClients = ceil($totalRows_rsClients/$maxRows_rsClients)-1;

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
		$totalColumns_rsClients=mysql_num_fields($rsClients);
		for ($x=0; $x<$totalColumns_rsClients; $x++) {
			if($x==$totalColumns_rsClients-1){$comma="";}else{$comma=$delim;}
			$output = $output.(str_replace("_", " ",mysql_field_name($rsClients, $x))).$comma;
		}
		$output = $output."\r\n";
	}

	do{$fixcomma=array();
    		foreach($row_rsClients as $r){array_push($fixcomma,str_replace($delim,$delim_replace,$r));}
		$line = join($delim,$fixcomma);
    		$line=str_replace("\r\n", " ",$line);
    		$line=str_replace("\r", " ",$line);
    		$line=str_replace("\n", " ",$line);
    		$line = "$line\n";
    		$output=$output.$line;}while($row_rsClients = mysql_fetch_assoc($rsClients));
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

$queryString_rsClients = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_rsClients") == false && 
        stristr($param, "totalRows_rsClients") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_rsClients = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_rsClients = sprintf("&totalRows_rsClients=%d%s", $totalRows_rsClients, $queryString_rsClients);
?>
<head>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
<style>

table {
	font-size: 9pt;	
}

</style>
<link rel="stylesheet" href="PaginationStyles/badoo/pagination.css" type="text/css" />
</head>
<?php include('menu.php'); ?>
<?php
// DMXzone Paginator PHP 1.0.2
$pag1 = new dmxPaginator();
$pag1->recordsetName = "rsClients";
$pag1->rowsTotal = $totalRows_rsClients;
$pag1->showNextPrev = true;
$pag1->showFirstLast = true;
$pag1->outerLinks = 1;
$pag1->pageNumSeparator = "...";
$pag1->adjacentLinks = 2;
$pag1->rowsPerPage = $maxRows_rsClients;
$pag1->prevLabel = "‹";
$pag1->nextLabel = "›";
$pag1->firstLabel = "‹‹";
$pag1->lastLabel = "››";
$pag1->addPagination();
?>
<body>
<div id="wrapper">
  <h1>Patient List</h1>
  <form name="excel" method="post">
    Export to Excel
    <input type="hidden" name="export" value="1" />
    <input type="submit" name="submit" value="Export"/>
  </form>
  <br />
  <br />
  Records <?php echo ($startRow_rsClients + 1) ?> to <?php echo min($startRow_rsClients + $maxRows_rsClients, $totalRows_rsClients) ?> of <?php echo $totalRows_rsClients ?> <br />
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Last Name</th>
        <th>First Name</th>
        <th>Address</th>
        <th>email</th>
        <th>homephone
          </td>
        <th>mobilephone
          </td>
        <th>dob</th>
        <th>sex</th>
        <th>edit</th>
        <th>delete</th>
      </tr>
    </thead>
    <tbody>
      <?php do { ?>
        <tr>
          <td><?php echo $row_rsClients['fname']; ?></td>
          <td><?php echo $row_rsClients['lname']; ?></td>
          <td><?php echo $row_rsClients['address1']; ?> <?php echo $row_rsClients['address2']; ?> <?php echo $row_rsClients['city']; ?>, <?php echo $row_rsClients['state']; ?> <?php echo $row_rsClients['zip']; ?></td>
          <td><?php echo $row_rsClients['email']; ?></td>
          <td><?php echo $row_rsClients['homephone']; ?></td>
          <td><?php echo $row_rsClients['mobilephone']; ?></td>
          <td><?php echo $row_rsClients['dob']; ?></td>
          <td><?php echo $row_rsClients['sex']; ?></td>
          <td><a href="editpatient.php?patientID=<?php echo $row_rsClients['patientID']; ?>">edit</a></td>
          <td><a href="deletepatient.php?patientID=<?php echo $row_rsClients['patientID']; ?>">delete</a></td>
        </tr>
        <?php } while ($row_rsClients = mysql_fetch_assoc($rsClients)); ?>
    </tbody>
  </table>
  <table border="0">
    <tr>
      <td><?php if ($pageNum_rsClients > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_rsClients=%d%s", $currentPage, 0, $queryString_rsClients); ?>">First</a>
          <?php } // Show if not first page ?></td>
      <td><?php if ($pageNum_rsClients > 0) { // Show if not first page ?>
          <a href="<?php printf("%s?pageNum_rsClients=%d%s", $currentPage, max(0, $pageNum_rsClients - 1), $queryString_rsClients); ?>">Previous</a>
          <?php } // Show if not first page ?></td>
      <td><?php if ($pageNum_rsClients < $totalPages_rsClients) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_rsClients=%d%s", $currentPage, min($totalPages_rsClients, $pageNum_rsClients + 1), $queryString_rsClients); ?>">Next</a>
          <?php } // Show if not last page ?></td>
      <td><?php if ($pageNum_rsClients < $totalPages_rsClients) { // Show if not last page ?>
          <a href="<?php printf("%s?pageNum_rsClients=%d%s", $currentPage, $totalPages_rsClients, $queryString_rsClients); ?>">Last</a>
          <?php } // Show if not last page ?></td>
    </tr>
  </table>
</div>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
<?php
mysql_free_result($rsClients);
?>
