<?php session_start() ?>
<?php	
	header('Content-Type: application/json; charset=UTF-8');
	//學生資料由SESSION 傳入
    $student = $_SESSION['student'];
	$PD_No=$_REQUEST["book"];
	$ST_Code =  $student['code'];	
	//echo $PD_No;
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
	//搜尋結束
	//print_r($note);
	$takebook = $note['note']."_". $note['course'];    //領取時 寫入已領取表格用的 書籍格式
	//echo $takebook;
	
	//判斷是否有報名這個科目
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
			
			
				echo json_encode(array('msg' => '這本書已領過！'));
			
				return 0;	
				
			}else{
				
					Buckle_stock ($ST_Qty,$PD_No) ;		//執行扣庫存的 function 
		
					Transaction($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
			
					take_book ($takebook,$ST_Code);		//執行寫入已領取書籍functuon
				
		$rw = ABCCC($ST_Code);				
		
		$rw = $rw['take'];
		
		echo json_encode(array('msg' => '可以領！' , 'book' => "$rw" ));
				
				return 0;
				
			}
		
	}
	
	//判斷 同科目多個老師的條件下可以領書的條件
	
	//判斷條件  #1 為   $string1(分割後的字串  ' 微積分秋 ') 是否有出現在  $student['take']( 拿過的書的字串內 ) 如沒有 出現 就為 未借過的書
	if (strchr($student['take'], $string1) == FALSE){
					
		
		Buckle_stock ($ST_Qty,$PD_No) ;		//執行扣庫存的 function 
		
		Transaction($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
		
		take_book ($takebook,$ST_Code);		//執行寫入已領取書籍functuon
		
		$rw = ABCCC($ST_Code);				
		
		$rw = $rw['take'];
		
		echo json_encode(array('msg' => '沒領過 可以領！' , 'book' => "$rw" ));
				
	// 判斷條件 #2 為  $string1(分割後的字串  ' 微積分秋 ')  及 $newstring  (重組後字串 ' 微積分秋@程中 ') 是否有出現在  $student['take']( 拿過的書的字串內 ) 
	// 及  $note['note'] ( 書籍名稱 ' 微積分秋@程中@講義@01 ') 不在陣列 $take 中      如都符合 就為同老師 的書
	
	}else if (strchr($student['take'], $string1) == TRUE && strchr($student['take'],$newstring) == TRUE && in_array($note['note'], $take)==FALSE){
		
		
		Buckle_stock ($ST_Qty,$PD_No) ;		//執行扣庫存的 function 
		
		Transaction($ST_Code,$PD_No) ;		//執行寫入異動表的 function 
		
		take_book ($takebook,$ST_Code);		//執行寫入已領取書籍functuon
		
		$rw = ABCCC($ST_Code);				
		
		$rw = $rw['take'];
		
		echo json_encode(array('msg' => '同樣老師的課 可以領！' , 'book' => "$rw" ));
	
	//判斷 同科目多個老師的條件下不可領書的條件
		
	}else{
		
		//判斷條件  #1  為  將 $note['note'] ( 書籍名稱 ' 微積分秋@程中@講義@01 ') 放入陣列$take 中搜尋 
		//如有比對到 則不可借
		if (in_array($note['note'], $take)){
			
			echo json_encode(array('msg' => '這本書已領過！'));
				
		
		//判斷條件 #2 為  $string1(分割後的字串  ' 微積分秋 ') 有出現在  $student['take']( 拿過的書的字串內 ) 且
						// $newstring  (重組後字串 ' 微積分秋@程中 ') 沒有出現在  $student['take']( 拿過的書的字串內 ) 則不能借
			
		}else if (strchr($student['take'], $string1) == TRUE && strchr($student['take'],$newstring) == FALSE) {
			
			echo json_encode(array('msg' => '已領過另一位老師的書！'));
			
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
	function Transaction ($data1 , $data2){						//$data1 = $ST_Code (學生編號)  $data2 = $PD_No  (書籍編號)
		
		$formnumber = Form_number();
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$QT_Qty = 1;
		
		$my_db= mysqli_connect("localhost" , "root" , "");	
		
		mysqli_select_db($my_db, "bookerp");
		
		mysqli_query($my_db,"SET NAMES 'utf8'");
		
		$sql = " INSERT INTO iostock VALUES 
				 (null , '$formnumber' , '$data2' , '$data1' , null , '$QT_Qty' , '$date' , CURRENT_TIMESTAMP)";
		
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

	function ABCCC($data){
		
	$my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	//搜尋條件
	$sql = "SELECT * FROM student where code = '$data' ";	
	
	$result= mysqli_query($my_db, $sql);
	//將搜尋後學生資料放入  $rs
	$rs = mysqli_fetch_assoc($result);
	//判斷有沒有搜尋到資料
	
	
	$takebook=$rs['take']; 
	$takebook=explode(";", $takebook);
	array_pop($takebook);
	$num=count($takebook);
				
		
	for ($i=0; $i < $num ; $i++) { 
		$takebook[$i] = substr($takebook[$i] , 0 , strpos($takebook[$i], "_"));
	}
		$takebook = implode(";", $takebook);
		$rs['take'] = $takebook;
		
		$_SESSION['student'] = $rs;	
		
		return $rs;
	}
	/*	$student['take'] = $student['take'].";".$note['note'];
	$_SESSION['student'] = $student;
	print_r($_SESSION['student']);*/
?>