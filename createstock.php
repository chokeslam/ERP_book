<?php 
    header('Content-Type: application/json; charset=UTF-8');
	
	$notenno = $_REQUEST["notenno"];
		
	$PD_No = $_REQUEST["pdno"];
	
	$ST_Qty = $_REQUEST["qty"];

	$ST_mi = $_REQUEST["miqty"];
	
	$ST_Place = $_REQUEST["place"];

	$Admin = $_REQUEST["admin"];

	$PR_Cdate= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));


	if (!isset($_REQUEST['qty']) || empty($_REQUEST['qty'])) {
		
        echo json_encode(array('msg' => '沒有輸入數量！'));

        return;
    }

	if (!isset($_REQUEST['miqty']) || empty($_REQUEST['miqty'])) {
		
        echo json_encode(array('msg' => '沒有輸入最低數量！'));

        return;
    }

	if (!isset($_REQUEST['admin']) || empty($_REQUEST['admin'])) {
		
        echo json_encode(array('msg' => '沒有輸入辦理人姓名！'));

        return;
    }

    include('mysql.php');

    $sql = "SELECT * FROM pdstock WHERE PD_No = '$PD_No' AND ST_Place = '$ST_Place'";

	$result= mysqli_query($my_db, $sql);

	$rs = mysqli_num_rows($result);

	if($rs > 0 ){

		echo json_encode(array('msg' => '已有庫存資料請查詢庫存資料表'));

		return;
	}

	$sql = "INSERT INTO pdstock VALUES 
				 (null , '$notenno' , '$PD_No' , '$ST_Qty' , '$ST_mi' , '$ST_Place' , '$Admin' , null , '$PR_Cdate' , CURRENT_TIMESTAMP)";

    $result= mysqli_query($my_db, $sql);

    echo json_encode("新增成功");

 ?>