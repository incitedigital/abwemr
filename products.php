<?php session_start(); ?>
<?php require_once('Connections/dbc.php'); ?>
<?php $patientID = $_GET['patientID']; ?>

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$centerID = $_POST['centerID'];
  $insertSQL = sprintf("INSERT INTO tbl_products (patientID, proteinshake, quantity1, dressings, quantity2, proteinbars, quantity3, centerID) VALUES (%s, %s, %s, %s, %s, %s, %s, '$centerID' )",
                       GetSQLValueString($_POST['patientID'], "int"),
                       GetSQLValueString($_POST['proteinshake'], "text"),
                       GetSQLValueString($_POST['pquantity'], "int"),
                       GetSQLValueString($_POST['dressings'], "text"),
                       GetSQLValueString($_POST['pquantity2'], "int"),
                       GetSQLValueString($_POST['proteinbar'], "text"),
                       GetSQLValueString($_POST['pquantity3'], "int"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());
  
  
mysql_select_db($database_dbc, $dbc);
$patientquery = "UPDATE tbl_queue SET status = 0 WHERE patientID = '$patientID'";
$rsRemove = mysql_query($patientquery, $dbc) or die(mysql_error());



  $insertGoTo = "viewpatient.php?patientID=" . $row_rsPatient['patientID'] . "";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

$colname_rsPatient = "-1";
if (isset($_GET['patientID'])) {
  $colname_rsPatient = $_GET['patientID'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsPatient = sprintf("SELECT * FROM tbl_patient WHERE patientID = %s", GetSQLValueString($colname_rsPatient, "int"));
$rsPatient = mysql_query($query_rsPatient, $dbc) or die(mysql_error());
$row_rsPatient = mysql_fetch_assoc($rsPatient);
$totalRows_rsPatient = mysql_num_rows($rsPatient);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
<link rel="stylesheet" type="text/css" href="style/style.css" />
</head>

<body>
<form id="form1" name="form1" method="POST" action="<?php echo $editFormAction; ?>">
  <table width="300" id="productid" border="0" cellpadding="10" cellspacing="10">
    <tr>
      <td>Protein Shakes</td>
      <td><label for="proteinshake"></label>
        <select name="proteinshake" id="proteinshake">
          <option value="yes">yes</option>
          <option value="no">no</option>
      </select></td>
    </tr>
    <tr>
      <td>Quantity</td>
      <td><label for="pquantity"></label>
        <select name="pquantity" id="pquantity">
          <option value="0">0</option>
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5</option>
          <option value="6">6</option>
      </select></td>
    </tr>
    <tr>
      <td>Dressings</td>
      <td><select name="dressings" id="dressings">
        <option value="yes">yes</option>
        <option value="no">no</option>
      </select></td>
    </tr>
    <tr>
      <td>Quantity</td>
      <td><select name="pquantity2" id="pquantity2">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
      </select></td>
    </tr>
    <tr>
      <td>Protein Bars</td>
      <td><select name="proteinbar" id="proteinbar">
        <option value="yes">yes</option>
        <option value="no">no</option>
      </select></td>
    </tr>
    <tr>
      <td>Quantity</td>
      <td><select name="pquantity3" id="pquantity3">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
      </select></td>
    </tr>
    <tr>
      <td><input type="hidden" name="patientID" value="<?php echo $row_rsPatient['patientID']; ?>" /></td>
      <td><input type="hidden" name="centerID" value="<?php echo $_SESSION['centerID']; ?>" /></td>
      <td><input type="submit" name="Submit" id="Submit" value="Submit" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
  </table>
  <input type="hidden" name="MM_insert" value="form1" />
</form>
<p>&nbsp;</p>
<p>&nbsp;</p>
</body>
</html>
<?php
mysql_free_result($rsPatient);
?>
