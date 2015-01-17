<?php session_start(); ?>

<?php require_once('Connections/dbc.php'); ?>
<?php require_once('ScriptLibrary/cGraphicMediator.php'); ?>
<?php require_once('ScriptLibrary/incPureUpload.php'); ?>
<?php 
//*** Pure PHP File Upload 3.0.1
// Process form form
$ppu = new pureFileUpload();
$ppu->timeOut = 600;
$ppu->nameConflict = "uniq";
$ppu->storeType = "file";
$ppu->path = "images";
$ppu->allowedExtensions = "GIF,JPG,JPEG,BMP,PNG"; // "all"
$ppu->redirectUrl = "";
$ppu->checkVersion("3.0.1");
$ppu->doUpload();
?>

<?php
// Smart Image Processor PHP 2.1.1
{
$sipp2 = new cGraphicMediator("upload", $ppu, "photo");
$sipp2->setComponent("Auto");
$sipp2->setMatteColor("#FFFFFF");
$sipp2->resizeEx(150, 150, true, false);
$sipp2->sharpen();
$sipp2->overwrite = false;
$sipp2->setMask("##path##thumb_##name##.##ext##");
$sipp2->save();
$sipp2->process();
}
?>
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form")) {
 $hiredate = date('Y-m-d', strtotime($_POST['hiredate']));	
  $insertSQL = sprintf("INSERT INTO tbl_admin (username, password, firstname, lname, photo, accesslevel, jobtitle, employmentstatus, hiredate) VALUES (%s, %s, %s, %s, %s, %s, %s, %s,'$hiredate')",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['firstname'], "text"),
                       GetSQLValueString($_POST['lname'], "text"),
                       GetSQLValueString($_POST['photo'], "text"),
                       GetSQLValueString($_POST['accesslevel'], "int"),
                       GetSQLValueString($_POST['jobtitle'], "text"),
                       GetSQLValueString($_POST['jobstatus'], "text"),
                       GetSQLValueString($_POST['hiredate'], "date"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "employees.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dbc, $dbc);
$query_rsEmployee = "SELECT * FROM tbl_admin ORDER BY lname ASC";
$rsEmployee = mysql_query($query_rsEmployee, $dbc) or die(mysql_error());
$row_rsEmployee = mysql_fetch_assoc($rsEmployee);
$totalRows_rsEmployee = mysql_num_rows($rsEmployee);
?>

<?php include('includes/headeradmin.php'); ?>
<script type="text/javascript"><?php echo $ppu->generateScriptCode() ?></script>
<script src="ScriptLibrary/incPU3.js" type="text/javascript"></script>
<script language='javascript' src='ScriptLibrary/incPureUpload.js'></script>
<div class="container">
<div class="row">
<div class="col-md-3"><h1>Employees</h1></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>

<div class="row fullwidth">

<button type="button" data-toggle="modal" data-target="#addemployee" class="btn btn-info btn-xs pull-right pushdown">Add New Employee</button>&nbsp;&nbsp;

<table class="table table-striped" >
  <thead>
    <tr>

      <td>Employee Info</td>
      <td>Job Title</td>
      <td>Employment Status</td>
      <td>Hire date</td>
      <td>Employee photo</td>
      <td>Actions</td>
    </tr>
    </thead>
    <tbody>
    <?php do { ?>
      <tr>
       
       
        <td><strong><a href="employeedetails.php?adminID=<?php echo $row_rsEmployee['adminID']; ?>"><?php echo $row_rsEmployee['lname']; ?>, <?php echo $row_rsEmployee['firstname']; ?></a></strong><br><?php echo $row_rsEmployee['address']; ?><br><?php echo $row_rsEmployee['city']; ?> <?php echo $row_rsEmployee['state']; ?> <?php echo $row_rsEmployee['zip']; ?><br>
        <?php echo $row_rsEmployee['phone']; ?> <br>
      
        </td>
        <td><?php echo $row_rsEmployee['jobtitle']; ?></td>
        <td><?php echo $row_rsEmployee['employmentstatus']; ?></td>
        <td><?php echo date('m/d/Y', strtotime($row_rsEmployee['hiredate'])); ?></td>
        <td align"right"><?php if ($row_rsEmployee['photo'] != NULL) {echo "<img src=\"http://abwemr.com/employeeportal/thumbnail/$row_rsEmployee[photo]\" alt=\"user\" width=\"150\" >";} else {echo "<img class=\"media-object\" src=\"images/photo_placeholder.gif\" alt=\"user\" width=\"150\" height=\"150\" />";}?></td>
        <td><a href="updateemployee.php?adminID=<?php echo $row_rsEmployee['adminID']; ?>">Edit</a> | <a href="deletephoto.php?adminID=<?php echo $row_rsEmployee['adminID']; ?>" class="confirm">Delete</a></td>
      </tr>
      <?php } while ($row_rsEmployee = mysql_fetch_assoc($rsEmployee)); ?>
  	</tbody>
  </table>
</div>


<?php include('includes/footer.php'); ?>
<!-- Modal -->
<div class="modal fade" id="addemployee" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add New Employee</h4>
      </div>
      <div class="modal-body">
      <form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="form" onSubmit="checkFileUpload(this,'GIF,JPG,JPEG,BMP,PNG',false,'','','','','','','');<?php echo $ppu->getSubmitCode() ?>;return document.MM_returnValue" role="form">
        <?php echo $ppu->getProgressField() ?>
        <div class="form-group">
          <label for="hiredate">Employee Hire Date</label>
          <input type="text" class="form-control" name="hiredate" id="hiredate" placeholder="Hire Date">
        </div>
        <div class="form-group">
          <label for="firstname">First Name</label>
          <input type="text" class="form-control" name="firstname" id="firstname" placeholder="First Name">
        </div>
        <div class="form-group">
          <label for="lname">Last Name</label>
          <input type="text" class="form-control" name="lname" id="lname" placeholder="Last Name">
        </div>
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" class="form-control" name="username" id="username" placeholder="Enter username">
        </div>
        <div class="form-group">
          <label for="exampleInputPassword1">Password</label>
          <input type="password" class="form-control" name="password" id="exampleInputPassword1" placeholder="Password">
        </div>
        <div class="form-group">
          <label for="jobtitle">Job Title</label>
          <input type="text" class="form-control" name="jobtitle" id="jobtitle" placeholder="Enter Job Title">
        </div>
        <div class="form-group">
          <label for="jobstatus">Job Status</label>
          <select name="jobstatus" class="form-control" id="jobstatus">
            <option value="">Select Job Status</option>
            <option value="Full-Time">Full-Time</option>
            <option value="Part-Time">Part-Time</option>
          </select>
        </div>
        <div class="form-group">
          <label for="photo">Employee Photo</label>
          <input name="photo" type="file" id="photo" onChange="<?php echo $ppu->getValidateCode() ?>;return document.MM_returnValue">
        </div>
        <div class="form-group">
          <label for="accesslevel">Access Level</label>
          <select name="accesslevel">
            <option value="2" <?php if (!(strcmp(2, 3))) {echo "SELECTED";} ?>>Regular User</option>
            <option value="3" <?php if (!(strcmp(3, 3))) {echo "SELECTED";} ?>>Administrator</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-primary" value="Add Employee">
          <input type="hidden" name="MM_insert" value="form">
        </div>
      </form>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal -->
<div class="modal fade" id="adddiscipline" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add Discipline</h4>
      </div>
      <div class="modal-body">
      <form name="adddiscipline">
      <div class="form-group">
          <label for="selectemployee">Select Employee</label>
          <select name="empID">
            <?php
do {  
?>
            <option value="<?php echo $row_rsEmployee['adminID']?>"><?php echo $row_rsEmployee['lname']?>, <?php echo $row_rsEmployee['firstname']?></option>
            <?php
} while ($row_rsEmployee = mysql_fetch_assoc($rsEmployee));
  $rows = mysql_num_rows($rsEmployee);
  if($rows > 0) {
      mysql_data_seek($rsEmployee, 0);
	  $row_rsEmployee = mysql_fetch_assoc($rsEmployee);
  }
?>
          </select>
        </div>
        
      	<div class="form-group">
          <label for="message">Enter Message</label>
         <textarea class="form-control" name="message" id="message">
         
         
         </textarea>
        </div>
        
       
          <input type="hidden" name="adminID" value="<?php echo $row_rsUsers['adminID']; ?>">
       
        
      <input type="submit" name="submit" value="Submit Record">
      
      </form>
     
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->



<script>
$('#hiredate').datepicker();
</script>
<?php
mysql_free_result($rsEmployee);
?>
