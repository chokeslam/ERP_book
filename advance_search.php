<?php
    header('Content-Type: application/json; charset=UTF-8');
	
	$adv_no=$_REQUEST['adv_no'];
	$PD_No=$_REQUEST['book'];
	$rebook = $_REQUEST['rebook'];
	
	include('mysql.php');
	
	$sql = "SELECT nno , PD_No , ST_Qty FROM pdstock where PD_No = '$PD_No' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$pdstock = mysqli_fetch_assoc($result);
	
	//print_r($pdstock);
	//搜尋結束
	
	//搜尋書籍TABLE
	$booknno = $pdstock["nno"];   //書籍編號
		
	$sql = "SELECT nno , course , note FROM watwin_tp.note where nno = '$booknno' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$note = mysqli_fetch_assoc($result);
	
	$note = $note["note"];
	
	//print_r($note);
	
	$sql = "SELECT adv_no , book_name FROM lendstock where adv_no = '$adv_no' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$lendbook= mysqli_fetch_assoc($result);
	
	//print_r($lendbook);
	
	$lendbook = substr($lendbook["book_name"],0,-1);
	
	$lendbook = explode(";",$lendbook);
	
	//print_r($lendbook);
	
	//echo in_array($note , $lendbook);
	
	if (!isset($_REQUEST["book"]) || empty($_REQUEST["book"])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍編號！'));

        return;
    }
    
    if (!isset($note) || empty($note)) {
		
        echo json_encode(array('msg' => '書籍編號錯誤！'));

        return;
    }
    
    if (!in_array($note , $lendbook)){
    	
		echo json_encode(array('msg' => '沒有借這本書！'));
		
		return;
		
    }
	if (stristr("$rebook", "$note") == TRUE){
		
		echo json_encode(array('msg' => '這本書刷過囉'));
		
		return 0;	
		
	}
	
	//$key =  array_search($note , $lendbook);	
	
	//unset($lendbook[$key]);
	
	$lendbook =implode(";", $lendbook);
	
	//print_r($lendbook);
	
	echo json_encode (array('msg' => " " , 'book' => "$lendbook" , 'rebook' => "$note" , 'rebookcode' => "$PD_No" ));
?>