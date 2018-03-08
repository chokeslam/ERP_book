<?php
    header('Content-Type: application/json; charset=UTF-8');
	
    $my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = "SELECT pdstock.nno , pdstock.PD_No , note.note , pdstock.ST_Qty , pdstock.ST_mi , pdstock.ST_Place , pdstock.PR_Cdate FROM pdstock INNER JOIN note ON pdstock.nno=note.nno ";
			
	$result = mysqli_query ($my_db, $sql);
	
	$dq = array();
	while ($rs = mysqli_fetch_assoc($result)) {
		
		//$rs['book_name'] = substr($rs['book_name'], 0 ,-1);
		
		//if($rs['book_name']){
		
		array_push($dq,$rs);
			
		//}
	}
	
	//print_r($dq);
	
	$data = array ("data" => $dq);
	echo json_encode($data);
?>