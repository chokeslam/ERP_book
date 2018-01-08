	$(document).ready(function() {
    
    // focus學生編號條碼
    $("#code").focus();
    $("#search").on('click',A1);
	
	//學生編號條碼輸入按ENTER 執行A1
	$("#code").keypress(function(){
  		if (event.which === 13){
			A1();
    	}   
  	});
  	//書籍條碼ENTER後換行
  	$("#test1").keypress(function(){
  		if (event.which === 13){
			$("#test2").focus();
			
			$("#test2").keypress(function(){
  				if (event.which === 13){
					$("#test3").focus();
					
					$("#test3").keypress(function(){
  						if (event.which === 13){
							$("#test4").focus();
							
							$("#test4").keypress(function(){
  								if (event.which === 13){
									$("#test5").focus();
    							}   
  							});
    					}   
  					});
    			}   
  			});
    	}   
  	});
  	
  	
  	
  	
  	//將sever.php 處理好的資料 回傳
	function A1 () {
	  $.ajax({
            type: "GET",
            url: "sever.php?code=" + $("#code").val(),
            dataType: "json",
            success: function(data) {
                
                if (data.code) {
                	//focus 到書籍條碼的input
                	$("#test1").focus();
                	//列出資料
                	$("img").attr("src",data.img);
                    
                    $("#searchname").html(
                        "學生姓名:" +data.name 
                    );
                    
                    $("#searchclassify").html(
                        "補習類別:" +data.classify
                    );
                    
                    $("#searchclassname").html(
                        "科系類別:" +data.class_name
                    );
                    var course = data.course.split(";");
                  	 $("#searchcourse").html(
                        "報名科目<br /><br />"
                    );
					$.each(course,function(index,value){
						
						$('#searchcourse').append(value+'<br />');
						
					});
                 
                } else {
                	$("#code").focus();
                    $("#searchname").html(data.msg);
                }
            },
            error: function(jqXHR) {
                alert("發生錯誤: " + jqXHR.status);
            }
        });
	};
});