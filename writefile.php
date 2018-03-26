<?php

    header('Content-Type: application/json; charset=UTF-8');
	
	$lendnno = $_REQUEST['lendnno'];
	
	$lendstudentnno = $_REQUEST['lendstudentnno'];
	
	$relendbook = $_REQUEST['lendbook'];


	include('mysql.php');
	
	$sql = "SELECT nno FROM waywin_tp.student where code = '$lendstudentnno'";
	
	$result= mysqli_query ($my_db, $sql);
	
	$rs = mysqli_fetch_assoc($result);
	
	$nno = $rs['nno'];

	if(!isset($_REQUEST['lendbook']) || empty($_REQUEST['lendbook'])){
		
		echo json_encode(array('msg' => '2'));
		
		return;
		
	}
	
	if(!isset($_REQUEST['lendstudentnno']) || empty($_REQUEST['lendstudentnno'])){
		
		echo json_encode(array('msg' => '3'));
		
		return;
		
	}
	
	if(!isset($rs) || empty($rs)){
		
		echo json_encode(array('msg' => '4'));
		
		return;
		
	}

	$sql = "SELECT takebook FROM takebook WHERE student_nno = '$nno'";

	$result= mysqli_query ($my_db, $sql);
	
	$takebook = mysqli_fetch_assoc($result);

	$takebook = $takebook['takebook'];

	//print_r($takebook);

//如果沒有領書資料
	if (!isset($takebook) || empty($takebook)) {

		date_default_timezone_set('Asia/Taipei');

		$date = date("Y/m/d H:i:s");

		$relendbook = explode(",",$relendbook);

		array_pop($relendbook);

		$bookarray = array();

		foreach ($relendbook as $key => $value) {

			$sql = "SELECT nno FROM waywin_tp.note WHERE note = '$value'";

			$result= mysqli_query ($my_db, $sql);

			$rs = mysqli_fetch_assoc($result);

			$getbook = $rs['nno'].'_'.$date;

			array_push($bookarray,$getbook);

		}

		$getbook = implode(";",$bookarray).';';

		$PD_Noarray = getPD_No($bookarray);

		Transaction_IN($lendnno,$PD_Noarray);

		Transaction_out ($lendstudentnno,$PD_Noarray);

		update_lendstock($lendnno,$relendbook);

		$sql = "INSERT INTO takebook VALUES('$nno','$getbook')";

		$result= mysqli_query ($my_db, $sql);

		echo json_encode(array('msg' => '轉換成功'));



	}

//如果有領書資料

	$takebook = explode(';',$takebook);

	array_pop($takebook);

//	把已領書籍的資料作整理
	foreach ($takebook as $key => $value) {

		$takebook[$key] = strchr($takebook[$key],'_',-1);

	}

	//print_r($takebook);

	$relendbook = explode(",",$relendbook);

	array_pop($relendbook);

	//print_r($relendbook);

	$bookarray = array();

//用書名搜尋書的nno
	foreach ($relendbook as $key => $value) {

		$sql = "SELECT nno FROM waywin_tp.note WHERE note = '$value'";

		$result= mysqli_query ($my_db, $sql);

		$rs = mysqli_fetch_assoc($result);

		$getbook = $rs['nno'];

		array_push($bookarray,$getbook);

	}

	

//	比對這次還的書跟已領的書是否有重複
	$Duplicate = array_intersect($bookarray,$takebook);

	//print_r($Duplicate);

	$Duplicatebook ='';

//重複領書
	if(!empty($Duplicate)){

		foreach ($Duplicate as $key => $value) {
			$Duplicatebook = $Duplicatebook . $relendbook[$key] . ",";
		}
		$Duplicatebook = substr($Duplicatebook, 0 ,-1);

		$num = count($Duplicate);
		
		$msg = array(
					"book"=>"$Duplicatebook",
					"num"=>"$num"
				);
		echo json_encode($msg);

		return;
	}

	date_default_timezone_set('Asia/Taipei');

	$date = date("Y/m/d H:i:s");

	foreach ($bookarray as $key => $value) {
		$bookarray[$key] = $bookarray[$key]."_".$date;
	}
	//print_r($bookarray);
	$getbook = implode(";",$bookarray).';';
	//print_r($getbook);

	$sql = "SELECT takebook FROM takebook WHERE student_nno = '$nno'";

	$result= mysqli_query ($my_db, $sql);
	
	$takebook = mysqli_fetch_assoc($result);

	$takebook = $takebook['takebook'].$getbook;

	$PD_Noarray = getPD_No($bookarray);

	Transaction_IN($lendnno,$PD_Noarray);

	Transaction_out ($lendstudentnno,$PD_Noarray);

	update_lendstock($lendnno,$relendbook);

	$sql = "UPDATE takebook SET takebook = '$takebook' WHERE student_nno = '$nno'";

	$result= mysqli_query ($my_db, $sql);

	//print_r($takebook);

	echo json_encode(array('msg' => '轉換成功'));


/*------------------------------------------------------------------------------------------------------------------------------------------*/
function Transaction_out ($ST_Code,$PD_Noarray){

	include('mysql.php');

	$formnumber = Form_number();

	$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));

	$QT_Qty = 1;

	foreach ($PD_Noarray as $key => $value) {

		$sql = " INSERT INTO iostock VALUES 

				 (null , '$formnumber' , '$value' , null , '$ST_Code' , null , '$QT_Qty' , '$date' , CURRENT_TIMESTAMP)";
		
		$result= mysqli_query($my_db, $sql);

	}

}

function Transaction_IN($adv_no,$PD_Noarray){

	include('mysql.php');

	$formnumber = Form_number();

	$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));

	$IN_Qty = 1;

	foreach ($PD_Noarray as $key => $value) {

		$sql = " INSERT INTO iostock VALUES 

				 (null , '$formnumber' , '$value' , '$adv_no' , null , '$IN_Qty' , null , '$date' , CURRENT_TIMESTAMP)";
		
		$result= mysqli_query($my_db, $sql);
	}

}

//取 PD_No 陣列
function getPD_No($bookarray){

	foreach ($bookarray as $key => $value) {

		$bookarray[$key] = strchr($bookarray[$key],'_',-1);
	}

	include('mysql.php');

	$PD_Noarray = array();

	foreach ($bookarray as $key => $value) {

		$sql = "SELECT PD_No FROM pdstock where nno = '$value'";

		$result= mysqli_query ($my_db, $sql);

		$rs = mysqli_fetch_assoc($result);

		$PD_Noarray[$key] = $rs['PD_No'];
	}
	return $PD_Noarray;
}

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

	function update_lendstock($lendnno,$relendbook){
		
		include('mysql.php');
		//print_r($lendnno);
		//print_r($relendbook);

		$sql = "SELECT adv_no , student_name , book_name  FROM lendstock where adv_no = '$lendnno'";

		$result= mysqli_query ($my_db, $sql);

		$rs = mysqli_fetch_assoc($result);

		$lendbook = $rs['book_name'];

		$lendbook = substr($lendbook, 0 ,-1);

		$lendbook = explode(";" ,"$lendbook" );

		//print_r($lendbook);

		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));

		$num = count($relendbook);

		for ($i=0; $i <$num ; $i++) { 

			$key = array_search($relendbook[$i], $lendbook);

			unset($lendbook[$key]);
			
		}

		if (empty($lendbook)) {

			$sql = "UPDATE lendstock set book_name = '',end_date = '$date' WHERE adv_no = '$lendnno'";

			$result= mysqli_query($my_db, $sql);
			
			return;

		}else {
			
			$lendbook = implode(";", $lendbook) . ";";
			
			$sql = "UPDATE lendstock set book_name = '$lendbook' WHERE adv_no = '$lendnno'";
			
			$result= mysqli_query($my_db, $sql);
				
			return;

			}
		//print_r($lendbook);
	}

?>