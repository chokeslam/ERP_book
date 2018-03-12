<?php

	header('Content-Type: application/json; charset=UTF-8');

	$my_db = mysqli_connect("localhost","root","");
			
	mysqli_select_db($my_db,"bookerp");

	mysqli_query($my_db,"SET NAMES 'utf8'");

?>