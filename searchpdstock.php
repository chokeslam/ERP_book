<?php
    header('Content-Type: application/json; charset=UTF-8');
	
	$nno = $_REQUEST['notenno'];
	 
	include('mysql.php');
	
	$sql = "SELECT note.nno , note.course, note.note , pdstock.PD_No ,  pdstock.ST_Qty ,  pdstock.ST_mi , pdstock.ST_Place  FROM pdstock INNER JOIN note ON pdstock.nno=note.nno WHERE note.nno = '$nno'";
			
	$result = mysqli_query ($my_db, $sql);	 
	 
	$rs = mysqli_fetch_assoc($result); 
	 
	echo json_encode($rs);
?>