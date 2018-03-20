<?php
    header('Content-Type: application/json; charset=UTF-8');
	
	$notenno = $_REQUEST["notenno"];
		
	$PD_No = $_REQUEST["pdno"];

	$Admin = $_REQUEST["admin"];
			
	$PR_Cdate= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	
	if (!isset($_REQUEST['notenno']) || empty($_REQUEST['notenno'])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍號碼！'));

        return;
    }	

	if (!isset($_REQUEST['pdno']) || empty($_REQUEST['pdno'])) {
		
        echo json_encode(array('msg' => '沒有輸入條碼編號！'));

        return;
    }

	if (!isset($_REQUEST['admin']) || empty($_REQUEST['admin'])) {
		
        echo json_encode(array('msg' => '沒有輸入承辦人資料！'));

        return;
    }

	// if(!preg_match("/^[0-9]{9}$/", $PD_No)){
		
	//     echo json_encode(array('msg' => '條碼編號 請輸入9碼數字！'));

 //        return;		
		
	// }
	
	include('mysql.php');
	
	$sql = "SELECT nno , PD_No FROM notecode where nno = '$notenno'";
	
	$result= mysqli_query($my_db, $sql);
	
	$rs= mysqli_fetch_assoc($result);
	
		
	if (isset($rs) || !empty($rs)) {
		
		$str = "書籍 No.".$notenno." 已有資料 請至書籍庫存查詢";
		
		echo json_encode(array('msg' => $str));
        

        return;
    }
		
	$sql = "SELECT nno , PD_No FROM notecode where PD_No = '$PD_No'";
	
	$result= mysqli_query($my_db, $sql);
	
	$rs= mysqli_fetch_assoc($result);
	
	if (isset($rs) || !empty($rs)) {
		
		$str = "條碼編號 ".$PD_No." 已重複 請至書籍庫存查詢";
		
		echo json_encode(array('msg' => $str));
        

        return;
    }
							
	include('mysql.php');

	$sql = "INSERT INTO notecode VALUES 
				 (null , '$notenno' , '$PD_No' , '$Admin' , '$PR_Cdate')";
	
	$result= mysqli_query($my_db, $sql);
		
	
	echo json_encode("新增成功");
?>