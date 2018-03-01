<?php
    header('Content-Type: application/json; charset=UTF-8');
	
    $my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = "SELECT adv_no , school_name , student_name , admin_name , book_name , return_date FROM restock ";
	
	$result = mysqli_query ($my_db, $sql);
	
	$dq = array();
	while ($rs = mysqli_fetch_assoc($result)) {
		
		$rs['book_name'] = substr($rs['book_name'], 0 ,-1);
		
		if($rs['book_name']){
		
		array_push($dq,$rs);
			
		}
	}
	
	//print_r($dq);
	
	$data = array ("data" => $dq);
	echo json_encode($data);
?>