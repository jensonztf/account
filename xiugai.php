<?php
    include_once("shangmian.php");
	$spending=0;
	$income=0;
?>

<?php
 if (isset($_GET['ok'])&&$_GET['ok']) {
               
    //针对$ok被激活后的处理：
    $shij=strtotime("$_GET[shijian]");
    $acid=$_GET['id'];
    $money0=$_GET['jine'];
	
	//调用proc_asset_edit，更新分类记录表
	$sql="select * from jizhang_account where acid='$_GET[id]' and jiid='$_SESSION[uid]'";
	$result = mysqli_query($conn,$sql);
	while($query = mysqli_fetch_array($result)){
		$zhifu0 = $query['zhifu'];
		$paymethod = $query['acpaymethod'];
	}
	//如果编辑的记录属于收入/支出类别
	if($zhifu0 == 1 || $zhifu0 == 2)
	{
		// 2 代表指定使用proc_asset_edit中的编辑功能
		$sql="call proc_asset_edit("
			.$shij.","
			.$money0.","
			.$zhifu0.","
			.$_SESSION['uid'].","
			.$paymethod.","
			.$acid.","
			."2)";
		$result = mysqli_query($conn,$sql);	
		if(!$result){
			printf("Error: %s\n", mysqli_error($conn));
			echo $sql;
			exit();
		}else{			
	        $sql = "update jizhang_account set acmoney='".$_GET['jine']."',acremark='".$_GET['beizhu']."',actime='".$shij."' where acid='".$_GET['id']."' and jiid='".$_SESSION['uid']."'";
	        $result = mysqli_query($conn,$sql);
	        if ($result)
	        echo("<script type='text/javascript'>alert('修改成功！');history.go(-2);</script>");
	        else
	        echo("<script type='text/javascript'>alert('修改失败！');history.go(-2);</script>");
		}  
	}
	//如果编辑的记录属于内部转账类别
	if($zhifu0 == 4)
	{
		// 2 代表指定使用proc_asset_edit中的编辑功能
		// 要同时编辑转出账户和被转入账户的“jizhang_asset_”表
		// 编辑转出账户的“jizhang_asset_”表
		$sql1="call proc_asset_edit("
			.$shij.","
			.$money0.","
			.$zhifu0.","
			.$_SESSION['uid'].","
			.$paymethod.","
			.$acid.","
			."2)";
		// 编辑被转入账户的“jizhang_asset_”表
		$money1 = -1 * $money0;
		$sql2="call proc_asset_edit("
			.$shij.","
			.$money1.","
			.$zhifu0.","
			.$_SESSION['uid'].","
			.$paymethod.","
			.$acid.","
			."2)";

		$result1 = mysqli_query($conn,$sql1);	
		$result2 = mysqli_query($conn,$sql2);	
		if(!$result1 || !$result2){
			printf("Error: %s\n", mysqli_error($conn));
			echo $sql;
			exit();
		}else{			
	        $sql = "update jizhang_account set acmoney='".$_GET['jine']."',acremark='".$_GET['beizhu']."',actime='".$shij."' where acid='".$_GET['id']."' and jiid='".$_SESSION['uid']."'";
	        $result = mysqli_query($conn,$sql);
	        if ($result)
	        echo("<script type='text/javascript'>alert('修改成功！');history.go(-2);</script>");
	        else
	        echo("<script type='text/javascript'>alert('修改失败！');history.go(-2);</script>");
		}  
	}        








}
else{
	if (isset($_GET['id'])&&$_GET['id']) {
		$sql = "select * from jizhang_account where acid='".$_GET['id']."' and jiid='".$_SESSION['uid']."'";
        $result = mysqli_query($conn,$sql);
        $row = mysqli_fetch_array($result);
                
        $sql2="select * from jizhang_account_class where classid= '".$row['acclassid']."' and ufid='".$_SESSION['uid']."'";
		$classquery = mysqli_query($conn,$sql2);
		$classinfo = mysqli_fetch_array($classquery);
				
				
		echo "<table align='left' width='100%' border='0' cellpadding='5' cellspacing='1' bgcolor='#B3B3B3' class='table table-striped table-bordered'>
		<tr>
        <td bgcolor='#EBEBEB'>　账目修改</td>
		</tr>
		<tr>
        <td bgcolor='#FFFFFF'>
		<form method=get action=''>
        <INPUT TYPE='hidden' name='id' value=".$row['acid'].">金额：<input type=text name='jine' value=".$row['acmoney']."><br /><br />
        账目分类：".$classinfo['classname']."<br /><br />
        收入/支出：";
        if($classinfo['classtype']==1){
             echo '收入';
        $income=$income+$row['acmoney'];
        }else{
            echo '支出';
         $spending=$spending+$row['acmoney'];
        }

        echo "<br /><br />
        时间：<input rows='1' cols='20' name='shijian' value='".date('Y-m-d H:i',$row['actime'])."' readonly='readonly'> <br /><br />
        备注：<input type=text name='beizhu' value=".$row['acremark']."><br /><br />
　      <input type=submit name=ok value='提交' class='btn btn-default'>
        </form></td>
        </tr>
        </table>";
				
		}

    }
?>



<?php
    include_once("xiamian.php");
?>