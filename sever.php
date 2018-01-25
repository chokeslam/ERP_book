<?php session_start() ?>
<?php
	header('Content-Type: application/json; charset=UTF-8');
	//執行學生資料查詢
	
	//從前端接收的資料
	$code=$_REQUEST["code"];
	//連DB
	$my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	//搜尋條件
	$sql = "SELECT * FROM student where code = '$code' ";
	//判斷有沒輸入
	if (!isset($_REQUEST["code"]) || empty($_REQUEST["code"])) {
		
        echo json_encode(array('msg' => '沒有輸入學生編號！'));

        return;
    }
	
	
	$result= mysqli_query ($my_db, $sql);
	//將搜尋後學生資料放入  $rs
	$rs = mysqli_fetch_assoc ($result);
	//判斷有沒有搜尋到資料
	if (isset($rs)){
	
		$takebook=$rs['take']; 		
		$takebook=explode(";", $takebook);

		array_pop($takebook);
		$take_time = $takebook;
		//print_r($take_time);
		//$take_time['0']= strchr($takebook['0'],"_",1) .strchr($takebook['0'],"2");
		//echo $take_time['0'];
		//print_r(strchr($takebook['0'],"_",1));
		//print_r(strchr($takebook['0'],"2"));
		$num=count($takebook);
		
		for ($i=0; $i < $num ; $i++) { 
			$take_time[$i] = strchr($take_time[$i],"_",1) .strrchr($take_time[$i],"_");
		}
		$take_time = implode(";", $take_time);
		$take_time =str_replace("_", " ", $take_time);
		//print_r($take_time);
				
		//print_r($takebook);
		//echo $takebook[0];
		
		for ($i=0; $i < $num ; $i++) { 
			$takebook[$i] = substr($takebook[$i] , 0 , strpos($takebook[$i], "_"));
		}
		$takebook = implode(";", $takebook);
		$rs['take'] = $takebook;
		$rs['taketime'] = $take_time;
		//echo $rs["course"];
		$_SESSION['student'] = $rs;		
		//print_r($rs);
		echo json_encode($rs);	
		 
	}else{
		//沒搜尋到資料
		echo json_encode(array('msg' => '沒有該學生！'));
		
	}

	//執行  借書資料查詢
	
	
	
?>