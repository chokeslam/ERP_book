<?php

	header('Content-Type: application/json; charset=UTF-8');
	
    $lendnno = $_REQUEST["lendnno"];
	
	$my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = "SELECT adv_no , school_name , student_name , sales_name , book_name , lend_date 
			FROM lendstock where adv_no = '$lendnno'";
	
	$result= mysqli_query ($my_db, $sql);
	
	$rs = mysqli_fetch_assoc($result);
	
	$rs['book_name'] = substr($rs['book_name'], 0,-1);

	if(!isset($_REQUEST["lendnno"]) || empty($_REQUEST["lendnno"])){
		
		echo json_encode(array('msg' => '3'));
		
		return;
		
	}

	if(!isset($rs['book_name']) || empty($rs['book_name'])){
		
		echo json_encode(array('msg' => '2'));
		
		return;
		
	}		
	//print_r($rs);
	
	//$data = array ("data" => $rs);
	
	echo json_encode($rs);
?>