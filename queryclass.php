<?php
session_start();
include("config.php");
if(isset($_POST["u"]) && $_POST["u"])
{
	$str = $_POST["u"];
	$sql = "";
	if($str=="sr")
	{
		$sql="select * from jizhang_account_class where classtype=1 and ufid='$_SESSION[uid]'";
	}else if($str=="zc")
	{
		$sql="select * from jizhang_account_class where classtype=2 and ufid='$_SESSION[uid]'";
	}else if($str=="neibuzz")
	{
		$sql="select * from jizhang_account_class where classtype=4 and ufid='$_SESSION[uid]'";
	}else if($str=="quan")
	{
		$sql = "";
	}
	if($sql<>"")
	{
		$query=mysqli_query($conn,$sql);
		if(!$query){printf("Error: %s\n", mysqli_error($conn));exit();}
		echo "<option value='quan2'>全部</option>";
		while($acclass=mysqli_fetch_array($query)){
			echo "<option value='$acclass[classid]'>$acclass[classname]</option>";
		}
		echo "</select>";
	}else
	{
		echo "<option value='quan2'>全部</option>";
		echo "</select>";
	}
}
?>