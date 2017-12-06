<?php
session_start();
date_default_timezone_set("Asia/Shanghai"); 
include("config.php");

//点击查询按钮则按以下规则检索数据库并绘制表格,且最大查询时间范围为62天，默认查询30天 
$time1 = strtotime($_POST['time1']." 0:0:0");
$time2 = strtotime($_POST['time2']." 23:59:59");
$rangofday = ceil(($time2-$time1)/24/60/60);
$sql = "";
if($rangofday < 62)
{

	//什么都没填
	if($_POST['classid']=="quan" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$sql="select * from jizhang_account where jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "默认查询";

		}

	//查询一个参数---------------------------------------
	//只查询备注
	if($_POST['classid']=="quan" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']<>"")
		{
		 $a="%";
		 $b =$_POST['beizhu'];
		 $c=$a.$b.$a;
		 $sql="select * from jizhang_account where acremark like '$c' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "只查询备注";
		}

	//只查询收入
	if($_POST['classid']=="sr" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{

		$sql="select * from jizhang_account where zhifu='1' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "只查询收入";
		}

	//只查询支出
	if($_POST['classid']=="zc" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{

		$sql="select * from jizhang_account where zhifu='2' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime)  ORDER BY actime DESC";
		echo "只查询支出";
		}
	//只查询内部转账
	//请注意：$_POST['paymethod']=="quan2" && $_POST['class']=="quan2" 
	//两个都是quan2，因为querryclass的关系，传回给class和paymethod的是同一个返回值
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']=="quan2" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{

		$sql="select * from jizhang_account where zhifu='4' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime)  ORDER BY actime DESC";
		echo "只查询内部转账";
		}
	//只查询分类
	if($_POST['classid']=="quan" && $_POST['paymethod']=="quan1" && $_POST['class']<>"quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sqlclassid="acclassid=".$_POST['class'];
		$sql="select * from jizhang_account where ".$sqlclassid." and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime)  ORDER BY actime DESC";
		echo "只查询分类";
		}
	//只查询支付方式(账户名称)
	if($_POST['classid']=="quan" && $_POST['paymethod']<>"quan1" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$sqlzhifufangshi = "acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where ".$sqlzhifufangshi." and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime)  ORDER BY actime DESC";
		echo "只查询支付方式(账户名称)";
		}

	//只查询日期
	if($_POST['classid']=="quan" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");
		$sql="select * from jizhang_account where ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "只查询日期";
		}

	//查询两个参数----------------------------------------
	//查询收入，备注
	if($_POST['classid']=="sr" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']<>"")
		{
		$type="1";
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sql="select * from jizhang_account where zhifu='$type' and acremark like '$c' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime)  ORDER BY actime DESC";
		echo "查询收入，备注";
		}
	//查询支出，备注
	if($_POST['classid']=="zc" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']<>"")
		{
		$type="2";
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sql="select * from jizhang_account where zhifu='$type' and acremark like '$c' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime)  ORDER BY actime DESC";
		echo "查询支出，备注";
		}

	//查询内部转账，备注
	//请注意：$_POST['paymethod']=="quan2" && $_POST['class']=="quan2" 
	//两个都是quan2，因为querryclass的关系，传回给class和paymethod的是同一个返回值
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']=="quan2" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']<>"")
		{
		$type="4";
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sql="select * from jizhang_account where zhifu='$type' and acremark like '$c' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime)  ORDER BY actime DESC";
		echo "查询支出，备注";
		}

	//查询收入，日期
	if($_POST['classid']=="sr" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{
		$type="1";

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where zhifu='$type' and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询收入，日期";
		}
	//查询支出，日期
	if($_POST['classid']=="zc" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{
		$type="2";

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where zhifu='$type' and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询支出，日期";
		}
	//查询内部转账，日期
	//请注意：$_POST['paymethod']=="quan2" && $_POST['class']=="quan2" 
	//两个都是quan2，因为querryclass的关系，传回给class和paymethod的是同一个返回值
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']=="quan2" && $_POST['class']=="quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{
		$type="4";

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where zhifu='$type' and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询内部转账，日期";
		}
	//查询日期，备注
	if($_POST['classid']=="quan" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2"  && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']<>"")
		{
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where ".$sqltime." and acremark like '$c' and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询日期，备注";
		}

	//查询分类，备注
	if($_POST['classid']<>"quan" && $_POST['paymethod']=="quan1" && $_POST['class']<>"quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']<>"")
		{
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sqlclassid="acclassid=".$_POST['class'];

		$sql="select * from jizhang_account where ".$sqlclassid." and acremark like '$c' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime)  ORDER BY actime DESC";
		echo "查询分类，备注";
		}

	//查询分类，日期
	if($_POST['classid']<>"quan" && $_POST['paymethod']=="quan1" && $_POST['class']<>"quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{

		$sqlclassid="acclassid=".$_POST['class'];
		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where ".$sqlclassid." and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询分类，日期";
		}

	//查询支付方式(账户名称)，收入
	if($_POST['classid']=="sr" && $_POST['paymethod']<>"quan1" && $_POST['class']=="quan2"  && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$type="1";

		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlzhifufangshi." and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "查询支付方式(账户名称)，收入";
		}
	//查询支付方式(账户名称)，支出
	if($_POST['classid']=="zc" && $_POST['paymethod']<>"quan1" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$type="2";

		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlzhifufangshi." and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "查询支付方式(账户名称)，支出";
		}
	//查询支付方式(账户名称)，内部转账
	//请注意：$_POST['paymethod']<>"quan2" && $_POST['class']=="quan2" 
	//两个都是quan2，因为querryclass的关系，传回给class和paymethod的是同一个返回值
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']<>"quan2" && $_POST['class']=="quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$type="4";

		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlzhifufangshi." and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "查询支付方式(账户名称)，内部转账";
		}
	//查询分类，收入
	if($_POST['classid']=="sr" && $_POST['paymethod']=="quan1" && $_POST['class']<>"quan2"  && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$type="1";
		$sqlclassid="acclassid=".$_POST['class'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlclassid." and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "查询分类，收入";
		}

	//查询分类，支出
	if($_POST['classid']=="zc" && $_POST['paymethod']=="quan1" && $_POST['class']<>"quan2"  && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$type="2";
		$sqlclassid="acclassid=".$_POST['class'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlclassid." and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "查询分类，支出";
		}

	//查询分类，内部转账
	//请注意：$_POST['paymethod']<>"quan2" && $_POST['class']=="quan2" 
	//两个都是quan2，因为querryclass的关系，传回给class和paymethod的是同一个返回值
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']=="quan2" && $_POST['class']<>"quan2"  && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
		{
		$type="4";
		$sqlclassid="acclassid=".$_POST['class'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlclassid." and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "查询分类，内部转账";
		}

	//查询三个参数------------------------------
	//查询收入，分类，支付方式(账户名称)
	if($_POST['classid']=="sr" && $_POST['paymethod']<>"quan1" && $_POST['class']<>"quan2"  && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
	{
		$type="1";
		$sqlclassid="acclassid=".$_POST['class'];
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlclassid." and ".$sqlzhifufangshi."  and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询收入，分类，支付方式(账户名称)";
	}

	//查询支出，分类，支付方式(账户名称)
	if($_POST['classid']=="zc" && $_POST['paymethod']<>"quan1" && $_POST['class']<>"quan2"  && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
	{
		$type="2";
		$sqlclassid="acclassid=".$_POST['class'];
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlclassid." and ".$sqlzhifufangshi."  and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询支出，分类，支付方式(账户名称)";
	}

	//查询内部转账，分类，支付方式(账户名称)
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']<>"quan2" && $_POST['class']<>"quan2"  && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']=="")
	{
		$type="4";
		$sqlclassid="acclassid=".$_POST['class'];
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlclassid." and ".$sqlzhifufangshi."  and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询内部转账，分类，支付方式(账户名称)";
	}
	//查询收入，日期，备注
	if($_POST['classid']=="sr" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2"  && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']<>"")
		{
		$type="1";
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where zhifu='$type' and ".$sqltime." and acremark like '$c' and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询收入，日期，备注";
		}

	//查询支出，日期，备注
	if($_POST['classid']=="zc" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2"  && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']<>"")
		{
		$type="2";
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where zhifu='$type' and ".$sqltime." and acremark like '$c' and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询支出，日期，备注";
		}
	
		
	//查询内部转账，日期，备注
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']=="quan1" && $_POST['class']=="quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']<>"")
		{
		$type="4";
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where zhifu='$type' and ".$sqltime." and acremark like '$c' and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询内部转账，日期，备注";

		}

		
	//查询分类，支付方式(账户名称)，日期
	
	/* if(($_POST['classid']=="sr" || $_POST['classid']=="zc") && $_POST['paymethod']<>"quan1" && $_POST['class']<>"quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sqlclassid="acclassid=".$_POST['class'];
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where ".$sqlclassid." and ".$sqlzhifufangshi." and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询分类，支付方式(账户名称)，日期";
		} */

	//查询分类，支付方式(账户名称)，备注
	if($_POST['classid']<>"quan" && $_POST['paymethod']<>"quan1" && $_POST['class']<>"quan2" && $_POST['time1']=="" && $_POST['time2']=="" && $_POST['beizhu']<>"")
		{
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sqlclassid="acclassid=".$_POST['class'];
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where ".$sqlclassid." and ".$sqlzhifufangshi." and acremark like '$c' and jiid='$_SESSION[uid]' and DATE_SUB(CURDATE(), INTERVAL 30 DAY)<=from_unixtime(actime) ORDER BY actime DESC";
		echo "查询分类，支付方式(账户名称)，备注";
		} 
	//查询支付方式(账户名称)，日期，备注
	if($_POST['classid']=="quan" && $_POST['paymethod']<>"quan1" && $_POST['class']=="quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']<>"")
		{
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where ".$sqlzhifufangshi." and ".$sqltime." and acremark like '$c' and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询支付方式(账户名称)，日期，备注";
		}

	//查询支付方式(账户名称)，收入，日期
	if($_POST['classid']=="sr" && $_POST['paymethod']<>"quan1" && $_POST['class']=="quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{
		$type="1";

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlzhifufangshi." and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询支付方式(账户名称)，收入，日期";
		}
	//查询支付方式(账户名称)，支出，日期
	if($_POST['classid']=="zc" && $_POST['paymethod']<>"quan1" && $_POST['class']=="quan2"  && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{
		$type="2";
		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlzhifufangshi." and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询支付方式(账户名称)，支出，日期";
		}
	//查询支付方式(账户名称)，内部转账，日期
	//请注意：$_POST['paymethod']<>"quan2" && $_POST['class']=="quan2" 
	//两个都是quan2，因为querryclass的关系，传回给class和paymethod的是同一个返回值
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']<>"quan2" && $_POST['class']=="quan2"  && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{
		$type="4";
		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlzhifufangshi." and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询支付方式(账户名称)，内部转账，日期";
		}

	//查询分类，内部转账，日期
	if($_POST['classid']=="neibuzz" && $_POST['paymethod']=="quan2" && $_POST['class']<>"quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']=="")
		{
		$type="4";

		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");
		$sqlclassid="acclassid=".$_POST['class'];
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sql="select * from jizhang_account where zhifu='$type' and ".$sqlclassid." and ".$sqltime." and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询分类，内部转账，日期";
		}
		

	//查询全部参数------------------------------
	//查询收/支/内，分类，支付方式(账户名称)，日期，备注
	if($_POST['classid']<>"quan" && $_POST['paymethod']<>"quan1" && $_POST['class']<>"quan2" && $_POST['time1']<>"" && $_POST['time2']<>"" && $_POST['beizhu']<>"")
		{
		$a="%";
		$b =$_POST['beizhu'];
		$c=$a.$b.$a;
		$sqlclassid="acclassid=".$_POST['class'];
		$sqlzhifufangshi="acpaymethod=".$_POST['paymethod'];
		$sqltime=" actime >".strtotime($_POST['time1']." 0:0:0")." and actime <".strtotime($_POST['time2']." 23:59:59");

		$sql="select * from jizhang_account where ".$sqlclassid." and ".$sqlzhifufangshi." and ".$sqltime." and acremark like '$c' and jiid='$_SESSION[uid]' ORDER BY actime DESC";
		echo "查询收/支/内，分类，支付方式(账户名称)，日期，备注";
		}

	
	//输出查询结果-------------------------------------------------
	//分页
	//每页显示的数
	$pagesize = 10;
	//确定页数 p 参数
/*	if(isset($_GET['p']))
	{
		$p = $_GET['p'];
	}
	else
	{
		$p = 1;
	}*/
	$p = $_POST['pagenum'];

	//数据指针
	$pp = $p-1;
	$offset = $pp*$pagesize;

	//为实现分页功能修改sql查询语句
	$sqlC = $sql;	//先把sql备份一个，后面用到
	$sql = $sql." LIMIT  $offset , $pagesize";
	echo <<< eot
		<thead>
		<tr>
			<th bgcolor='#EBEBEB' style="width:20%">记录号：</br>分类(或转入户头)</th>
			<th bgcolor='#EBEBEB'>收支</th>
			<th bgcolor='#EBEBEB'>金额</th>
			<th bgcolor='#EBEBEB'>时间</th>
			<th bgcolor='#EBEBEB'>账户(或转出户头)</th>
			<th bgcolor='#EBEBEB'>备注</th>
			<th bgcolor='#EBEBEB'>操作</th>
		</tr>
		</thead>
eot;

	//echo $sql;
	$query=mysqli_query($conn,$sql);
	if(!$query)
	{
	printf("Error: %s\n", mysqli_error($conn));
	exit();
	}
	$income=0;
	$spending=0;
	echo "<tbody>";
	while($row = mysqli_fetch_array($query))
	{
		$sql="select * from jizhang_account_class where classid= $row[acclassid] and ufid='$_SESSION[uid]'";
		$classquery=mysqli_query($conn,$sql);
		$classinfo = mysqli_fetch_array($classquery);

		$sqlpaymethod="select * from jizhang_account_class where classid= $row[acpaymethod] and ufid='$_SESSION[uid]'";
		$paymethodquery=mysqli_query($conn,$sqlpaymethod);
		$paymethodinfo = mysqli_fetch_array($paymethodquery);
		echo "<tr>";
		if($classinfo['classtype']==1)
		{
			echo "<td align='left' bgcolor='#FFFFFF' data-title='分类(或转入户头)'><font color='MediumSeaGreen'>记录号:" . $row['acid'] . "</br>"  . $classinfo['classname'] . "</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='收支'><font color='MediumSeaGreen'>收入</font></td>";                
			echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='MediumSeaGreen'>" . $row['acmoney'] . "</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='MediumSeaGreen'>".date("Y-m-d",$row['actime'])."</font></td>";
			echo "<td align='left' bgcolor='#FFFFFF' data-title='账户(或转出户头)'><font color='MediumSeaGreen'>". $paymethodinfo['classname'] ."</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='MediumSeaGreen'>". $row['acremark'] ."</font></td>";

			$income=$income+$row['acmoney'];
		}
		else if($classinfo['classtype']==2)
		{
            echo "<td align='left' bgcolor='#FFFFFF' data-title='分类(或转入户头)'><font color='red'>记录号:" . $row['acid'] . "</br>" . $classinfo['classname'] . "</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='收支'><font color='red'>支出</font></td>";
			echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='red'>" . $row['acmoney'] . "</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='red'>".date("Y-m-d",$row['actime'])."</font></td>";
		    echo "<td align='left' bgcolor='#FFFFFF' data-title='账户(或转出户头)'><font color='red'>". $paymethodinfo['classname'] ."</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='red'>". $row['acremark'] ."</font></td>";
			$spending=$spending+$row['acmoney'];    
		}else if($classinfo['classtype']==4){
			echo "<td align='left' bgcolor='#FFFFFF' data-title='分类(或转入户头)'><font color='DeepPink'>记录号:" . $row['acid'] . "</br>" . $classinfo['classname'] . "</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='收支'><font color='DeepPink'>内部</br>转账</br>还款</font></td>";
			echo "<td align='left' bgcolor='#FFFFFF' data-title='金额'><font color='DeepPink'>" . $row['acmoney'] . "</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='时间'><font color='DeepPink'>".date("Y-m-d",$row['actime'])."</font></td>";
		    echo "<td align='left' bgcolor='#FFFFFF' data-title='账户(或转出户头)'><font color='DeepPink'>". $paymethodinfo['classname'] ."</font></td>";
            echo "<td align='left' bgcolor='#FFFFFF' data-title='备注'><font color='DeepPink'>". $row['acremark'] ."</font></td>";
		}
		echo "<td align='left' bgcolor='#FFFFFF' data-title='操作'><a href=xiugai.php?id=".$row['acid'].">编辑</a> <a href=shanchu.php?id=".$row['acid'].">删除</a></td>";
        echo "</tr>";
        echo "</tbody>";

	}
	echo "</table>";
	//绘制分页链接
	echo "<table border='0' align='left' cellpadding='5' cellspacing='1' bgcolor='#B3B3B3' class='table table-striped table-bordered'>
	      <tr>
	      <td align='left' bgcolor='#FFFFFF' colspan=7>";
	//修改sql获取查询结果的总条目数
	$start=strpos($sqlC,"*");
	$start=$start+1;
	$sqlC=substr($sqlC,$start);
	$sqlC="select count(*) as count".$sqlC;
	//echo $sqlC."</br>";
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
	            echo ' <a href="javascript:void(0)" onclick="querydetail(',$i,')">',$i,'</a>';

	        }
	    }
	}
	echo "</td></tr></table>";
}
else
{
echo("<script type='text/javascript'>alert('已超过查询范围！');</script>");
}


?>
<script language="javascript">
document.getElementById("tongji").innerHTML="<?='总共收入<font color=blue> '.$income.'</font> 总共支出 <font color=red>'.$spending.'</font>'?>"
</script>
