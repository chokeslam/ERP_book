<?php session_start() ?>
<?php
	header('Content-Type: application/json; charset=UTF-8');
	//執行學生資料查詢
	
	//從前端接收的資料
	$code=$_REQUEST["code"];
	//連DB
	include("mysql.php");
	//搜尋條件
	$sql = "SELECT * FROM waywin_tp.student where code = '$code' ";
	//判斷有沒輸入
	if (!isset($_REQUEST["code"]) || empty($_REQUEST["code"])) {
		
        echo json_encode(array('msg' => '沒有輸入學生編號！'));

        return;
    }

	$result= mysqli_query ($my_db, $sql);
	//將搜尋後學生資料放入  $rs
	$rs = mysqli_fetch_assoc ($result);
	//print_r($rs);
	$date = date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	$date = decrease10years($date);
	//判斷該如何處理停權時間資料
	//$rs['halt_date_period'] = "2006-05-17~2018-08-31_~2007-08-31";
	//$rs['halt_date_period'] = "2006-05-17~2008-08-31_2007-08-31~2018-08-31";
	//$rs['halt_date_period'] = "2017-05-17";
	//$rs['halt_date_period'] = "_2017-08-31";
	//$rs['halt_date_period'] = "_2007-08-31~2018-08-31";
	$halfdate = explode("_", $rs['halt_date_period']);
	//print_r($halfdate);
	
	//判斷有沒有搜尋到資料
	if (isset($rs)){
		
	//判斷全部停權日期 及 部分停權日期	
	//如果 $halfdate的array 內 [0]與[1]的位置都有值 代表 全部停權日期 及部分停權日期都有值
	if(empty($halfdate[0]) == FALSE && empty($halfdate[1]) == FALSE){
		
		$parthalfdate = $halfdate[1];
		$allhalfdate = $halfdate[0];
		
	//如果 $halfdate的array 內 [0]的位置都有值  [1]的位置沒有值 代表 全部停權日期有值	
	}else if(empty($halfdate[0]) == FALSE && empty($halfdate[1]) == TRUE){
		
		$allhalfdate = $halfdate[0];
		$parthalfdate = null;
		
	//如果 $halfdate的array 內 [1]的位置都有值  [0]的位置沒有值 代表 部分停權日期有值	
	}else if(empty($halfdate[0]) == TRUE && empty($halfdate[1]) == FALSE){
		
		$parthalfdate = $halfdate[1];
		$allhalfdate = null;
	}else{
		
		$parthalfdate = null;	
		$allhalfdate = null;
	}
	
	$parthalfdate=explode("~",$parthalfdate);
	$allhalfdate =explode("~",$allhalfdate);
	
	//print_r($parthalfdate);
	//print_r($allhalfdate);
	
	
	
	
		//判斷學生 條碼     y = 有效  ,  n = 過期   , b = 全退    ,  c = 沒課程
		
		if($rs['validatesta'] == 'n'){
			
			echo json_encode(array('msg' => '此學生條碼已過期！'));
			
			return;
		}
		
		if($rs['validatesta'] == 'b'){
			
			echo json_encode(array('msg' => '此學生已退費！'));
			
			return;
		}
		
		if($rs['validatesta'] == 'c'){
			
			echo json_encode(array('msg' => '此學生沒有選課程！'));
			
			return;
					
		}
		
		
		
		/*if($rs['halt_sta'] == 'y' && empty($allhalfdate[0]) && empty($allhalfdate[1])){
			echo "此學生已被停權 無時間";
			echo json_encode(array('msg' => '此學生帳號已被停權！'));
			
			return;
		}*/
		
		//  halt_sta = y 且只有開始時間 	且目前時間大於開始時間
		if($rs['halt_sta'] == 'y' && !empty($allhalfdate[0]) && 
		
			empty($allhalfdate[1])  && strtotime($date) >= strtotime($allhalfdate[0])){
				
			$allhalfdate[0] = increase10years($allhalfdate[0]);	
			
			$msg =  "此學生帳號從 ".$allhalfdate[0]."開始被停權!";
			
			echo json_encode(array('msg' => "$msg"));
			
			return;
		}
			
		//  halt_sta = y 且只有結束時間 且目前時間小於結束時間	
		if($rs['halt_sta'] == 'y' && empty($allhalfdate[0]) && 
			
			!empty($allhalfdate[1]) && strtotime($date) <= strtotime($allhalfdate[1]) ){
				
			$allhalfdate[1] = increase10years($allhalfdate[1]);	
				
			$msg = "此學生帳號已被停權至 ".$allhalfdate[1];
			
			echo json_encode(array('msg' => "$msg"));
			
			return;
		}
			
		// halt_sta = y 有開始時間及結束時間 且目前時間在時間區間內	
		if($rs['halt_sta'] == 'y' && !empty($allhalfdate[0]) && 
		
			!empty($allhalfdate[1])&& strtotime($date) <= strtotime($allhalfdate[1])){
			
			$allhalfdate[0] = increase10years($allhalfdate[0]);	
			
			$allhalfdate[1] = increase10years($allhalfdate[1]);	
				
			$msg = "此學生帳號從 ".$allhalfdate[0]."開始停權至".$allhalfdate[1];
			
			echo json_encode(array('msg' => "$msg"));
			
			return;
		}				


			
		
		/*if($rs['halt_func_sta'] == 'y' && stristr($rs['halt_part_func'] , "fee-note") == TRUE && 
		
		empty($parthalfdate[0]) && empty($parthalfdate[1])){
			echo "此學生領書功能已被停權 無時間";
			echo json_encode(array('msg' => '此學生領書功能已被停權！'));
			
			return;
		}*/
		
		//halt_func_sta = y 和 halt_part_func = fee-note 且只有開始時間 且目前時間大於開始時間
		if($rs['halt_func_sta'] == 'y' && stristr($rs['halt_part_func'] , "fee-note") == TRUE && 
		
			!empty($parthalfdate[0]) && empty($parthalfdate[1])  && strtotime($date) >= strtotime($parthalfdate[0])){
			
			$parthalfdate[0] = increase10years($parthalfdate[0]);
				
			$msg = "此學生 ' 領書功能 ' 從 ".$parthalfdate[0]."開始被停權";
			
			echo json_encode(array('msg' => "$msg"));
			
			return;
		}
		
		//halt_func_sta = y 和 halt_part_func = fee-note 且只有結束時間 且目前時間小於結束時間
		if($rs['halt_func_sta'] == 'y' && stristr($rs['halt_part_func'] , "fee-note") == TRUE && 
		
			empty($parthalfdate[0]) && !empty($parthalfdate[1]) && strtotime($date) <= strtotime($parthalfdate[1]) ){
			
			$parthalfdate[1] = increase10years($parthalfdate[1]);
				
			$msg = "此學生 ' 領書功能 ' 已被停權至 ".$parthalfdate[1];
			
			echo json_encode(array('msg' => "$msg"));
			
			return;
		}
			
		//halt_func_sta = y 和 halt_part_func = fee-note 有開始時間及結束時間 且目前時間在時間區間內	
		if($rs['halt_func_sta'] == 'y' && stristr($rs['halt_part_func'] , "fee-note") == TRUE && 
		
			!empty($parthalfdate[0]) && !empty($parthalfdate[1])&& strtotime($date) <= strtotime($parthalfdate[1])){
				
			$parthalfdate[0] = increase10years($parthalfdate[0]);
			
			$parthalfdate[1] = increase10years($parthalfdate[1]);
					
			$msg = "此學生 ' 領書功能 ' 功能從 ".$parthalfdate[0]."開始停權至".$parthalfdate[1];
			
			echo json_encode(array('msg' => "$msg"));
			
			return;
		}

		$student_code = $rs['code'];

		$student_name = $rs['name'];

		$student_nno = $rs['nno'];

		$student_course = $rs['course'];

		$student_classify = $rs['classify'];

		$student_classname = $rs['class_name'];

		$sql = "SELECT * FROM takebook where student_nno = '$student_nno' ";

		$result = mysqli_query($my_db,$sql);

		$rw = mysqli_fetch_assoc($result);

		$takebook = explode(';',$rw['takebook']);

		array_pop($takebook);

		//print_r($takebook);
		$taketime = array();
		foreach ($takebook as $key => $value) {
			$taketime[$key] = strchr($takebook[$key],"_");
			$takebook[$key] = strchr($takebook[$key],"_",1);
			
		}
		// print_r($takebook);
		// print_r($taketime);
		foreach ($takebook as $key => $value) {
			$sql = "SELECT note FROM waywin_tp.note WHERE nno = '$value'";
			$result = mysqli_query($my_db,$sql);
			$rw = mysqli_fetch_assoc($result);
			$takebook[$key] = $rw['note'];
		}
		foreach ($takebook as $key => $value) {
			$taketime[$key] = $takebook[$key].$taketime[$key];
		}
		// print_r($takebook);

		// print_r($taketime);

		$taketime = implode(";",$taketime);

		$taketime =str_replace("_", " ", $taketime);
		// $rs = [
		// 		'code'=>$student_code,
		// 		'name'=>$student_name , 
		// 		'classify'=>$student_classify,
		// 		'class_name'=>$student_classname,
		// 		'course'=>$student_course,
		// 		'taketime'=>$taketime,

		// 	  ];
		$rs['taketime'] = $taketime;
		echo json_encode($rs);

		
		
		// print_r($rs);
		// print_r($taketime);
		// ------------------------------------------------------------------------------
		// $takebook=$rs['take']; 
		// $takebook=explode(";", $takebook);

		// array_pop($takebook);
		// $take_time = $takebook;
		// //print_r($take_time);
		// //$take_time['0']= strchr($takebook['0'],"_",1) .strchr($takebook['0'],"2");
		// //echo $take_time['0'];
		// //print_r(strchr($takebook['0'],"_",1));
		// //print_r(strchr($takebook['0'],"2"));
		// $num=count($takebook);
		
		// for ($i=0; $i < $num ; $i++) { 
		// 	$take_time[$i] = strchr($take_time[$i],"_",1) .strrchr($take_time[$i],"_");
		// }
		// $take_time = implode(";", $take_time);
		// $take_time =str_replace("_", " ", $take_time);
		// //print_r($take_time);
				
		// //print_r($takebook);
		// //echo $takebook[0];
		
		// for ($i=0; $i < $num ; $i++) { 
		// 	$takebook[$i] = substr($takebook[$i] , 0 , strpos($takebook[$i], "_"));
		// }
		// $takebook = implode(";", $takebook);
		// $rs['take'] = $takebook;
		// $rs['taketime'] = $take_time;
		// //echo $rs["course"];
		// //$_SESSION['student'] = $rs;		
		// //print_r($rs);
		// echo json_encode($rs);
		 
	}else{
		//沒搜尋到資料
		echo json_encode(array('msg' => '沒有該學生！'));
		
	}

	

	//更正資料庫抓出來的時間
	function decrease10years($data){
		
	$date=date_create($data);
	
	date_sub($date,date_interval_create_from_date_string("10 years"));
	
	return date_format($date,"Y-m-d");
	
	}
	
	function increase10years($data){
		
	$date=date_create($data);
	
	date_add($date,date_interval_create_from_date_string("10 years"));
	
	return date_format($date,"Y-m-d");
	
	}
	
	
?>