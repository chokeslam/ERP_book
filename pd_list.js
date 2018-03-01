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
	
		var table =	$('#example').DataTable({
            	//"sPaginationType" : "full_numbers",
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
			"lengthMenu": [[12, 25, 50, -1], [12, 25, 50, "All"]],
			  		
			select: true,
			  	
			//用 ajax 到後端  advance_list.php 撈資料
        	"ajax": "pd_list.php",
        		
        		//資料擺放在列的順序	
        	"columns":[
        		        			
            		{ "data": "nno" },				//adv_no : 借貨單號
            						
            		{ "data": "PD_No" },			//school_name : 學校名稱
            						
            		{ "data": "note" },			//student_name : 學生名稱            	
            						
            		{ "data": "ST_Qty" },			//lend_date : 借貨日期
            	
            		{ "data": "ST_Place" },			//sales_name : 業務名稱            						                  	
      	 		
      	 			{ "data": "PR_Cdate" },
      	 	]
      		
  		});
			
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

});
