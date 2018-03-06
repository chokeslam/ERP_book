<?php
    header('Content-Type: application/json; charset=UTF-8');
	
	$nno = $_REQUEST['notenno'];
	 
    $my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = "SELECT note.nno , note.course, note.note , pdstock.PD_No ,  pdstock.ST_Qty , pdstock.ST_Place  FROM pdstock INNER JOIN note ON pdstock.nno=note.nno WHERE note.nno = '$nno'";
			
	$result = mysqli_query ($my_db, $sql);	 
	 
	$rs = mysqli_fetch_assoc($result); 
	 
	echo json_encode($rs);
?>