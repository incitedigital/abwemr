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
$query_rsUsers = "SELECT adminID, username, password, firstname, lname, accesslevel FROM tbl_admin";
$rsUsers = mysql_query($query_rsUsers, $dbc) or die(mysql_error());
$row_rsUsers = mysql_fetch_assoc($rsUsers);
$totalRows_rsUsers = mysql_num_rows($rsUsers);
?>
<?php include('menu.php'); ?>
<body>
<div id="wrapper">
<h1>Admistrators</h1>
<h3><a href="adduser.php">Add User</a></h3>
<table border="1" cellpadding="5" cellspacing="5" width="100%">
  <tr>
 
    <th>Username</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Access Level</th>
    <th>Edit</th>
    <th>Delete</th>
  </tr>
  <?php do { ?>
    <tr>
      
      <td><?php echo $row_rsUsers['username']; ?></td>
      <td><?php echo $row_rsUsers['firstname']; ?></td>
      <td><?php echo $row_rsUsers['lname']; ?></td>
      <td><?php 
      
      if($row_rsUsers['accesslevel'] == 3) {echo 'Administrator';} elseif ($row_rsUsers['accesslevel'] == 2) {echo 'General User';} ; 
      
      ?></td>
      <td><a href="edituser.php?adminID=<?php echo $row_rsUsers['adminID']; ?>">Edit</a></td>
      <td><a href="deleteuser.php?adminID=<?php echo $row_rsUsers['adminID']; ?>">Delete</a></td>
    </tr>
    <?php } while ($row_rsUsers = mysql_fetch_assoc($rsUsers)); ?>
</table>
</div>
</body>
</html>
<?php
mysql_free_result($rsUsers);
?>
