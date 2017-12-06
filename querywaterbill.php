<?php
session_start();
date_default_timezone_set("Asia/Shanghai"); 
include("config.php");

//点击查询按钮则按以下规则检索数据库并绘制表格,默认查询30天 
$time1_origin = strtotime($_POST['time1']." 0:0:0");
$time2_origin = strtotime($_POST['time2']." 23:59:59");
$paymethod = $_POST['paymethod'];
$sql = "select classname from jizhang_account_class where classid = " .$paymethod. " and ufid='$_SESSION[uid]';";
$query = mysqli_query($conn,$sql);
$row= mysqli_fetch_array($query);
$classname = $row[0];
echo $classname;
$classname1 = $classname."[内部]";
$sql = "select classid from jizhang_account_class where classname = '" .$classname1. "' and ufid='$_SESSION[uid]';";
$query = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($query);
$paymethod1 = $row[0];
//查询示例中的表的第一行的as_type字段的值
$jizhang_asset_paymethod = "jizhang_asset_".$paymethod;
$jizhang_asset_paymethod1 = "jizhang_asset_".$paymethod1;

//余额分为两部分，户头和户头[内部]，通过户头名查询到户头和户头[内部]对应的paymethod，按照时间范围，同时查询它们的jizhang_asset_表，示例如下：
// mysql> select * from (select * from jizhang_asset_29 where as_time between 1506787200 and 1512057600 union select * from jizhang_asset_41 where as_time between 1506787200 and 1512057600) as c order by as_time;
// +---------+--------+------------+-------------+---------+------+----------+------------+------+-------------+-------------+
// | assetid | number | as_time    | as_time_ymd | as_type | acid | as_money | as_balance | ufid | modify_info | modify_time |
// +---------+--------+------------+-------------+---------+------+----------+------------+------+-------------+-------------+
// |      97 |     76 | 1506787200 | 2017-10-01  |       2 |  673 |      800 |       7504 |    1 | 0           |           0 |
// |      98 |     77 | 1507046400 | 2017-10-04  |       2 |  678 |      200 |       7704 |    1 | 0           |           0 |
// |      21 |      7 | 1507132800 | 2017-10-05  |       4 |  680 |    -1000 |      -4500 |    1 | 0           |           0 |
// |      99 |     78 | 1507132800 | 2017-10-05  |       2 |  679 |      305 |       8009 |    1 | 0           |           0 |
// |     100 |     79 | 1507305600 | 2017-10-07  |       2 |  681 |       18 |       8027 |    1 | 0           |           0 |
// |     101 |     80 | 1511366400 | 2017-11-23  |       2 |  795 |       59 |       8086 |    1 | 0           |           0 |
// |     103 |     82 | 1511452800 | 2017-11-24  |       2 |  810 |       20 |       8112 |    1 | 0           |           0 |
// |     102 |     81 | 1511452800 | 2017-11-24  |       2 |  806 |        6 |       8092 |    1 | 0           |           0 |
// |     104 |     83 | 1511625600 | 2017-11-26  |       2 |  814 |       25 |       8137 |    1 | 0           |           0 |
// |      22 |      8 | 1511798400 | 2017-11-28  |       4 |  817 |    -2000 |      -6500 |    1 | 0           |           0 |
// |     105 |     84 | 1511798400 | 2017-11-28  |       2 |  819 |     1800 |       9937 |    1 | 0           |           0 |
// |     106 |     85 | 1511884800 | 2017-11-29  |       2 |  820 |       40 |       9977 |    1 | 0           |           0 |
// |     107 |     86 | 1512057600 | 2017-12-01  |       2 |  824 |        5 |       9982 |    1 | 0           |           0 |
// +---------+--------+------------+-------------+---------+------+----------+------------+------+-------------+-------------+
//表中未涂黄的是“户头”的数据，涂黄的是“户头[内部]”的数据

//“户头”余额有分为两部分，收入(1)和支出(2)部分，上表所示的时间范围就没有收入部分的记录，因此要判断一下：
//如果此时间范围内第一个“户头”数据是支出(2)，则要向前查离第一个“户头”数据最近的一个收入(1)数据，反之，
//如果此时间范围内第一个“户头”数据是收入(1)，则要向前查离第一个“户头”数据最近的一个支出(2)数据
//“户头[内部]”只有一个类型(as_type)，但是as_money有正负之分("户头"的as_money只有正数),负数为转入，正数为转出，也要查离时间范围最近的一条记录的数据

//以上表为例，演示如何对余额进行填表
//1.查得离2017-10-01最近的收入(1)数据为assetid=74条数据，其as_balance=3846，代表此账户到此条记录位置总共收入3846，记录为变量B1
//2.上表第一条数据为支出(2)数据，as_balance=7504，代表此账户到此条记录位置总共支出7504，记录为变量B2
//3.查得离2017-10-01最近的内部转账(4)数据为assetid=20条数据，其as_balance=-3500，代表此账户到此条记录位置总共被转入3500，记录为变量B4
//4.第一条查询结果的余额为B=B1-B2-B3=3846-7504+3500；
//5.第二条查询结果，as_type=2，则B2变量更新为7704，B=B1-B2-B3=3846-7704+3500；
//6.第三条查询结果，as_type=4，则B4变量更新为-4500，B=B1-B2-B3=3846-7704+4500；
//7.往下依次类推，完成填表
//按acid查询jizhang_account表，得到分类acclassid，金额acmoney，时间actime，备注acremark，逐条填入表格


//输出查询结果-------------------------------------------------
//分页
//每页显示的数
$pagesize = 10;
//确定页数 p 参数
$p = $_POST['pagenum'];
//数据指针
$pp = $p-1;
$offset = $pp*$pagesize;

$sql = "select as_type , as_balance , acid , as_time_ymd , as_time , as_money from (select * from "
       .$jizhang_asset_paymethod
	   ." where as_time between "
	   .$time1_origin." and ".$time2_origin
	   ." union select * from "
	   .$jizhang_asset_paymethod1
	   ." where as_time between "
	   .$time1_origin." and ".$time2_origin
	   .") as c order by as_time";
//为实现分页功能修改sql查询语句
$sqlC = $sql;	//先把sql备份一个，后面用到
$sql = $sql." LIMIT  $offset , $pagesize";
//查询各项数据-----------------------------------------------------------------------------------------------------------------------------------------
//查询与$_POST['paymethod']对应的户头[内部]paymethod1


//每次分页载入后，要重新求取余额的初始值，对应地要修改$sql中的$time1的值
$query = mysqli_query($conn,$sql);
$row = mysqli_fetch_array($query);
$as_type = $row[0];
$time1 = $row[4];
//根据as_type的值，确定各余额的初始值
$B1 = 0;
$B2 = 0;
$B4 = 0;
if($as_type == 1)
{
	$B1 = $row[1];
	$sql = "select as_balance from ".$jizhang_asset_paymethod." where as_type = 2 and as_time <= ".$time1." order by as_time desc limit 0,1;";
	$query = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($query);
	$B2 = $row[0];
	$sql = "select as_balance from ".$jizhang_asset_paymethod1." where as_type = 4 and as_time <= ".$time1." order by as_time desc limit 0,1;";
	$query = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($query);
	$B4 = $row[0];
}
if($as_type == 2)
{
	$B2 = $row[1];
	$sql = "select as_balance from ".$jizhang_asset_paymethod." where as_type = 1 and as_time <= ".$time1." order by as_time desc limit 0,1;";
	$query = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($query);
	$B1 = $row[0];
	$sql = "select as_balance from ".$jizhang_asset_paymethod1." where as_type = 4 and as_time <= ".$time1." order by as_time desc limit 0,1;";
	$query = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($query);
	$B4 = $row[0];
}
if($as_type == 4)
{
	$B4 = $row[1];
	$sql = "select as_balance from ".$jizhang_asset_paymethod." where as_type = 1 and as_time <= ".$time1." order by as_time desc limit 0,1;";
	$query = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($query);
	$B1 = $row[0];
	$sql = "select as_balance from ".$jizhang_asset_paymethod." where as_type = 2 and as_time <= ".$time1." order by as_time desc limit 0,1;";
	$query = mysqli_query($conn,$sql);
	$row = mysqli_fetch_array($query);
	$B2 = $row[0];
}
$B = $B1-$B2-$B4;
//输出表头
echo <<< eot
	<thead>
	<tr>
		<th bgcolor='#EBEBEB' style="width:20%">记录号</th>
		<th bgcolor='#EBEBEB'>时间</th>
		<th bgcolor='#EBEBEB'>金额</th>
		<th bgcolor='#EBEBEB'>备注</th>
		<th bgcolor='#EBEBEB'>余额(含[内部])</th>
	</tr>
	</thead>
eot;

echo "<tr>";
$sql = $sqlC;
$sql = $sql." LIMIT  $offset , $pagesize";
$query=mysqli_query($conn,$sql);
if(!$query)
{
printf("Error: %s\n", mysqli_error($conn));
exit();
}
echo "<tbody>";

while($row = mysqli_fetch_array($query))
{
	$as_type = $row[0];
	$acid = $row[2];
	$as_time_ymd = $row[3];
	$as_money = $row[5];
	$sql_remark = "select acremark from jizhang_account where acid = ".$acid.";";
	$query_remark = mysqli_query($conn,$sql_remark);
	$row_remark = mysqli_fetch_array($query_remark);
	$remark = $row_remark[0];
	echo "<tr>";
	if($as_type == 1)
	{
		$B1 = $row[1];
		$B = $B1-$B2-$B4;
		echo "<td align='left' bgcolor='#FFFFFF' data-title='记录号'><font color='MediumSeaGreen'>".$acid." | 收入</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='MediumSeaGreen'>".$as_time_ymd."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='MediumSeaGreen'>".$as_money."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='MediumSeaGreen'>".$remark."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='余额'><font color='MediumSeaGreen'>".$B."</font></td>"; 
	}
	else if($as_type == 2)
	{
		$B2 = $row[1];
		$B = $B1-$B2-$B4;
		echo "<td align='left' bgcolor='#FFFFFF' data-title='记录号'><font color='red'>".$acid." | 支出</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='red'>".$as_time_ymd."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='red'>".$as_money."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='red'>".$remark."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='余额'><font color='red'>".$B."</font></td>";    
	}else if($as_type == 4)
	{
		$B4 = $row[1];
		$B = $B1-$B2-$B4;
		echo "<td align='left' bgcolor='#FFFFFF' data-title='记录号'><font color='DeepPink'>".$acid." | 内部转账</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='DeepPink'>".$as_time_ymd."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='DeepPink'>".$as_money."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='DeepPink'>".$remark."</font></td>"; 
		echo "<td align='left' bgcolor='#FFFFFF' data-title='余额'><font color='DeepPink'>".$B."</font></td>"; 
	}
	echo "</tr>";
	echo "</tbody>";

}
echo "</table>";
//绘制分页链接
echo "<table border='0' align='left' cellpadding='5' cellspacing='1' bgcolor='#B3B3B3' class='table table-striped table-bordered'>
	  <tr>
	  <td align='left' bgcolor='#FFFFFF' colspan=7>";
//修改sql获取查询结果的总条目数
$start=strpos($sqlC,"as_money");
$start=$start+strlen("as_money");
$sqlC=substr($sqlC,$start);
$sqlC="select count(*) as count".$sqlC;

//分页代码
//计算总数
$count_result = mysqli_query($conn,$sqlC);
$count_array = mysqli_fetch_array($count_result);
//计算总的页数
$pagenum=ceil($count_array['count']/$pagesize);
echo '共记 ',$count_array['count'],' 条 '; echo ' 这里最多显示最近 ',$pagesize,' 条';
//循环输出各页数目及连接
if ($pagenum > 1) {
	for($i=1;$i<=$pagenum;$i++) {
		if($i==$p) {
			echo ' [',$i,']';
		} else {
			echo ' <a href="javascript:void(0)" onclick="querywaterbill(',$i,')">',$i,'</a>';

		}
	}
}
echo "</td></tr></table>";

?>