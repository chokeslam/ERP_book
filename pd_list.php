<?php
    header('Content-Type: application/json; charset=UTF-8');
	
    include('mysql.php');
	
	$sql = "SELECT nno ,PD_No , note , ST_Qty , ST_mi , ST_Place , PR_Cdate ,PR_Update FROM soldbook_pdstock UNION ALL SELECT nno ,PD_No , note , ST_Qty , ST_mi , ST_Place , PR_Cdate ,PR_Update FROM waywin_tp.note LEFT JOIN bookERP.pdstock USING(nno) where pdstock.nno=note.nno";
			
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