<?php
include_once("shangmian.php");
?>

<script language="javascript"> 
function daochu()
{ 
	if(confirm("确定要导出吗？"))
	{ 
		return true; 
	}
	else
	{ 
		return false; 
	} 
} 
</script>

<?php
if(isset($_GET['Submit'])&&$_GET['Submit']){
	//获取时间范围
	$time1 = "";
	$time2 = "";
	$time1 = strtotime($_GET['time1']." 0:0:0");
	$time2 = strtotime($_GET['time2']." 23:59:59");
	//总表显示的是time2所在的月份
	$mdays = 0;
	$mdays = date('t',$time2);
	$BeginOfMonth = strtotime(date('Y-m-1 00:00:00',$time2));
	$EndOfMonth = strtotime(date('Y-m-'.$mdays.' 23:59:59',$time2));
	echo date('Y-m-1 00:00:00',$time2);
	echo $BeginOfMonth;
	echo "</br>";
	echo date('Y-m-'.$mdays.' 23:59:59',$time2);
	echo $EndOfMonth;

	//-------------------------------------------------------------------------------------------------
	//设置Excel参数
	//-------------------------------------------------------------------------------------------------
	error_reporting(E_ALL);  
	date_default_timezone_set('Asia/Shanghai');  
	/** PHPExcel */  
	require_once './phpexcelClasses/PHPExcel.php'; 
	$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_discISAM;
	//Windows
	$cacheSettings = array( 'dir'  => 'C:\\phpExceltemp');
	//Linux
	//$cacheSettings = array( 'dir'  => '/usr/phpExceltemp');
	PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
	// Create new PHPExcel object  
	$objPHPExcel = new PHPExcel(); 
	// Set properties  
	$objPHPExcel->getProperties()->setCreator("zzberrypi")  
	                             ->setLastModifiedBy("zzberrypi")  
	                             ->setTitle("MyAccount")  
	                             ->setSubject("MyAccount")  
	                             ->setDescription("MyAccount from zzberrypi.")  
	                             ->setKeywords("Account")  
	                             ->setCategory("Account");  
	?>

	<?php
	//-------------------------------------------------------------------------------------------------
	//设置创建Excel工作表
	//-------------------------------------------------------------------------------------------------
	//设置第一张工作表(总表)
	$objPHPExcel->getActiveSheet()->setTitle("总表");
	$objPHPExcel->getActiveSheet()->mergeCells('A1:E1'); 
	$objPHPExcel->getActiveSheet()->setCellValue("A1","月统计: ".date("Y-m",$EndOfMonth)); 
	$objPHPExcel->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);  
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue("A2","账户名称")
				->setCellValue("B2","上月结余")
				->setCellValue("C2","本月收入")
				->setCellValue("D2","本月支出")
				->setCellValue("E2","当前结余");
	$ColumnIndexOftotalTable = 2;

	//设置其他工作表(分表)
	$query_paymethod_sql = "select * from jizhang_account_class where classtype = 3 and ufid = ".$_SESSION['uid'];
	$query_paymethod = mysqli_query($conn,$query_paymethod_sql);
	while($row_paymethod = mysqli_fetch_array($query_paymethod))
	{
		$name = $row_paymethod['classname'];
		$newworksheet = new PHPExcel_Worksheet($objPHPExcel, $name);
		$objPHPExcel->addSheet($newworksheet);

		$objPHPExcel->getSheetByName($name)->mergeCells('A1:F1');
		$objPHPExcel->getSheetByName($name)->setCellValue("A1",$name.": ".date("Y-m-d",$time1)."至".date("Y-m-d",$time2));
		$objPHPExcel->getSheetByName($name)->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getSheetByName($name)->getColumnDimension('D')->setWidth(20);
		$objPHPExcel->getSheetByName($name)->getColumnDimension('E')->setWidth(30);
		$objPHPExcel->getSheetByName($name)->getColumnDimension('F')->setWidth(30);
		$objPHPExcel->getSheetByName($name)
					->setCellValue("A2","记录号")
				    ->setCellValue("B2","时间")
				    ->setCellValue("C2","金额")
				    ->setCellValue("D2","收入/支出/内部转账")
				    ->setCellValue("E2","分类")
				    ->setCellValue("F2","备注");
    }

	?>


	<?php
	//-------------------------------------------------------------------------------------------------
	//计算总表账目数据并写如Excel
	//-------------------------------------------------------------------------------------------------

	//计算总表账目数据
	error_reporting(E_ALL || ~E_NOTICE);
	$query_paymethod_sql = "select * from jizhang_account_class where classtype = 3 and ufid = ".$_SESSION['uid'];
	$query_paymethod = mysqli_query($conn,$query_paymethod_sql);
	/*//curdate()获取零点时间
	$query_FristdayOfMonth = mysqli_query($conn,"select unix_timestamp(date_sub(curdate(),interval dayofmonth(curdate())-1 day))");
	while($row_FristdayOfMonth = mysqli_fetch_array($query_FristdayOfMonth)){
		$FristdayOfMonth = $row_FristdayOfMonth[0]; //本月第一天时间戳
	}
	$timenow = time();//当前时间戳*/


	$total_balance0 = 0; //上月总结余
	$total_expense1 = 0; //本月总支出
	$total_income1 = 0; //本月总收入
	$total_balance1 = 0; //本月总结余
	$total_income1_net = 0; //本月净收入
	$total_expense1_net = 0; //本月净支出

	while($row_paymethod = mysqli_fetch_array($query_paymethod))
	{
		$class = $row_paymethod['classname'];
		$classid = $row_paymethod['classid'];

		$nbzz_classname = '';
		$nbzz_classid = 0;
		
		$income0 = 0;
		$income01 = 0;
		$income02 = 0;
		$income03 = 0;
		$income04 = 0;

		$expense0 = 0;
		$expense01 = 0;
		$expense02 = 0;
		$expense03 = 0;
		$expense04 = 0;

		$balance0 = 0;
		$income1 = 0;
		$expense1 = 0;
		$balance1 = 0;


		//echo $class."</br>";
		//查询上月收入结余，“jizhang_asset_”表里上月最后一个收入记录点的余额
		$query_income01_sql = "select as_balance from jizhang_asset_"
						   .$row_paymethod['classid']
						   ." where ufid = "
						   .$_SESSION['uid']
						   ." and as_type = 1 and as_time < "
						   .$BeginOfMonth
						   ." order by number desc limit 1";
		$query_income01 = mysqli_query($conn,$query_income01_sql);
		if($query_income01 == FALSE){
			//echo mysqli_error($conn); // TODO: better error handling
			$income01 = 0;
		}else{
		while($row_income01 = mysqli_fetch_array($query_income01)){
			$income01 = $row_income01[0];
			if($income01 == null){$income01 = 0;}
			//echo "income01:".$income01."</br>";
			}
		}
		
		//上月收入结余
		$income0 = $income01;
		//echo "上月收入结余：".$income0."</br>";

		//查询上月支出结余,“jizhang_asset_”表里上月最后一个支出记录点的余额
		$query_expense01_sql = "select as_balance from jizhang_asset_"
						   .$row_paymethod['classid']
						   ." where ufid = "
						   .$_SESSION['uid']
						   ." and as_type = 2 and as_time < "
						   .$BeginOfMonth
						   ." order by number desc limit 1";
		$query_expense01 = mysqli_query($conn,$query_expense01_sql);
		if($query_expense01 == FALSE){
			//echo (mysqli_error($conn)); // TODO: better error handling
			$expense01 = 0;
		}else{
		while($row_expense01 = mysqli_fetch_array($query_expense01)){
			$expense01 = $row_expense01[0];
			if($expense01 == null){$expense01 = 0;}
			//echo  "expense01:".$expense01."</br>";
			}
		}

		//上月支出结余
		$expense0 = $expense01;
		//echo "上月支出结余".$expense0."</br>";
		

		


		//查询上月内部转账结余,“jizhang_asset_”表里上月最后一个内部转账记录点的余额
		//内部转账名称的格式为“paymethod[内部]”，经过字符串处理查找jizhang_account_class表中
		//内部转账户头的classid
		$nbzz0 = 0;
		$nbzz_classname = $row_paymethod['classname']."[内部]";
		//echo $nbzz_classname."</br>";
		$query_nbzz_sql = "select classid from jizhang_account_class where classname = '"
				   .$nbzz_classname.
				    "' and classtype = 4 and ufid = "
				   .$_SESSION['uid'];
		$query_nbzz = mysqli_query($conn,$query_nbzz_sql);
		while($row_nbzz = mysqli_fetch_array($query_nbzz))
		{
			if($row_nbzz == null)
			{
				//echo mysqli_error($conn); 
			}else{
				$nbzz_classid = $row_nbzz[0];
				//echo "nbzz_classid = ".$nbzz_classid."</br>";
			}
		}
		$query_nbzz_sql = "select as_balance from jizhang_asset_"
						   .$nbzz_classid
						   ." where ufid = "
						   .$_SESSION['uid']
						   ." and as_type = 4 and as_time < "
						   .$BeginOfMonth
						   ." order by number desc limit 1";
		$query_nbzz = mysqli_query($conn,$query_nbzz_sql);
		if($query_nbzz == FALSE){
			//echo mysqli_error($conn); // TODO: better error handling
			$nbzz0 = 0;
		}else{
		while($row_nbzz = mysqli_fetch_array($query_nbzz)){
			if($row_nbzz[0] == null){ $nbzz0 = 0;}
			else{$nbzz0 = $row_nbzz[0];}
			//echo "上月内部转账结余：".$nbzz0."</br>";
			}
		}
		//上月结余 = 上月收入结余-上月支出结余+上月内部转账结余
		//内部转账结余为正表示入不敷出，结余为负表示家中有粮，因此计算总结余是要减去$nbzz0
		$balance0 = 0;
		$balance0 = $income0 - $expense0 - $nbzz0;
		
		//查询本月收入part1，支出收入类
		$query_income11_sql = "select sum(acmoney) from jizhang_account"
						   ." where jiid = "
						   .$_SESSION['uid']
						   ." and acpaymethod = "
						   .$row_paymethod['classid']
						   ." and zhifu = 1 and actime between "
						   .$BeginOfMonth
						   ." and "
						   .$EndOfMonth;		   
		$query_income11 = mysqli_query($conn,$query_income11_sql);
		if($query_income11 == FALSE){
			//die(mysqli_error($conn)); // TODO: better error handling
			$income11 = 0;
			//echo "income11 is null.</br>";
		}
		while($row_income11 = mysqli_fetch_array($query_income11)){
			$income11 = $row_income11[0];
			//echo "income11:".$income1."</br>";
		}
		//查询本月收入part2，内部转账类
		$query_income12_sql = "select sum(acmoney) from jizhang_account"
							 ." where jiid = "
							 .$_SESSION['uid']
							 ." and acclassid = " //acclassid即为被转入账户的classid，为收入
							 .$nbzz_classid
							 ." and zhifu =4 and actime between "
							 .$BeginOfMonth
						   	 ." and "
						     .$EndOfMonth;
		$query_income12 = mysqli_query($conn,$query_income12_sql);
		if($query_income12 == FALSE){
			//die(mysqli_error($conn)); // TODO: better error handling
			$income12 = 0;
			//echo "income12 is null.</br>";
		}
		while($row_income12 = mysqli_fetch_array($query_income12)){
			$income12 = $row_income12[0];
			//echo "income12:".$income12."</br>";
		}
		$income1 = $income11 + $income12;
		//echo "income1:".$income1."</br>";
		
		
		//查询本月支出part1,支出收入类
		$query_expense11_sql = "select sum(acmoney) from jizhang_account"
						   ." where jiid = "
						   .$_SESSION['uid']
						   ." and acpaymethod = "
						   .$row_paymethod['classid']
						   ." and zhifu = 2 and actime between "
						   .$BeginOfMonth
						   ." and "
						   .$EndOfMonth;
						   
		$query_expense11 = mysqli_query($conn,$query_expense11_sql);
		if($query_expense11 == FALSE){
			//die(mysqli_error($conn)); // TODO: better error handling
			$expense1 = 0;
			//echo "expense11 is null.</br>";
		}
		while($row_expense11 = mysqli_fetch_array($query_expense11)){
			$expense11 = $row_expense11[0];
			//echo "expense11:".$expense11."</br>";
		}
		//查询本月支出part2,内部转账类
		$query_expense12_sql = "select sum(acmoney) from jizhang_account"
						   ." where jiid = "
						   .$_SESSION['uid']
						   ." and acpaymethod = "   //acpaymethod即为转出账户的classid，为支出
						   .$nbzz_classid
						   ." and zhifu = 4 and actime between "
						   .$BeginOfMonth
						   ." and "
						   .$EndOfMonth;
						   
		$query_expense12 = mysqli_query($conn,$query_expense12_sql);
		if($query_expense12 == FALSE){
			//die(mysqli_error($conn)); // TODO: better error handling
			$expense12 = 0;
			//echo "expense11 is null.</br>";
		}
		while($row_expense12 = mysqli_fetch_array($query_expense12)){
			$expense12 = $row_expense12[0];
			//echo "expense12:".$expense12."</br>";
		}
		$expense1 = $expense11 + $expense12;
		//echo "expense1:".$expense1."</br>";
		
		//当前结余
		$balance1 = $balance0 + $income1 - $expense1;
		
		//做总计的计算
		$total_balance0 = $total_balance0 + $balance0;
		$total_income1 = $total_income1 + $income1;
		$total_expense1 = $total_expense1 + $expense1;
		$total_balance1 = $total_balance1 + $balance1;
		
		//本月净收支,即不含本月内部转账的数据
		$total_income1_net = $total_income1_net + $income11;
		$total_expense1_net = $total_expense1_net + $expense11;

	    // 将数据写入Excel
	    $ColumnIndexOftotalTable += 1;
	    $column_Name = "A".$ColumnIndexOftotalTable;
	    $column_balance0 = "B".$ColumnIndexOftotalTable;
	    $column_income1 = "C".$ColumnIndexOftotalTable;
	    $column_expense1 = "D".$ColumnIndexOftotalTable;
	    $column_balance1 = "E".$ColumnIndexOftotalTable;

	    $objPHPExcel->getSheetByName("总表")
	    			->setCellValue($column_Name,$class)
	    			->setCellValue($column_balance0,$balance0)
	    			->setCellValue($column_income1,$income1)
	    			->setCellValue($column_expense1,$expense1)
	    			->setCellValue($column_balance1,$balance1);
	    //超链接
	    $objPHPExcel->getSheetByName("总表")
	    			->getCell($column_Name)
	    			->getHyperlink()
	    			->setUrl("sheet://".$class."!A1");
	    //超链接字体设为蓝色带下划线
	    $objPHPExcel->getSheetByName("总表")
	    			->getStyle($column_Name)
	    			->getFont()
	    			->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
	    $objPHPExcel->getSheetByName("总表")
	    			->getStyle($column_Name)
	    			->getFont()
	    			->getColor()
	    			->setARGB(PHPExcel_Style_Color::COLOR_BLUE);
	
	}
	//净收支
	$ColumnIndexOftotalTable += 1;
    $column_Name = "A".$ColumnIndexOftotalTable;
    $column_balance0 = "B".$ColumnIndexOftotalTable;
    $column_income1 = "C".$ColumnIndexOftotalTable;
    $column_expense1 = "D".$ColumnIndexOftotalTable;
    $column_balance1 = "E".$ColumnIndexOftotalTable;
	$objPHPExcel->getSheetByName("总表")
				->getStyle($column_Name)
				->getAlignment()
				->setWrapText(true);
	$objPHPExcel->getSheetByName("总表")
	    		->setCellValue($column_Name,"净收支\n(不含内部转账)")
	    		->setCellValue($column_balance0,"--")
	    		->setCellValue($column_income1,$total_income1_net)
	    		->setCellValue($column_expense1,$total_expense1_net)
	    		->setCellValue($column_balance1,"--");
	//总计
	$ColumnIndexOftotalTable += 1;
    $column_Name = "A".$ColumnIndexOftotalTable;
    $column_balance0 = "B".$ColumnIndexOftotalTable;
    $column_income1 = "C".$ColumnIndexOftotalTable;
    $column_expense1 = "D".$ColumnIndexOftotalTable;
    $column_balance1 = "E".$ColumnIndexOftotalTable;
	$objPHPExcel->getSheetByName("总表")
	    		->setCellValue($column_Name,"总计")
	    		->setCellValue($column_balance0,$total_balance0)
	    		->setCellValue($column_income1,$total_income1)
	    		->setCellValue($column_expense1,$total_expense1)
	    		->setCellValue($column_balance1,$total_balance1);
	//打开worksheet时默认选中A1
	$objPHPExcel->getSheetByName("总表")
				->getStyle("A1");
	//-------------------------------------------------------------------------------------------------
	//计算分表账目数据并写入Excel
	//-------------------------------------------------------------------------------------------------
	$query_paymethod_sql = "select * from jizhang_account_class where (classtype = 3 or classtype = 4) and ufid = ".$_SESSION['uid'];
	$query_paymethod = mysqli_query($conn,$query_paymethod_sql);
	$sheetname = "";
	while($row_paymethod = mysqli_fetch_array($query_paymethod))
	{
		$class = $row_paymethod['classname'];
		if($row_paymethod['classtype'] == 3)
		{
			$sheetname = $class;
		}else
		{
			$sheetname = substr($class,0,strpos($class,"["));
		}
		$classid = $row_paymethod['classid'];
		$query_account_sql = "select * from jizhang_account"
						   ." where jiid = "
						   .$_SESSION['uid']
						   ." and acpaymethod = "
						   .$row_paymethod['classid']
						   ." and actime between "
						   .$time1
						   ." and "
						   .$time2
						   ." order by actime";
		$query_account = mysqli_query($conn,$query_account_sql);
		while($row_account = mysqli_fetch_array($query_account))
		{
			//记录号
			$recordId = $row_account['acid'];
			//时间
			$recordTime = date("Y-m-d",$row_account['actime']);
			//金额，收入为正，支出为负，转账出为负，转账入为正（下面的代码会再查一遍转账入，这里只有转账出）
			$recordMoney = $row_account['acmoney'];
			if($row_account['zhifu'] == 1)
			{
				$recordMoney = 1 * $recordMoney;
			}else if($row_account['zhifu'] == 2){
				$recordMoney = -1 * $recordMoney;
			}else if($row_account['zhifu'] == 4){
				$recordMoney = -1 * $recordMoney;
			}
			//收入/支出/内部转账
			if($row_account['zhifu'] == 1)
			{
				$recordType = "收入";
			}else if($row_account['zhifu'] == 2){
				$recordType = "支出";
			}else if($row_account['zhifu'] == 4){
				$recordType = "内部转账";
			}
			//分类
			$query_class_sql = "select classname from jizhang_account_class"
							  ." where classid = "
						      .$row_account['acclassid'];
			$query_class = mysqli_query($conn,$query_class_sql);
			while($row_class = mysqli_fetch_array($query_class))
			{
				$recordClass = $row_class[0];
			}
			//备注
			$recordRemark = $row_account['acremark'];
			//写入Excel
			$ColumnIndexOftotalTable_detail = $objPHPExcel->getSheetByName($sheetname)->getHighestRow();
			$ColumnIndexOftotalTable_detail += 1;
    		$column_recordId = "A".$ColumnIndexOftotalTable_detail;
    		$column_recordTime = "B".$ColumnIndexOftotalTable_detail;
   			$column_recordMoney = "C".$ColumnIndexOftotalTable_detail;
    		$column_recordType = "D".$ColumnIndexOftotalTable_detail;
    		$column_recordClass = "E".$ColumnIndexOftotalTable_detail;
    		$column_recordRemark = "F".$ColumnIndexOftotalTable_detail;
    		$objPHPExcel->getSheetByName($sheetname)
	    			    ->setCellValue($column_recordId,$recordId)
	    			    ->setCellValue($column_recordTime,$recordTime)
	    			    ->setCellValue($column_recordMoney,$recordMoney)
	    			    ->setCellValue($column_recordType,$recordType)
	    			    ->setCellValue($column_recordClass,$recordClass)
	    			    ->setCellValue($column_recordRemark,$recordRemark);
		}
		
		//如果是内部转账的账号，再查一下被转入的记录
		if($row_paymethod['classtype'] == 4)	
		{
			$classid = $row_paymethod['classid'];
			$query_account_1_sql = "select * from jizhang_account"
							     ." where jiid = "
							   	 .$_SESSION['uid']
							     ." and acclassid = "
							     .$row_paymethod['classid']
							     ." and actime between "
							     .$time1
							     ." and "
							     .$time2
							     ." order by actime";
			$query_account_1 = mysqli_query($conn,$query_account_1_sql);
			while($row_account_1 = mysqli_fetch_array($query_account_1))
			{
				//记录号
				$recordId = $row_account_1['acid'];
				//时间
				$recordTime = date("Y-m-d",$row_account_1['actime']);
				//金额，转账入为正
				$recordMoney = $row_account_1['acmoney'];
				//收入/支出/内部转账
				$recordType = "内部转账";
				//分类
				$query_class_sql = "select classname from jizhang_account_class"
								  ." where classid = "
							      .$row_account['acpaymethod'];
				$query_class = mysqli_query($conn,$query_class_sql);
				while($row_class = mysqli_fetch_array($query_class))
				{
					$recordClass = $row_class[0];
				}
				//备注
				$recordRemark = $row_account_1['acremark'];
				//写入Excel
				$ColumnIndexOftotalTable_detail = $objPHPExcel->getSheetByName($sheetname)->getHighestRow();
				$ColumnIndexOftotalTable_detail += 1;
	    		$column_recordId = "A".$ColumnIndexOftotalTable_detail;
	    		$column_recordTime = "B".$ColumnIndexOftotalTable_detail;
	   			$column_recordMoney = "C".$ColumnIndexOftotalTable_detail;
	    		$column_recordType = "D".$ColumnIndexOftotalTable_detail;
	    		$column_recordClass = "E".$ColumnIndexOftotalTable_detail;
	    		$column_recordRemark = "F".$ColumnIndexOftotalTable_detail;
	    		$objPHPExcel->getSheetByName($sheetname)
		    			    ->setCellValue($column_recordId,$recordId)
		    			    ->setCellValue($column_recordTime,$recordTime)
		    			    ->setCellValue($column_recordMoney,$recordMoney)
		    			    ->setCellValue($column_recordType,$recordType)
		    			    ->setCellValue($column_recordClass,$recordClass)
		    			    ->setCellValue($column_recordRemark,$recordRemark);
			}
		}

		
	}
			
	//给分表写入总计公式
	//classtype=3 只用执行这个条件就行了，如果(classtype = 3 or classtype = 4)的话反而会重复
	$query_paymethod_sql = "select * from jizhang_account_class where classtype = 3 and ufid = ".$_SESSION['uid'];
	$query_paymethod = mysqli_query($conn,$query_paymethod_sql);
	$sheetname = "";
	while($row_paymethod = mysqli_fetch_array($query_paymethod))
	{
		$class = $row_paymethod['classname'];
		if($row_paymethod['classtype'] == 3)
		{
			$sheetname = $class;
		}else
		{
			$sheetname = substr($class,0,strpos($class,"["));
		}
		$currentSheet = $objPHPExcel->getSheetByName($sheetname);
		$highestRow = $currentSheet->getHighestRow();
		$jj = $highestRow + 1;
		if($jj == 3)   //等于3说明表里没有记录
		{
			$currentSheet->setCellValue("B".$jj,"总计")
			             ->setCellValue("C".$jj,0);
		}
		else
		{
			$currentSheet->setCellValue("B".$jj,"总计")
			             ->setCellValue("C".$jj,"=SUM(C3:C".$highestRow.")");
		}
		
	}
	//导出Excel
    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="account.xls"');
    header('Cache-Control: max-age=0');

    $objPHPExcel->setActiveSheetIndex(0);
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');

    exit;
}
?>

<table align="left" width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>
	<tr>
	<td bgcolor="#EBEBEB">导出到Excel</td>
	</tr>
	<td bgcolor="#FFFFFF">
	<form id="myform" name="form1" method="get" onsubmit="return checkpost();">
	选择时间：<br/><br/>
	从<input type="date" name="time1" id="time1" style="height:26px;width:160px;"/> <br/>
	<br/>
	到<input type="date" name="time2" id="time2" style="height:26px;width:160px;"/> <br/>
	<br/>
	<input type="submit" name="Submit" value="导出" class="btn btn-default" onclick="daochu()"/>
	<br/>
	<span style="color:red">注意：记账开始时间为2017-01-01。<br/></span>
	</form>
	</td>
</table>





<?php
    include_once("xiamian.php");
?>