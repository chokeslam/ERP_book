	$(document).ready(function() {
    
    $("#search").on('click',A1);
	
	$("#code").keypress(function(){
  		if (event.which === 13){
			A1();
    	}   
  	});
	function A1 () {
	  $.ajax({
            type: "GET",
            url: "sever.php?code=" + $("#code").val(),
            dataType: "json",
            success: function(data) {
                
                if (data.code) {
                	
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
                    $("#searchname").html(data.msg);
                }
            },
            error: function(jqXHR) {
                alert("發生錯誤: " + jqXHR.status);
            }
        });
	};
});