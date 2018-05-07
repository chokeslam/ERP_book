<?php
    header('Content-Type: application/json; charset=UTF-8');
	
	$notenno = $_REQUEST['notenno'];
	
	include('mysql.php');
	
	$sql = "SELECT nno , course , note ,PD_No FROM waywin_tp.note LEFT JOIN bookERP.pdstock USING(nno) where nno = '$notenno' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$note = mysqli_fetch_assoc($result);
	
	if (!isset($_REQUEST['notenno']) || empty($_REQUEST['notenno'])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍號碼！'));

        return;
    }
	if (!isset($note) || empty($note)) {
		
        echo json_encode(array('msg' => '沒有這本書的資料！'));

        return;
    }
    if ($note['PD_No'] !=Null) {

    	echo json_encode(array('msg' => '這本書已有條碼資料！'));
    	return;
    }
	
	echo json_encode($note);
?>