$(document).ready(function() {
	
	//書籍條碼ENTER後將成功嶺取的書 顯示在下方
	$("#book").keypress(function(){
		if (event.which === 13){
  					
			book_request();
										
    	}
   		//將 book內容清空	
    	//$("#book").val("");	   
  	});
				
	function book_request (){
					
		$.ajax({
			type: "POST" ,
						
			url: "getbook.php" ,
						
			data:{
							
			book:$("#book").val()
							
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
