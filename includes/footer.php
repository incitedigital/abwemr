</div> <!-- End Wrapper -->
<div class="clear"></div>
<div id="footer">
&copy; Copyright <?php echo date('Y'); ?>    A Better Weigh, Inc. All rights reserved.

</div>
<script src="js/facebox.js" type="text/javascript"></script>
<script src="js/scripts.js" type="text/javascript"></script>
<script src="js/languages/jquery.validationEngine-en.js" type="text/javascript" charset="utf-8"></script>
<script src="js/jquery.validationEngine.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="js/jquery.form.js"></script>
    <script type="text/javascript" src="js/bbq.js"></script>
    <script type="text/javascript" src="js/jquery.form.wizard.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    
  <script type="text/javascript">
			$(function(){
				$("#demoForm").formwizard({ 
				 	validationEnabled: true,
				 	focusFirstInput : true,
				 }
				);
  		});
    </script>
</body>

<script type="text/javascript">

	$("#datepicker").datepicker({
	
	   changeMonth: true,
      changeYear: true
    });
	
	$(".datepicker").datepicker({
	
	   changeMonth: true,
      changeYear: true
    });
	
	

</script>



<script>
		jQuery(document).ready(function(){
			// binds form submission and fields to the validation engine
			jQuery("#registration").validationEngine();

		});


jQuery(document).ready(function(){
			// binds form submission and fields to the validation engine
			jQuery("#passwordForm").validationEngine();

		});


	$('.iconbutton').tooltip();
	
	
	
	
	

	var t = document.getElementById('newweight');

// jQuery to get the content of row 4, column 1
var val1 = $(t.rows[1].cells[1]).text();  

var val2 = $(t.rows[2].cells[1]).text();  

if ((val2 - val1) > 0) {
	 
$(".weightloss").text("Patient lost " + (val1-val2) + " lbs"); 
$(".weightloss").addClass('badge');

}

else if ((val2 - val1) < 0) {
	 
$(".weightloss").text("Patient gained " + (val1-val2) + " lbs"); 
$(".weightloss").addClass('badge');
}



	</script>
	
	
<script>
$('#treatmentoptions').validationEngine();
	
</script>	
	
	
<script>	
	$("#qualifyform").validationEngine();
	</script>	
	
	
	
	
	
	
	
	