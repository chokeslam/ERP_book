<?php
    header('Content-Type: application/json; charset=UTF-8');

    include('mysql.php');
	
	$sql = "SELECT adv_no , school_name , student_name , sales_name , book_name , lend_date , ST_Place FROM lendstock ";
	
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