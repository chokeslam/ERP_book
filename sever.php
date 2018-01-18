<?php
	header('Content-Type: application/json; charset=UTF-8');
	//從前端接收的資料
	$code=$_REQUEST["code"];
	//連DB
	$my_db= mysqli_connect("localhost" , "root" , "");
	
	mysqli_select_db($my_db, "my_db");
	
	mysqli_query($my_db,"SET NAMES 'utf8'");
	//搜尋條件
	$sql = "SELECT * FROM student where code = '$code' ";
	//判斷有沒輸入
	if (!isset($_REQUEST["code"]) || empty($_REQUEST["code"])) {
		
        echo json_encode(array('msg' => '沒有輸入學生編號！'));

        return;
    }
	
	
	$result= mysqli_query($my_db, $sql);
	//將搜尋後學生資料放入  $rs
	$rs = mysqli_fetch_assoc($result);
	//判斷有沒有搜尋到資料
	if (isset($rs)){
		//有搜尋到 將 course 這欄的的字串以  ; 做拆開
		/*$rs['course']=explode(";", $rs['course']);
		//將   ,  作為每個字串的分隔符號
		$rs['course']=implode("','", $rs['course']);	
		// 把多出來的  , 做切割
		$rs['course']=substr($rs['course'], 0 ,-2);	
		//組成搜尋的字串
		$keyword ="'". $rs['course'];
		//DB 搜尋條件
		$sql="SELECT note FROM note where course in ($keyword)";
		//echo $sql;
		//搜尋 報名科目可借書籍
		$result= mysqli_query($my_db, $sql);
		//取DB 搜尋出來的總筆數
		//$num = mysqli_num_rows($result);
		//把可領取書籍的資料放入 $takebook 內
		$book=mysqli_fetch_all($result); //可領取書籍
		//將 二維陣列轉換成一維陣列 
		$book=call_user_func_array('array_merge', $book);
		//將每筆書的資料存進 $book裡面
		/*for ($i=0; $i < $num; $i++) {
			$book = mysqli_fetch_assoc($result);	 
			$book=$book.$book['note'].',';
		
			
		}*/
		//$book=substr($book,0,-1);
		//把拿過的書放入$takebook 內
	/*	$takebook=$rs['take']; 
		$takebook=explode(";", $takebook);
		array_pop($takebook);
		$num=count($takebook);
				
		//print_r($takebook);
		//echo $takebook[0];
		
		for ($i=0; $i < $num ; $i++) { 
			$takebook[$i] = substr($takebook[$i] , 0 , strpos($takebook[$i], "_"));
		}
		$rs['take']=implode("','", $takebook);
		/*echo "已經借的書";
		
		print_r($takebook);
		//echo substr($takebook[0] , 0 , strpos($takebook[0], "_"));
		echo "可以借的書";
		print_r($book);
		
		$ra = array_intersect($takebook,$book);
		echo "已領取書籍";
		print_r($ra);
		//print_r($book);
		echo json_encode($takebook);*/
		//echo $rs["course"];
		//print_r($rs);
		$takebook=$rs['take']; 
		$takebook=explode(";", $takebook);
		array_pop($takebook);
		$num=count($takebook);
				
		//print_r($takebook);
		//echo $takebook[0];
		
		for ($i=0; $i < $num ; $i++) { 
			$takebook[$i] = substr($takebook[$i] , 0 , strpos($takebook[$i], "_"));
		}
		$takebook = implode(";", $takebook);
		$rs['take'] = $takebook;
		//echo $rs["course"];
		echo json_encode($rs);	
		
	}else{
		//沒搜尋到資料
		echo json_encode(array('msg' => '沒有該學生！'));
		
	}
	 /*if(isset($rs)){
	$rs['course']=explode(";", $rs['course']);
	$rs['course']=implode(",", $rs['course']);
	
	}
	echo(isset($rs))  ? json_encode($rs): json_encode(array('msg' => '沒有該學生！'));*/

	
?>