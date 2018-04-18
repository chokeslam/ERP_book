<?php 

    header('Content-Type: application/json; charset=UTF-8');

    include('class/decrease_stock.php');
    include('mysql.php');

    $PD_No = $_REQUEST['PD_No'];
    $ST_Place = $_REQUEST['ST_Place'];
    $ST_Qty = $_REQUEST['ST_Qty'];

	if (!isset($_REQUEST['PD_No']) || empty($_REQUEST['PD_No'])) {

		echo json_encode(array('errormsg' => '無書籍資料 請輸入條碼！'));

		return;
	}

	if (!isset($_REQUEST['ST_Place']) || empty($_REQUEST['ST_Place'])) {

		echo json_encode(array('errormsg' => '請選擇班別！'));

		return;
	}

	$ST_Qty = new decrease_one($ST_Qty);

	$PR_Update = $ST_Qty->date;

	$ST_Qty = $ST_Qty-> decrease();

	$sql = "SELECT PD_No FROM pdstock WHERE PD_No = '$PD_No' AND ST_Place = '$ST_Place' ";

	$result= mysqli_query($my_db, $sql);

	$rs = mysqli_fetch_assoc($result);

	if (!isset($rs) || empty($rs)) {

		$sql = "UPDATE soldbook_pdstock SET ST_Qty='$ST_Qty' , PR_Update = '$PR_Update' where PD_No = '$PD_No' AND ST_Place = '$ST_Place'";

		$result= mysqli_query($my_db, $sql);

		echo json_encode(array('msg' => "扣庫存成功"));

		return;
	}

		$sql = "UPDATE pdstock SET ST_Qty='$ST_Qty' , PR_Update = '$PR_Update' where PD_No = '$PD_No' AND ST_Place = '$ST_Place'";

		$result= mysqli_query($my_db, $sql);

		echo json_encode(array('msg' => "扣庫存成功"));



 ?>