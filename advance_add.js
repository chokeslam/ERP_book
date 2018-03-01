		$(document).ready(function() {	
    		//$("#check").on('click',hh);	
    		//$("#check").on('click',add_request);
			$("#check").on('click',function(){
				
				//check_input();
				if(check_input() == 0){
					return 0;
				}
				add_request();
			
			});
    		
    		function check_input(){
    			
    			if($("#schoolname").val() == ""){
    				
    				alert("沒有輸入學校名稱");
    				
    				return 0;
    				
    			}else if($("#studentname").val() == ""){
    				
    				alert("沒有輸入學生名稱");
    				
    				return 0;
    				
    			}else if($("#salesname").val() == ""){
    				
    				alert("沒有輸入業務姓名");
    				
    				return 0;
    				
    			}
    		}
    		function add_request (){
								
					
				$.ajax({
					type: "POST" ,
						
					url: "advance_add.php" ,									
						
					data:{
							
						schoolname : $("#schoolname").val(),
						
						studentname : $("#studentname").val(),
						
						salesname : $("#salesname").val(),
						
						bookname : $("#bookname").text(),
						
						pdno : $("#pdno").text()
							
					} ,
						
					datatype: "json" ,
						
					success: function(data) {
						
						alert(data.msg);
						
						window.location.href = "advance_list.html";

					} ,
					
        			error: function(jqXHR) {
            				
						alert("發生錯誤: " + jqXHR.status);
       	 			}
				});
			};
				
		});	