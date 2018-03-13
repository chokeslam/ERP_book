<?php

    header('Content-Type: application/json; charset=UTF-8');
	
	$lendnno = $_REQUEST['lendnno'];
	
	$lendstudentnno = $_REQUEST['lendstudentnno'];
	
	//$lendadmin = $_REQUEST['lendadmin'];
	
	$relendbook = $_REQUEST['lendbook'];
	
	include('mysql.php');
	
	$sql = "SELECT name , course , take   FROM student where code = '$lendstudentnno'";
	
	$result= mysqli_query ($my_db, $sql);
	
	$rs = mysqli_fetch_assoc($result);
	
	$lendbook = $rs['take'];
	
	/*$lendbook = substr($lendbook, 0 ,-1);
		
	$lendbook = explode(";" ,"$lendbook" );*/
		
	$relendbook = substr($relendbook, 0 ,-1);
		
	$relendbook = explode("," ,"$relendbook" );
	
	$course = $rs['course'];
	
	$course = substr($course, 0 ,-1);
	
	$course = explode(";" ,"$course" );
		
	$value = get_value($course,$relendbook);					//微積分秋@程中@講義@01_96微積分_2018/02/27 12:31:52;
	
	
	
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
	
	if(empty($rs['take'])){
		
		$sql = "UPDATE student SET take ='$value' where code = '$lendstudentnno' ";		//存
		
		$result= mysqli_query ($my_db, $sql);
		
		update_lendstock($lendnno,$relendbook);
		
		echo json_encode(array('msg' => '轉換成功'));
		
		return;
			
	}else{
	
		$takebook_name = takebook_name($lendbook);
		
		$answer = array_intersect($takebook_name,$relendbook);
			
	}
	
	if(empty($answer)){
		
		$lendbook = $lendbook.$value;
		
		$sql = "UPDATE student SET take ='$lendbook' where code = '$lendstudentnno' ";	//存
		
		$result= mysqli_query ($my_db, $sql);
		
		update_lendstock($lendnno,$relendbook);
		
		echo json_encode(array('msg' => '轉換成功'));
		
		return;
		
	}else{
			
		$answerstring = "";	
		
		foreach ($answer as $value) {
			
			$answerstring = $answerstring . $value . ",";	
		}
		
		$answerstring = substr($answerstring, 0 ,-1);
			
		$num = count($answer);
		
		$msg = array(
					"book"=>"$answerstring",
					"num"=>"$num"
				);
		
		echo json_encode($msg);
				
	}
//------------------------------------------------------------------------------------------------------------------------	
	function takebook_name($lendbook){
				
		$lendbook = substr($lendbook, 0 ,-1);
		
		$lendbook = explode(";" ,"$lendbook" );
	
		$num = count($lendbook);
	
		for ($i=0; $i <$num ; $i++) { 
		
			$lendbook[$i] = strchr($lendbook[$i],"_",1) ;
		
		}
		return 	$lendbook;	
	}
//------------------------------------------------------------------------------------------------------------------------	
	function get_value($course,$relendbook){
		
		$num = count($relendbook);
		
		$course_num = count($course);
		
		$date= date ("Y/m/d H:i:s" , mktime(date('H'),  date('i'),date('s'), date('m'), date('d'), date('Y'))) ; 
		
		$coursearray = array();
		
		for ($i=0; $i < $num; $i++) { 
		
			$name = $relendbook[$i];
		
			for ($j=0; $j < $course_num; $j++) {
			
				$coursename = $course[$j];
				
				include('mysql.php');
			
				$sql = "SELECT course , note FROM note where course = '$coursename' AND note = '$name '";
			
				$result= mysqli_query ($my_db, $sql);
			
				$rs = mysqli_fetch_row($result);
				
				if(!empty($rs)){
		
					array_push($coursearray , $rs);

				}
				
			}
			
		}
		$relendbook = "";
	
		foreach ($coursearray as $value) {
		
			$relendbook= $relendbook. $value[1]."_" .$value[0]."_".$date. ";";
	
		}

		return $relendbook;
	}
		
//------------------------------------------------------------------------------------------------------------------------
	
	function update_lendstock($lendnno,$relendbook){
		
		include('mysql.php');
		
		$sql = "SELECT adv_no , student_name , book_name  FROM lendstock where adv_no = '$lendnno'";
		
		$result= mysqli_query ($my_db, $sql);
	
		$rs = mysqli_fetch_assoc($result);
	
		$lendbook = $rs['book_name'];
	
		$lendbook = substr($lendbook, 0 ,-1);
		
		$lendbook = explode(";" ,"$lendbook" );
	
		//$relendbook = substr($relendbook, 0 ,-1);
		
		//$relendbook = explode("," ,"$relendbook" );
	
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
		
	}
	

	
	
	

	

	//
	
?>