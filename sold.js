$(document).ready(function() {

	$('#mymodal').load('index.html #formmodal');

	$('#mymodal1').load('index.html #takemodal');

	$('#mymodal2').load('index.html #prompt');
		//載入 index.html 的 nav	
	$('#nav').load('index.html nav',function(){

		$("#getmodal").on('click',function(){
					
			$.getScript("modal.js");
				
		});
	});

	$("#code").keypress(function(){

		if (event.which === 13){

			search_request ();
		}   
	});

	$('#soldok').click(function(){
		soldbook();
	});
		function soldbook(){
			$.ajax({
				type: "POST",

				url: "sold.php",

				data:{
					PD_No:$('#pdno').val(),
					ST_Place:$('#place').val(),
					ST_Qty:$('#qty').val()
				},
				success: function(data) {

					if(typeof data.msg == "undefined"){
						alert(data.errormsg);
					}else{
						alert(data.msg);
						window.location.href = "pd_list.html";
					}

				},
				error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		}
    	function search_request (){
					
			$.ajax({
				type: "POST" ,
						
				url: "search_sold.php" ,
						
				data:{
					ST_Place:$('#place').val(),
					PD_No:$('#code').val()
				} ,
						
				datatype: "json" ,
						
				success: function(data) {

					if (typeof data.PD_No == "undefined"){

						$("#bookmsg").css('color' , 'red');
						$("#bookmsg").html(data.errormsg+'<br>');
						$('#code').val('');

					}else{

						$("#bookmsg").css('color' , '');
						$("#bookmsg").html(data.note+'<br>');
						$("#pdno").val(data.PD_No);
						$("#qty").val(data.ST_Qty);
						$('#code').val('');
					}

				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		};

});