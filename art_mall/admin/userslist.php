<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre.'admin_user';
//post
if( $action == 'add' || $action == 'edit' )
{ 
	if($action == 'edit' && $id > 0  ) {
		if(!empty($password))
		{
			$password = mkmd5($password);
			$Afields=array('password'=>$password,'utid'=>$utid,'ifmanger'=>$ifmanger);
		}else
		{
			$Afields=array('utid'=>$utid,'ifmanger'=>$ifmanger);
		}
		$condition = "id = ".$id." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($pre.'users',$Afields,$condition);
		
		$user_id = $oPub->getOne('SELECT user_id FROM '.$pre.'admin_user where user_id ='.$id.' AND domain_id="'.$Aconf['domain_id'].'"'); 
		if($user_id > 0  && $ifmanger < 1)
		{
			$condition = "user_id='".$id."' AND domain_id='".$Aconf['domain_id']."'";  
			$oPub->delete($pre.'admin_user',$condition);
		}elseif($user_id < 1 && $ifmanger > 0)
		{
			$Afields=array('user_id'=>$id,'user_name'=>$user_name, 'add_time'=>gmtime(),'domain_id'=>$Aconf['domain_id']);
            $oPub->install($pre.'admin_user',$Afields);
		}
	}
	unset($Anorm);unset($_POST);
} 
 
if( $action == 'gedit'){
	$Anorm = $oPub->getRow('SELECT * FROM '.$pre.'users where id ='.$id.' AND domain_id="'.$Aconf['domain_id'].'"'); 
}

if( $action == 'del'){ 
	//avatar
	$avatar = $oPub->getOne('SELECT avatar FROM '.$pre.'users where id ='.$id.' AND domain_id="'.$Aconf['domain_id'].'"'); 
	if($avatar > 0)
	{ 
		@unlink('../data/userimg/avatar_big/'.$avatar.'.jpg');
		@unlink('../data/userimg/avatar_origin/'.$avatar.'.jpg');
		@unlink('../data/userimg/avatar_small/'.$avatar.'.jpg');
	}

	$condition = "id='".$id."' AND domain_id='".$Aconf['domain_id']."'";  
	$oPub->delete($pre.'users',$condition);	 

	$condition = "users_id='".$id."' AND domain_id='".$Aconf['domain_id']."'";  
	$oPub->delete($pre.'users_comms',$condition);	

	$condition = "user_id='".$id."' AND domain_id='".$Aconf['domain_id']."'";  
	$oPub->delete($pre.'usersverify',$condition);
	
}


 
//page
$strWhere = ' WHERE domain_id="'.$Aconf['domain_id'].'"';
$row = $oPub->getRow('SELECT count( * ) AS count FROM '.$pre.'users'.$strWhere); 
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 40;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$AnormAll = $oPub->select('SELECT * FROM '.$pre.'users WHERE domain_id="'.$Aconf['domain_id'].'" ORDER BY id desc limit '.$strOffSet); 
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;   
		$tmpstr = ($n % 2 == 0)?"even":"odd";
		$StrtypeAll .= '<TR class='.$tmpstr.'>'; 

		$StrtypeAll .= '<TD align=left>'.($value["ifmanger"]?'<span style="color:#F00">是</span>':'否').'</TD>';

		$Urow = $oPub->getRow('SELECT estats,tstats FROM '.$pre.'usersverify WHERE  user_id="'.$value['id'].'"  limit 1'); 
		$etmp = $Urow["estats"] < 1 ?'<span style="font-size: 7px">未验证</span>':'<span style="font-size: 7px;color:#F00">已验证</span>';
		$ttmp = $Urow["tstats"] < 1 ?'<span style="font-size: 7px">未验证</span>':'<span style="font-size: 7px;color:#F00">已验证</span>';

		$StrtypeAll .= '<TD align=left>'.$value["email"].$etmp.'</TD>';
		$tmp = ($value["avatar"] > 0 )?'<IMG SRC="../data/userimg/avatar_small/'.$value["avatar"].'_small.jpg"  BORDER="0">':'';
		$StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
		$StrtypeAll .= '<TD align=left>'.$value["user_name"].'</TD>';

		$userstypename = $oPub->getOne('SELECT name FROM '.$pre.'userstype where orders ='.$value["utid"].' AND domain_id="'.$Aconf['domain_id'].'"');  
		$StrtypeAll .= '<TD align=left>'.$userstypename.'</TD>';

		$StrtypeAll .= '<TD align=left>'.$value["money"].'</TD>';

		$StrtypeAll .= '<TD align=left>'.($value["sex"]>0?'女':'男').'</TD>';



		$StrtypeAll .= '<TD align=left>'.$value["birthday"].'</TD>'; 
		$StrtypeAll .= '<TD align=left>'.date("Y-m-d",$value["reg_time"]).'</TD>';
		$StrtypeAll .= '<TD align=left>'.date("Y-m-d H:i",$value["last_login"]).'</TD>';
		$StrtypeAll .= '<TD align=left>'.$value["visit_count"].'</TD>';
		$StrtypeAll .= '<TD align=left>'.$value["qq"].'</TD>';
		$StrtypeAll .= '<TD align=left>'.$value["mobile_phone"].$ttmp.'</TD>';
		$StrtypeAll .= '<TD align=left>'.$value["addrs"].'</TD>';
		$StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=gedit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> '; 
		$StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=del" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
		$StrtypeAll .= '</TR>';  	   
}

 
$AnormAll = $oPub->select("SELECT name,orders FROM ".$pre."userstype where  domain_id=".$Aconf['domain_id']." ORDER BY orders ASC");  
$Struserstypeopt = '<SELECT NAME="utid">';
if($AnormAll)
foreach($AnormAll as $key => $value)
{ 
	$selected = ($Anorm['utid'] == $value["orders"])? 'SELECTED':'';
	$Struserstypeopt .= '<OPTION VALUE="'.$value["orders"].'" '.$selected.' >'.$value["name"].'</OPTION>'; 
}
$Struserstypeopt .= '</SELECT>';

?>

<?php
   include_once( "header.php");
	if ($strMessage != '')
	{
		 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
	}
?>
<DIV class=content>
	<?php if($Anorm['id'] > 0 ){ ?>
		<form name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>" style="margin: 0">
			<TABLE width="100%" border=0>
			  <TR> 
				<TD align="left"> 
					<span style="font-weight: bold">帐号:</span>	
					<?php echo ($Anorm['id'])?$Anorm['user_name']:''?> 
					<span style="font-weight: bold">密码:</span>		
					<input name="password" type="text" value="" size="6" /> 
					<?php echo $Struserstypeopt;?>
					<span style="font-weight: bold">后台管理:</span>
					<SELECT NAME="ifmanger">
						<OPTION VALUE="0" <?php echo $Anorm['ifmanger'] < 1?'SELECTED':'';?>>否</OPTION>
						<OPTION VALUE="1" <?php echo $Anorm['ifmanger'] == 1?'SELECTED':'';?>>是</OPTION>
					</SELECT>
					<input type="hidden" name="action" value="<?php echo ($Anorm['id'])?'edit':'add'?>" /> 
					<input type="hidden" name="user_name" value="<?php echo ($Anorm['id'])?$Anorm['user_name']:''?>" /> 
					<input type="submit" name="Submit" value="<?php echo ($Anorm['id'])?' 修改 ':'添加' ?>" style="background-color: #FFCC66;"/>
					<input type="hidden" name="id" value="<?php echo ($Anorm['id'])?$Anorm['id']:'0'?>" />  
				</TD> 
			  </TR>
			</TABLE>  
		</form> 
	<?php } ?>
	<TABLE width="100%" border=0>
	  <TR class=bg5> 
			<TD align=left>后台管理</TD>
			<TD align=left>EMAIL</TD>
			<TD align=left>头像</TD>
			<TD align=left>帐号</TD>
			<TD align=left>类型</TD>
			<TD align=left>余额</TD>
			<TD align=left>性别</TD>
			<TD align=left>生日</TD>
			<TD align=left>注册日期</TD>
			<TD align=left>登录日期</TD>
			<TD align=left>登录次数</TD>
			<TD align=left>QQ</TD>
			<TD align=left>电话</TD>
			<TD align=left>地址</TD>
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
 
<?php
include_once( "footer.php");
?>