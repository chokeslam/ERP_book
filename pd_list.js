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
		
		$("#addmodal").on('show.bs.modal', function(){
 			
    		var $this = $(this);
          
        	var $modal_dialog = $this.find('.modal-dialog');
           
        	$this.css('display', 'block');
          
        	$modal_dialog.css({'margin-top': Math.max(0, ($(window).height() - $modal_dialog.height()) / 2) });
          
   	 	});
   	 	
   	 	$("#searchnote").on('click',function(){
					
			notenno_request ();
				
		});

		$("#notenno").keypress(function(){
  		
  		if (event.which === 13){
			
			$("#searchnote").click();

    	}   
  	
  		});
		
		$("#ok1").on('click',function(){
					
			createstock_request ();
				
		});
		
		$("#createstock").on('hide.bs.modal', function(){
		
		 window.location.reload();
	
		});
		
		$("#addmodal").on('hide.bs.modal', function(){
		
		 window.location.reload();
	
		});
		

	    

						
		var table =	$('#example').DataTable({


        	"oLanguage" : {
        	
                	"sLengthMenu": "每頁顯示 _MENU_ 筆紀錄",
                
                	"sZeroRecords": "抱歉， 没有找到",
                
                	"sInfo": "從 _START_ 到 _END_ /共 _TOTAL_ 筆資料",
                
                	"sInfoEmpty": "沒有資料",
                
                	"sInfoFiltered": "(從 _MAX_ 筆資料中查詢)",
                
                	"sZeroRecords": "沒有符合的資料",
                
                 	"sSearch": "名稱:",
                 
                	"oPaginate": {
                	
                		"sFirst": "首页",
                		
                		"sPrevious": "上一頁",
                		
                		"sNext": "下一頁"         
                }           
       		},

			// datatable dom 位置排序  並使用 bootstrap "justify-content-center" 將底下分頁按鈕置中	
			dom:'Brt<"justify-content-center"p>',
			  	
			//每頁顯示的資料筆數調整
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			  		
			select: true,
			  	
			//用 ajax 到後端  advance_list.php 撈資料
        	"ajax": "pd_list.php",
        		
        		//資料擺放在列的順序	
        	"columns":[
        		    
        		    

            		{ "data": "nno"  },				
            						
            		{ "data": "PD_No" },			
            						
            		{ "data": "note" },			           	
            						
            		{ "data": "ST_Qty" },

            		{ "data": "ST_mi" },			
            	
            		{ "data": "ST_Place" },			          						                  	
      	 		
      	 			{ "data": "PR_Cdate" },

               		{                
                  	  "className":'click',
                  
                  	  "orderable":false,
                 	
                      "data":null,
                 	
                 	// bootstrap 互動視窗class
                      "defaultContent":'<button type="button" class="btn btn-link" data-toggle="modal" data-target="#createstock">'+"修改"+'</button>'
                    },        	 			
      	 	],
      	 	
      	 	"columnDefs": [
    			
    			{ className: "bbbb", "targets": [3] },
    			
    			{ "width": "10%", "targets": 0 },
    			{ "width": "10%", "targets": 1 },
    			{ "width": "20%", "targets": 2 },
    			{ "width": "10%", "targets": 3 },
    			{ "width": "10%", "targets": 4 },
    			{ "width": "10%", "targets": 5 },
    			{ "width": "10%", "targets": 6 },
    			{ "width": "10%", "targets": 7 }
    			
  			],
  			 "initComplete": function(settings, json) {
  			 	
  			 	
  			 		/* $('table tr').each(function () {
  			 		 	$('td').eq(3).css('color','red');
  			 		 });*/
  			 		$('tbody tr').each(function () {
  			 			
						var st_qty = parseInt($(this).children().eq(3).text());
						
						var st_mi  = parseInt($(this).children().eq(4).text());
                		
                		if(st_mi > st_qty){
                			
                			//$(this).children().eq(3).css('color' , 'red');
                			//$(this).css('color' , 'red');
                			$(this).children().eq(3).addClass("bg-danger text-white");

                			$("#lowqty").append("<li>"+$(this).children().eq(2).text()+"</li><br />");
                			
                		}
                	
            		});
  			 	
  			 	
    			
  			
  			}	
      	 			
  		});

  		$("#lowbtn").on('click' ,function(){

  			$("#table").removeClass("col-12");
  			$(".col-5").removeClass("col-8");
  			$("#tttt").show();


  		});

  		

		$("#x").on('click',function(){

			$("#tttt").hide();
			$("#table").addClass("col-12");
			$(".col-5").addClass("col-8");

		});
			
    		//每個欄位上的 搜尋欄位 
			$('#example tfoot th').each( function () {
				
        		var title = $(this).text();
        		
        		$(this).html( '<input type="text"  placeholder="搜尋 '+title+'"style="width: 100%;" />' );
        	
    		} );
 			
 			$('.click input').hide();
 			
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

    	$('#example tbody').on('click','td.click button',function(){
    		
    		 		
    		$("h5").text("修改書籍庫存");
    		
    		$("#place").replaceWith('<input type="text" class="form-control" id="place" style="margin-top: 10px;">');
    		
    		$("#ok1").attr('id','ok2');
    		
    		var nno =$(this).parents('tr').children("td").eq(0).text();
    		
    		searchpdstock_request (nno);
    	
    		$("#ok2").unbind( );
    		
    		$("#ok2").on('click',function(){
					
				updatestock_request ();
				
			});
    	 		
    	});		
		
	
    	/* $('#example tbody').on('click', 'tr', function () {
        	
        	var name = $('td', this).eq(3).text();
        	
        	
        		
        		$('td', this).eq(3).css('color','red');
        		//alert( '數量為 '+name+'' );
        	
        
    	} );	*/
    	
    	function updatestock_request (){
					
			$.ajax({
				type: "POST" ,
						
				url: "updatestock.php" ,
						
				data:{
					
					notenno : $("#nnonote").val(),
					pdno : $("#pdno").val(),
					qty : $("#qty").val(),
					miqty : $("#miqty").val(),
					place : $("#place").val()
							
				} ,
						
				datatype: "json" ,
						
				success: function(data) {
					
					
					
					if (typeof data.msg == "undefined"){
						
						alert(data);
						window.location.href = "pd_list.html";
						
					}else{
						
						$("#msg2").text(data.msg);
					}
									
				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		};    
    	
    	
    	function searchpdstock_request (nno){
					
			$.ajax({
				type: "POST" ,
						
				url: "searchpdstock.php" ,
						
				data:{
					
					notenno : nno,
										
				} ,
						
				datatype: "json" ,
						
				success: function(data) {
					
					$("#nnonote").val(data.nno);
						
					$("#course").val(data.course);
						
					$("#bookname1").val(data.note);
					
					$("#pdno").val(data.PD_No);
					
					$("#qty").val(data.ST_Qty);

					$("#miqty").val(data.ST_mi);
					
					$("#place").val(data.ST_Place);						
									
				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		};    	
    	
    	
    	function createstock_request (){
					
			$.ajax({
				type: "POST" ,
						
				url: "createstock.php" ,
						
				data:{
					
					notenno : $("#nnonote").val(),
					pdno : $("#pdno").val(),
					qty : $("#qty").val(),
					miqty : $("#miqty").val(),
					place : $("#place").val()
							
				} ,
						
				datatype: "json" ,
						
				success: function(data) {
					
					
					
					if (typeof data.msg == "undefined"){
						
						alert(data);
						window.location.href = "pd_list.html";
						
					}else{
						
						$("#msg2").text(data.msg);
					}
									
				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		};    	
    	
    	
    	function notenno_request (){
					
			$.ajax({
				type: "POST" ,
						
				url: "getnote.php" ,
						
				data:{
					
					notenno:$("#notenno").val(),
							
				} ,
						
				datatype: "json" ,
						
				success: function(data) {
					
					if (typeof data.msg == "undefined"){
							
						$("#createstock").modal();
						
						$("#nnonote").val(data.nno);
						
						$("#course").val(data.course);
						
						$("#bookname1").val(data.note);
								
					}else{
							
						$("#msg3").text(data.msg);
						
						$("#notenno").val("");	
					}
									
				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		};
});
