<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="UTF-8">
<title>安装 php mysql 简单记账xptt程序</title>
<?php
//define('IN_SYS',TRUE);//定义了IN_SYS后不予访问php
?>

<p align="center">
<?php
include("config.php");
echo "创建数据库.....";
if(indatabase($db_dbname,$conn)){
	echo "已经存在数据库，跳过<br />";
}else{
	$sql = "create database ".$db_dbname." default character SET utf8 COLLATE utf8_general_ci";
	$query=mysqli_query($conn,$sql);
	if($query){
		echo "成功<br />";
	}else{
		echo "失败<br /><font color='red'>安装失败啦，请检查config.php相关配置。</font></body></html>";
		
	}
}
echo "创建表 jizhang_account .....";
if(intable($db_dbname,$qianzui."account",$conn)){
	echo "已存在<br /><font color='red'>已经安装过啦，表前缀已经存在。</font></body></html>";
	
}else{
	$sql = "CREATE TABLE `$db_dbname`.`jizhang_account` (`acid` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, `acmoney` INT(5) NOT NULL, `acclassid` INT(8) NOT NULL, `actime` INT(11) NOT NULL, `acremark` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, `acpaymethod` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, `jiid` INT(8) NOT NULL, `zhifu` INT(8) NOT NULL) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci";
	$query=mysqli_query($conn,$sql);
	if($query){
	echo "成功<br />";
	}else{
		echo $sql;
		echo "<br />失败<br /><font color='red'>安装失败啦，请检查config.php相关配置。</font></body></html>";
		
	}
}
echo "创建表 jizhang_account_class .....";
if(intable($db_dbname,$qianzui."account_class",$conn)){
	echo "已存在<br /><font color='red'>已经安装过啦，表前缀已经存在。</font></body></html>";
	
}else{
	$sql = "CREATE TABLE `$db_dbname`.`jizhang_account_class` (`classid` INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY, `classname` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, `classtype` INT(1) NOT NULL, `ufid` INT(8) NOT NULL) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$query=mysqli_query($conn,$sql);
	if($query){
	echo "成功<br />";
	}else{
		echo "失败<br /><font color='red'>安装失败啦，请检查config.php相关配置。</font></body></html>";
		
	}
}

echo "创建表 jizhang_user .....";
if(intable($db_dbname,$qianzui."user",$conn)){
	echo "已存在<br /><font color='red'>已经安装过啦，表前缀已经存在。</font></body></html>";
	
}else{
	$sql = "CREATE TABLE `$db_dbname`.`jizhang_user` (`uid` INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY, `username` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, `password` VARCHAR(35) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, `email` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, `utime` INT(11) NOT NULL) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;";
	$query=mysqli_query($conn,$sql);
	if($query){
	echo "成功<br />";
	}else{
		echo "失败<br /><font color='red'>安装失败啦，请检查config.php相关配置。</font></body></html>";
	}
}


//删除原来的存储过程
mysqli_select_db($conn,'jizhang');
$rs=mysqli_query($conn,'show procedure status like "proc_asset_%"');
while($arr=mysqli_fetch_array($rs))
{
	$FT=mysqli_query($conn,"drop procedure $arr[1]");
	if($FT)
	{
		echo "$arr[1] 删除成功。</br>";
	}
}

//删除原来的jizhang_asset_paymethod表删除
mysqli_select_db($conn,'jizhang');
$rs=mysqli_query($conn,'show tables');
while($arr=mysqli_fetch_array($rs))
{
	$TF=strpos($arr[0],'jizhang_asset_');
	if($TF===0)
	{
		$FT=mysqli_query($conn,"drop table $arr[0]");
		if($FT)
		{
			echo "$arr[0] 删除成功。</br>";
		}
		else
		{
			echo mysqli_error($conn);
		}
	}
}
//创建存储过程和原来的jizhang_asset_paymethod表
echo "创建数据库jizhang的存储过程1 .....";
$system = PHP_OS;
if(strpos($system,'Linux') !== false)
{
$sqlproc =  "mysql "
			 ."--host=localhost "
			 ."--user=root "
			 ."--password=jensonztf "
			 ."--database=jizhang "
			 ."--execute=\"source /usr/local/apache/htdocs/account/proc_asset_insert.sql\"";	 
			 
}else if(strpos($system,'WINNT') !== false)
{
$sqlproc =  "E:\MyServer\MySQL\bin\mysql "
			 ."--host=localhost "
			 ."--user=root "
			 ."--password=jensonztf "
			 ."--database=jizhang "
			 ."--execute=\"source E:\\MyServer\\WWW\\account\\proc_asset_insert.sql\"";
}
echo shell_exec($sqlproc);
$sqlproc = "SELECT * FROM mysql.proc WHERE db='jizhang' and type='procedure' and name='proc_asset_insert';";
$query=mysqli_query($conn,$sqlproc);
if($query){
			echo "成功<br />";
}else{
	printf("Error: %s\n","请检查mysql.exe和proc_asset.sql路径！");
	exit();
}


echo "创建数据库jizhang的存储过程2 .....";
$system = PHP_OS;
if(strpos($system,'Linux') !== false)
{
$sqlproc =  "mysql "
			 ."--host=localhost "
			 ."--user=root "
			 ."--password=jensonztf "
			 ."--database=jizhang "
			 ."--execute=\"source /usr/local/apache/htdocs/account/proc_asset_edit.sql\"";	 
			 
}else if(strpos($system,'WINNT') !== false)
{
$sqlproc =  "E:\MyServer\MySQL\bin\mysql "
			 ."--host=localhost "
			 ."--user=root "
			 ."--password=jensonztf "
			 ."--database=jizhang "
			 ."--execute=\"source E:\\MyServer\\WWW\\account\\proc_asset_edit.sql\"";
}
echo shell_exec($sqlproc);
$sqlproc = "SELECT * FROM mysql.proc WHERE db='jizhang' and type='procedure' and name='proc_asset_edit';";
$query=mysqli_query($conn,$sqlproc);
if($query){
			echo "成功<br />";
}else{
	printf("Error: %s\n","请检查mysql.exe和proc_asset_edit.sql路径！");
	exit();
}

echo "创建数据库jizhang的存储过程3 .....";
$system = PHP_OS;
if(strpos($system,'Linux') !== false)
{
$sqlproc =  "mysql "
			 ."--host=localhost "
			 ."--user=root "
			 ."--password=jensonztf "
			 ."--database=jizhang "
			 ."--execute=\"source /usr/local/apache/htdocs/account/proc_asset_install.sql\"";	 
			 
}else if(strpos($system,'WINNT') !== false)
{
$sqlproc =  "E:\MyServer\MySQL\bin\mysql "
			 ."--host=localhost "
			 ."--user=root "
			 ."--password=jensonztf "
			 ."--database=jizhang "
			 ."--execute=\"source E:\\MyServer\\WWW\\account\\proc_asset_install.sql\"";
}
echo shell_exec($sqlproc);
$sqlproc = "SELECT * FROM mysql.proc WHERE db='jizhang' and type='procedure' and name='proc_asset_install';";
$query=mysqli_query($conn,$sqlproc);
if($query){
			echo "成功<br />";
}else{
	printf("Error: %s\n","请检查mysql.exe和proc_asset_edit.sql路径！");
	exit();
}

//调用proc_asset_install()创建proc_asset_paymethod表
echo "创建jizhang_asset_paymethod表 .....";
$sqlasset1="call proc_asset_install(1)";
$queryasset1=mysqli_query($conn,$sqlasset1);
$sqlasset2="call proc_asset_install(2)";
$queryasset2=mysqli_query($conn,$sqlasset2);
if($queryasset1&&$queryasset2){
			echo "成功<br />";
}else{
	echo mysqli_error($conn);
	printf("Error: %s\n","请检查mysql.exe和proc_asset_install.sql路径！");
	exit();
}

/*echo "<br />加入默认用户.....";
$sql="select * from jizhang_user where username='admin'";
	$query=mysqli_query($conn,$sql);
	$attitle=is_array($row=mysqli_fetch_array($query));
	if($attitle){
		echo "默认用户已存在！<br /><a href='denglu.php'>点这里立即登录</a>";
		exit();
	}else{
	$utime=strtotime("now");
	
$sql="insert into jizhang_user (username, password,email,utime) values ('admin', 'a4341a98cc97458bc1f817b4acd9ef6a','000000000@qq.com','$utime')";
$query=mysqli_query($conn,$sql);
if($query){
	echo "Ok了！<br />使用用户名：admin 密码：xpttcom 即可登录<br />";
}else{
	echo "失败<br /><font color='red'>安装OK了。</font></body></html>";
}

}*/
?>
<br /><a href="denglu.php">点这里立即登录</a>
</p>

</body>
</html>
