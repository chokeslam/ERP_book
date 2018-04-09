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

			//書籍條碼ENTER後透過Ajax 送到Returns.php 處理
			$("#book").keypress(function(){
				if (event.which === 13){
  					
					book_request();
										
    			}
 
  			});
  			
  			function book_request (){
					
				$.ajax({
					type: "POST" ,
						
					url: "Returns.php" ,
						
					data:{
							
					book:$("#book").val(),
					place:$("#place").val()
							
				} ,
						
					datatype: "json" ,
						
					success: function(data) {
							
							
						$("#showbook").html(data.msg);
						$("#book").val("");
						var take = data.book.split(";");
						$("#searchtake").html(
         		 			"已領過以下書籍<br /><br />"
          			 	);
          				  $.each(take,function(index,value){
						
							$("#searchtake").append(value+'<br />');
				
						});
								
                				
                					
							
					} ,
        			error: function(jqXHR) {
            				
						alert("發生錯誤: " + jqXHR.status);
       	 			}
				});
			};
			
		});