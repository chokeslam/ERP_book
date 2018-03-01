<?php session_start() ?>
<?php
	header('Content-Type: application/json; charset=UTF-8');

    $book_barcode = $_REQUEST['book_barcode'];
	
	$bookname = $_REQUEST['bookname'];
	
	$my_db= mysqli_connect("localhost" , "root" , "");
	
	//echo $book_barcode;
	
	mysqli_select_db($my_db, "bookerp");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	
	$sql = "SELECT nno FROM pdstock where PD_No = '$book_barcode' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$rs = mysqli_fetch_assoc ($result);
	
	$rs = $rs['nno'];
	
	$sql = "SELECT note FROM note where nno = '$rs' ";
	
	$result= mysqli_query($my_db, $sql);
	
	$rs = mysqli_fetch_assoc ($result);
	
	//判斷 有無輸入書籍編號
	if (!isset($_REQUEST["book_barcode"]) || empty($_REQUEST["book_barcode"])) {
		
        echo json_encode(array('msgprompt' => '沒有輸入書籍編號！'));

        return 0;
    }
	
	//判斷 書籍編號 是否正確
	if (!isset($rs) || empty($rs)) {
		
        echo json_encode(array('msgprompt' => '書籍編號錯誤！'));

        return 0;
    }
	
	
	if (empty($bookname)){
			
		$msg = 	$rs['note'];
		
		echo json_encode(array('msg' => "$msg",'nno'=>"$book_barcode"));
		
		return 0;	
	}
	
	//echo $bookname;
	
	//echo $rs['note'];
	if (stristr("$bookname", $rs['note']) == TRUE){
		
		echo json_encode(array('msgprompt' => '這本書刷過囉'));
		
		return 0;	
		
	}else{
		
		
		$msg = 	$rs['note']; 
		
		echo json_encode(array('msg' => "$msg",'nno'=>"$book_barcode"));
	}
	
	/*$msg = $rs['note'];
	
	echo json_encode(array('msg' => "$msg"));*/
	
	
	//unset ($_SESSION['note']);
?>