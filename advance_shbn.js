		$(document).ready(function() {
			
			//載入 index.html 的 預領轉領書表單	
			$('#mymodal').load('index.html #formmodal');
			
			$('#mymodal1').load('index.html #takemodal');
			
			$('#mymodal2').load('index.html #prompt');
			//載入 index.html 的 nav	
			$('#nav').load('index.html nav',function(){
				
				$("#getmodal").on('click',function(){
					
					$.getScript("modal.js");
				
				});
			});
			
			
			$("#book_barcode").keypress(function(){
				if (event.which === 13){
  					
				bookname_request();
				$("#book_barcode").val("");						
    			}   
  			});
			
			function bookname_request (){
								
					
				$.ajax({
					type: "POST" ,
						
					url: "advance_shbn.php" ,
						
					data:{
							
						book_barcode:$("#book_barcode").val(),
						
						bookname: $("#bookname").text()
							
					} ,
						
					datatype: "json" ,
						
					success: function(data) {
						
						if (typeof data.msg == "undefined"){
						
							
							alert(data.msgprompt);
								
						}else{
							
							$("#prompt").html("");
							$("#pdno").append(data.nno + " "+"<br />");
							$("#bookname").append(data.msg + " "+"<br />");
						}
						
					} ,
					
        			error: function(jqXHR) {
            				
						alert("發生錯誤: " + jqXHR.status);
       	 			}
				});
			};
		});