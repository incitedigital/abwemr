<?php require_once('Connections/dbc.php'); ?>
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
 $start = date('Y-m-d', strtotime($_POST['start']));
$end = date('Y-m-d', strtotime($_POST['end']));
$title = $_POST['title'];
$id= $_GET['id'];	
  $updateSQL = "UPDATE evenement SET start ='$start', end ='$end', title='$title' WHERE id='$id'";
  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($updateSQL, $dbc) or die(mysql_error());

  $updateGoTo = "manageevents.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

$colname_rsEvent = "-1";
if (isset($_GET['id'])) {
  $colname_rsEvent = $_GET['id'];
}
mysql_select_db($database_dbc, $dbc);
$query_rsEvent = sprintf("SELECT * FROM evenement WHERE id = %s", GetSQLValueString($colname_rsEvent, "int"));
$rsEvent = mysql_query($query_rsEvent, $dbc) or die(mysql_error());
$row_rsEvent = mysql_fetch_assoc($rsEvent);
$totalRows_rsEvent = mysql_num_rows($rsEvent);
?>

<?php include('includes/headeradmin.php'); ?>
<div class="container">
<div class="row">
<div class="col-md-3"><h1>Edit Event</h1></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>

<div class="row">

<div class="col-md-12">
<div class="panel panel-default">
 
  <div class="panel-body">
  	<form method="post" name="form1" action="<?php echo $editFormAction; ?>">
  <table align="center" class="table">
   <tr valign="baseline">
      <td nowrap align="right">Title:</td>
      <td><input type="text" name="title" value="<?php echo htmlentities($row_rsEvent['title'], ENT_COMPAT, ''); ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">Start:</td>
      <td><input type="text" name="start" id="start" value="<?php echo date("m/d/Y", strtotime($row_rsEvent['start'])); ?>" size="32"></td>
    </tr>
    <tr valign="baseline">
      <td nowrap align="right">End:</td>
      <td><input type="text" name="end" id="end" value="<?php echo date("m/d/Y", strtotime($row_rsEvent['end'])); ?>" size="32"></td>
    </tr>
   
    <tr valign="baseline">
      <td nowrap align="right">&nbsp;</td>
      <td><input type="submit" value="Update record" class="btn btn-primary" ></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1">
  <input type="hidden" name="id" value="<?php echo $row_rsEvent['id']; ?>">
</form>
<p>&nbsp;</p>
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
        <h4 class="modal-title" id="myModalLabel">Add Event</h4>
      </div>
      <div class="modal-body">
      <form method="POST" action="<?php echo $editFormAction; ?>" name="eventscalendar">
       <div class="form-group">
            <label for="subject">Title</label>
            <input type="text" class="form-control" id="subject" name="title" placeholder="Title">
            
          </div>
           <div class="form-group"> 
            <label for="start">Start Date</label>
             <input type="text" class="form-control" id="startdate"  name="start">
            </div>
            
            <div class="form-group"> 
            <label for="end">End Date</label>
             <input type="text" class="form-control" id="enddate"  name="end">
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <input type="submit" class="btn btn-primary" value="Save changes"/>
        <input type="hidden" name="MM_insert" value="eventscalendar">
       </form>
      </div>
     
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal --><script language="javascript">

$('#start').datepicker();
$('#end').datepicker();

</script>






</div> <!-- end container -->

<?php include('includes/footer.php'); ?>

<?php
mysql_free_result($rsEvent);

?>
