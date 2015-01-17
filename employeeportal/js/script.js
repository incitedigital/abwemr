$(document).ready(function () { 
            $("#tb").wijpiechart().hide(); 
        }); 


        $(document).ready(function () { 
            $("#bymonth").wijlinechart({ width: 840, height: 350, 
                showChartLabels: false, 
                 compass: "north",
                  orientation: "horizontal",
                hint: { 
                    content: function () { 
                        return this.y; 
                    }, 
                    offsetY: -10 
                }, 
                 seriesStyles: [ 
                    { stroke: "#a230c7", "stroke-width": 4 }, 
                    { stroke: "#27AE23", "stroke-width": 4 }, 
                    { stroke: "#196DFF", "stroke-width": 4 }, 
                ], 
                 seriesHoverStyles: [ 
                    { "stroke-width": 4 }, 
                    { "stroke-width": 4 }, 
                    { "stroke-width": 4 }, 
                ], 
                axis: { 
                    y: { 
                    }, 
                    x: { 
                        labels: { 
                            style: { 
                                rotation: -45 
                            } 
                        }, 
                        tickMajor: { position: "outside", style: { stroke: "#999999"} } 
                    } 
                } 
            }).hide(); 
        }); 

  

function updatesum() {
document.form.total.value = ((document.form.hundreds.value * 100) + (document.form.fiftys.value * 50) + (document.form.twentys.value * 20) + (document.form.tens.value * 10) + (document.form.fives.value * 5) + (document.form.ones.value * 1) + (document.form.quarters.value * .25) + (document.form.dimes.value * .10) + (document.form.nickels.value * .05) + (document.form.pennys.value * .01) + (document.form.giftcards.value * 1) + (document.form.groupons.value * 1) + (document.form.credit.value * 1) +(document.form.checks.value * 1)).toFixed(2);
}


function validateForm(){

if(document.getElementById('datepicker').value == null || document.getElementById('datepicker').value == ""){
		alert('Please enter a date!');	
		return false;
	}
	

	
	if(document.getElementById('startingregister').value == null || document.getElementById('startingregister').value == ""){
		alert('Please enter a starting register!');	
		return false;
	}
	
	if(document.getElementById('witnessconfirmation').value == null || document.getElementById('witnessconfirmation').value == ""){
		alert('Please enter a Witness Confirmation code!');	
		return false;
	}
	
		
		if (document.getElementById('cashierconfirmation').value == document.getElementById('witnessconfirmation').value )
	{
		alert('You can not approve your own register!');	
		return false;
	}

}

$('#datepicker').datepicker();


	
$('#centerID5').click(function(){
	
		$('#totals').load('hello.html');
		
});


  $(function() {
    $( "#from" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 2,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      changeMonth: true,
      numberOfMonths: 2,
      onClose: function( selectedDate ) {
        $( "#from" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
  });

$('input, textarea').placeholder();

$('#hiredate').datepicker();

$("#loginform").validationEngine();

$('#startdate').datepicker();

$('#enddate').datepicker();



        