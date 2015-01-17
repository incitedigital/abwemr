<?php require_once('../Connections/dbc.php'); ?>
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
$query_rsPackages = "SELECT * FROM tbl_package";
$rsPackages = mysql_query($query_rsPackages, $dbc) or die(mysql_error());
$row_rsPackages = mysql_fetch_assoc($rsPackages);
$totalRows_rsPackages = mysql_num_rows($rsPackages);
?>
<?php include('menu.php'); ?>
<body>
<div id="wrapper">
<h1> Manage Packages </h1>
<a href="addpackage.php">Add New Package</a>
<br/><br/>
<table border="1" cellpadding="5" cellspacing="5" width="100%">
  <tr>
    <th width="5%">packageId</th>
    <th width="85%">packagename</th>
    <th width="5%">edit</th>
    <th width="5%">delete</th>
  </tr>
  <?php do { ?>
    <tr>
      <td><?php echo $row_rsPackages['packageId']; ?></td>
      <td><?php echo $row_rsPackages['packagename']; ?></td>
      <td><a href="editpackage.php?packageId=<?php echo $row_rsPackages['packageId']; ?>">edit</a></td>
      <td><a href="deletepackage.php?packageId=<?php echo $row_rsPackages['packageId']; ?>">delete</a></td>
    </tr>
    <?php } while ($row_rsPackages = mysql_fetch_assoc($rsPackages)); ?>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsPackages);
?>
