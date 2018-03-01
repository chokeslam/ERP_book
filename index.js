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
  	//開啟 預領轉領書表單
  	$("#getmodal").click(function() {
  		
  		$.getScript("modal.js");
  		
  	 });
  		
 		/*$("#formmodal").on('show.bs.modal', function(){
 			
          var $this = $(this);
          
          var $modal_dialog = $this.find('.modal-dialog');
          
         
          $this.css('display', 'block');
          
          $modal_dialog.css({'margin-top': Math.max(0, ($(window).height() - $modal_dialog.height()) / 2) });
          
        });
        
        $("#formmodal").modal();
        
 
	});
	
	$('#find').on('click',function(){
		
		//$("#formmodal").modal('hide');
		lendstock_request ();				
		
	});
	
	
	$("#prompt").on('show.bs.modal', function(){
 			
    	var $this = $(this);
          
        var $modal_dialog = $this.find('.modal-dialog');
           
        $this.css('display', 'block');
          
        $modal_dialog.css({'margin-top': Math.max(0, ($(window).height() - $modal_dialog.height()) / 2) });
          
    });
	
	
	$("#ok").on('click',function(){
		
		writestudent_request ();
		
		$("#checkbox").html("");
		
		lendstock_request ();
	
	});
	
	function writestudent_request (){
		$.ajax({
			  type: "POST",
			  url: "writefile.php",
			  data: {
			  	  lendnno : $("#lendnno").val(),
			  	  lendstudentnno : $("#lendstudentnno").val(),
			  	  lendadmin : $("#lendadmin").val(),
			  	  lendbook : get_value()
			  },
			  datatype: "json",
			  
			  success: function(data) {
					
				if (typeof data.msg == "undefined"){
						
						$("#msg").html("");
						
						var bookname = data.book.split(",");
						
						$("#jjj").html("");
						
						$("#prompt").modal();
						
						$.each(bookname,function(index,value){
						
							$("#jjj").append(value+'<br />');
				
						});
						
						$("#sss").html('這 '+data.num +' 本書 已經領過');
						
				}else if(data.msg == 2){
						
						$("#msg").html("請勾選要轉為領書的書籍");
						
				}else if(data.msg == 3){
						
						$("#msg").html("請輸入學生編號");
						
				}else if(data.msg == 4){
						
						$("#msg").html("請輸入正確的學生編號");
				}else{
					
					alert(data.msg);
					window.location.href = "index.html";
					$("#formmodal").modal();
					
				}										
				
				
				
				bookstring = "";

			  },
			  
			  error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
				
       	 	  }				
		});	
	}; 	

	var bookstring = "";
	
	function get_value(){
		
		$("input:checkbox:checked").each(function(index,value){
			
			if($(this).prop("checked") ==true){
			
				bookstring = bookstring+$(this).val()+",";
			}else{
				
				bookstring = bookstring.replace("/"+$(this).val()+"/", "bookstring");
			}
			
		});
		
		return bookstring;
	};
	
	
	
	
	
	function lendstock_request (){
		$.ajax({
			  type: "POST",
			  url: "lendstock_response.php",
			  data: {
			  	  lendnno:$("#inputnno").val()
			  },
			  datatype: "json",
			  
			  success: function(data) {
			  		
			  		if(data.msg == 3){
						
						$("#msg1").html("請輸入借書單號");
						
						return 0;
						
					}else if(data.msg == 2){
						
						$("#msg1").html("請輸入正確的借書單號");
						
						return 0;
					}
			  		
			  		$("#takemodal").modal();
			  		
			  		$("#lendnno").val(data.adv_no);
			  		
			  		$("#lendschool").val(data.school_name);
			  		
			  		$("#lendstudent").val(data.student_name);
			  		
			  		$("#lendsales").val(data.sales_name);
			  		
			  		$("#lenddate").val(data.lend_date);
			  		
			  		var lendbook = data.book_name.split(";");
			  		
					$.each(lendbook,function(index,value){
						
						$("#checkbox").append(
							
							'<div><input type="checkbox" name="test"  value='+value+' />'+value+'</div>'
						
						);
				
					});
					
						$("input[type=checkbox]").on('click',function(){
		
							if($(this).prop("checked") ==true){
							
								$(this).parent().css("color","#00bb00");
							
							}else{
									
								$(this).parent().css("color","red");
							}
							
						});
							
			  },
			  error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
				
       	 	  }				
		});	
	}; */
	
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
                	//將 error內容清空
                	$("#error").html("");
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
					var take = data.taketime.split(";");
					$("#searchtake").html(
                        "已領過以下書籍<br /><br />"
                    );
                    $.each(take,function(index,value){
						
						$("#searchtake").append(value+'<br />');
						
					});
					
                //請求成功但沒尋找到資料 或學生權限不足
                } else {
                	//focus 回 code 欄位
                	$("#code").focus();
                	//將 code 清空
                	$("#code").val("");

                	//在 error 顯示訊息
					$("#error").html(data.msg);
					
					//將前一筆學生資料清空
					$("#searchname").html("");
					$("#searchclassify").html("");
					$("#searchclassname").html("");
                	$("#searchcourse").html("");
                	$("#searchtake").html("");
                	$("#showbook").html("");
                }
            },
            //發生錯誤
            error: function(jqXHR) {
                alert("發生錯誤: " + jqXHR.status);
            }
        });
	};
});