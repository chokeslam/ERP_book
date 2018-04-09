<?php
	
	header('Content-Type: application/json; charset=UTF-8');
	
	$schoolname = $_REQUEST['schoolname'];
						
	$studentname = $_REQUEST['studentname'];
						
	$salesname = $_REQUEST['salesname'];

	$ST_Place = $_REQUEST['place'];
						
	$bookname = rtrim($_REQUEST['bookname']," ");
	
	$PD_No = rtrim($_REQUEST['pdno']," ");


	
	if (!isset($bookname) || empty($bookname)) {
		
        echo json_encode(array('msg' => '請輸入正確書籍條碼取得書籍名稱！'));

        return;
    }
	
	$nno_search= explode(" ", $bookname);
	
	//print_r($nno_search);
	
	//array_pop($nno_search);
	
	$num = count($nno_search);
	
	//$sql = "SELECT nno FROM note where note =";
	
	//$nno =  search ($nno_search,$sql,"nno");
	
	//print_r($nno);
	
	//$sql = "SELECT PD_No FROM pdstock where nno =";
	
	$PD_No = explode(" ", $PD_No);
	
	//print_r($PD_No);
	
	$adv_no = advance_number();						//預領單號 取得
		
	Buckle_stock($PD_No,$num,$ST_Place);					//執行扣庫存 function

	Transaction_out ($PD_No,$adv_no,$num);		//寫入異動紀錄表
	
	lendstock($adv_no,$schoolname,$studentname,$salesname,$bookname,$ST_Place);		//寫入借出紀錄表
	
	$msg = "借書成功";
	
	
	echo json_encode(array('msg' => "$msg"));
	
	
	 
//---------------------------------------------------------------------------------------------------------------------------
	//多目標搜尋
/*	function search ($search,$sql_string,$Fieldname){			//$search = 搜尋條件  $sql_string = sql字串   $Fieldname = 資料表欄位名稱
			
		$my_db= mysqli_connect("localhost" , "root" , "");
	
		mysqli_select_db($my_db, "bookerp");
	
		mysqli_query($my_db,"SET NAMES 'utf8'");	
		
		$target = array();
		
		
		foreach ($search as $key => $value) {
						
			$sql = "$sql_string"."'$value'";
		
			$result= mysqli_query ($my_db, $sql);
		
			$rs = mysqli_fetch_array($result);
			
			$rs = $rs["$Fieldname"];
		
			array_push($target,$rs);		
		
		}	
		
		return $target;
	}*/
	
//--------------------------------------------------------------------------------------------------------------------------	
	//扣庫存
	function Buckle_stock($data_PDNo,$data_num,$ST_Place){
					
		include('mysql.php');
		
		$target = array();
		
		//使用PD_NO(書籍編號)將各項書籍的數量抓出來放入陣列 $target
		foreach ($data_PDNo as $key => $value) {
						
			$sql = "SELECT ST_Qty FROM pdstock where PD_NO ='$value' AND ST_Place = '$ST_Place'";
		
			$result= mysqli_query ($my_db, $sql);
		
			$rs = mysqli_fetch_array($result);
			
			$rs = $rs["ST_Qty"];
			
			array_push($target,$rs);		
		
		}
		
		//用 迴圈 將 各項書籍數量 扣 1
		for ($i=0; $i <$data_num ; $i++) {
			 
			$target[$i] = $target[$i]-1;
			
		}
		//用 迴圈 將各項書籍的新數量 寫入;
		for ($i=0; $i < $data_num ; $i++) {
			
			$pdno = $data_PDNo[$i];
			
			$stqty = $target[$i];
			
			$sql = "UPDATE pdstock set ST_Qty = '$stqty' where PD_No = '$pdno' AND ST_Place = '$ST_Place'";
			
			$result= mysqli_query ($my_db, $sql);
			
		}
			
	}
	
//------------------------------------------------------------------------------------------------------------------------------------------	

	//取得預領單號 function
	function advance_number (){
		
		include('mysql.php');
		
		$sql = "SELECT MAX(adv_no) FROM iostock WHERE adv_no LIKE 'L-%'";
		
		$result= mysqli_query($my_db, $sql);
		
		$rs = mysqli_fetch_row($result);
		
		$rs = $rs[0];
		
		$date= date("Ymd",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$a = "L-"."$date"."<br>";
		$b = substr($rs, 0, -4);		
		if ("L-"."$date" == substr($rs, 0, -4)){
			
			$rs = substr($rs, 2)+1;
			$rs = "L-".$rs;
			return $rs;
			
		}else{
			
			return "L-".$date.'0001';
			
		}
			
		
	}
	
//------------------------------------------------------------------------------------------------------------------------------------------	
	
	
	//取得流水號 function
	function Form_number (){
		
		include('mysql.php');
		
		$sql = "SELECT MAX(IO_Blno) FROM iostock";
		
		$result= mysqli_query($my_db, $sql);
		
		$rs = mysqli_fetch_row($result);
		
		$rs = $rs[0];
		
		$date= date("Ymd",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		if ($date == substr($rs, 0, -4)){
			
			return $rs+1;
			
		}else{
			
			return $date.'0001';
			
		}
			
		
	}

//-------------------------------------------------------------------------------------------------------------------------------------------

	 
	//寫入異動表 function
	function Transaction_out ($data_pdno,$data_advno,$data_num){						//$data1 = $ST_Code (學生編號)  $data2 = $PD_No  (書籍編號)
		
		$formnumber = Form_number();
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$QT_Qty = 1;
		
		include('mysql.php');
		
		for ($i=0; $i <$data_num ; $i++) {
				$PD_no = $data_pdno[$i];
		$sql = " INSERT INTO iostock VALUES 
				 (null , '$formnumber' , '$PD_no' , '$data_advno' , 'null' , null , '$QT_Qty' , '$date' , CURRENT_TIMESTAMP)";
			//echo $sql;
			$result= mysqli_query($my_db, $sql);	 			
		}
		
	}	

//--------------------------------------------------------------------------------------------------------------------------------------------

	function lendstock ($advno,$school_name,$student_name,$sales_name,$book_name,$ST_Place) {
		
		$bookname = explode(" ", $book_name);
		
		$bookname = implode($bookname, ";");
		
		$bookname = $bookname.";";
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		include('mysql.php');
		
		$sql = "INSERT INTO lendstock VALUES
				(null , '$advno' , '$school_name' , '$student_name' , '$sales_name' , '$bookname' , '$ST_Place' , '$date' , null , CURRENT_TIMESTAMP)";
		
		$result= mysqli_query ($my_db, $sql);
	}
   
?>