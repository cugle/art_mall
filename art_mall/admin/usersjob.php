<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

 

if( $action == 'del'){  
	$condition = "id='".$id."' AND domain_id='".$Aconf['domain_id']."'";  
	$oPub->delete($pre.'users_job',$condition);	  
}


 
//page
$strWhere = ' WHERE domain_id="'.$Aconf['domain_id'].'"';
$row = $oPub->getRow('SELECT count( * ) AS count FROM '.$pre.'users_job'.$strWhere); 
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 40;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$AnormAll = $oPub->select('SELECT * FROM '.$pre.'users_job WHERE domain_id="'.$Aconf['domain_id'].'" ORDER BY id desc limit '.$strOffSet); 
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;   
		$tmpstr = ($n % 2 == 0)?"even":"odd";
		$StrtypeAll .= '<TR class='.$tmpstr.'>';
		$Rusers = '';
		$Rusers = $oPub->getRow('SELECT avatar,user_name FROM '.$pre.'users where id ="'.$value['users_id'].'" AND domain_id="'.$Aconf['domain_id'].'" limit 1');   
		$StrtypeAll .= '<TD align=left>'.$Rusers["user_name"] .'</TD>';
		$tmp = ($Rusers["avatar"] > 0 )?'<IMG SRC="../data/userimg/avatar_small/'.$Rusers["avatar"].'_small.jpg"  BORDER="0">':'';
		$StrtypeAll .= '<TD align=left>'.$tmp.'</TD>'; 
		$StrtypeAll .= '<TD align=left>'.$value["xingming"].'</TD>'; 
		$StrtypeAll .= '<TD align=left>'.$value["sex"].'</TD>'; 
		$StrtypeAll .= '<TD align=left>'.$value["shengri"].'</TD>'; 
		$StrtypeAll .= '<TD align=left>'.$value["email"].'</TD>'; 
		$StrtypeAll .= '<TD align=left>'.$value["tel"].'</TD>'; 
		$StrtypeAll .= '<TD align=left>'.$value["addres"].'</TD>'; 
		$StrtypeAll .= '<TD align=left>'.$value["yingpingzw"].'</TD>';  
		$StrtypeAll .= '<TD align=left>'.date("Y-m-d",$value["dateadd"]).'</TD>'; 
		$StrtypeAll .= '<TD align=left><a   style="cursor:pointer" onmousedown="show_job(\'show\',\''.$value["id"].'\')"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查看"></a> '; 
		$StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=del" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
		$StrtypeAll .= '</TR>';  	   
}
 

?>

<?php
   include_once( "header.php");
	if ($strMessage != '')
	{
		 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
	}
?>
<DIV class=content>
 
	<TABLE width="100%" border=0>
	  <TR class=bg5>
 
			<TD align=left>帐号</TD>
			<TD align=left>头像</TD>
			<TD align=left>姓名</TD> 
			<TD align=left>性别</TD>
			<TD align=left>生日</TD>
			<TD align=left>邮箱</TD> 
			<TD align=left>电话</TD>
			<TD align=left>地址</TD>
			<TD align=left>应聘职位</TD>
			<TD align=left>申请时间</TD>
			<TD align=left>操作</TD>
	  </TR>
	  <?php echo $StrtypeAll?> 
	</TABLE> 

	<TABLE width="100%" border=0>
	  <TR class=bg5>
		<TD align=right>
		<span style="float: right">
		<?php echo $showpage = $page->ShowLink();?>
		</span>
		</TD>
	  </TR>
	</TABLE> 
</DIV>
<style>
 
		.jobx{clear:left;width:700px;margin: 5px;}
		.jobx .titlex{margin: 0 auto;font-size:20px;font-weight: bold;  padding:5px;}
		.jobx .descsx{letter-spacing:2px; line-height:20px;text-align:left;} 

		.jobx table,td,tr,th{margin: 0 auto;font-size:12px;} 
		#wdt{margin-top:2px;}
		.oddhr{ background-color:#FFFFFF;padding: 2px}
		.evenhr{ background-color:#F0F8FF;padding: 2px}
		.httd{ font-weight:bold;text-align: left;background-color: #EBEBEB;padding: 5px;}
		.hltd{ font-weight:bold;text-align: right;width:70px}
		.hrtd{ font-weight:lighter;text-align: left;width:120px }
		input{width:80px}
		TEXTAREA{width:690px;height:40px}
		.input2{width:120px}
		.input3{width:130px}
 
		.hltd1{ font-weight:bold;text-align: right;width:120px}
		.hrtd1{ font-weight:lighter;text-align: left;width:130px } 

		.hltd3{ font-weight:bold;text-align: center;width:120px}
 

	#gouwuche{display:block; bottom:10px; right:105px; position:fixed;width:710px;height:480px;float:right;margin:0.1em;padding: 5px;background-color: #F2F2F2;border-style:solid; border-width:1px; border-color:#F00;overflow:auto;}
</style>
 <div id="gouwuche" style="display:none">
dfgdfgdf
</div>
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT> 
<script type="text/javascript" language="JavaScript"> 
function show_job(op,a)
{
	obj = "gouwuche"; 
	if(op == 'hidden'){
		gouwuche.style.display='none';
	}else{
		gouwuche.style.display='';
		var strTemp = "ajax_users_job.php?id=" + a +"&op=" + op;  
		//alert(strTemp);
		send_request(strTemp);	 
	} 
}  
 
</script>
<?php
include_once( "footer.php");
?>