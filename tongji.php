<?php
    include_once("shangmian.php");
	
?>
<script language="javascript">
function fun1(str)
{
	alert(str);
}
</script>


<?php
//点击了刷新按钮后执行
if(isset($_GET['Submit'])&&$_GET['Submit']){
	// 3 代表指定使用proc_asset_edit中的刷新功能
	$sql="call proc_asset_edit(0,0,0,0,0,3)";
	$result = mysqli_query($conn,$sql);	
	if(!$result){
		printf("Error: %s\n", mysqli_error($conn));
		echo $sql;
		exit();
	}else{			
        echo("<script type='text/javascript'>alert('刷新成功！');</script>");
	}  
}
?>

<table align="left" border="0" cellpadding="0" cellspacing="0" class='table table-striped'>
  <tr>
    <form>
    <td height="5">当月统计
    	<!-- <input type="submit" name="Submit" id="Submit" value="刷新" /> -->
    </td>
     </form>
  </tr>
</table>

<table border='0' align='left'  bgcolor='#B3B3B3' class='table table-striped table-bordered table-bordered'>
	<tr>
	<th bgcolor='#EBEBEB' style="padding-right: 0"><font color="red">收入、支出类统计</font></th>
	</tr>
    <tr>
    <th bgcolor='#EBEBEB' style="padding-right: 0">账户名称</th>
    <th bgcolor='#EBEBEB' style="padding-right: 0"><div style="width:50px">上月<br />结余</div></th>
    <th bgcolor='#EBEBEB' style="padding-right: 0">本月<br />收入<br />
		<span id="bysrmx" style="cursor:pointer;text-decoration:underline" onclick="fun1(this.id)">
			（明细）</span></th>
    <th bgcolor='#EBEBEB' style="padding-right: 0">本月<br />支出<br />
		<span id="byzcmx" style="cursor:pointer;text-decoration:underline" onclick="fun1(this.id)">
			（明细）</span></th>
    <th bgcolor='#EBEBEB' style="padding-right: 0"><div style="width:50px">当前<br />结余</div></th>
    </tr>
<?php
	error_reporting(E_ALL || ~E_NOTICE);
	$query_paymethod_sql = "select * from jizhang_account_class where classtype = 3 and ufid = ".$_SESSION['uid'];
	$query_paymethod = mysqli_query($conn,$query_paymethod_sql);
	//curdate()获取零点时间
	$query_FristdayOfMonth = mysqli_query($conn,"select unix_timestamp(date_sub(curdate(),interval dayofmonth(curdate())-1 day))");
	while($row_FristdayOfMonth = mysqli_fetch_array($query_FristdayOfMonth)){
		$FristdayOfMonth = $row_FristdayOfMonth[0]; //本月第一天时间戳
	}
	$timenow = time();//当前时间戳

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
						   .$FristdayOfMonth
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
						   .$FristdayOfMonth
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
						   .$FristdayOfMonth
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
						   .$FristdayOfMonth
						   ." and "
						   .$timenow;
						   
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
							 .$FristdayOfMonth
						   ." and "
						   .$timenow;
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
						   .$FristdayOfMonth
						   ." and "
						   .$timenow;
						   
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
						   .$FristdayOfMonth
						   ." and "
						   .$timenow;
						   
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
		

	echo <<<eot
	<tr>
	<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="black">$class</font></td>
	<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="blue">$balance0</font></td>
	<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="mediumSeaGreen">$income1</font></td>
	<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="red">$expense1</font></td>
	<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="blue">$balance1</font></td>
	</tr>	
eot;
	}

	echo <<<eot
	<tr>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="black">净收支<br />(不含内部转账)</font></td>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="blue">--</font></td>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="mediumSeaGreen">$total_income1_net</font></td>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="red">$total_expense1_net</font></td>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="blue">--</font></td>
	</tr>
	<tr>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="black">总计</font></td>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="blue">$total_balance0</font></td>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="mediumSeaGreen">$total_income1</font></td>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="red">$total_expense1</font></td>
	<th align='left' bgcolor='#FFFFFF' style="font-style:italic;padding-right:0"><font color="blue">$total_balance1</font></td>
	</tr>	
eot;
?>
	<tr>
	<th bgcolor='#EBEBEB' style="padding-right: 0"><font color="red">内部转账及还款</font></th>
	</tr>
    <tr>
    <th bgcolor='#EBEBEB' style="padding-right: 0">被转入账户</th>
    <th bgcolor='#EBEBEB' style="padding-right: 0"><div style="width:50px">转出<br />账户</div></th>
    <th bgcolor='#EBEBEB' style="padding-right: 0">本月<br />转入<br />
		<span id="bysrmx" style="cursor:pointer;text-decoration:underline" onclick="fun1(this.id)">
			（明细）</span></th>
    <th bgcolor='#EBEBEB' style="padding-right: 0">本月<br />转出<br />
		<span id="byzcmx" style="cursor:pointer;text-decoration:underline" onclick="fun1(this.id)">
			（明细）</span></th>
    <th bgcolor='#EBEBEB' style="padding-right: 0">备注</th>
<?php
	error_reporting(E_ALL || ~E_NOTICE);
	$query_paymethod_sql = "select * from jizhang_account_class where classtype = 4 and ufid = ".$_SESSION['uid'];
	$query_paymethod = mysqli_query($conn,$query_paymethod_sql);
	//curdate()获取零点时间
	$query_FristdayOfMonth = mysqli_query($conn,"select unix_timestamp(date_sub(curdate(),interval dayofmonth(curdate())-1 day))");
	while($row_FristdayOfMonth = mysqli_fetch_array($query_FristdayOfMonth)){
		$FristdayOfMonth = $row_FristdayOfMonth[0]; //本月第一天时间戳
	}
	$timenow = time();//当前时间戳
	while($row_paymethod =  mysqli_fetch_array($query_paymethod))
	{
		//查询分类
		$class = $row_paymethod['classname'];
		//查询此分类本月截止目前的记录
		$query_nbzz_sql = "select * from jizhang_account where acclassid = "
						.$row_paymethod['classid']
						." and jiid = "
						.$_SESSION['uid']
						." and actime between "
						.$FristdayOfMonth
						." and "
						.$timenow;
		$query_nbzz = mysqli_query($conn,$query_nbzz_sql);
		if($query_nbzz == FALSE){
			echo mysqli_error($conn); // TODO: better error handling
		}
		while($row_nbzz = mysqli_fetch_array($query_nbzz))
		{
			//查询此笔转入户头名称
			$acpaymethod = $row_nbzz['acpaymethod'];
			$query_temp_sql =  "select classname from jizhang_account_class where classid = ".$acpaymethod;
			$query_temp = mysqli_query($conn,$query_temp_sql);
			if($query_temp == FALSE){
			die(mysqli_error($conn)); // TODO: better error handling
			}
			while($row_temp = mysqli_fetch_array($query_temp))
			{
				$class0 = $row_temp[0];
			};
			//查询此笔转入金额
			$money0 = $row_nbzz['acmoney'];
			//查询此笔转入的备注
			$beizhu0 = $row_nbzz['acremark'];

			//显示记录
			echo <<<eot
			<tr>
				<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="black">$class</font></td>
				<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="blue"><div style="width:50px;word-wrap:break-word;word-break:break-all;white-space: pre-wrap">$class0</div></font></td>
				<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="mediumSeaGreen">$money0</font></td>
				<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="red"></font></td>
				<td align='left' bgcolor='#FFFFFF' style="padding-right:0"><font color="blue"><div style="width:50px;word-wrap:break-word;word-break:break-all;white-space: pre-wrap">$beizhu0</div></font></td>
			</tr>	
eot;
		}

	}
?>	
</table>
           
<?php
    include_once("xiamian.php");
?>
