<?php session_start(); ?>
<?php include('includes/header.php'); ?>
<?php require_once('Connections/dbc.php'); ?>

<?php
	$fname = $_GET['fname'];
	$lname = $_GET['lname'];?>
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

$maxRows_rsPatientSearch = 15;
$pageNum_rsPatientSearch = 0;
if (isset($_GET['pageNum_rsPatientSearch'])) {
  $pageNum_rsPatientSearch = $_GET['pageNum_rsPatientSearch'];
}
$startRow_rsPatientSearch = $pageNum_rsPatientSearch * $maxRows_rsPatientSearch;

mysql_select_db($database_dbc, $dbc);
$query_rsPatientSearch = "SELECT * FROM tbl_patient WHERE fname LIKE '$fname%' And lname LIKE '$lname%' ORDER BY lname ASC";
$query_limit_rsPatientSearch = sprintf("%s LIMIT %d, %d", $query_rsPatientSearch, $startRow_rsPatientSearch, $maxRows_rsPatientSearch);
$rsPatientSearch = mysql_query($query_limit_rsPatientSearch, $dbc) or die(mysql_error());
$row_rsPatientSearch = mysql_fetch_assoc($rsPatientSearch);

if (isset($_GET['totalRows_rsPatientSearch'])) {
  $totalRows_rsPatientSearch = $_GET['totalRows_rsPatientSearch'];
} else {
  $all_rsPatientSearch = mysql_query($query_rsPatientSearch);
  $totalRows_rsPatientSearch = mysql_num_rows($all_rsPatientSearch);
}
$totalPages_rsPatientSearch = ceil($totalRows_rsPatientSearch/$maxRows_rsPatientSearch)-1;

$colname_rsAdmin = "-1";
if (isset($_SESSION['username'])) {
  $colname_rsAdmin = $_SESSION['username'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsAdmin = sprintf("SELECT * FROM tbl_admin WHERE username = %s", GetSQLValueString($colname_rsAdmin, "text"));
$rsAdmin = mysql_query($query_rsAdmin, $dbc) or die(mysql_error());
$row_rsAdmin = mysql_fetch_assoc($rsAdmin);
$totalRows_rsAdmin = mysql_num_rows($rsAdmin);

$colname_rsCenter = "-1";
if (isset($_SESSION['centerID'])) {
  $colname_rsCenter = $_SESSION['centerID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsCenter = sprintf("SELECT * FROM tbl_center WHERE centerID = %s", GetSQLValueString($colname_rsCenter, "int"));
$rsCenter = mysql_query($query_rsCenter, $dbc) or die(mysql_error());
$row_rsCenter = mysql_fetch_assoc($rsCenter);
$totalRows_rsCenter = mysql_num_rows($rsCenter);

?>

<?php 

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
}

?>











<?php if ($totalRows_rsPatientSearch == 0) { // Show if recordset empty ?>
    No Patients Found | <a href="findpatient.php">Search Again?</a>
    
 
    <?php } // Show if recordset empty ?>
  <?php if ($totalRows_rsPatientSearch > 0) { // Show if recordset not empty ?>
Records <?php echo ($startRow_rsPatientSearch + 1) ?> to <?php echo min($startRow_rsPatientSearch + $maxRows_rsPatientSearch, $totalRows_rsPatientSearch) ?> of <?php echo $totalRows_rsPatientSearch ?>  
<br/>
<table border="0" class="table table-striped"  cellpadding="15" cellspacing="5" width="100%" id="activity">
  <thead>
  <tr>
    
     <th>Name</th>
      <th>Address</th>
    <th>Home Phone</th>
    <th>View Profile</th>
    <th>Add to Queue</th>
  </tr>
  </thead>
  <tbody>
  <?php do { ?>
    <tr>
     
      <td><a href="viewpatient.php?patientID=<?php echo $row_rsPatientSearch['patientID']; ?>"><?php echo $row_rsPatientSearch['lname']; ?>, <?php echo $row_rsPatientSearch['fname']; ?></a></td>
      <td><?php echo $row_rsPatientSearch['address1']; ?> <?php echo $row_rsPatientSearch['address2']; ?> <?php echo $row_rsPatientSearch['city']; ?>, <?php echo $row_rsPatientSearch['state']; ?> <?php echo $row_rsPatientSearch['zip']; ?></td>
      
      <td><?php echo $row_rsPatientSearch['homephone']; ?></td>
   	<td align="center"><a href="viewpatient.php?patientID=<?php echo $row_rsPatientSearch['patientID']; ?>"><img src="images/145-persondot.png" width="24" height="26"></a></td>
      <td align="center"><a href="addtoqueue.php?username=<?php echo $_SESSION['MM_Username']; ?>&amp;patientID=<?php echo $row_rsPatientSearch['patientID']; ?>"><img src="images/56-cloud.png" width="24" height="16"></a></td>
      
    </tr>
    <?php } while ($row_rsPatientSearch = mysql_fetch_assoc($rsPatientSearch)); ?>
    </tbody>
</table>

<div id="paginate">

<?php
// DMXzone Paginator PHP 1.0.2
$pag1 = new dmxPaginator();
$pag1->recordsetName = "rsPatientSearch";
$pag1->rowsTotal = $totalRows_rsPatientSearch;
$pag1->showNextPrev = true;
$pag1->showFirstLast = true;
$pag1->outerLinks = 1;
$pag1->pageNumSeparator = "...";
$pag1->adjacentLinks = 2;
$pag1->rowsPerPage = $maxRows_rsPatientSearch;
$pag1->prevLabel = "‹";
$pag1->nextLabel = "›";
$pag1->firstLabel = "‹‹";
$pag1->lastLabel = "››";
$pag1->addPagination();
?>
</div>

</div>

<?php } // Show if recordset not empty ?>

<!-- End Right_Col -->


<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsPatientSearch);

mysql_free_result($rsAdmin);

mysql_free_result($rsCenter);
?>
