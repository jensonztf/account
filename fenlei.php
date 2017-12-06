<?php
    include_once("shangmian.php");
	$status_text = '';
?>

<script language="JavaScript"> 
function checkpost() 
{ 
   if(myform.classname.value==""){alert("请输入分类名称");
	window.location='tianjia.php';
    return false; 
   }  
 }
</script>
<?php
if(isset($_GET["Submit"])&&$_GET["Submit"]){
	//先对内部转账的分类名称格式进行判断，错误的进行提示
	if ($_GET['classtype']==4) {
		$classname = $_GET['classname'];
		$pos = strpos($classname,"[内部]");
		if($pos===false)
		{
			echo("<script type='text/javascript'>alert('内部转账的分类名称格式错误！');</script>");
		}
		else
		{
			$classname = substr($classname,0,$pos);
			$sql="select * from jizhang_account_class where classtype=3 and ufid='$_SESSION[uid]'";
			$query=mysqli_query($conn,$sql);
			while($row=mysqli_fetch_array($query))
			{
				$done =	false;
				if($row['classname']==$classname)
				{
					$sql="select * from jizhang_account_class where classname='$_GET[classname]' and ufid='$_SESSION[uid]'";
					$query=mysqli_query($conn,$sql);
					$attitle=is_array($row=mysqli_fetch_array($query));
					if($attitle)
					{
						echo("<script type='text/javascript'>alert('此分类名称已存在！');</script>");
						$done = true;
						break;
					}
					else
					{
						$sql="insert into jizhang_account_class (classname, classtype,ufid) values ('$_GET[classname]', '$_GET[classtype]',$_SESSION[uid])";
						$query=mysqli_query($conn,$sql);
						if($query){
							$status_text="<font color=#00CC00>添加成功！</font>";
							echo "<meta http-equiv=refresh content='0; url=fenlei.php'>";
						}else{
							$status_text="<font color=#FF0000>添加失败,写入数据库时发生错误！</font>";
							echo "<meta http-equiv=refresh content='0; url=fenlei.php'>";
						}
						$done = true;
						break;
					}
					
				}

			}
			if($done == false)
			{
				echo("<script type='text/javascript'>alert('账户不存在！');</script>");
			}
		
		}
	}
	else if($_GET['classtype']!=4){
		//其他类别名称不允许出现[内部]
		$classname = $_GET['classname'];
		$pos = strpos($classname,"[内部]");
		if($pos!==false)
		{
			echo("<script type='text/javascript'>alert('非内部转账！名称错误！');</script>");
		}
		else
		{
			$sql="select * from jizhang_account_class where classname='$_GET[classname]' and ufid='$_SESSION[uid]'";
			$query=mysqli_query($conn,$sql);
			$attitle=is_array($row=mysqli_fetch_array($query));
			if($attitle){
				$status_text="此分类名称已存在！";
			}else{
				$sql="insert into jizhang_account_class (classname, classtype,ufid) values ('$_GET[classname]', '$_GET[classtype]',$_SESSION[uid])";
				$query=mysqli_query($conn,$sql);
				if($query){
					$status_text="<font color=#00CC00>添加成功！</font>";
					echo "<meta http-equiv=refresh content='0; url=fenlei.php'>";
				}else{
					$status_text="<font color=#FF0000>添加失败,写入数据库时发生错误！</font>";
					echo "<meta http-equiv=refresh content='0; url=fenlei.php'>";
				}
			}
		}
	}
}
?>


<table align="left" width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>
        <tr>
          <td bgcolor="#EBEBEB">　新建分类</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">
<form id="myform" name="form1" method="get" onsubmit="return checkpost();">
            分类名称：<input name="classname" type="text" id="classname" />
            <select name="classtype" id="classtype"  style="height:26px;">
              <option value="1">收入</option>
              <option value="2">支出</option>
			  <option value="3">账户</option>
			  <option value="4">内部转账</option>
            </select><br /><br />



            <input type="submit" name="Submit" value="新建" class="btn btn-default" />
			<br /><br />
            <span style="color:red">注意：内部转账的分类名称格式为“账户”+“[内部]”，<br /><br />如：支付宝[内部]。</span>
            <?php echo $status_text;?>
          </form></td>
        </tr>
      </table>
      
<table align="left" width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>
        <tr>
          <td bgcolor="#EBEBEB">　分类管理</td>
        </tr>
      </table>
 
	<table width="100%" border="0" align="left" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>

                <tr>
              <th width="35%" align="left" bgcolor="#EBEBEB">类别名称</th>
              <th width="35%" align="left" bgcolor="#EBEBEB"><font color='MediumSeaGreen'>收入</font></th>
              <th width="30%" align="left" bgcolor="#EBEBEB">操作</th>
            </tr>
			<?php 
			$sql="select * from jizhang_account_class where ufid='$_SESSION[uid]' and classtype='1'";
			$query=mysqli_query($conn,$sql);
			while($row = mysqli_fetch_array($query)){
			  echo "<tr><td align='left' bgcolor='#FFFFFF'><font color='MediumSeaGreen'>".$row['classname']."</font></td>";
			  if($row['classtype']==1)
			  	echo "<td align='left' bgcolor='#FFFFFF'><font color='MediumSeaGreen'>收入</font></td>";
			  echo "<td align='left' bgcolor='#FFFFFF'><a href='xiugaifenlei.php?type=1&classid=".$row['classid']."'>修改</a> <a href='xiugaifenlei.php?type=2&classid=".$row['classid']."'>转移</a> <a href='xiugaifenlei.php?type=3&classid=".$row['classid']."'>删除</a></td>";
			 }
			 echo "</tr>";
			?>
          </table>
              
          <table width="100%" border="0" align="left" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>
                <tr>
              <th width="35%" align="left" bgcolor="#EBEBEB">类别名称</th>
              <th width="35%" align="left" bgcolor="#EBEBEB"><font color='red'>支出</font></th>
              <th width="30%" align="left" bgcolor="#EBEBEB">操作</th>
            </tr>
			<?php 
			$sql="select * from jizhang_account_class where ufid='$_SESSION[uid]' and classtype='2'";
			$query=mysqli_query($conn,$sql);
			while($row = mysqli_fetch_array($query)){
			  echo "<tr><td align='left' bgcolor='#FFFFFF'><font color='red'>".$row['classname']."</font></td>";
			  if($row['classtype']==2){
			  	echo "<td align='left' bgcolor='#FFFFFF'><font color='red'>支出</font></td>";			 
			 }
 echo "<td align='left' bgcolor='#FFFFFF'><a href='xiugaifenlei.php?type=1&classid=".$row['classid']."'>修改</a> <a href='xiugaifenlei.php?type=2&classid=".$row['classid']."'>转移</a> <a href='xiugaifenlei.php?type=3&classid=".$row['classid']."'>删除</a></td>";
			 echo "</tr>";
 }
			?>
          </table>
		  
  <table width="100%" border="0" align="left" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>

                <tr>
              <th width="35%" align="left" bgcolor="#EBEBEB">类别名称</th>
              <th width="35%" align="left" bgcolor="#EBEBEB"><font color='Indigo'>账户</font></th>
              <th width="30%" align="left" bgcolor="#EBEBEB">操作</th>
            </tr>
			<?php 
			$sql="select * from jizhang_account_class where ufid='$_SESSION[uid]' and classtype='3'";
			$query=mysqli_query($conn,$sql);
			while($row = mysqli_fetch_array($query)){
			  echo "<tr><td align='left' bgcolor='#FFFFFF'><font color='Indigo'>".$row['classname']."</font></td>";
            if($row['classtype']==3)
			  	echo "<td align='left' bgcolor='#FFFFFF'><font color='Indigo'>账户</font></td>";
			  echo "<td align='left' bgcolor='#FFFFFF'><a href='xiugaifenlei.php?type=1&classid=".$row['classid']."'>修改</a> ";
			 }
			 echo "</tr>";
			?>
          </table>
		    <table width="100%" border="0" align="left" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>

                <tr>
              <th width="35%" align="left" bgcolor="#EBEBEB">类别名称</th>
              <th width="35%" align="left" bgcolor="#EBEBEB"><font color='DeepPink'>内部转账及还款</font></th>
              <th width="30%" align="left" bgcolor="#EBEBEB">操作</th>
            </tr>
			<?php 
			$sql="select * from jizhang_account_class where ufid='$_SESSION[uid]' and classtype='4'";
			$query=mysqli_query($conn,$sql);
			while($row = mysqli_fetch_array($query)){
			  echo "<tr><td align='left' bgcolor='#FFFFFF'><font color='DeepPink'>".$row['classname']."</font></td>";
              if($row['classtype']==4)
				echo "<td align='left' bgcolor='#FFFFFF'><font color='DeepPink'>内部转账及还款</font></td>";
			  echo "<td align='left' bgcolor='#FFFFFF'><a href='xiugaifenlei.php?type=1&classid=".$row['classid']."'>修改</a>";
			 }
			 echo "</tr>";
			?>
          </table>

<?php
    include_once("xiamian.php");
?>