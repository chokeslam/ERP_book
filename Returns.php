<?php session_start() ?>
<?php
	
    header('Content-Type: application/json; charset=UTF-8');
	
	$student = $_SESSION['student']; 
	$PD_No=$_REQUEST["book"];         	//書籍條碼編號
	$ST_Code =  $student['code'];		//學生條碼
	//print_r($student);
	//echo $ST_Code;
	//搜尋庫存TABLE
	$my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = "SELECT nno , PD_No , ST_Qty FROM pdstock where PD_No = '$PD_No' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$pdstock = mysqli_fetch_assoc($result); //庫存表
	//搜尋結束
	//print_r($pdstock);
	//搜尋書籍TABLE
	$booknno = $pdstock["nno"];   //書籍nno編號
	
	$ST_Qty = $pdstock["ST_Qty"] + 1;  //庫存數量
	
	//print_r($pdstock);
	
	$sql = "SELECT nno , course , note FROM note where nno = '$booknno' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$note = mysqli_fetch_assoc($result); //書籍資料
	
	
	//搜尋結束
	//print_r($note);
	$takebook = $note['note']."_". $note['course'];    //刪除用字串
	//echo $takebook;
	//分割 $student['take'] 欄位字串
	$take = explode(";", $student['take']);		////學生已領過的書籍  將   ' ; ' 拿掉後  放入陣列  $take中
	//print_r($take);
	
	// 判斷有無輸入書籍編號
	if (!isset($_REQUEST["book"]) || empty($_REQUEST["book"])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍編號！'));

        return;
    }
    
    if (!isset($note) || empty($note)) {
		
        echo json_encode(array('msg' => '書籍編號錯誤！'));

        return;
    }	
	
	
	if (in_array($note['note'], $take) == FALSE){
		
		echo json_encode(array('msg' => '沒借過這本書！'));
				
		return 0;
	}

	return_book($note['note'],$ST_Code);
	
	Transaction_IN($ST_Code,$PD_No) ;
	
	Buckle_stock ($ST_Qty,$PD_No);
	
	$rw = reload($ST_Code);				
		
	$rw = $rw['taketime'];
	
	echo json_encode(array('msg' => '退書成功！' , 'book' => "$rw" ));
//------------------------------------------------------fuction--------------------------------------------------------------//	
	
	//更改學生 已領取書籍functuon
	function return_book($data1,$data2){
			
		$my_db= mysqli_connect("localhost" , "root" , "");	
		
		mysqli_select_db($my_db, "bookerp");
		
		mysqli_query($my_db,"SET NAMES 'utf8'");
		
		$sql= "SELECT take FROM student where code = '$data2' ";
		
		$result= mysqli_query($my_db, $sql);
		
		$rs = mysqli_fetch_row($result);
		
		$rs = $rs[0];	
		
		//echo $rs;
		
		$bookdocument = substr($rs,strpos($rs ,$data1),strpos($rs ,";")+1);
		
		$rs=str_replace($bookdocument, "", $rs);
		
		//echo $bookdocument;
		
		$sql = "UPDATE student set take = '$rs' where code = '$data2'";
		
		$result= mysqli_query($my_db, $sql);			
	}
	
//---------------------------------------------------------------------------------------------------------------------------
	
	// 寫入異動表
	function Transaction_IN ($data1 , $data2){				//$data1 = $ST_Code (學生編號)  $data2 = $PD_No  (書籍編號)
		
		$formnumber = Form_number();
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$IN_Qty = 1;
		
		$my_db= mysqli_connect("localhost" , "root" , "");	
		
		mysqli_select_db($my_db, "bookerp");
		
		mysqli_query($my_db,"SET NAMES 'utf8'");
		
		$sql = " INSERT INTO iostock VALUES 
				 (null , '$formnumber' , '$data2' , null , '$data1' , '$IN_Qty' , null , '$date' , CURRENT_TIMESTAMP)";
		
		$result= mysqli_query($my_db, $sql);
		//echo $formnumber;
		
	}
	
//--------------------------------------------------------------------------------------------------------------------------
		
	//取得流水號 function
	function Form_number (){
		
		$my_db= mysqli_connect("localhost" , "root" , "");	
		
		mysqli_select_db($my_db, "bookerp");
		
		mysqli_query($my_db,"SET NAMES 'utf8'");
		
		$sql = "SELECT MAX(IO_Blno) FROM iostock";
		
		$result= mysqli_query($my_db, $sql);
		
		$rs = mysqli_fetch_row($result);
		
		$rs = $rs[0];
		
		$date= date("Ymd",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		//echo substr($rs, 0, -3);
		
		if ($date == substr($rs, 0, -4)){
			
			return $rs+1;
			
		}else{
			
			return $date.'0001';
			
		}
			
		
	}
//--------------------------------------------------------------------------------------------------------------------------
	
	
	//加庫存 function     
	function Buckle_stock ($data1 , $data2){					//$data1 = $ST_Qty (庫存數量)  $data2 = $PD_No  (書籍編號)
		
		$my_db= mysqli_connect("localhost" , "root" , "");	
		
		mysqli_select_db($my_db, "bookerp");
		
		mysqli_query($my_db,"SET NAMES 'utf8'");
		
		$sql = "UPDATE pdstock set ST_Qty = '$data1' where PD_No = '$data2'";
		
		$result= mysqli_query($my_db, $sql);
		
		return 0;
	}
	
//---------------------------------------------------------------------------------------------------------------------------
	
	//重新載入function   	
	function reload($data){
		
		$my_db= mysqli_connect("localhost" , "root" , "");
	
		mysqli_select_db($my_db, "bookerp");
	
		mysqli_query($my_db,"SET NAMES 'utf8'");
		//搜尋條件
		$sql = "SELECT * FROM student where code = '$data' ";	
	
		$result= mysqli_query($my_db, $sql);
		//將搜尋後學生資料放入  $rs
		$rs = mysqli_fetch_assoc($result);
	
	
	
		$takebook=$rs['take']; 
		$takebook=explode(";", $takebook);
		array_pop($takebook);
		$take_time = $takebook;
		$num=count($takebook);
	
		for ($i=0; $i < $num ; $i++) { 
			$take_time[$i] = strchr($take_time[$i],"_",1) .strrchr($take_time[$i],"_");
		}
		$take_time = implode(";", $take_time);
		$take_time =str_replace("_", " ", $take_time);	
				
		
		for ($i=0; $i < $num ; $i++) { 
			$takebook[$i] = substr($takebook[$i] , 0 , strpos($takebook[$i], "_"));
		}
			$takebook = implode(";", $takebook);
			$rs['take'] = $takebook;
			$rs['taketime'] = $take_time;
			$_SESSION['student'] = $rs;	
		
			return $rs;
		}	
	
?>