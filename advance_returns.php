<?php
    
    header('Content-Type: application/json; charset=UTF-8');
	
	$adv_no = $_REQUEST["adv_no"];  //借出單編號
	
	$admin = $_REQUEST["admin"];	//承辦人
	
	$rebookname = rtrim($_REQUEST["rebookname"]," ");  //要還書 名稱
	
	$rebook_PDNO = trim($_REQUEST["rebookcode"]," ");  //要還書  編號
	
	if (!isset($_REQUEST["adv_no"]) || empty($_REQUEST["adv_no"])) {
		
        echo json_encode(array('msg' => '沒有輸入書籍編號！' ));

        return;
    }
	if (!isset($_REQUEST["admin"]) || empty($_REQUEST["admin"])) {
		
        echo json_encode(array('msg' => '沒有輸入承辦人名稱！' ));

        return;
    }	

	if (!isset($_REQUEST["rebookname"]) || empty($_REQUEST["rebookname"])) {
		
        echo json_encode(array('msg' => '無要還書籍名稱！' ));

        return;
    }

		
	$rebookname = explode(" " ,"$rebookname" );
	
	$rebookname = implode(";", $rebookname) . ";";
	
	$rebook_PDNO = explode(" ","$rebook_PDNO");
	
	$num = count($rebook_PDNO);
	
	include('mysql.php');
	
	$sql = "SELECT book_name , end_date FROM lendstock WHERE adv_no = '$adv_no'";

	$result= mysqli_query($my_db, $sql);
			
	$rs = mysqli_fetch_assoc($result);	
	
	$bookname = $rs['book_name'];
	
	$bookname = substr($bookname, 0 , -1);
	
	Transaction_IN($adv_no , $rebook_PDNO ,$num);			//寫入異動表
	Buckle_stock ($rebook_PDNO , $num);					//增加庫存
	add_restock($adv_no,$rebookname,$admin);				//寫入還書紀錄
	update_lendstock($rebookname,$bookname,$adv_no);		//更新借書紀錄
	
	echo json_encode(array('msg' => "還書成功"));

//------------------------------------------------------------------------------------------------------------------------------------------
	
	function update_lendstock($rebookname,$bookname,$adv_no){

		include('mysql.php');
		
		$rebookname = substr($rebookname, 0 , -1);
		
		$rebookname = explode(";", "$rebookname");
		
		$bookname = explode(";", "$bookname");
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$num = count($rebookname);
		
		for ($i=0; $i <$num ; $i++) { 
			
			$key = array_search($rebookname[$i], $bookname);
			
			unset($bookname[$key]);
			
		}
		
		if (empty($bookname)) {
				
			$sql = "UPDATE lendstock set book_name = '',end_date = '$date' WHERE adv_no = '$adv_no'";
			
			$result= mysqli_query($my_db, $sql);
			
		}else {
			
			$bookname = implode(";", $bookname) . ";";
			
			$sql = "UPDATE lendstock set book_name = '$bookname' WHERE adv_no = '$adv_no'";
			
			$result= mysqli_query($my_db, $sql);

		}
					
	}
	
//-----------------------------------------------------------------------------------------------------------------------
	//寫入還貨單 function
	
	function add_restock($adv_no,$rebookname,$admin){
		
		include('mysql.php');
		
		$sql = "SELECT adv_no , school_name , student_name FROM lendstock WHERE adv_no = '$adv_no'";

		$result= mysqli_query($my_db, $sql);
			
		$rs = mysqli_fetch_assoc($result);
	
		$school_name = $rs['school_name'];
	
		$student_name = $rs['student_name'];
	
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	
		$sql = " INSERT INTO restock VALUES 
				(null , '$adv_no' , '$school_name' , '$student_name' , '$admin' ,'$rebookname' , '$date', CURRENT_TIMESTAMP)";	
			
		$result= mysqli_query($my_db, $sql);
			
	}
	
//-----------------------------------------------------------------------------------------------------------------------

	function Transaction_IN ($adv_no , $rebook_PDNO , $num){				//$data1 = $adv_no (借出單號)  $data2 = $PD_No  (書籍編號)
		
		$formnumber = Form_number();
		
		$date= date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		$IN_Qty = 1;
		
		include('mysql.php');
		
		for ($i=0; $i < $num ; $i++) { 
		
			$PD_No = $rebook_PDNO[$i];	
			
			//echo $PD_No;
			$sql = " INSERT INTO iostock VALUES 
				 (null , '$formnumber' , '$PD_No' , '$adv_no' , null , '$IN_Qty' , null , '$date' , CURRENT_TIMESTAMP)";
				
		$result= mysqli_query($my_db, $sql);
		}
		
	}
		
			
//----------------------------------------------------------------------------------------------------------------------
	
	//取得流水號 function
	function Form_number (){
		
		include('mysql.php');
		
		$sql = "SELECT MAX(IO_Blno) FROM iostock";
		
		$result= mysqli_query($my_db, $sql);
		
		$rs = mysqli_fetch_row($result);
		
		$rs = $rs[0];
		
		$date= date("Ymd",mktime(0,0,0,date("m"),date("d"),date("Y")));
		
		//echo substr($rs, 0, -3);
		
		if ($date == substr($rs, 0, -4)){
			
			return $rs+1;
			
		}else{
			
			return $date.'0001';
			
		}
			
		
	}
//--------------------------------------------------------------------------------------------------------------------------
	
	
	//加庫存 function     
	function Buckle_stock ($rebook_PDNO , $num){					//$data1 = $ST_Qty (庫存數量)  $data2 = $PD_No  (書籍編號)
				
		include('mysql.php');
		
		for ($i=0; $i < $num ; $i++) {
			
			$PD_No =  $rebook_PDNO[$i];
			
			$sql = "SELECT nno , PD_No , ST_Qty FROM pdstock where PD_No = '$PD_No' ";
			
			$result= mysqli_query($my_db, $sql);
			
			$pdstock = mysqli_fetch_assoc($result);
			
			$ST_Qty = $pdstock["ST_Qty"] + 1;
			
			$sql = "UPDATE pdstock set ST_Qty = '$ST_Qty' where PD_No = '$PD_No'";
			
			$result= mysqli_query($my_db, $sql);
						
		}

	}	
?>