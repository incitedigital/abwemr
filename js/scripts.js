$(function(){

				// Accordion
				$("#accordion").accordion({ header: "h3" });
				
				$('#tabs').tabs();
				
				$('#dialog').dialog({
					autoOpen: false,
					width: 600,
					buttons: {
						"Ok": function() {
							$(this).dialog("close");
						},
						"Cancel": function() {
							$(this).dialog("close");
						}
					}
				});
				
			
			// Dialog Link
				$('#dialog_link').click(function(){
					$('#dialog').dialog('open');
					return false;
				});
			
			
			
$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 500,
			width: 550,
			modal: true,
			buttons: {
				"Create an account": function() {
					var bValid = true;
					allFields.removeClass( "ui-state-error" );

					bValid = bValid && checkLength( name, "username", 3, 16 );
					bValid = bValid && checkLength( email, "email", 6, 80 );
					bValid = bValid && checkLength( password, "password", 5, 16 );

					bValid = bValid && checkRegexp( name, /^[a-z]([0-9a-z_])+$/i, "Username may consist of a-z, 0-9, underscores, begin with a letter." );
					// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
					bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
					bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );

					if ( bValid ) {
						$( "#users tbody" ).append( "<tr>" +
							"<td>" + name.val() + "</td>" + 
							"<td>" + email.val() + "</td>" + 
							"<td>" + password.val() + "</td>" +
						"</tr>" ); 
						$( this ).dialog( "close" );
					}
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});

		$( "#create-user" )
			.button()
			.click(function() {
				$( "#dialog-form" ).dialog( "open" );
			});
			
			
			
			$( "#find-user" )
			.button()
			.click(function() {
				$( "#dialog-form" ).dialog( "open" );
			});
			
			
			
				$( "#add-vitals" )
			.button()
			.click(function() {
				$( "#addvitals" ).dialog( "open" );
			});
			
			
			
			$( "#remove" )
			.button()
			.click(function() {
				$( "#dialog-form" ).dialog( "open" );
			});
			
			
			
			
			
			
			
			$( "#addvitals" ).dialog({
			autoOpen: false,
			height: 500,
			width: 550,
			modal: true,
					});

			
			

			
			
			});
			
	
			$(function(){

				// Accordion
				$("#accordion").accordion({ header: "h3" });
				
				$('#tabs').tabs();
				
				$('#dialog').dialog({
					autoOpen: false,
					width: 600,
					buttons: {
						"Ok": function() {
							$(this).dialog("close");
						},
						"Cancel": function() {
							$(this).dialog("close");
						}
					}
				});
				
				$('#dob').datepicker();
				$('#fromdate').datepicker();
				$('#todate').datepicker();

			
			// Dialog Link
				$('#dialog_link').click(function(){
					$('#dialog').dialog('open');
					return false;
				});
			
			
			
$( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 420,
			width: 450,
			modal: true
		
		});

		$( "#create-user" )
			.button()
			.click(function() {
				$( "#dialog-form" ).dialog( "open" );
			});
			
			
			
			$( "#find-user" )
			.button()
			.click(function() {
				$( "#finduser" ).dialog( "open" );
			});
			
			$( "#finduser" ).dialog({
		autoOpen: false,
			height: 350,
			width: 400,
			modal: true,
		});
			
			
			
			
				$( "#add-vitals" )
			.button()
			.click(function() {
				$( "#addvitals" ).dialog( "open" );
			});
			
			$( "#addnewnote" )
			.button()
			.click(function() {
				$( "#addnote" ).dialog( "open" );
			});
			
			$( ".addpackage" )
			.button()
			.click(function() {
				$( "#addpackageform" ).dialog( "open" );
			});
			
			$( ".windowbutton" )
			.button()
			.click(function() {
				var myVariable = 2;
				$('#divwindow').load('something.php?packageId=myVariable').dialog();
			});
			
			
			
			$( "#remove" )
			.button()
			.click(function() {
				$( "#dialog-form" ).dialog( "open" );
			});
			
			
			$("#CreateUser").button();
			$("#search").button();
			
			
			$( "#addvitals" ).dialog({
			autoOpen: false,
			height: 350,
			width: 400,
			modal: true,
			show: "fade"
			
		});
		
		$( "#addnote" ).dialog({
			autoOpen: false,
			height: 350,
			width: 400,
			modal: true,
			show: "fade"
			
		});
		
		$( "#addpackageform" ).dialog({
			autoOpen: false,
			height: 350,
			width: 500,
			modal: true,
			show: "fade"
			
		});
		
		
		$( "#finduser" ).dialog({
		autoOpen: false,
			height: 350,
			width: 400,
			modal: true,
			show: "fade"
		});
		
			
			
			

$('#modal-div').data('mydata', data).dialog('open');
var data = $('#modal-div').data('mydata');
			
				
                
                function hello() {
					
					
					alert(this.value);
				}
                
                
                
                
    jQuery(document).ready(function($) {
  $('a[rel*=facebox]').facebox() 
})            
                
            		jQuery(document).ready(function(){
			// binds form submission and fields to the validation engine
			jQuery("#addpatient").validationEngine();
			jQuery("#noteform").validationEngine();
			jQuery("#addvitalform").validationEngine();
			jQuery("#form1").validationEngine();
		});

		/**
		*
		* @param {jqObject} the field where the validation applies
		* @param {Array[String]} validation rules for this field
		* @param {int} rule index
		* @param {Map} form options
		* @return an error string if validation failed
		*/
		function checkHELLO(field, rules, i, options){
			if (field.val() != "HELLO") {
				// this allows to use i18 for the error msgs
				return options.allrules.validate2fields.alertText;
			}
		}
	
			});
			
	$(document).ready(function(){
			
				$('#searchresults tbody tr:even').addClass('stripe');
			});
					


	$(document).ready(function(){
	
		$('#activity tbody tr:even').addClass('stripe');
	});		
			
			$(document).ready(function(){
	
		$('#activity tbody tr:odd').addClass('oddstripe');
	});		
			
	$(document).ready(function(){
	
		$('.tabledesign tbody tr:even').addClass('stripe');
	});		
			
			$(document).ready(function(){
	
		$('.tabledesign tbody tr:odd').addClass('oddstripe');
	});		
			
	