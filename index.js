	$(document).ready(function() {
    
    // focus學生編號條碼
    $("#code").focus();
    //按鈕search按下後執行student_request向後端請求學生資料
    $("#search").on('click',student_request);
	
	//學生編號條碼輸入按ENTER 執行student_request向後端請求學生資料
	$("#code").keypress(function(){
  		if (event.which === 13){
			student_request();
    	}   
  	});
  	
		
  	//向sever.php 發出請求並將處理好的學生資料 回傳
	function student_request () {
	  $.ajax({
            type: "POST",
            url: "sever.php",
            data:{
            	code:$("#code").val()
            },
            dataType: "json",
           //請求成功將內容列出
            success: function(data) {
                
                if (data.code) {
                	
                	//將 code內容清空	
                	$("#code").val("");
                	//focus 到書籍條碼的input
                	$("#book").focus();                	
                	//列出學生資料
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
                    //列出補習科目
					$.each(course,function(index,value){
						
						$('#searchcourse').append(value+'<br />');
						
					});
					var take = data.take.split(";");
					$("#searchtake").html(
                        "已領書籍<br /><br />"
                    );
                    $.each(take,function(index,value){
						
						$("#searchtake").append(value+'<br />');
						
					});
					
                //請求成功但沒尋找到資料
                } else {
                	//focus 回 code 欄位
                	$("#code").focus();
                	//將 code 清空
                	$("#code").val("");
                	//在 searchname 顯示錯誤訊息
                    $("#searchname").html(data.msg);
                }
            },
            //發生錯誤
            error: function(jqXHR) {
                alert("發生錯誤: " + jqXHR.status);
            }
        });
	};
});