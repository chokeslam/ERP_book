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
		$("#addmodal2").on('show.bs.modal', function(){
 			
    		var $this = $(this);
          
        	var $modal_dialog = $this.find('.modal-dialog');
           
        	$this.css('display', 'block');
          
        	$modal_dialog.css({'margin-top': Math.max(0, ($(window).height() - $modal_dialog.height()) / 2) });
          
   	 	});
		$("#createcode").on('show.bs.modal', function(){
 			
    		var $this = $(this);
          
        	var $modal_dialog = $this.find('.modal-dialog');
           
        	$this.css('display', 'block');
          
        	$modal_dialog.css({'margin-top': Math.max(0, ($(window).height() - $modal_dialog.height()) / 2) });
          
   	 	});
		$("#createstock").on('show.bs.modal', function(){
 			
    		var $this = $(this);
          
        	var $modal_dialog = $this.find('.modal-dialog');
           
        	$this.css('display', 'block');
          
        	$modal_dialog.css({'margin-top': Math.max(0, ($(window).height() - $modal_dialog.height()) / 2) });
          
   	 	});

		$("#createsold").on('show.bs.modal', function(){
 			
    		var $this = $(this);
          
        	var $modal_dialog = $this.find('.modal-dialog');
           
        	$this.css('display', 'block');
          
        	$modal_dialog.css({'margin-top': Math.max(0, ($(window).height() - $modal_dialog.height()) / 2) });
          
   	 	});
   	 	
   	 	$("#searchnote").on('click',function(){
					
			notenno_request ();
				
		});
		$("#searchnote1").on('click',function(){
					
			notenno_request1 ();
				
		});

		$("#notenno").keypress(function(){
  		
  		if (event.which === 13){
			
			$("#searchnote").click();

    	}   
  	
  		});
		$("#notenno1").keypress(function(){
  		
  		if (event.which === 13){
			
			$("#searchnote1").click();

    	}   
  	
  		});
		
		$("#ok1").on('click',function(){
					
			createbookcode_request ();
				
		});
		$("#ok2").on('click',function(){
					
			createstock_request ();
				
		});

		$("#soldok").on('click',function(){
					
			create_sold ();
				
		});
		
		$("#createstock").on('hide.bs.modal', function(){
		
		 window.location.reload();
	
		});

		$("#createcode").on('hide.bs.modal', function(){
		
		 window.location.reload();
	
		});
		
		$("#addmodal").on('hide.bs.modal', function(){
		
		 window.location.reload();
	
		});

		$("#addmodal2").on('hide.bs.modal', function(){
		
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

            		{ "data": "course" },			
            						
            		{ "data": "note" },			           	
            						
            		{ "data": "ST_Qty" },

            		{ "data": "ST_mi" },			
            	
            		{ "data": "ST_Place" },			          						                  	
      	 		
      	 			{ "data": "PR_Cdate" },

      	 			{ "data": "admin" },

      	 			{ "data": "PR_Update" },

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
    			
    			// { "width": "7%", "targets": 0 },
    			// { "width": "10%", "targets": 1 },
    			// { "width": "10%", "targets": 2 },
    			// { "width": "20%", "targets": 3 },
    			// { "width": "10%", "targets": 4 },
    			// { "width": "10%", "targets": 5 },
    			// { "width": "10%", "targets": 6 },
    			// { "width": "10%", "targets": 7 },
    			// { "width": "10%", "targets": 7 },
    			// { "width": "10%", "targets": 7 },
    			
  			],

  			  "createdRow": function( row, data, dataIndex ) {

  			  		var st_qty =Number($('td', row).eq(4).text());
  			  		var st_mi  =Number($('td', row).eq(5).text());
    				if ( st_qty < st_mi ) {
      				$('td', row).eq(4).addClass("bg-danger text-white");
      				$("#lowqty").append("<li>"+$('td', row).eq(3).text()+'('+$('td', row).eq(6).text()+")</li><br />");
    }
  }	
      	 			
  		});

  		$("#lowbtn").on('click' ,function(){

  			$("#table").removeClass("col-12");
  			$(".col-4").removeClass("col-7");
  			$("#tttt").show();


  		});

  		

		$("#x").on('click',function(){

			$("#tttt").hide();
			$("#table").addClass("col-12");
			$(".col-4").addClass("col-7");

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
    		
    		$("#place").replaceWith('<input type="text" readonly="readonly" class="form-control" id="place" style="margin-top: 10px;">');
    		
    		$("#ok2").unbind( );

    		$("#ok2").attr('id','ok3');

    		$("#msg5").unbind( );

    		$("#msg5").attr('id','msg6');
    		
    		$("#nnonote1").val($(this).parents('tr').children("td").eq(0).text());

     		$("#bookname2").val($(this).parents('tr').children("td").eq(3).text());
					
			 $("#pdno1").val($(this).parents('tr').children("td").eq(1).text());

			 $("#qty").val($(this).parents('tr').children("td").eq(4).text());

			 $("#miqty").val($(this).parents('tr').children("td").eq(5).text());
					
			 $("#place").val($(this).parents('tr').children("td").eq(6).text());	
    	
    		
    		
    		$("#ok3").on('click',function(){

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
					
					pdno : $("#pdno1").val(),
					qty : $("#qty").val(),
					miqty : $("#miqty").val(),
					place : $("#place").val(),
					admin : $("#admin1").val()
							
				} ,
						
				datatype: "json" ,
						
				success: function(data) {
					
					
					
					if (typeof data.msg == "undefined"){
						
						alert(data);
						window.location.href = "pd_list.html";
						
					}else{
						
						$("#msg6").text(data.msg);
					}
									
				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		};    
    	
//
    	function createstock_request (){
					
			$.ajax({
				type: "POST" ,
						
				url: "createstock.php" ,
						
				data:{
					
					notenno : $("#nnonote1").val(),
					pdno : $("#pdno1").val(),
					qty : $("#qty").val(),
					miqty : $("#miqty").val(),
					place : $("#place").val(),
					admin : $("#admin1").val()
							
				} ,
						
				datatype: "json" ,
						
				success: function(data) {
					
					
					
					if (typeof data.msg == "undefined"){
						
						alert(data);
						window.location.href = "pd_list.html";
						
					}else{
						
						$("#msg5").text(data.msg);
					}
									
				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		}; 
    	// 
    	function createbookcode_request (){
					
			$.ajax({
				type: "POST" ,
						
				url: "createbookcode.php" ,
						
				data:{
					
					notenno : $("#nnonote").val(),
					pdno : $("#pdno").val(),
					admin : $("#admin").val()
							
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
							
						$("#createcode").modal();
						
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

    	function notenno_request1 (){
					
			$.ajax({
				type: "POST" ,
						
				url: "searchnote.php" ,
						
				data:{
					
					notenno:$("#notenno1").val(),
							
				} ,
						
				datatype: "json" ,
						
				success: function(data) {
					
					if (typeof data.msg == "undefined"){
							
						$("#createstock").modal();

						$("#nnonote1").val(data.nno);
						
						$("#bookname2").val(data.note);
						
						$("#pdno1").val(data.PD_No);
					}else{
							
						$("#msg4").text(data.msg);
						
						$("#notenno").val("");	
					}
									
				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		};

    	function create_sold (){
					
			$.ajax({

				type: "POST" ,
						
				url: "createsold.php" ,
						
				data:{
					
					note:$("#soldbookname").val(),

					PD_No:$("#soldpdno").val(),

					ST_Qty:$("#soldqty").val(),

					ST_mi:$("#soldmiqty").val(),

					ST_Place:$("#soldplace").val(),

					admin:$("#soldadmin").val()
							
				} ,
						
				datatype: "json" ,
						
				success: function(data) {
					if (typeof data.msg == "undefined"){
						
						alert(data);
						window.location.href = "pd_list.html";
						
					}else{
						
						$("#soldmsg").text(data.msg);
					}
				} ,
        		error: function(jqXHR) {
            				
					alert("發生錯誤: " + jqXHR.status);
       	 		}
			});
		};
});
