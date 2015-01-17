<?php require_once('Connections/dbc.php'); ?>
<?php include('includes/headeradmin.php'); ?>
<?php 
	if(isset($_POST['password']) && $_POST['password'] != "") {
	$password = $_POST['password'];
	mysql_select_db($database_dbc, $dbc);
	$query_emps = "SELECT * FROM tbl_admin WHERE password = '$password' AND accesslevel = 3";
	$emps = mysql_query($query_emps, $dbc) or die(mysql_error());
	$row_emps = mysql_fetch_assoc($emps);
	$totalRows_emps = mysql_num_rows($emps);
	if($totalRows_emps > 0 ){
		echo("<script>window.location=\"http://www.abwemr.com/revenue/revenuemanager.php\"</script>");
	}
	else if ($totalRows_emps == 0) {
		echo "<div class=\"container\"><div class=\"alert alert-danger\">
		  <strong>Alert!</strong> You are not allowed to access the Revenue Management System.
      </div></div>";
	}
	}
?>

<div class="container movedown">

<div class="row">

 <div class="col-lg-6 leftcol"><h1>Employee Manager<?php echo $loginStrGroup; ?></h1>
 
 <img src="images/people.png" alt="people" width="300" height="288" />
 
 <br>

  <a href="employeemanager.php" class="btn btn-success btn-lg">Enter </a>
 </div>
<div class="col-lg-6 rightcol"><h1>Revenue Manager</h1>

<div class="form-group">

  <form class="form-inline" name="revmgr" id="revmgr" method="POST" action="revenuemanager.php">
  <div class="input-group">
  <span class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></span>
  <input type="password" name="password" class="form-control" placeholder="Password">
 
</div>
  </div>
  

 <input type="submit" class="btn btn-success btn-lg" value="Enter"> 
  </form>
</div>


</div>

</div>



</div>



<?php include('includes/footer.php'); ?>