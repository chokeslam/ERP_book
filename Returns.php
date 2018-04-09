<?php session_start() ?>
<?php
	
    header('Content-Type: application/json; charset=UTF-8');
	
	$student = $_SESSION['student']; 
	$PD_No=$_REQUEST["book"];         	//書籍條碼編號
	$ST_Code =  $student['code'];		//學生條碼
	$ST_Place = $_REQUEST['place'];
	//print_r($student);
	//echo $ST_Code;
	//搜尋庫存TABLE
	include('mysql.php');
	
	$sql = "SELECT nno , PD_No , ST_Qty FROM pdstock where PD_No = '$PD_No' AND ST_Place = '$ST_Place'";
	
	$result= mysqli_query($my_db, $sql);
	
	$pdstock = mysqli_fetch_assoc($result); //庫存表
	//搜尋結束
	//print_r($pdstock);
	//搜尋書籍TABLE
	$booknno = $pdstock["nno"];   //書籍nno編號
	
	$studentnno = $student['nno'];

	$ST_Qty = $pdstock["ST_Qty"] + 1;  //庫存數量
	
	//print_r($pdstock);
	$sql = "SELECT nno , course , note FROM waywin_tp.note where nno = '$booknno' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$note = mysqli_fetch_assoc($result);

	$sql = "SELECT * FROM takebook WHERE student_nno = '$studentnno'";

	$result= mysqli_query($my_db, $sql);

	$rs = mysqli_fetch_assoc($result);

	//print_r($rs);

	$take = explode(';',$rs['takebook']);

	$taketime = array();

	array_pop($take);

	foreach ($take as $key => $value) {

		$taketime[$key] = strchr($take[$key],'_');

		$take[$key] = strchr($take[$key],'_',-1);
		
	}

	if (!isset($_REQUEST['place']) || empty($_REQUEST['place'])) {
		
        echo json_encode(array('msg' => '沒有選擇班別！'));

        return;
    }

	if (!isset($_REQUEST["book"]) || empty($_REQUEST["book"])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍編號！'));

        return;
    }
    
    if (!isset($note) || empty($note)) {
		
        echo json_encode(array('msg' => '書籍編號錯誤！'));

        return;
    }	
	
	
	if (in_array($booknno, $take) == FALSE){
		
		echo json_encode(array('msg' => '沒借過這本書！'));
				
		return 0;
	}

	$key = array_search($booknno,$take);

	$str = $take[$key].$taketime[$key];

	$returnarray = explode(';',$rs['takebook']);

	array_pop($returnarray);

	//print_r($returnarray);

	$key = array_search($str,$returnarray);

	unset($returnarray[$key]);

	//print_r($returnarray);


	//echo $str;
	Buckle_stock ($ST_Qty,$PD_No,$ST_Place);
	Transaction_IN($ST_Code,$PD_No) ;
	return_book($returnarray,$studentnno);
	$rw = reload($studentnno);
	echo json_encode(array('msg' => '退書成功！' , 'book' => "$rw" ));

//------------------------------------------------------fuction--------------------------------------------------------------//	
	
	//更改學生 已領取書籍functuon
	function return_book($returnarray,$studentnno){

		include('mysql.php');

		$str = implode(";",$returnarray).';';

		$sql = "UPDATE takebook SET takebook = '$str' WHERE student_nno = '$studentnno'";

		$result= mysqli_query($my_db, $sql);
	}
	
//---------------------------------------------------------------------------------------------------------------------------
	
	// 寫入異動表
	function Transaction_IN ($data1 , $data2){				//$data1 = $ST_Code (學生編號)  $data2 = $PD_No  (書籍編號)
		
		$formnumber = Form_number();
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$IN_Qty = 1;
		
		include('mysql.php');
		
		$sql = " INSERT INTO iostock VALUES 
				 (null , '$formnumber' , '$data2' , null , '$data1' , '$IN_Qty' , null , '$date' , CURRENT_TIMESTAMP)";
		
		$result= mysqli_query($my_db, $sql);
		//echo $formnumber;
		
	}
	
//--------------------------------------------------------------------------------------------------------------------------
		
	//取得流水號 function
	function Form_number (){
		
		include('mysql.php');
		
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
	function Buckle_stock ($ST_Qty,$PD_No,$ST_Place){					//$data1 = $ST_Qty (庫存數量)  $data2 = $PD_No  (書籍編號)
		
		include('mysql.php');
		
		$sql = "UPDATE pdstock set ST_Qty = '$ST_Qty' where PD_No = '$PD_No' AND ST_Place = '$ST_Place'";
		
		$result= mysqli_query($my_db, $sql);
		
		return 0;
	}
	
//---------------------------------------------------------------------------------------------------------------------------
	
	//重新載入function   	
	function reload($studentnno){
		
		include('mysql.php');
		//搜尋條件

		$sql = "SELECT * FROM takebook where student_nno = '$studentnno' ";

		$result = mysqli_query($my_db,$sql);

		$rw = mysqli_fetch_assoc($result);

		$takebook = explode(';',$rw['takebook']);

		array_pop($takebook);

		$taketime = array();

		foreach ($takebook as $key => $value) {

			$taketime[$key] = strchr($takebook[$key],"_");

			$takebook[$key] = strchr($takebook[$key],"_",1);
			
		}

		foreach ($takebook as $key => $value) {

			$sql = "SELECT note FROM waywin_tp.note WHERE nno = '$value'";

			$result = mysqli_query($my_db,$sql);

			$rw = mysqli_fetch_assoc($result);

			$takebook[$key] = $rw['note'];

		}

		foreach ($takebook as $key => $value) {

			$taketime[$key] = $takebook[$key].$taketime[$key];
		}

		$taketime = implode(";",$taketime);

		$taketime =str_replace("_", " ", $taketime);

		return $taketime;
	}	
	
?>