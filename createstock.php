<?php
    header('Content-Type: application/json; charset=UTF-8');
	
	$notenno = $_REQUEST["notenno"];	
	$PD_No = $_REQUEST["pdno"];
	$ST_Qty = $_REQUEST["qty"];
	$ST_Place = $_REQUEST["place"];	
	
	$PR_Cdate= date("Ymd",mktime(0,0,0,date("m"),date("d"),date("Y")));
	
	if (!isset($_REQUEST['notenno']) || empty($_REQUEST['notenno'])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍號碼！'));

        return;
    }	

	if (!isset($_REQUEST['pdno']) || empty($_REQUEST['pdno'])) {
		
        echo json_encode(array('msg' => '沒有輸入條碼編號！'));

        return;
    }
		
	if (!isset($_REQUEST['qty']) || empty($_REQUEST['qty'])) {
		
        echo json_encode(array('msg' => '沒有輸入數量！'));

        return;
    }
	
	if (!isset($_REQUEST['place']) || empty($_REQUEST['place'])) {
		
        echo json_encode(array('msg' => '沒有輸入庫存地！'));

        return;
    }

	if(!preg_match("/^[0-9]{9}$/", $PD_No)){
		
	    echo json_encode(array('msg' => '條碼編號 請輸入9碼數字！'));

        return;		
		
	}
	
	$my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = "SELECT nno , PD_No FROM pdstock where nno = '$notenno'";
	
	$result= mysqli_query($my_db, $sql);
	
	$rs= mysqli_fetch_assoc($result);
	
		
	if (isset($rs) || !empty($rs)) {
		
		$str = "書籍 No.".$notenno." 已有庫存資料 請至書籍庫存查詢";
		
		echo json_encode(array('msg' => $str));
        

        return;
    }					
	/*$my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = " INSERT INTO pdstock VALUES 
				 (null , '$notenno' , '$PD_No' , '$ST_Qty' , '$ST_Place' , '$PR_Cdate' , CURRENT_TIMESTAMP)";;
	
	$result= mysqli_query($my_db, $sql);*/
		
	
	echo json_encode("新增成功");
?>