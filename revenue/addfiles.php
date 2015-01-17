<?php require_once('Connections/dbc.php'); ?>
<?php require_once('ScriptLibrary/incPureUpload.php'); ?>
<?php 
//*** Pure PHP File Upload 3.0.1
// Process form filelocation
$ppu = new pureFileUpload();
$ppu->nameConflict = "uniq";
$ppu->storeType = "file";
$uploadfilelocation = $ppu->files("filelocation");
$uploadfilelocation->path = "docs";
$ppu->redirectUrl = "";
$ppu->checkVersion("3.0.1");
$ppu->doUpload();
?>

<?php require_once('Connections/dbc.php'); 
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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "filelocation")) {
  $insertSQL = sprintf("INSERT INTO tbl_companyinfo (title, filelocation) VALUES (%s, %s)",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['filelocation'], "text"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "addfiles.php?message=addfiles";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}


?>
<?php
mysql_select_db($database_dbc, $dbc);
$query_rsCompanyInfo = "SELECT * FROM tbl_companyinfo";
$rsCompanyInfo = mysql_query($query_rsCompanyInfo, $dbc) or die(mysql_error());
$row_rsCompanyInfo = mysql_fetch_assoc($rsCompanyInfo);
$totalRows_rsCompanyInfo = mysql_num_rows($rsCompanyInfo);
?>
<?php include('includes/headeradmin.php'); ?>
<script type="text/javascript"><?php echo $ppu->generateScriptCode() ?></script>
<script src="ScriptLibrary/incPU3.js" type="text/javascript"></script>
<div class="container">
<div class="row">
<div class="col-md-3"><h1>Add Company Information</h1></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>

<div class="row">

<div class="col-md-12">
<div class="panel panel-default">
 
  <div class="panel-body">
  <button class="btn btn-info" data-toggle="modal" data-target="#addevent">Add Company Information</button>
  <br><br>
  <table border="1" class="table">
 
  <?php do { ?>
  <tr>
   
   
    <td width="95%"><a href="docs/<?php echo $row_rsCompanyInfo['filelocation']; ?>"><?php echo $row_rsCompanyInfo['title']; ?></a></td>
    <td><a href="deletefiles.php?fileID=<?php echo $row_rsCompanyInfo['fileID']; ?>" class="confirm">Delete</a></td>
  </tr>
  <?php } while ($row_rsCompanyInfo = mysql_fetch_assoc($rsCompanyInfo)); ?>
</table>
  </div>
  
</div>
</div>
</div>


<!-- Modal -->
<div class="modal fade" id="addevent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Add Company Information</h4>
      </div>
      <div class="modal-body">
      <form action="<?php echo $editFormAction; ?>" method="post" enctype="multipart/form-data" name="filelocation" onSubmit="<?php echo $ppu->getSubmitCode() ?>;return document.MM_returnValue">
        <?php echo $ppu->getProgressField() ?>
        <div class="form-group">
          <label for="title">Title</label>
          <input type="text" class="form-control" id="subject" name="title" placeholder="Title">
        </div>
        <div class="form-group">
          <label for="filename">Upload File</label>
          <input name="filelocation" type="file" id="filelocation" onChange="<?php echo $uploadfilelocation->getValidateCode() ?>;return document.MM_returnValue">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-primary" value="Upload File"/>
          <input type="hidden" name="MM_insert" value="filelocation">
        </div>
      </form>
      </div>
     
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script language="javascript">

$('#startdate').datepicker();
$('#enddate').datepicker();

</script>




</div> <!-- end container -->

<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsCompanyInfo);
?>
