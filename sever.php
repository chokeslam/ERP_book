<?php
	header('Content-Type: application/json; charset=UTF-8');
	$code=$_REQUEST["code"];
	$my_db= mysqli_connect("localhost" , "root" , "");
	mysqli_select_db($my_db, "my_db");
	mysqli_query($my_db,"SET NAMES 'utf8'");
	$sql = "SELECT * FROM student where code = '$code' ";

	if (!isset($_GET['code']) || empty($_GET['code'])) {
        echo json_encode(array('msg' => '沒有輸入學生編號！'));

        return;
    }
	
	
	$result= mysqli_query($my_db, $sql);
	$rs = mysqli_fetch_assoc($result);
	
	
	echo(isset($rs))  ? json_encode($rs): json_encode(array('msg' => '沒有該學生！'));
	
	
?>