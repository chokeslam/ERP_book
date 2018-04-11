<?php 
    header('Content-Type: application/json; charset=UTF-8');

    $note = $_REQUEST['note'];
    $PD_No = $_REQUEST['PD_No'];
    $ST_Qty = $_REQUEST['ST_Qty'];
    $ST_mi = $_REQUEST['ST_mi'];
    $ST_Place = $_REQUEST['ST_Place'];
    $admin = $_REQUEST['admin'];
	$PR_Cdate= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));

	if (!isset($_REQUEST['note']) || empty($_REQUEST['note'])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍名稱！'));

        return;
    }

	if (!isset($_REQUEST['PD_No']) || empty($_REQUEST['PD_No'])) {
		
        echo json_encode(array('msg' => '沒有輸入條碼！'));

        return;
    }

	if (!isset($_REQUEST['ST_Qty']) || empty($_REQUEST['ST_Qty'])) {
		
        echo json_encode(array('msg' => '沒有輸入數量！'));

        return;
    }

	if (!isset($_REQUEST['ST_mi']) || empty($_REQUEST['ST_mi'])) {
		
        echo json_encode(array('msg' => '沒有輸入最低數量！'));

        return;
    }

	if (!isset($_REQUEST['ST_Place']) || empty($_REQUEST['ST_Place'])) {
		
        echo json_encode(array('msg' => '沒有輸入庫存地！'));

        return;
    }

	if (!isset($_REQUEST['admin']) || empty($_REQUEST['admin'])) {
		
        echo json_encode(array('msg' => '沒有輸入辦理人！'));

        return;
    }

    include('mysql.php');

    $sql = "SELECT * FROM soldbook_pdstock WHERE PD_No = '$PD_No' AND ST_Place = '$ST_Place'";

	$result= mysqli_query($my_db, $sql);

	$rs = mysqli_num_rows($result);

	if($rs > 0 ){

		echo json_encode(array('msg' => '已有庫存資料請查詢庫存資料表'));

		return;
	}

	$sql = "INSERT INTO soldbook_pdstock VALUES 
				 (null , '$PD_No' , '$note' , '$ST_Qty' , '$ST_mi' , '$ST_Place' , '$admin' , null , '$PR_Cdate' , CURRENT_TIMESTAMP)";

    $result= mysqli_query($my_db, $sql);

    echo json_encode("新增成功");
 ?>