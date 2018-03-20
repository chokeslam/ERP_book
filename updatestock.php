<?php
    header('Content-Type: application/json; charset=UTF-8');
		
	$PD_No = $_REQUEST["pdno"];
	
	$ST_Qty = $_REQUEST["qty"];

	$ST_mi = $_REQUEST["miqty"];
	
	$ST_Place = $_REQUEST["place"];

	$Admin = $_REQUEST["admin"];
	
	$PR_Update= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	
		
	if (!isset($_REQUEST['qty']) || empty($_REQUEST['qty'])) {
		
        echo json_encode(array('msg' => '沒有輸入數量！'));

        return;
    }

	if (!isset($_REQUEST['miqty']) || empty($_REQUEST['miqty'])) {
		
        echo json_encode(array('msg' => '沒有輸入最低庫存數量！'));

        return;
    }    

	if (!isset($_REQUEST["admin"]) || empty($_REQUEST["admin"])) {
		
        echo json_encode(array('msg' => '沒有輸入承辦人姓名！'));

        return;
    }
	
	include('mysql.php');

	$sql = "UPDATE pdstock SET PD_No = '$PD_No' , ST_Qty = '$ST_Qty' , ST_mi = '$ST_mi' , ST_Place = '$ST_Place' , admin = '$Admin' , PR_Update = '$PR_Update' where PD_No = '$PD_No' AND ST_Place = '$ST_Place'";	
	
	$result= mysqli_query($my_db, $sql);
	
	echo json_encode("成功");
?>