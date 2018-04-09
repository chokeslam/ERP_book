<?php session_start() ?>
<?php	
	header('Content-Type: application/json; charset=UTF-8');
	//學生資料由SESSION 傳入
    $student = $_SESSION['student'];

	$PD_No=$_REQUEST["book"];

	$ST_Code =  $student['code'];

	$ST_Place = $_REQUEST['place'];
	
	//搜尋庫存TABLE
	include('mysql.php');
	
	$sql = "SELECT nno , PD_No , ST_Qty FROM pdstock where PD_No = '$PD_No' AND ST_Place = '$ST_Place' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$pdstock = mysqli_fetch_assoc($result);
	//搜尋結束
	
	//搜尋書籍TABLE
	$booknno = $pdstock["nno"];   //書籍編號
	
	$ST_Qty = $pdstock["ST_Qty"] - 1;  //庫存數量
	
	$sql = "SELECT nno , course , note FROM waywin_tp.note where nno = '$booknno' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$note = mysqli_fetch_assoc($result);

	$coursename = $note['course'];

	$sql = "SELECT nno FROM waywin_tp.teacher where course = '$coursename'";
	
	$result= mysqli_query($my_db, $sql);
	
	$coursenno = mysqli_fetch_assoc($result);
	
	$coursenno = $coursenno['nno'];
	
	$studentnno = $student['nno'];
	
	$sql = "SELECT * FROM waywin_tp.member_1_permission where studb_nno = '$studentnno' AND course_nno = '$coursenno'";
	
	$result= mysqli_query($my_db, $sql);
	
	$courseloa = mysqli_fetch_assoc($result);
	
	$date = date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	
	$date = decrease10years($date);

	if(empty($courseloa['halt_date_period']) == FALSE){
		
		$haltdate=explode("~",$courseloa['halt_date_period']);
		
	}



	
	if ($courseloa['fee_note'] == "Y" && !empty($haltdate[0]) && 
		
		empty($haltdate[1])  && strtotime($date) >= strtotime($haltdate[0])){
					
		$haltdate[0] = 	increase10years($haltdate[0]);	
			
		$msg =  "此科目". " ' ".$coursename." ' "."已從".$haltdate[0]."開始停權";
		
		echo json_encode(array('msg' => "$msg"));
		
		return ;
		
	}
			
	if ($courseloa['fee_note'] == "Y" && empty($haltdate[0]) && 
		
		!empty($haltdate[1])  && strtotime($date) <= strtotime($haltdate[1])){
		
		$haltdate[1] = 	increase10years($haltdate[1]);
		
		$msg = "此科目". " ' ".$coursename." ' "."停權至".$haltdate[1];
		
		echo json_encode(array('msg' => "$msg"));
		
		return ;
		
	}
			
	if ($courseloa['fee_note'] == "Y" && !empty($haltdate[0]) && 
		
		!empty($haltdate[1])  && strtotime($date) <= strtotime($haltdate[1])){
			
		$haltdate[0] = 	increase10years($haltdate[0]);
		
		$haltdate[1] = 	increase10years($haltdate[1]);		
		
		$msg = "此科目"." ' ".$coursename." ' "."已從".$haltdate[0]."開始停權至".$haltdate[1];
		
		echo json_encode(array('msg' => "$msg"));
		
		return ;
		
	}

	$sql = "SELECT * FROM takebook WHERE student_nno = '$studentnno'";

	$result = mysqli_query($my_db,$sql);

	$rs = mysqli_fetch_assoc($result);

	// print_r($rs);

	$take = explode(';',$rs['takebook']);

	$taketime = array();

	array_pop($take);

	foreach ($take as $key => $value) {

		$taketime[$key] = strchr($take[$key],'_');

		$take[$key] = strchr($take[$key],'_',-1);
		
	}

	$takename = array();

	foreach ($take as $key => $value) {

		$sql = "SELECT note FROM waywin_tp.note WHERE nno = '$value'";

		$result = mysqli_query($my_db,$sql);

		$rw = mysqli_fetch_assoc($result);

		$takename[$key] = $rw['note'];

	}
	$takename = implode(';',$takename);

	//分割 $student['course'] 欄位字串
	$course = explode(";", $student['course']);  //學生報名的科目 將   ' ; ' 拿掉後  放入陣列  $course中

	//分割並重組 比對課程老師字串   以  微積分秋@程中@講義@01 為例
	
	$string1 =  strchr($note['note'], "@" ,1);       //  切割後 為  " 微積分秋  "
	
	$string2 = substr(strchr($note['note'], "@") , 1);   // 切割後 為   " 程中@講義@01  "
	
	$string3 = strchr($string2, "@" ,1);    //   切割後 為   " 程中  "

	$newstring = $string1 . "@" .$string3;  //  重組後為  " 微積分秋@程中   "

	if (!isset($_REQUEST["place"]) || empty($_REQUEST["place"])) {
		
        echo json_encode(array('msg' => '請選擇班別'));

        return;
    }
	// 判斷有無輸入書籍編號
	if (!isset($_REQUEST["book"]) || empty($_REQUEST["book"])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍編號！'));

        return;
    }
    
    if (!isset($note) || empty($note)) {
		
        echo json_encode(array('msg' => '書籍編號錯誤！'));

        return;
    }
    
	//判斷  是否是該學生報名的科目   $note['course'] 是否在 $course陣列裡
	
	
	if (!in_array($note['course'], $course)){
	
		echo json_encode(array('msg' => '未報名的科目！'));
		
		return 0;
	}

	if (in_array($note['nno'], $take)){
			
		$time = $taketime[array_search($note['nno'], $take)];

		$time = substr($time,1);

		echo json_encode(array('msg' =>$note['note'] . '<br />'.'這本書已在 ' . $time . " 領過"));

		return 0;
	}

	if (strchr($note['note'] , "@")){

		if (strchr($takename, $string1) && !strchr($takename,$newstring)){

			$takename = explode(";",$takename);

			$string=null;
			
			foreach ($takename as $key => $value) {
				if (strchr($value, $string1) == TRUE){
					$string=$string. $value;
				}
			}

			$string = substr(strchr($string, "@") , 1);

			$string = strchr($string, "@" ,1);

			 echo json_encode(array('msg' =>'已領過    "'.$string.'"    老師的書！'));

			 return 0;

		}
		//$string1(分割後的字串  ' 微積分秋 ') 是否有出現在  $takename( 拿過的書的字串內 ) 如沒有 出現 就為 未借過的書
		if (!strchr($takename, $string1)){

			Buckle_stock ($ST_Qty,$PD_No,$ST_Place) ;		//執行扣庫存的 function 
			
			Transaction_out($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
				
			take_book ($note['nno'],$studentnno);		//執行寫入已領取書籍functuon
				
			$rw = reload($studentnno);

			echo json_encode(array('msg' => '吳領過的多老師科目領取成功！' , 'book' => "$rw" ));

			return 0;

		}
		//$string1(分割後的字串  ' 微積分秋 ')  及 $newstring  (重組後字串 ' 微積分秋@程中 ') 是否有出現在  $student['take']( 拿過的書的字串內 )
		//如都符合 就為同老師 的書
		if (strchr($takename, $string1) && strchr($takename,$newstring)){

			Buckle_stock ($ST_Qty,$PD_No,$ST_Place) ;		//執行扣庫存的 function 
			
			Transaction_out($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
				
			take_book ($note['nno'],$studentnno);		//執行寫入已領取書籍functuon
				
			$rw = reload($studentnno);

			echo json_encode(array('msg' => '同老師領取成功！' , 'book' => "$rw" ));

			return 0;


		}

	}
		
		Buckle_stock ($ST_Qty,$PD_No,$ST_Place) ;		//執行扣庫存的 function 
		
		Transaction_out($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
			
		take_book ($note['nno'],$studentnno);		//執行寫入已領取書籍functuon
			
		$rw = reload($studentnno);

		echo json_encode(array('msg' => '無領過領取成功！' , 'book' => "$rw" ));

	
//-----------------------------------------------------function--------------------------------------------------------------//
	
	
	//扣庫存 function     
	function Buckle_stock ($ST_Qty,$PD_No,$ST_Place){					//$data1 = $ST_Qty (庫存數量)  $data2 = $PD_No  (書籍編號)
		
		include('mysql.php');
		
		$sql = "UPDATE pdstock set ST_Qty = '$ST_Qty' where PD_No = '$PD_No' AND ST_Place = '$ST_Place'";
		
		$result= mysqli_query($my_db, $sql);
		
		return 0;
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
	 
	//寫入異動表 function
	function Transaction_out ($data1 , $data2){						//$data1 = $ST_Code (學生編號)  $data2 = $PD_No  (書籍編號)
		
		$formnumber = Form_number();
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$QT_Qty = 1;
		
		include('mysql.php');
		
		$sql = " INSERT INTO iostock VALUES 
				 (null , '$formnumber' , '$data2' , 'null' , '$data1' , null , '$QT_Qty' , '$date' , CURRENT_TIMESTAMP)";
		
		$result= mysqli_query($my_db, $sql);
		//echo $formnumber;
		
	}
	

// //--------------------------------------------------------------------------------------------------------------------------	
	
	// 寫入學生 已領取書籍functuon

	function take_book ($data1 , $data2) {				//$data1 = $takebook (此次領取的書籍資料)  $data2 =  $ST_Code (學生編號) 
		
		date_default_timezone_set('Asia/Taipei');

		$datetime = date("Y/m/d H:i:s");
		
		$newtakebook = $data1 . "_" . $datetime . ";";
		
		include('mysql.php');
		
		$sql= "SELECT takebook FROM takebook where student_nno = '$data2' ";
		
		$result= mysqli_query($my_db, $sql);
		
		$rs = mysqli_fetch_row($result);
		
		$rs = $rs[0];
		
		$newtakebook = $rs . $newtakebook;
		
		$sql = "UPDATE takebook set takebook = '$newtakebook' where student_nno = '$data2'";
		
		$result= mysqli_query($my_db, $sql);
		
	}

// //--------------------------------------------------------------------------------------------------------------------------
	
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
	
//----------------------------------------------------------------------------------------------------------------------

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