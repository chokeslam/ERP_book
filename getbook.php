<?php session_start() ?>
<?php	
	header('Content-Type: application/json; charset=UTF-8');
	//學生資料由SESSION 傳入
    $student = $_SESSION['student'];
	$PD_No=$_REQUEST["book"];
	$ST_Code =  $student['code'];	
	
	//print_r($student);
	
	//搜尋庫存TABLE
	$my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = "SELECT nno , PD_No , ST_Qty FROM pdstock where PD_No = '$PD_No' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$pdstock = mysqli_fetch_assoc($result);
	//搜尋結束
	
	//搜尋書籍TABLE
	$booknno = $pdstock["nno"];   //書籍編號
	
	$ST_Qty = $pdstock["ST_Qty"] - 1;  //庫存數量
	
	//print_r($pdstock);
	
	$sql = "SELECT nno , course , note FROM note where nno = '$booknno' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$note = mysqli_fetch_assoc($result);
	
	$coursename = $note['course'];
	
	$sql = "SELECT nno FROM teacher where course = '$coursename' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$coursenno = mysqli_fetch_assoc($result);
	
	$coursenno = $coursenno['nno'];
	
	$studentnno = $student['nno'];
	
	$sql = "SELECT * FROM member_1_permission where studb_nno = '$studentnno' AND course_nno = '$coursenno'";
	
	$result= mysqli_query($my_db, $sql);
	
	$courseloa = mysqli_fetch_assoc($result);
	
	//print_r($courseloa);
	
	$date = date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	
	$date = decrease10years($date);

	//print_r($courseloa['halt_date_period']);

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
	//搜尋結束
	//print_r($note);
	$takebook = $note['note']."_". $note['course'];    //領取時 寫入已領取表格用的 書籍格式
	//echo $takebook;
	
	
	//分割 $student['course'] 欄位字串
	$course = explode(";", $student['course']);  //學生報名的科目 將   ' ; ' 拿掉後  放入陣列  $course中
	//分割 $student['take'] 欄位字串
	$take = explode(";", $student['take']);		////學生已領過的書籍  將   ' ; ' 拿掉後  放入陣列  $take中


	//分割並重組 比對課程老師字串   以  微積分秋@程中@講義@01 為例
	
	$string1 =  strchr($note['note'], "@" ,1);       //  切割後 為  " 微積分秋  "
	
	$string2 = substr(strchr($note['note'], "@") , 1);   // 切割後 為   " 程中@講義@01  "
	
	$string3 = strchr($string2, "@" ,1);    //   切割後 為   " 程中  "

	$newstring = $string1 . "@" .$string3;  //  重組後為  " 微積分秋@程中   "

	
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
    
	//判斷  是否是該學生報名的科目   $note['course'] 是否在 $course陣列裡
	
	
	if (!in_array($note['course'], $course)){
	
		echo json_encode(array('msg' => '未報名的科目！'));
		
		return 0;
	}
	
	//用  @ 判斷這個科目是否有多個老師
	if (strchr($note['note'] , "@") == FALSE){
		
		//判斷單一老師的條件下  是否有領過書
		
		//將 $note['note'] ( 書籍名稱 ' 微積分秋@程中@講義@01 ') 放入陣列$take 中搜尋 
		
		
		if (in_array($note['note'], $take)){
				
			$time = strchr(strchr($student['taketime'], $note['note']),";" , 1);
			$time = substr(strchr($time," "),1);
			
			echo json_encode(array('msg' =>$note['note'] . '<br />'.'這本書已在 ' . $time . " 領過"));
			
				return 0;	
				
			}else{
				
					Buckle_stock ($ST_Qty,$PD_No) ;		//執行扣庫存的 function 
		
					Transaction_out($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
			
					take_book ($takebook,$ST_Code);		//執行寫入已領取書籍functuon
				
		$rw = reload($ST_Code);				
		
		$rw = $rw['taketime'];
		
		echo json_encode(array('msg' => '可以領！' , 'book' => "$rw" ));
				
				return 0;
				
			}
		
	}
	
	//判斷 同科目多個老師的條件下可以領書的條件
	
	//判斷條件  #1 為   $string1(分割後的字串  ' 微積分秋 ') 是否有出現在  $student['take']( 拿過的書的字串內 ) 如沒有 出現 就為 未借過的書
	if (strchr($student['take'], $string1) == FALSE){
					
		
		Buckle_stock ($ST_Qty,$PD_No) ;		//執行扣庫存的 function 
		
		Transaction_out($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
		
		take_book ($takebook,$ST_Code);		//執行寫入已領取書籍functuon
		
		$rw = reload($ST_Code);				
		
		$rw = $rw['taketime'];
		
		echo json_encode(array('msg' => '沒領過 可以領！' , 'book' => "$rw" ));
				
	// 判斷條件 #2 為  $string1(分割後的字串  ' 微積分秋 ')  及 $newstring  (重組後字串 ' 微積分秋@程中 ') 是否有出現在  $student['take']( 拿過的書的字串內 ) 
	// 及  $note['note'] ( 書籍名稱 ' 微積分秋@程中@講義@01 ') 不在陣列 $take 中      如都符合 就為同老師 的書
	
	}else if (strchr($student['take'], $string1) == TRUE && strchr($student['take'],$newstring) == TRUE && in_array($note['note'], $take)==FALSE){
		
		
		Buckle_stock ($ST_Qty,$PD_No) ;		//執行扣庫存的 function 
		
		Transaction_out($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
		
		take_book ($takebook,$ST_Code);		//執行寫入已領取書籍functuon
		
		$rw = reload($ST_Code);				
		
		$rw = $rw['taketime'];
		
		echo json_encode(array('msg' => '同樣老師的課 可以領！' , 'book' => "$rw" ));
	
	//判斷 同科目多個老師的條件下不可領書的條件
		
	}else{
		
		//判斷條件  #1  為  將 $note['note'] ( 書籍名稱 ' 微積分秋@程中@講義@01 ') 放入陣列$take 中搜尋 
		//如有比對到 則不可借
		if (in_array($note['note'], $take)){
			
			$time = strchr(strchr($student['taketime'], $note['note']),";" , 1);
			
			$time = substr(strchr($time," "),1);
			
			echo json_encode(array('msg' =>$note['note'] . '<br />'.'這本書已在 ' . $time . " 領過"));
				
		
		//判斷條件 #2 為  $string1(分割後的字串  ' 微積分秋 ') 有出現在  $student['take']( 拿過的書的字串內 ) 且
						// $newstring  (重組後字串 ' 微積分秋@程中 ') 沒有出現在  $student['take']( 拿過的書的字串內 ) 則不能借
			
		}else if (strchr($student['take'], $string1) == TRUE && strchr($student['take'],$newstring) == FALSE) {
			
			$abb=null;
			
			foreach ($take as $key => $value) {
				if (strchr($value, $string1) == TRUE){
					$abb=$abb. $value;
				}
			}
	
			$string1 =  strchr($abb, "@" ,1);       //  切割後 為  " 微積分秋  "
	
			$string2 = substr(strchr($abb, "@") , 1);   // 切割後 為   " 程中@講義@01  "
	
			$string3 = strchr($string2, "@" ,1);    //   切割後 為   " 程中  "

			$abb = $string1 . "@" .$string3;  //  重組後為  " 微積分秋@程中   " 
				
			
			echo json_encode(array('msg' =>'已領過    "'.$string3.'"    老師的書！'));
			
		}
			
	}
	
	
	
	
	
//-----------------------------------------------------function--------------------------------------------------------------//
	
	
	//扣庫存 function     
	function Buckle_stock ($data1 , $data2){					//$data1 = $ST_Qty (庫存數量)  $data2 = $PD_No  (書籍編號)
		
		$my_db= mysqli_connect("localhost" , "root" , "");	
		
		mysqli_select_db($my_db, "bookerp");
		
		mysqli_query($my_db,"SET NAMES 'utf8'");
		
		$sql = "UPDATE pdstock set ST_Qty = '$data1' where PD_No = '$data2'";
		
		$result= mysqli_query($my_db, $sql);
		
		return 0;
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
	 
	//寫入異動表 function
	function Transaction_out ($data1 , $data2){						//$data1 = $ST_Code (學生編號)  $data2 = $PD_No  (書籍編號)
		
		$formnumber = Form_number();
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$QT_Qty = 1;
		
		$my_db= mysqli_connect("localhost" , "root" , "");	
		
		mysqli_select_db($my_db, "bookerp");
		
		mysqli_query($my_db,"SET NAMES 'utf8'");
		
		$sql = " INSERT INTO iostock VALUES 
				 (null , '$formnumber' , '$data2' , 'null' , '$data1' , null , '$QT_Qty' , '$date' , CURRENT_TIMESTAMP)";
		
		$result= mysqli_query($my_db, $sql);
		//echo $formnumber;
		
	}
	

//--------------------------------------------------------------------------------------------------------------------------	
	
	// 寫入學生 已領取書籍functuon
	
	function take_book ($data1 , $data2) {				//$data1 = $takebook (此次領取的書籍資料)  $data2 =  $ST_Code (學生編號) 
			
		$datetime = date ("Y/m/d H:i:s" , mktime(date('H')+8, date('i'), date('s'), date('m'), date('d'), date('Y'))) ; 
		
		$newtakebook = $data1 . "_" . $datetime . ";";
		
		$my_db= mysqli_connect("localhost" , "root" , "");	
		
		mysqli_select_db($my_db, "bookerp");
		
		mysqli_query($my_db,"SET NAMES 'utf8'");
		
		$sql= "SELECT take FROM student where code = '$data2' ";
		
		$result= mysqli_query($my_db, $sql);
		
		$rs = mysqli_fetch_row($result);
		
		$rs = $rs[0];
		
		$newtakebook = $rs . $newtakebook;
		
		$sql = "UPDATE student set take = '$newtakebook' where code = '$data2'";
		
		$result= mysqli_query($my_db, $sql);
		
	}

//--------------------------------------------------------------------------------------------------------------------------
	
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
	/*	$student['take'] = $student['take'].";".$note['note'];
	$_SESSION['student'] = $student;
	print_r($_SESSION['student']);*/
?>