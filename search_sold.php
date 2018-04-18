<?php 

    header('Content-Type: application/json; charset=UTF-8');

    $PD_No = $_REQUEST['PD_No'];
    $ST_Place = $_REQUEST['ST_Place'];

	if (!isset($_REQUEST['PD_No']) || empty($_REQUEST['PD_No'])) {

		echo json_encode(array('errormsg' => '請輸入條碼！'));

		return;
	}

	if (!isset($_REQUEST['ST_Place']) || empty($_REQUEST['ST_Place'])) {

		echo json_encode(array('errormsg' => '請選擇班別！'));

		return;
	}

	include('mysql.php');

	$sql = "SELECT PD_No , note , ST_Qty FROM soldbook_pdstock WHERE PD_No = '$PD_No' AND ST_Place = '$ST_Place' UNION ALL SELECT PD_No , note , ST_Qty FROM waywin_tp.note LEFT JOIN bookERP.pdstock USING(nno) where pdstock.nno=note.nno AND PD_No = '$PD_No' AND ST_Place = '$ST_Place'";
			
	$result = mysqli_query ($my_db, $sql);

	$rs = mysqli_fetch_assoc($result);

	if (!isset($rs) || empty($rs)) {

		echo json_encode(array('errormsg' => '條碼錯誤！'));

		return;
	}

	//print_r($rs);

    echo json_encode($rs);

 ?>