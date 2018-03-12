<?php
    header('Content-Type: application/json; charset=UTF-8');
	
	$notenno = $_REQUEST["notenno"];
		
	$PD_No = $_REQUEST["pdno"];
	
	$ST_Qty = $_REQUEST["qty"];

	$ST_mi = $_REQUEST["miqty"];
	
	$ST_Place = $_REQUEST["place"];
	
	$PR_Update= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	
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

	if (!isset($_REQUEST['miqty']) || empty($_REQUEST['miqty'])) {
		
        echo json_encode(array('msg' => '沒有輸入最低庫存數量！'));

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
	
	include('mysql.php');
		
	$sql = "SELECT PD_No FROM pdstock where nno != '$notenno'";
	
	$result= mysqli_query($my_db, $sql);
		
	$num = mysqli_num_rows($result);
	
	$check = array();
	
	for ($i=0; $i < 9; $i++) {
		 
		$rs= mysqli_fetch_assoc($result);
		
		$checkstr = $rs['PD_No'];
			
		array_push($check,"$checkstr");
	}
	
	if (in_array($PD_No, $check)) {
		
		$str = "條碼編號 ".$PD_No." 已重複 請至書籍庫存查詢";
		
		echo json_encode(array('msg' => $str));
        
        return;
    }
	
	$sql = "UPDATE pdstock SET PD_No = '$PD_No' , ST_Qty = '$ST_Qty' , ST_mi = '$ST_mi' , ST_Place = '$ST_Place' , PR_Update = '$PR_Update' 
	
			where nno = '$notenno'";	
	
	$result= mysqli_query($my_db, $sql);
	
	echo json_encode("成功");
?>