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
$query_rsCenters = "SELECT * FROM tbl_center";
$rsCenters = mysql_query($query_rsCenters, $dbc) or die(mysql_error());
$row_rsCenters = mysql_fetch_assoc($rsCenters);
$totalRows_rsCenters = mysql_num_rows($rsCenters);
?>
<?php include('menu.php'); ?>
<body>
<div id="wrapper"><h1>Centers</h1>
<a href="addcenter.php">Add center</a>
<br/>
<table border="1" width="100%">
  <tr>
   
    <th>locationname</th>
    <th>locationaddress</th>
    <th>city</th>
    <th>state</th>
    <th>zip</th>
    <th>phone</th>
     <th>edit</th>
      <th>delete</th>
  </tr>
  <?php do { ?>
    <tr>
    
      <td><?php echo $row_rsCenters['locationname']; ?></td>
      <td><?php echo $row_rsCenters['locationaddress']; ?></td>
      <td><?php echo $row_rsCenters['city']; ?></td>
      <td><?php echo $row_rsCenters['state']; ?></td>
      <td><?php echo $row_rsCenters['zip']; ?></td>
      <td><?php echo $row_rsCenters['phone']; ?></td>
      <td><a href="editcenter.php?centerID=<?php echo $row_rsCenters['centerID']; ?>">edit</a></td>
      <td><a href="deletecenter.php?centerID=<?php echo $row_rsCenters['centerID']; ?>">delete</a></td>
    </tr>
    <?php } while ($row_rsCenters = mysql_fetch_assoc($rsCenters)); ?>
</table>
</div>

</body>
</html>
<?php
mysql_free_result($rsCenters);
?>
