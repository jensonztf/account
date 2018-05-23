<?php
    include_once("shangmian.php");
?>


<script  type="text/javascript">
function querywaterbill(str)
{
	document.getElementById("pagenum").value = str;
	var formData = $("#form1").serialize();
	//alert(formData);

	$.ajax ({
	    url: "querywaterbill.php",
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
    <td bgcolor="#EBEBEB">流水</td>
   </tr>
   <form id="form1">
   <tr>
    <td>
	<input type="button" name="Submit" id="submit" value="查流水" onclick="querywaterbill(1)" />
	<div style="display: none;"><input name="pagenum" type="text" id="pagenum" value=<?php echo $PageNum?> size="8"/></div>
	</td>
	</tr>
	<tr>
	<td>
	账户名称</br>(或转出户头)：
	<select name="paymethod" id="paymethod">
	<!--  <option value="quan1">全部</option>  -->
	<?php
	$sql="select * from jizhang_account_class where classtype=3 and ufid='$_SESSION[uid]'";
	$query=mysqli_query($conn,$sql);
	while($acclass=mysqli_fetch_array($query)){
	echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
	}
	?>
	</select>
	</td>
	</tr>
	<tr>
	<td>
	从 <input type="date" name="time1" id="time1" style="height:26px;width:160px;"/> <br/>
	到 <input type="date" name="time2" id="time2" style="height:26px;width:160px;"/> <br/>	
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