<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre.'users'; //udetail
//post
if( $action == 'add' || $_POST['action'] == 'edit' )
{ 
	if($_POST['action'] == 'edit' && $id > 0 && !empty($password)) {
		  $password = md5($password);
          $Afields=array('password'=>$password);
	      $condition = "id = ".$id." AND domain_id=".$Aconf['domain_id'];
	      $oPub->update($pre.'users',$Afields,$condition); 
	}
	unset($Anorm);unset($_POST);
} 
 

if( $action == 'del'){  
}


//users_id bankname remmoney payname paynums dateadd checked 
//page
$strWhere = ' WHERE domain_id="'.$Aconf['domain_id'].'"';
$row = $oPub->getRow('SELECT count( * ) AS count,sum(remmoney) as remmoney FROM '.$pre.'udetail '.$strWhere); 
$count = $row['count'];
$T_remmoney = $row['remmoney'];
unset($row);
$page = new ShowPage;
$page->PageSize = 40;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$AnormAll = $oPub->select('SELECT * FROM '.$pre.'udetail '.$strWhere.' ORDER BY id desc limit '.$strOffSet); 
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;  

	   $tmpstr = ($n % 2 == 0)?"even_m":"odd_m";
       $StrtypeAll .= '<TR class="'.$tmpstr.'" onMouseOver="this.style.backgroundColor=\'#FFFFFF\';" onMouseOut="this.style.backgroundColor=\'#e6e6e6\'; ">';
 
		//邮箱验证 短信验证状态
		$Urow = $oPub->getRow('SELECT user_name,money FROM '.$pre.'users WHERE  id="'.$value['users_id'].'"  limit 1'); 
 
	   $StrtypeAll .= '<TD align=left> <A>'.$Urow["user_name"].'</A> </TD>'; 
	   $StrtypeAll .= '<TD align=left>'.$Urow["money"].'</TD>';  
		if($value['type'] < 1)
		{
			$StrtypeAll .= '<TD align=left><span style="color:#F00">'.$value["remmoney"].'</span></TD>';
		}else
		{
			$StrtypeAll .= '<TD align=left><span style="color:#00F">'.$value["remmoney"].'</span></TD>';
		}
	   $StrtypeAll .= '<TD align=left>'.$value["bankname"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["payname"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["paynums"].'</TD>'; 
	   $StrtypeAll .= '<TD align=left>'.date("Y-m-d H:i",$value["dateadd"]).'</TD>'; 
		if($value['type'] < 1)
		{  
			 $StrtypeAll .= '<TD align=left>'.($value["checked"] < 1?'未确认':'<span style="color:#F00">已确认</span>').'</TD>';
		}else
		{
			 $StrtypeAll .= '<TD align=left><span style="color:#00F">订单扣款</span></TD>';
		}
	  
		if($value["checked"] > 0)
		{
			$checkdesc = sub_str($value["checkdesc"],4);
			$checktxt = $value["checkdesc"];
		}else
		{
			$checkdesc = $checktxt = '';
			if($value['type'] < 1)
			{  
				$checkdesc = '<A onmousedown="usermoney(\'check\','.$value["id"].')" style="cursor:pointer;color: #3366FF">还没有操作</A>';
			}
		} 	
       $StrtypeAll .= '<TD align=left><div id="check_'.$value["id"].'" title="'.$checktxt.'">'.$checkdesc.'</div></TD>'; 
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
<style>
.even_m {
	PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px; BACKGROUND-COLOR: #dee3e7
}
.odd_m {
	PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; PADDING-TOP: 5px; BACKGROUND-COLOR: #e6e6e6
}
</style>
<DIV class=content>
	<?php if($Anorm['id'] > 0 ){ ?>
		<form name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>" style="margin: 0">
			<TABLE width="100%" border=0>
			  <TR> 
				<TD align="left"> 
					<span style="font-weight: bold">帐号:</span>	
					<?php echo ($Anorm['id'])?$Anorm['user_name']:'http://'?> 
					<span style="font-weight: bold">密码:</span>		
					<input name="password" type="text" value="" size="6" />  
					<input type="hidden" name="action" value="<?php echo ($Anorm['id'])?'edit':'add'?>" /> 
					<input type="submit" name="Submit" value="<?php echo ($Anorm['id'])?' 密码修改 ':'' ?>" style="background-color: #FFCC66;"/>
					<input type="hidden" name="id" value="<?php echo ($Anorm['id'])?$Anorm['id']:'0'?>" />  
				</TD> 
			  </TR>
			</TABLE>  
		</form> 
	<?php } ?>
	<TABLE width="100%" border=0>
	  <TR class=bg5> 
			<TD align=left>帐号</TD>
			<TD align=left>余额</TD>
			<TD align=left>充值金额</TD>
			<TD align=left>银行</TD>
			<TD align=left>姓名</TD>
			<TD align=left>交易序号或订单号</TD> 
			<TD align=left>记录日期</TD>
			<TD align=left>状态</TD> 
			<TD align=left>操作记录</TD>
	  </TR>
	  <?php echo $StrtypeAll?> 
	  <TR class="odd"> 
			<TD align=left> </TD>
			<TD align=middle>合计</TD>
			<TD align=left colspan="7"><?php echo $T_remmoney;?></TD> 
	  </TR>
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
	#userlist{display:block; bottom:90px; right:3px; position:fixed;width:350px;float:right;margin:0.1em;padding: 5px;background-color: #F2F2F2;border-style:solid; border-width:1px; border-color:#F00;}
</style>
<div id="userlist" style="display:none">
</div>
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<script type="text/javascript" language="JavaScript"> 
  function usermoney(op,id)
  {
     obj = op + "_" + id;
     var strTemp = "ajax_usermoney.php?op=" + op + "&id=" + id;
	 //alert(strTemp);
	 send_request(strTemp);
  }

</script> 
<?php
include_once( "footer.php");
?>