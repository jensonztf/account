<?php
    include_once("shangmian.php");
?>
<?php
    if (isset($_GET['id'])&&$_GET['id'])
    {
		//调用proc_asset_edit，更新分类记录表
		$sql="select * from jizhang_account where acid='$_GET[id]' and jiid='$_SESSION[uid]'";
		$result = mysqli_query($conn,$sql);
		while($query = mysqli_fetch_array($result)){
			$acid = $query['acid'];
			$classid = $query['acclassid'];
			$time0 = $query['actime'];
			$money0 = $query['acmoney'];
			$zhifu0 = $query['zhifu'];
			$ufid0 = $query['jiid'];
			$paymethod = $query['acpaymethod'];
		}
		//如果删除的记录属于收入/支出类别
		if($zhifu0 == 1 || $zhifu0 == 2)
		{
			// 1 代表指定使用proc_asset_edit中的删除功能
			$sql="call proc_asset_edit("
			    .$time0.","
				.$money0.","
				.$zhifu0.","
				.$ufid0.","
				.$paymethod.","
				.$acid.","
				."1)";
			echo $sql;
			$result = mysqli_query($conn,$sql);	
			if(!$result){
				printf("Error: %s\n", mysqli_error($conn));
				exit();
			}else{
				$sql="delete from jizhang_account where acid='$_GET[id]' and jiid='$_SESSION[uid]'";
				$result = mysqli_query($conn,$sql);
	            if ($result)
	            echo("<script type='text/javascript'>alert('已删除一条记录！');location.href=document.referrer;</script>");
	            else
	            echo("<script type='text/javascript'>alert('删除出错！');location.href=document.referrer;</script>"); 
			}

		}
		//如果删除的记录属于内部转账类别
		if($zhifu0 == 4)
		{
			// 1 代表指定使用proc_asset_edit中的删除功能
			// 要同时删除转出账户和被转入账户的“jizhang_asset_”表
			// 删除转出账户的“jizhang_asset_”表
			$sql1="call proc_asset_edit("
			    .$time0.","
				.$money0.","
				.$zhifu0.","
				.$ufid0.","
				.$paymethod.","
				.$acid.","
				."1)";
			echo $sql1;
			// 删除被转入账户的“jizhang_asset_”表
			$money1 = -1 * $money0;
			$sql2="call proc_asset_edit("
			    .$time0.","
				.$money1.","
				.$zhifu0.","
				.$ufid0.","
				.$classid.","
				.$acid.","
				."1)";
			$result1 = mysqli_query($conn,$sql1);	
			$result2 = mysqli_query($conn,$sql2);	
			if(!$result1 || !$result2){
				printf("Error: %s\n", mysqli_error($conn));
				exit();
			}else{
				$sql="delete from jizhang_account where acid='$_GET[id]' and jiid='$_SESSION[uid]'";
				$result = mysqli_query($conn,$sql);
	            if ($result)
	            echo("<script type='text/javascript'>alert('已删除一条记录！');location.href=document.referrer;</script>");
	            else
	            echo("<script type='text/javascript'>alert('删除出错！');location.href=document.referrer;</script>"); 
			}

		}
    }

?>

<?php
if (isset($_REQUEST['shanchu'])&&$_REQUEST['shanchu'] ){
 if(isset($_POST['del_id'])&&$_POST['del_id']!=""){
             $del_id= implode(",",$_POST['del_id']); 
             mysqli_query($conn,"delete from jizhang_account where jiid='$_SESSION[uid]' and acid in ($del_id)"); 
             echo("<script type='text/javascript'>alert('删除成功！');location.href=document.referrer;</script>"); 
      }else{ 
             echo("<script type='text/javascript'>alert('请先选择项目！');location.href=document.referrer;</script>"); 
      } 
	  } 
?>

<?php
if (isset($_REQUEST['go'])&&$_REQUEST['go'] ){
	echo "<meta http-equiv=refresh content='0; url=xiugai.php?p=$_POST[zhuan]'>";
		  } 
?>
<br /><br />
<?php
    include_once("xiamian.php");
?>
