<?php
    include_once("shangmian.php");
?>

<table align="center" width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>
        <tr>
          <td bgcolor="#EBEBEB">　账号管理</td>
        </tr>
        <tr>
          <td bgcolor="#FFFFFF">
          
           <form action="" method="post" name="submitxiugai">

        <?php $sqlzhanghu = "SELECT * FROM jizhang_user where uid='$_SESSION[uid]'";
        $result2 = mysqli_query($conn,$sqlzhanghu);
$row = mysqli_fetch_array($result2);
         ?>
		 
	<script language="javascript">
<!--
 function linkok(url){
  question = confirm("数据即将清除，确认吗？");
  if (question){
   window.location.href = url;
  }
 }
//-->
</script>
		 
        用户名：<?echo $row[username]; ?> <a href="javascript:linkok('shanchu.php?uid=$_SESSION[uid]')">清除全部数据</a>（操作前请备份导出）<br /><br />
注册时间：<?echo date("Y-m-d H:i",$row[utime]); ?><br /><br />
        电子邮箱 <input for="Email" name="email" id="email" value="<?php echo $row['email']; ?>"> <br /><br />
        旧密码：<input type="password" name="jiupassword" id="jiupassword" size="18" maxlength="15" /><br /><br />
        新密码：<input type="password" name="password" id="password" size="18" maxlength="15" /> *(最多15个字符) <br /><br />
        <input name="Submitxiugai" type="submit" value="提交" class="btn btn-default" /> 
        <?php 
        if(isset($_POST['Submitxiugai'])&&$_POST['Submitxiugai']){   
         $jiumima=md5($_POST[jiupassword]);
 
      if($jiumima==$row[password]){
        if($_POST[password]<>""){
          $umima=md5($_POST[password]);
        }else{
          $umima=$jiumima;
        }
      $sql="update jizhang_user set password='$umima',email='$_POST[email]' where uid='$_SESSION[uid]'";
      $query=mysqli_query($conn,$sql);
      if($query){
        echo "<font color='green'>OK，修改成功！需要重新登陆，马上为你跳转。。</font><meta http-equiv=refresh content='2; url=index.php'>";
      }else{
        echo "<font color='red'>出错啦，写入数据库时出错！</font>";
    
        }
    }else{
            echo "<font color='red'>密码不能为空或旧密码错误！</font>";
      exit();
    }
    }
?>

   
    </form>
          
          </td>
        </tr>
      </table>

<?php
    include_once("xiamian.php");
?>