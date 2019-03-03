<?php
    include_once("shangmian.php");
?>

 <table align="left" width="100%" height="20" border="0" align="left" cellpadding="5" cellspacing="1" bgcolor="#B3B3B3" >
  <tr>
    <td align="left" bgcolor="#EBEBEB"><font id="tongji"></font></td>
  </tr>
</table>

<script language="javascript">
function linkokok(url){
  question = confirm("数据即将清除，确认吗？");
  if (question){
   window.location.href = url;
 }
}
</script>

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
                echo "<td align='left' bgcolor='#FFFFFF' data-title='操作'><a href=xiugai.php?id=".$row['acid'].">编辑</a> <a href=\"javascript:linkokok('shanchu.php?id=".$row['acid']."')\">删除</a></td>";
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
	echo '<a href="current.php?p=',$curPage-1,'">上一页</a>';
}
for($i=$firstPage;$i<=$endPage;$i++){
	if($i==$curPage){
		echo ' [',$i,']';
	}else{
		echo ' <a href="current.php?p=',$i,'">',$i,'</a>';
	}
}
if($endPage != $totalPage)
{
	echo '<a href="current.php?p=',$curPage+1,'">下一页</a>';
}


echo "</td></tr></table>";
?>
		    
<script language="javascript">
document.getElementById("tongji").innerHTML="<?='总共收入<font color=MediumSeaGreen> '.$income.'</font> 总共支出 <font color=red>'.$spending.'</font>'?>"
</script>


<?php
    include_once("xiamian.php");
?>
