<?php session_start(); ?>
<?php require_once('Connections/dbc.php'); ?>
<?php $patientID= $_GET['patientID'];  ?>
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
$query_rsproducts = "SELECT tbl_products.productID, tbl_products.centerID, tbl_products.patientID, proteinshake, quantity1, dressings, quantity2, proteinbars, quantity3, sex, fname, lname FROM tbl_products JOIN tbl_patient on tbl_products.patientID = tbl_patient.patientID WHERE tbl_products.status = 1 and tbl_products.centerID = '$_SESSION[centerID]'";
$rsproducts = mysql_query($query_rsproducts, $dbc) or die(mysql_error());
$row_rsproducts = mysql_fetch_assoc($rsproducts);
$totalRows_rsproducts = mysql_num_rows($rsproducts);mysql_select_db($database_dbc, $dbc);
$query_rsproducts = "SELECT tbl_products.productID, tbl_products.centerID, tbl_products.patientID, proteinshake, quantity1, dressings, quantity2, proteinbars, quantity3, sex, fname, lname, photo FROM tbl_products JOIN tbl_patient on tbl_products.patientID = tbl_patient.patientID WHERE tbl_products.status = 1 and tbl_products.centerID = '$_SESSION[centerID]'";
$rsproducts = mysql_query($query_rsproducts, $dbc) or die(mysql_error());
$row_rsproducts = mysql_fetch_assoc($rsproducts);
$totalRows_rsproducts = mysql_num_rows($rsproducts);

mysql_select_db($database_dbc, $dbc);
$centerID =  $_SESSION['centerID'];
$query_rsCheckout = "SELECT tbl_patientpackage.*,packageId, patientID, method, fname, lname, photo, insurance, copay, insuranceco, sex, tbl_patient.zip, packagename, price, insuranceprice FROM tbl_patientpackage JOIN tbl_patient on  tbl_patientpackage.patient_ID = tbl_patient.patientID JOIN tbl_package on tbl_patientpackage.package_ID = tbl_package.packageID JOIN tbl_center on tbl_patientpackage.centerID = tbl_center.centerID WHERE tbl_patientpackage.status = 'active' AND tbl_patientpackage.centerID ='$centerID' ";
$rsCheckout = mysql_query($query_rsCheckout, $dbc) or die(mysql_error());
$row_rsCheckout = mysql_fetch_assoc($rsCheckout);
$totalRows_rsCheckout = mysql_num_rows($rsCheckout);

session_start(); 
?>

<?php include('includes/header.php'); ?>


<div id="wrapper">

<div id="left_col">
<?php include('includes/menu.php'); ?>
		
</div> <!-- End Left_Col -->


<div id="right_col">

<h1>Pharmacy Checkout</h1>

  <?php if ($totalRows_rsCheckout == 0) { // Show if recordset empty ?>
    No records found
  <?php } // Show if recordset empty ?>
  <?php if ($totalRows_rsCheckout > 0) { // Show if recordset not empty ?>
   <ul class="checkoutqueue">
  <?php do { ?>
  <li>
  <div class="patienticon">
  <a href="viewpatient.php?patientID=<?php echo $row_rsCheckout['patient_ID']; ?>"><img src="uploadedfilesforbw/<?php if ($row_rsCheckout['photo'] =="" or NULL) { echo 'nophoto.jpg';} else { echo $row_rsCheckout['photo'];} ?>" width="95px"><br/>  <?php echo $row_rsCheckout['fname']; ?> <?php echo $row_rsCheckout['lname']; ?></a>
  </div>
  <div class="patientdetails">
    <strong><?php echo $row_rsCheckout['packagename']; ?></strong><br/>
    <strong>Package Price: $<?php if ($row_rsCheckout['packageId']== 5 && $row_rsCheckout['method'] == 'injection' && $row_rsCheckout['insurance'] == "Yes") {echo "250";} else if ($row_rsCheckout['packageId']== 5 && $row_rsCheckout['method'] == 'injection' && $row_rsCheckout['insurance'] != "Yes") {echo "495";} else if ($row_rsCheckout['packageId']== 6 && $row_rsCheckout['method'] == 'injection') {echo "695";} else if ($row_rsCheckout['packageId']== 6 && $row_rsCheckout['method'] == 'sublingual') {echo "795";} else if ($row_rsCheckout['packageId']== 5 && $row_rsCheckout['method'] == 'sublingual' && $row_rsCheckout['insurance'] == "Yes") {echo "350";} else if ($row_rsCheckout['packageId']== 5 && $row_rsCheckout['method'] == 'sublingual' && $row_rsCheckout['insurance'] != "Yes") {echo "595";}else if($row_rsCheckout['insurance']=="Yes") {echo $row_rsCheckout['insuranceprice'];} else {echo $row_rsCheckout['price'];} ?></strong><br/>
    <?php if ($row_rsCheckout['insurance'] == "Yes") { echo $row_rsCheckout['billinginfo'] ."<br>" .$row_rsCheckout['insuranceco']. "<br>Co-Pay: $" . $row_rsCheckout['copay'];}?> 
  
 
    <br/>
    <a href="patientcheckout.php?patientpackageID=<?php echo $row_rsCheckout['patientPackageID']; ?>&patientID=<?php echo $row_rsCheckout['patient_ID']; ?>">Release Patient</a>
    </div>
    </li>
    <?php } while ($row_rsCheckout = mysql_fetch_assoc($rsCheckout)); ?>
    
    
    
    </ul>
    <?php } // Show if recordset not empty ?>
    <?php if ($totalRows_rsproducts > 0) { // Show if recordset not empty ?>
      <ul class="checkoutqueue">
        <?php do { ?>
          <li>
            <div class="patienticon"> <a href="viewpatient.php?patientID=<?php echo $row_rsproducts['patientID']; ?>">
             <?php if ($row_rsPatient['photo'] == ""){echo "<img src=\"images/nophoto.jpg\" alt=\"nophoto\" width=\"95\" height=\"175\" />";}else{echo "<img src=\"uploadedfilesforbw/$row_rsPatient[photo]\"  alt=\"Profile Photo\" >";}?><br/>
              <br/>
              <?php echo $row_rsproducts['fname']; ?> <?php echo $row_rsproducts['lname']; ?></a> </div>
            <div class="patientdetails">
              <h3>Products</h3>
              <?php if ( $row_rsproducts['proteinshake'] == 'yes') {echo  "# Protein Shakes : " . $row_rsproducts['quantity1']; } ?>
              <br/>
              <?php if ( $row_rsproducts['dressings'] == 'yes') {echo "# Dressings :  " . $row_rsproducts['quantity2']; }?>
              <br/>
              <?php if ($row_rsproducts['proteinbars'] == 'yes') { echo "# Protein Bars :  " . $row_rsproducts['quantity3']; }?>
              <br/>
              <a href="updatecheckout.php?productID=<?php echo $row_rsproducts['productID']; ?>&patientID=<?php echo $row_rsCheckout['patient_ID']; ?>">Release Patient</a> </div>
          </li>
          <?php } while ($row_rsproducts = mysql_fetch_assoc($rsproducts)); ?>
      </ul>
      <?php } // Show if recordset not empty ?>
</div> <!-- End Right_Col -->


<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsCheckout);

mysql_free_result($rsproducts);
?>
