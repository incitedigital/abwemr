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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "eventscalendar")) {
	$start = date('Y-m-d', strtotime($_POST['start']));
$end = date('Y-m-d', strtotime($_POST['end']));
$timeoff = $_POST['timeoff'];
  $insertSQL = sprintf("INSERT INTO evenement (title, `start`, `end`,timeoff) VALUES (%s, '$start', '$end', '$timeoff')",
                       GetSQLValueString($_POST['title'], "text"),
                       GetSQLValueString($_POST['start'], "date"),
                       GetSQLValueString($_POST['end'], "date"));

  mysql_select_db($database_dbc, $dbc);
  $Result1 = mysql_query($insertSQL, $dbc) or die(mysql_error());

  $insertGoTo = "manageevents.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

mysql_select_db($database_dbc, $dbc);
$query_rsEvents = "SELECT * FROM evenement ORDER BY id DESC";
$rsEvents = mysql_query($query_rsEvents, $dbc) or die(mysql_error());
$row_rsEvents = mysql_fetch_assoc($rsEvents);
$totalRows_rsEvents = mysql_num_rows($rsEvents);
?>
<?php include('includes/headeradmin.php'); ?>
<div class="container">
<div class="row">
<div class="col-md-3"><h1>Manage Events</h1></div>
<div class="col-md-9">
<?php include('includes/headermenu.php'); ?>
</div>
</div>

<div class="row">

<div class="col-md-12">
<div class="panel panel-default">
 
  <div class="panel-body">
  <button class="btn btn-info" data-toggle="modal" data-target="#addevent">Add Event</button>
  <br><br>
    <table class="table">
    <thead>
      <tr>
       
        <td width="60%">Event Title</td>
        <td>Start</td>
        <td>End</td>
        <td>Actions</td>
      </tr>
      </thead>
      <tbody>
      <?php do { ?>
        <tr>
          
          <td><?php echo $row_rsEvents['title']; ?></td>
          <td><?php echo date("m/d/Y", strtotime($row_rsEvents['start'])); ?></td>
          <td><?php echo  date("m/d/Y",strtotime($row_rsEvents['end'])); ?></td>
          <td><a href="editevents.php?id=<?php echo $row_rsEvents['id']; ?>">Edit</a> | <a href="deleteevent.php?id=<?php echo $row_rsEvents['id']; ?>">Delete</a></td>
        </tr>
        <?php } while ($row_rsEvents = mysql_fetch_assoc($rsEvents)); ?>
        </tbody>
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
            
            <div class="form-group"> 
            
             <input type="checkbox"  id="timeoff"  name="timeoff" value="1"> Check for Time Off Request
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
</div><!-- /.modal -->

<script language="javascript">

$('#startdate').datepicker();
$('#enddate').datepicker();

</script>




</div> <!-- end container -->

<?php include('includes/footer.php'); ?>
<?php
mysql_free_result($rsEvents);
?>
