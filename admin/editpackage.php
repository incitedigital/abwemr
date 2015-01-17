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
  $updateSQL = sprintf("UPDATE tbl_package SET packagename=%s WHERE packageId=%s",
                       GetSQLValueString($_POST['packagename'], "text"),
                       GetSQLValueString($_POST['packageId'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "packages.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rspackages = "-1";
if (isset($_GET['packageId'])) {
  $colname_rspackages = $_GET['packageId'];
}
mysql_select_db($database_dbc, $dbc);
$query_rspackages = sprintf("SELECT * FROM tbl_package WHERE packageId = %s", GetSQLValueString($colname_rspackages, "int"));
$rspackages = mysql_query($query_rspackages, $dbc) or die(mysql_error());
$row_rspackages = mysql_fetch_assoc($rspackages);
$totalRows_rspackages = mysql_num_rows($rspackages);
?>
<?php include('menu.php'); ?>
<div id="wrapper">

<body>
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table align="center">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">PackageId:</td>
      <td><?php echo $row_rspackages['packageId']; ?></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Packagename:</td>
      <td><input type="text" name="packagename" value="<?php echo htmlentities($row_rspackages['packagename'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="packageId" value="<?php echo $row_rspackages['packageId']; ?>" />
</form>
</div>
</body>
</html>
<?php
mysql_free_result($rspackages);
?>
