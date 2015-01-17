<?php require_once('includes/header.php'); ?>


<h1>Patient Search</h1>
<form name="search" method="get" action="searchresults.php">
<table>
<tr><td><label for = "lname">Last Name:</label></td><td><input name="lname" class="form-control lname" type="text" />
<script type='text/javascript'>
jQuery(document).ready(
function() {
jQuery('.lname').autocomplete('autocomplete-findpatient-php-1.php',
{
	opacity : .7,
	delay : 50,
	minChars : 1,
	
	fxShow : { type:'slide' },
	fxHide : { type:'slide' }

})});
</script></td></tr>
<tr><td><label for = "fname">First Name:</label></td><td><input type="text" name="fname" class="fname form-control"></td></tr>
<tr><td></td><td><input type = "submit" name="submit" value="Search" class="buttoncolor"></td></tr>
</table>
<script type='text/javascript'>
jQuery(document).ready(
function() {
jQuery('.fname').autocomplete('autocomplete-findpatient-php-2.php',
{
	opacity : .7,
	delay : 50,
	minChars : 1,
	
	fxShow : { type:'slide' },
	fxHide : { type:'slide' }

})});
</script>
</form>

<div class="clear"></div>


<?php require_once('includes/footer.php'); ?>
</div>
    

</body>
</html>