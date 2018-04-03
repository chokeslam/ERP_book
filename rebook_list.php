<?php 
    header('Content-Type: application/json; charset=UTF-8');

	include('mysql.php');

	$sql = "SELECT ST_Code , name ,iostock.PD_No , note , IO_Date FROM waywin_tp.note JOIN bookERP.pdstock ON pdstock.nno=note.nno JOIN bookERP.iostock ON pdstock.PD_No= iostock.PD_No JOIN waywin_tp.student ON student.code = iostock.ST_Code WHERE iostock.IN_Qty !='' AND iostock.ST_Code != ''";

	$result = mysqli_query ($my_db, $sql);

	$dq = array();

	while ($rs = mysqli_fetch_assoc($result)) {

		array_push($dq,$rs);
	}

	$data = array ("data" => $dq);

	echo json_encode($data);

 ?>