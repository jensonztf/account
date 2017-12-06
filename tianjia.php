<?php
    include_once("shangmian.php");
?>

<script language="JavaScript"> 
function checkpost() 
{ 
   if(myform.money.value==""){alert("请输入金额");
	window.location='tianjia.php';
    return false; 
   } 
   if(myform.classid.value==""){alert("请添加分类");
	window.location='fenlei.php';
    return false; 
   } 
   if(myform.paymethod.value==""){alert("请添加账户");
	window.location='fenlei.php';
    return false; 
   } 
} 

function checkpost2() 
{ 

 	if(myform2.money.value==""){alert("请输入金额");
	window.location='tianjia.php';
    return false; 
   }
    if(myform2.classid.value==""){alert("请添加分类");
	window.location='fenlei.php';
    return false; 
   }  
   if(myform2.paymethod.value==""){alert("请添加账户");
	window.location='fenlei.php';
    return false; 
   }  
 }
 
 function checkpost3() 
{ 

 	if(myform3.money.value==""){alert("请输入金额");
	window.location='tianjia.php';
    return false; 
 	}
    if(myform2.classid.value==""){alert("请添加分类");
	window.location='fenlei.php';
    return false; 
   } 
   if(myform3.paymethod.value==""){alert("请添加账户");
	window.location='fenlei.php';
    return false; 
   } 
 }
</script>

<?php
$income=0;
$spending=0;
//检查是否记账并执行
if(isset($_GET['Submit'])&&$_GET['Submit']){
	$time100=strtotime($_GET['time']);
	$timenow=time();
	$sql="insert into jizhang_account (acmoney, acclassid, actime, acremark,acpaymethod,jiid,zhifu) values ('$_GET[money]', '$_GET[classid]', '$time100', '$_GET[remark]','$_GET[paymethod]', '$_SESSION[uid]', '$_GET[zhifu]')";
	//记录属于收入/支出类别
	if($_GET['zhifu'] == 1 || $_GET['zhifu'] == 2)
	{
		//先执行jizhang_account，以便取得新增记录的acid
		$query=mysqli_query($conn,$sql);
		//取得新增记录acid
		$sql1="select max(acid) from jizhang_account;";
		$query1=mysqli_query($conn,$sql1);
		if($query1 == false)
		{
			echo (mysqli_error($conn));
		}else{
			while($row_query1=mysqli_fetch_array($query1))
			{
				$acid = $row_query1[0];
				if($acid == null){$acid = 0;}
				echo "acid:".$acid."</br>";
			}
		}
		//记录当前数据
		$sql2="call proc_asset_insert('$time100','$_GET[money]','$_GET[zhifu]','$_SESSION[uid]','$_GET[paymethod]','$acid');";
		
		$query2=mysqli_query($conn,$sql2);
		
		$prompttext = '';
		if($query && $query1 && $query2){
		$prompttext="<font color='#009900'>OK，记完了！  3秒后自动返回</font>";
		echo "$prompttext <meta http-equiv=refresh content='2; url=tianjia.php'>";}
		else{
		printf("Error: %s\n", mysqli_error($conn));
		exit();
		$prompttext="<font color='red'>出错啦，写入数据库时出错！5秒后自动返回</font>";
		echo "$prompttext <meta http-equiv=refresh content='4; url=tianjia.php'>";}
	}
	//记录属于内部转账类别，要同时更新转出账户和被转入账户的“jizhang_asset_”表
	//jizhang_account表中acclassid代表被转入账户，acpaymethod代表转出账户
	if($_GET['zhifu'] == 4)
	{
		//先执行jizhang_account，以便取得新增记录的acid
		$query=mysqli_query($conn,$sql);
		//取得新增记录acid
		$sql1="select max(acid) from jizhang_account;";
		$query1=mysqli_query($conn,$sql1);
		if($query1 == false)
		{
			echo (mysqli_error($conn));
		}else{
			while($row_query1=mysqli_fetch_array($query1))
			{
				$acid = $row_query1[0];
				if($acid == null){$acid = 0;}
				echo "acid:".$acid."</br>";
			}
		}
		//记录当前数据
		//记录转出账户的“jizhang_asset_”表,金额为页面提交金额
		$sql2="call proc_asset_insert('$time100','$_GET[money]','$_GET[zhifu]','$_SESSION[uid]','$_GET[paymethod]','$acid');";
		//记录被转入账户的“jizhang_asset_”表，金额为页面提交金额的负数
		$jjj = -1 * $_GET['money'];
		$sql3="call proc_asset_insert('$time100','$jjj','$_GET[zhifu]','$_SESSION[uid]','$_GET[classid]','$acid');";

		$query2=mysqli_query($conn,$sql2);
		$query3=mysqli_query($conn,$sql3);
		

		$prompttext = '';
		if($query && $query1 && $query2 && $query3){
		$prompttext="<font color='#009900'>OK，记完了！  3秒后自动返回</font>";
		echo "$prompttext <meta http-equiv=refresh content='2; url=tianjia.php'>";}
		else{
		printf("Error: %s\n", mysqli_error($conn));
		exit();
		$prompttext="<font color='red'>出错啦，写入数据库时出错！5秒后自动返回</font>";
		echo "$prompttext <meta http-equiv=refresh content='4; url=tianjia.php'>";}
	}
	
}
?>
<table align="left" width="100%" border="0" cellpadding="5" cellspacing="10" bgcolor='#B3B3B3' class='table table-striped table-bordered'>
<tr>
    <td bgcolor="#EBEBEB">　<font color="red">支出</font></td>
</tr>
<tr><td bgcolor="#FFFFFF">
	<form id="form2" name="myform2" method="get" onsubmit="return checkpost2();">
		<font color="red">&nbsp;&nbsp;&nbsp;金额：</font><input name="money" type="text" id="money" size="8" /><div style="display:none;"><input name="zhifu" type="text" id="zhifu" value="2" size="8" /></div>
		<br /><br />
		<font color="red">&nbsp;&nbsp;&nbsp;分类：</font>
		<select name="classid" id="classid" style="height:26px;">
		<?php
		$sql="select * from jizhang_account_class where classtype=2 and ufid='$_SESSION[uid]'";
		$query=mysqli_query($conn,$sql);
		while($acclass=mysqli_fetch_array($query)){
			echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
		}
		?>
		</select> 
		<br /><br />
		<font color="red">&nbsp;&nbsp;&nbsp;账户(资金户头)：</font>
		<select name="paymethod" id="paymethod" style="height:26px;">
		<?php
		$sql="select * from jizhang_account_class where classtype=3 and ufid='$_SESSION[uid]'";
		$query=mysqli_query($conn,$sql);
		while($acclass=mysqli_fetch_array($query)){
		echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
		}
		?>
		</select> 
		<font color="red"><a href="fenlei.php" style="color:#ccc;">添加分类</a></font>
		<br /><br />&nbsp;&nbsp;&nbsp;备注：
		<input name="remark" type="text" id="remark" /> <font color="#ccc">方便搜索</font>
		<br /><br />&nbsp;&nbsp;&nbsp;时间：<input type="text" name="time" id="time" value="<?php $xz=date("Y-m-d");;echo "$xz"; ?>"/>
		<input name="Submit" type="submit" id="Submit" value="记账" /> 
</form>
</td></tr>

	<tr>
	<td bgcolor="#EBEBEB">　<font color="MediumSeaGreen">收入</font></td>
	</tr>
 	<tr><td bgcolor="#FFFFFF">
	<form id="form" name="myform" method="get" onsubmit="return checkpost();">
	　<font color="MediumSeaGreen">金额：</font>
	<input name="money" type="text" id="money" size="8" /><div style="display:none;"><input name="zhifu" type="text" id="zhifu" value="1" size="8" /></div>
	<br /><br />
	　<font color="MediumSeaGreen">分类：</font>
	<select name="classid" id="classid" style="height:26px;">
		<?php
		$sql="select * from jizhang_account_class where classtype=1 and ufid='$_SESSION[uid]'";
		$query=mysqli_query($conn,$sql);
		while($acclass=mysqli_fetch_array($query)){
			echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
		}
		?>
	</select>
	<br /><br />
	<font color="MediumSeaGreen">&nbsp;&nbsp;&nbsp;账户(资金户头)：</font><select name="paymethod" id="paymethod" style="height:26px;">
	<?php
	$sql="select * from jizhang_account_class where classtype=3 and ufid='$_SESSION[uid]'";
	$query=mysqli_query($conn,$sql);
	while($acclass=mysqli_fetch_array($query)){
		echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
	}
	?>
	</select> 
	<font color="MediumSeaGreen"><a href="fenlei.php" style="color:#ccc;">添加分类</a></font>
	<br /><br />　备注：
	<input name="remark" type="text" id="remark" /> <font color="#ccc">方便搜索</font>
	<br /><br />　时间：<input type="text" name="time" id="time" value="<?php $xz=date("Y-m-d");;echo "$xz"; ?>"/>
	<input type="submit" name="Submit" id="Submit" value="记账" /> 
</form>
        
</td></tr>

    <tr>
	<td bgcolor="#EBEBEB">　<font color="Blue">内部转账及还款</font></td>
    </tr>
 	<tr><td bgcolor="#FFFFFF">
	<form id="form" name="myform3" method="get" onsubmit="return checkpost3();">
		<font color="Blue">&nbsp;&nbsp;&nbsp;金额：</font>
		<input name="money" type="text" id="money" size="8" /><div style="display:none;"><input name="zhifu" type="text" id="zhifu" value="4" size="8" /></div>
		<br /><br />
		　<font color="Blue">转入户头：</font>
		<select name="classid" id="classid" style="height:26px;">
			<?php
			$sql="select * from jizhang_account_class where classtype=4 and ufid='$_SESSION[uid]'";
			$query=mysqli_query($conn,$sql);
			while($acclass=mysqli_fetch_array($query)){
				echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
			}
			?>
		</select>
		<br /><br />
		<font color="Blue">&nbsp;&nbsp;&nbsp;转出户头：</font><select name="paymethod" id="paymethod" style="height:26px;">
		<?php
		$sql="select * from jizhang_account_class where classtype=4 and ufid='$_SESSION[uid]'";
		$query=mysqli_query($conn,$sql);
		while($acclass=mysqli_fetch_array($query)){
			echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
		}
		?>
		</select> 
		<font color="MediumSeaGreen"><a href="fenlei.php" style="color:#ccc;">添加分类</a></font>
		<br /><br />　备注：
		<input name="remark" type="text" id="remark" /> <font color="#ccc">方便搜索</font>
		<br /><br />　时间：<input type="text" name="time" id="time" value="<?php $xz=date("Y-m-d");;echo "$xz"; ?>"/>
		<input type="submit" name="Submit" id="Submit" value="记账" /> 
</form>
        
</td></tr>
</table>

 <table align="left" width="100%" height="20" border="0" align="left" cellpadding="5" cellspacing="1" bgcolor="#B3B3B3" >
  <tr>
    <td align="left" bgcolor="#EBEBEB"><font id="tongji"></font></td>
  </tr>
</table>
            
<?php        
//每页显示的数
$pagesize = 10;

//确定页数 p 参数
/* if(isset($_GET['p'])){
$p = $_GET['p']?$_GET['p']:1;
} */
if(isset($_GET['p']))
{
	$p = $_GET['p'];
}
else
{
	$p = 1;
}

//数据指针
$pp = $p-1;
$offset = $pp*$pagesize;
//查询本页显示的数据
$query_sql = "SELECT * FROM jizhang_account where jiid='$_SESSION[uid]' ORDER BY actime DESC LIMIT  $offset , $pagesize";
$query=mysqli_query($conn,$query_sql);

			echo "<div id='no-more-tables'>
				<table width='100%' border='0' align='left' cellpadding='5' cellspacing='1' bgcolor='#B3B3B3' class='table table-striped table-bordered'>
                <thead>
                <tr>
                <th bgcolor='#EBEBEB'>记录号：</br>分类(或转入户头)</th>
                <th bgcolor='#EBEBEB'>收支</th>
                <th bgcolor='#EBEBEB'>金额</th>
                <th bgcolor='#EBEBEB'>时间</th>
				<th bgcolor='#EBEBEB'>账户(或转出户头)</th>
                <th bgcolor='#EBEBEB'>备注</th>
                <th bgcolor='#EBEBEB'>操作</th>
                </tr>
                </thead>";
             
//             if($result === FALSE) {
			   if($query == FALSE){
			   die(mysqli_error($conn)); // TODO: better error handling
			   }
			echo "<tbody>";
			while($row = mysqli_fetch_array($query)){
				$sql="select * from jizhang_account_class where classid=$row[acclassid] and ufid='$_SESSION[uid]'";
				$sqlpay="select * from jizhang_account_class where classid=$row[acpaymethod] and ufid='$_SESSION[uid]'";
				$classquery=mysqli_query($conn,$sql);
				$classinfo = mysqli_fetch_array($classquery);
				$paymethodquery=mysqli_query($conn,$sqlpay);
				/* if(!$paymethodquery){
					printf("Error: %s\n", mysqli_error($conn));
					exit();
				} */
				$paymethodinfo = mysqli_fetch_array($paymethodquery); 

                echo "<tr>";
            if($classinfo['classtype']==1){
                echo "<td align='left' bgcolor='#FFFFFF' data-title='分类(或转入户头)'><font color='MediumSeaGreen'>记录号:" . $row['acid'] . "</br>" . $classinfo['classname'] . "</font></td>";
                echo "<td align='left' bgcolor='#FFFFFF' data-title='收支'><font color='MediumSeaGreen'>收入</font></td>";                
				echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='MediumSeaGreen'>" . $row['acmoney'] . "</font></td>";
                echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='MediumSeaGreen'>".date("Y-m-d",$row['actime'])."</font></td>";
				echo "<td align='left' bgcolor='#FFFFFF' data-title='账户(或转出户头)'><font color='MediumSeaGreen'>". $paymethodinfo['classname'] ."</font></td>";
                echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='MediumSeaGreen'>". $row['acremark'] ."</font></td>";
            }else if($classinfo['classtype']==2){
                echo "<td align='left' bgcolor='#FFFFFF' data-title='分类(或转入户头)'><font color='red'>记录号:" . $row['acid'] . "</br>"  . $classinfo['classname'] . "</font></td>";
                echo "<td align='left' bgcolor='#FFFFFF' data-title='收支'><font color='red'>支出</font></td>";
				echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='red'>" . $row['acmoney'] . "</font></td>";
               echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='red'>".date("Y-m-d",$row['actime'])."</font></td>";
			    echo "<td align='left' bgcolor='#FFFFFF' data-title='账户(或转出户头)'><font color='red'>". $paymethodinfo['classname'] ."</font></td>";
               echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='red'>". $row['acremark'] ."</font></td>";
            }else if($classinfo['classtype']==4){
				echo "<td align='left' bgcolor='#FFFFFF' data-title='分类(或转入户头)'><font color='DeepPink'>记录号:" . $row['acid'] . "</br>"  . $classinfo['classname'] . "</font></td>";
                echo "<td align='left' bgcolor='#FFFFFF' data-title='收支'><font color='DeepPink'>内部</br>转账</br>还款</font></td>";
				echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='DeepPink'>" . $row['acmoney'] . "</font></td>";
               echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='DeepPink'>".date("Y-m-d",$row['actime'])."</font></td>";
			    echo "<td align='left' bgcolor='#FFFFFF' data-title='账户(或转出户头)'><font color='DeepPink'>". $paymethodinfo['classname'] ."</font></td>";
               echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='DeepPink'>". $row['acremark'] ."</font></td>";
			}
                echo "<td align='left' bgcolor='#FFFFFF' data-title='操作'><a href=xiugai.php?id=".$row['acid'].">编辑</a> <a href=shanchu.php?id=".$row['acid'].">删除</a></td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
            echo "</div>";
			
			
echo "<table width='100%' border='0' align='left' cellpadding='5' cellspacing='1' bgcolor='#B3B3B3' class='table table-striped table-bordered'>
      <tr><td align='left' bgcolor='#FFFFFF'>";
			
//分页代码
//计算总数
$count_result = mysqli_query($conn,"SELECT count(*) as count FROM jizhang_account where jiid='$_SESSION[uid]'");
$count_array = mysqli_fetch_array($count_result);

//计算总的页数
$pagenum=ceil($count_array['count']/$pagesize);
echo '共记 ',$count_array['count'],' 条 '; echo ' 这里最多显示最近 ',$pagesize,' 条';
echo '</br>';
//循环输出各页数目及连接
/*if ($pagenum > 1) {
    for($i=1;$i<=$pagenum;$i++) {
        if($i==$p) {
            echo ' [',$i,']';
        } else {
            echo ' <a href="tianjia.php?p=',$i,'">',$i,'</a>';
        }
    }
}*/

//当前页码
$curPage = 1;
//总页码数
$totalPage = 1;
//中间分页页码数，5
$mpsize = 5;
//中间页码第一个页码数
$firstPage = 1;
//中间页码最后一个页码数
$endPage = 1;

$totalPage = $pagenum;
$curPage = $p;
if($totalPage < $mpsize){
	$firstPage = 1;  
     $endPage = $mpsize;  
}
else{
	if($curPage <= 3){  
     $firstPage = 1;  
     $endPage = $mpsize;  
     }else{  
          $firstPage = $curPage - 2;  
          $endPage = $curPage + $mpsize - 3;  
          if($endPage>$totalPage){
          	$endPage = $totalPage;  
          }  
     }  
}  

if($firstPage != 1)
{
	echo '<a href="tianjia.php?p=',$curPage-1,'">上一页</a>';
}
for($i=$firstPage;$i<=$endPage;$i++){
	if($i==$curPage){
		echo ' [',$i,']';
	}else{
		echo ' <a href="tianjia.php?p=',$i,'">',$i,'</a>';
	}
}
if($endPage != $totalPage)
{
	echo '<a href="tianjia.php?p=',$curPage+1,'">下一页</a>';
}


echo "</td></tr></table>";
?>
		  

<?php
			
$sql="select * from jizhang_account where jiid='$_SESSION[uid]' ORDER BY actime ASC";
$query=mysqli_query($conn,$sql);
while($row = mysqli_fetch_array($query)){
	$sql="select * from jizhang_account_class where classid= $row[acclassid] and ufid='$_SESSION[uid]'";
	$classquery=mysqli_query($conn,$sql);
	$classinfo = mysqli_fetch_array($classquery);
	
	if($classinfo['classtype']==1){
	 	
		$income=$income+$row['acmoney'];
	}else if($classinfo['classtype']==2){
		$spending=$spending+$row['acmoney'];
	}
}
	
 ?>	  
		  
	  
<script language="javascript">
document.getElementById("tongji").innerHTML="<?='总共收入<font color=MediumSeaGreen> '.$income.'</font> 总共支出 <font color=red>'.$spending.'</font>'?>"
</script>
<?php
    include_once("xiamian.php");
?>
