<?php
    include_once("shangmian.php");
?>


<script  type="text/javascript">

/*function queryusingway(str)
{
	var xmlhttp = new XMLHttpRequest();
	if(str=="")
	{
		document.getElementById("txtHint").innerHTML="";
		return;
	}
	xmlhttp.onreadystatechange=function()
	{
		
		if(xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
			
		}
	}
	xmlhttp.open("POST","queryclass.php?u="+str,true);
	xmlhttp.send();
}*/

function queryusingway(str)
{
	var str0 = "u="
	var formData = str0.concat(str);
	$.ajax ({
	    url: "queryclass.php",
	    type:"post",
	    data:formData,
	    dataType:"html",
	    async:false,
	    success: function(data)
	    {
	    	document.getElementById("class").innerHTML=data;
	    	if(document.getElementById("classid").value=="neibuzz")
	    	{
	    		document.getElementById("paymethod").innerHTML=data;
	    	}else
	    	{
	    		var str1 = document.getElementById("hiddencode").innerHTML;
	    		document.getElementById("paymethod").innerHTML = str1;  
	    	}
	    }
	});
}

function querydetail(str)
{
	document.getElementById("pagenum").value = str;
	var formData = $("#form1").serialize();
	//alert(formData);

	$.ajax ({
	    url: "querydetail.php",
	    type:"post",
	    data:formData,
	    async:false,
	    success: function(data)
	    {
	    	document.getElementById("txtHint1").innerHTML=data;
	    	//alert(data);
	    }
	});

}


</script>

<table align="left" width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered'>
   <tr>
    <td bgcolor="#EBEBEB">总账目（本帐号）</td>
   </tr>
   <form id="form1">
   <tr>
    <td>
	<input type="button" name="Submit" id="submit" value="查账" onclick="querydetail(1)" />
	<div style="display: none;"><input name="pagenum" type="text" id="pagenum" value=<?php echo $PageNum?> size="8"/></div>
	</td>
	</tr>
	<tr>
	<td>
	<select name="classid" id="classid" onchange="queryusingway(this.value)">
	 <option value="quan">收&nbsp/支&nbsp/内部转账</option>
	 <option value="sr">收入</option>
	 <option value="zc">支出</option>
	 <option value="neibuzz">内部转账</option>
	</select>
	</td>
	</tr>
	<tr>
    <td>
    分类名称</br>(或转入户头)：
	<!--ajax传回的text将写在<select></select>之间-->
	<select name="class" id="class">	
		<option value="quan2">全部</option>
	</select>
	</td>
	</tr>
	<tr>
	<td>
	账户名称</br>(或转出户头)：
	<select name="paymethod" id="paymethod">
		<option value="quan1">全部</option>
		<?php
		$sql="select * from jizhang_account_class where classtype=3 and ufid='$_SESSION[uid]'";
		$query=mysqli_query($conn,$sql);
		while($acclass=mysqli_fetch_array($query)){
		echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
	}
	//echo "<option value='nbzz'>内部转账</option>";
	?>
	</select>
	</td>
	</tr>
<!--
	一个隐藏的div，保存了显示支付方式的html代码，
	以供queryusingway()调用
-->
	<div style="display:none;" name="hiddencode" id="hiddencode">
	<option value="quan1">全部</option>
	<?php
	$sql="select * from jizhang_account_class where classtype=3 and ufid='$_SESSION[uid]'";
	$query=mysqli_query($conn,$sql);
	while($acclass=mysqli_fetch_array($query)){
	echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
	}
	//echo "<option value='nbzz'>内部转账</option>";
	?>
	</div>
	<tr>
	<td>
	从 <input type="date" name="time1" id="time1" style="height:26px;width:160px;"/> <br/>
	到 <input type="date" name="time2" id="time2" style="height:26px;width:160px;"/> <br/>	
	<p style="color:red">默认查询30天，最大间隔2个月</p>
	</td>
	</tr>
	<tr>
	<td>
	备注：<br/>
	<input type="text" name="beizhu" id="beizhu" />
	</td>
   </tr>
   </form>
</table>
<div id='no-more-tables'>
<table align="left" border="0" cellpadding="5" cellspacing="1" bgcolor='#B3B3B3' class='table table-striped table-bordered' id="txtHint1">
   <tr>
    <td bgcolor="#EBEBEB">查询结果：</td>
   </tr>
</table>
</div>


<?php
    include_once("xiamian.php");
?>