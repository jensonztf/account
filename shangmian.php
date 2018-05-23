<?php
session_start();
date_default_timezone_set("Asia/Shanghai"); 
?>
<?php
  if(isset($_GET['tj'])&&($_GET['tj'] == 'logout')){
  session_start(); //开启session
  session_destroy();  //注销session
  header("location:index.php"); //跳转到首页
  }
?>


<!DOCTYPE HTML>
<html>
	<head>  
	<meta charset="utf-8">

		<!-- 包含头部信息用于适应不同设备 -->
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- 包含 bootstrap 样式表 -->
		<link rel="stylesheet" href="./src/bootstrap.min.css">
		<!-- 包含 no-more-tables 样式表 ,手机显示自适应-->
		<link rel="stylesheet" href="./src/no-more-tables.css">
		<!-- 包含侧滑边栏样式表-->
		<link rel="stylesheet" href="./src/bootstrap-off-canvas-nav.css">
	<title>php mysql 简单记账xptt程序</title>
	</head>
	
	<body class="off-canvas-nav-left" style="padding-top:70px;max-width:1200px;margin-left:auto;margin-right:auto;">
	
		<?php
		include("config.php");
		if(isset($_SESSION['uid'])&&isset($_SESSION['user_shell'])){
		$arr=user_shell($conn,$_SESSION['uid'],$_SESSION['user_shell']);//对权限进行判断
		}
		?>
	
	    <!-- <div class="table-responsive">  -->
		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container"> 
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						 <span class="sr-only">导航</span>
						 <span class="icon-bar"></span>
						 <span class="icon-bar"></span>
						 <span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="tianjia.php">记一笔</a>
				</div>
				
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active"><a href="current.php">当前</a></li>
						<li><a href="fenlei.php">分类编辑</a></li>
						<li><a href="xiugai.php">账目修改</a></li>
						<li><a href="tongji.php">当月统计</a></li>
						<li><a href="liushui.php">流水</a></li>
						<li><a href="zongzhang.php">查询</a></li>
						<li><a href="genExcel.php">导出导入</a></li>
						<li><a href="zhanghao.php"><?php echo"账号";echo $arr['username'];?></a></li>
						<li><a href="index.php?tj=logout">退出</a></li>
					</ul>
				</div>
			</div>
		</nav>
		
		
		
