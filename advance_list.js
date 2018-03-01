		$(document).ready(function() {
			
			//撈資料 function
			function format ( d ) {
    			// `d` is the original data object for the row
    			var course = d.book_name.split(";");
    			var string = "";
    			$.each(course,function(index,value){
						
						string = string+value+"<br>"+"<hr>";
						
					});
    			bookname = string.substring(0,string.length-1);
    			return '<div class="bg-primary text-white" >已預領的書籍</div>'+'<div class ="book" style="padding:10px 0;">'+bookname+'</div>';
    			
			}
			
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
					
			//使用 datatable
			var table =	$('#example').DataTable({
				
				// datatable dom 位置排序  並使用 bootstrap "justify-content-center" 將底下分頁按鈕置中	
			  	dom:'Brt<"justify-content-center"p>',
			  	
			  	//每頁顯示的資料筆數調整
			  	"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			  		
			  	select: true,
			  	
			  	//用 ajax 到後端  advance_list.php 撈資料
        		"ajax": "advance_list.php",
        		
        		//資料擺放在列的順序	
        		"columns":[
        		        			
            	{ "data": "adv_no" },				//adv_no : 借貨單號
            						
            	{ "data": "school_name" },			//school_name : 學校名稱
            						
            	{ "data": "student_name" },			//student_name : 學生名稱
            						
            	{ "data": "sales_name" },			//sales_name : 業務名稱
            						
            	{ "data": "lend_date" },			//lend_date : 借貨日期
            						
            	
            	// 新增的欄位 1 : 下拉展開	內容為 借出的書名					
            	{
                  "className":'details-control book-content',
                
               	  "orderable":false,
                
                  "data":null,
                
                  "defaultContent": '<button type="button" class="btn btn-link">'+"查看內容"+'</button>'
            	},
            	
            	//新增欄位 2 : 修改及還書 欄位  點擊展開	bootstrap 互動視窗
                {                
                  "className":'click',
                  
                  "orderable":false,
                 	
                  "data":null,
                 	
                 	// bootstrap 互動視窗class
                  "defaultContent":'<button type="button" class="btn btn-link" data-toggle="modal" data-target="#exampleModal">'+"修改/還書"+'</button>'
                },  
        		]
        	
        	
        		
  			});
		
				//下拉展開	
    		$('#example tbody').on('click', 'td.details-control', function () {
    	
        		var tr = $(this).closest('td');
        
        		var row = table.row( tr );
 
        		if ( row.child.isShown() ) {
            		// This row is already open - close it
           			row.child.hide();		//隱藏
            
            		tr.removeClass('shown');       			
        		}
        		else {
            		// Open this row
            		row.child( format(row.data()) ).show();	
            			
            		tr.addClass('shown');
        		}
        		
    		} );
    		
    		//每個欄位上的 搜尋欄位 
			$('#example tfoot th').each( function () {
				
        		var title = $(this).text();
        		
        	$(this).html( '<input type="text"  placeholder="搜尋 '+title+'" />' );
        	
    		} );
 	
    			// DataTable
    		var table = $('#example').DataTable();
 
    			// Apply the search
    		table.columns().every( function () {
        		var that = this;
 
        		$( 'input', this.footer() ).on( 'keyup change', function () {
        			
            		if ( that.search() !== this.value ) {
            			
                		that.search( this.value ).draw();
                		
            		}
            		
        		} );
        		
    		} );
    		
    		//隱藏	下拉展開 及 修改欄位上的搜尋框
    		$('.book-content input').hide();
    		$('.click input').hide();
    		
    		
    		//修改按鈕	function  將該欄位資料 傳入互動視窗表單中
    		$('#example tbody').on('click','td.click button',function(){
    			
    			//alert 清空
    			$('#alert').html('');    			
    			$('#bar').val('');
    			
    			//	使用 for 迴圈 將前面 5個欄位的資料傳入	單號、學生姓名、學校名稱、業務名稱、借出時間		
  				for(i=0;i< 5;i++){
  					
  					$('#input').children('div').children('input').eq(i).val(
  						
  						$(this).parents('tr').children("td").eq(i).text()
  						
  					);
  					
  				}
  				
  				//將 下拉展開的書籍名稱內容 傳入 
				var tr = $(this).closest('td');
				
				var row = table.row( tr );
				
				row.child( format(row.data()) ).show();	//打開下拉展開的 TD 抓資料
				
				//將內容傳入
				$('#input').children('div').children('#bookname').html(
					
					$('.book').html()
					
				);
				
				//視窗打開後 focus 到條碼輸入的 input 欄位
				$('#exampleModal').on('shown.bs.modal', function () {
						$('#bar').focus();
				});
				
				//將拉展開的 TD 中 <hr>刪除
				$('hr').remove(),
				
				row.child( format(row.data()) ).hide();//關閉下拉展開的 TD 
				
				
				  
				
  			});	

			//條碼輸入後 AJAX 到後端比對資料				
			$('#bar').keypress(function(){
			
  				if (event.which === 13){
  					
  					
					book_request ();
    			}   
  			});

			$("#submit").on('click',function(){
				
				if(check_input() == 0){
					return 0;
				}
				
				book_upload();
				
			});
  			
//-----------------------------------------------------------------------------------------------------------
	
			function book_request (){
					
				$.ajax({
					type: "POST" ,
						
					url: "advance_search.php" ,
						
					data:{
				
					adv_no:$("#inputno").val(),
							
					book:$("#bar").val(),
					
					rebook:$("#rebookname").text()
							
					} ,
						
					datatype: "json" ,
						
					success: function(data) {
					
						$("#alert").html(data.msg);
						
						$("#bar").val("");

						var take = data.book.split(";");

						$("#bookname").html("");
								
						$.each(take,function(index,value){
						
						$("#bookname").append(value+'<br />');
				
						});	
						
						$("#rebookname").append(data.rebook + " "+"<br />");
						$("#rebookcode").append(data.rebookcode + " "+"<br />");		
								
					} ,
        			error: function(jqXHR) {
            				
						alert("發生錯誤: " + jqXHR.status);
       	 			}
				});
			};
			
    		function check_input(){
    			
    			if($("#inputadmin").val() == ""){
    				
    				alert("沒有輸入承辦人名稱");
    				
    				return 0;
    				
    			}else if($("#rebookname").text() == ""){
    				
    				alert("無要還書籍名稱");
    				
    				return 0;
    				
    			}
    		}			
								
			function book_upload(){
				
				$.ajax({
					
					type: "POST" ,
						
					url: "advance_returns.php" ,
						
					data:{
				
					adv_no:$("#inputno").val(),
							
					rebookcode:$("#rebookcode").text(),
					
					rebookname:$("#rebookname").text(),
					
					admin:$("#inputadmin").val()
							
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
