						
				
					
	$("#formmodal").on('show.bs.modal', function(){
 			
		var $this = $(this);
          
		var $modal_dialog = $this.find('.modal-dialog');
          
		$this.css('display', 'block');
          
		$modal_dialog.css({'margin-top': Math.max(0, ($(window).height() - $modal_dialog.height()) / 2) });
          
	});
				
	$("#formmodal").modal();
	
	$("#find").on('click',function(){
		
		//$("#formmodal").modal('hide');
		lendstock_request ();
		
	});

	$("#inputnno").keypress(function(){
  		
  		if (event.which === 13){
			
			$("#find").click();

    	}   
  	
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
	
	$("#takemodal").on('hide.bs.modal', function(){
		
		 window.location.reload();
	
	});
	
	$("#formmodal").on('hide.bs.modal', function(){
	
		 window.location.reload();
	
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
	}; 