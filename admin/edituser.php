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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE tbl_admin SET username=%s, password=%s, firstname=%s, lname=%s, accesslevel=%s WHERE adminID=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['accesslevel'], "int"),
                       GetSQLValueString($_POST['adminID'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "users.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsUsers = "-1";
if (isset($_GET['adminID'])) {
  $colname_rsUsers = $_GET['adminID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsUsers = sprintf("SELECT * FROM tbl_admin WHERE adminID = %s", GetSQLValueString($colname_rsUsers, "int"));
$rsUsers = mysql_query($query_rsUsers, $dbc) or die(mysql_error());
$row_rsUsers = mysql_fetch_assoc($rsUsers);
$totalRows_rsUsers = mysql_num_rows($rsUsers);
?>
<?php include('menu.php'); ?>
<div id="wrapper">
<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Username:</td>
      <td><input type="text" name="username" value="<?php echo htmlentities($row_rsUsers['username'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Password:</td>
      <td><input type="password" name="password" value="<?php echo $row_rsUsers['password']; ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">First Name:</td>
      <td><input type="text" name="firstname" value="<?php echo htmlentities($row_rsUsers['firstname'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Last Name:</td>
      <td><input type="text" name="lname" value="<?php echo htmlentities($row_rsUsers['lname'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Access Level:</td>
      <td><select name="accesslevel">
        <option value="2" <?php if (!(strcmp(2, htmlentities($row_rsUsers['accesslevel'], ENT_COMPAT, 'UTF-8')))) {echo "SELECTED";} ?>>General User</option>
        <option value="3" <?php if (!(strcmp(3, htmlentities($row_rsUsers['accesslevel'], ENT_COMPAT, 'UTF-8')))) {echo "SELECTED";} ?>>Administrator</option>
      </select></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" /></td>
    </tr>
  </table>
  <input type="hidden" name="adminID" value="<?php echo $row_rsUsers['adminID']; ?>" />
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="adminID" value="<?php echo $row_rsUsers['adminID']; ?>" />
</form>
</div>

</body>
</html>
<?php
mysql_free_result($rsUsers);
?>
