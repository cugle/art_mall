<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
} 
 

if( $action == 'edit')
{ 
	$id = $id + 0;  
	$condition = 'domain_id ='.$Aconf['domain_id'].' and id = '.$id;
	$Afields=array('supports'=>$supports,'ip'=>real_ip(),'domain_id'=>$Aconf['domain_id']);
	$oPub->update($pre."support_re",$Afields,$condition);
	$strMessage = "编辑回复成功";
	$action = false; 
}

//get
if( $op == 'getedit'){
	$id = $id + 0;
	$Aspp = $oPub->getRow("SELECT * FROM ".$pre."support_re WHERE id = ".$id. " AND domain_id=".$Aconf['domain_id']); 
}

if( $action == 'del'){
	$id + 0;
    $condition = 'id='.$id;
	$spid = $oPub->getOne("SELECT spid FROM ".$pre."support_re WHERE id = ".$id. " AND domain_id=".$Aconf['domain_id']);
    $oPub->delete($pre."support_re",$condition);	
	if($spid > 0)
	{
		$oPub->query('update '. $pre.'support set comms=comms-1 where spid='.$spid.' limit 1'); 
	}
}
/* 批量删除 */
if ($action == 'del_checkbox')
{
    if (isset($checkboxes))
    {
        $count = 0;
        foreach ($checkboxes AS $key => $id)
        {
			$id = $id +0; 
			$condition = 'id='.$id;
			$oPub->delete($pre."support_re",$condition); 
		}
	}
}


//page
$strWhere = " where domain_id=".$Aconf['domain_id'];
if($spid > 0){
	$strWhere .= ' and spid='.$spid; 
}

$sql = "SELECT count( * ) AS count FROM ".$pre."support_re ".$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 40;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array('spid'=>$spid); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."support_re ".$strWhere." ORDER BY id desc limit ".$strOffSet;
$AsppAll = $oPub->select($sql);
//users_id comms 
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AsppAll) ) {
	$tmpstr = ($n % 2 == 0)?"even":"odd";
	$n ++ ;
	$StrtypeAll .= '<TR class='.$tmpstr.'>';
	$StrtypeAll .= '<TD align=left>';
	$StrtypeAll .= '<input type="checkbox" name="checkboxes['.$value["id"].']" value="'.$value["id"].'" />';
	$StrtypeAll .= '</TD>';

	$supports = $oPub->getOne('SELECT supports from '.$pre.'support where domain_id = '.$Aconf['domain_id'].' and spid="'.$value['spid'].'"');  
	$StrtypeAll .= '<TD align=left><A HREF="'.$_SERVER["PHP_SELF"].'?spid='.$value['spid'].'">'.sub_str(clean_html($supports),6).'</A></TD>';

	$user_name = $oPub->getOne('SELECT user_name from '.$pre.'users where domain_id = '.$Aconf['domain_id'].' and id="'.$value['users_id'].'"'); 
	$user_name = (empty($user_name))?'匿名':$user_name;  
	$StrtypeAll .= '<TD align=left>'.$user_name.'</TD>'; 

	$supports = sub_str(clean_html($value["supports"]),50);
	$StrtypeAll .= '<TD align=left>'.$supports.'</TD>';
 
	$StrtypeAll .= '<TD align=left>'.$value["ip"].'</TD>';
	$StrtypeAll .= '<TD align=left>'.date("m.d h:i", $value[dateadd]).'</TD>'; 

	$StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&op=getedit&spid='.$value["spid"].'&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	$StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=del&spid='.$value["spid"].'&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';

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
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
  <TR class=bg5>
    <TD width="5%" align=left>序号</TD>
	<TD width="10%" align=left>主题</TD>
	<TD width="10%" align=left>帐号</TD>
    <TD width="45%" align=left>内容</TD> 
	<TD width="10%" align=left>ip</TD>
	<TD width="10%" align=left>日期</TD> 
    <TD width="10%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="7" align=right>
    <span style="float: left">
	全选删除:<input onclick=selectAll() type="checkbox" name="check_all"/>
	<INPUT TYPE="submit" name="submit" value="确认删除" style="background-color: #FF9900">
	<INPUT TYPE="reset" name="reset" value="恢复" style="background-color: #CCFF99"> 
	<INPUT TYPE="hidden" name="action" value="del_checkbox"> 
    </span>
	<?php echo $showpage = $page->ShowLink();?>
	</TD>
  </TR>
  </FORM>
</TABLE>
<?php if($Aspp['id'] > 0){ ?> 
	<form name="form1" method="post" action=""> 
		<TABLE width="900" border=0>
			<TR class="odd" > 
				<TD width="900" align="left" colspan="7"> 
					<br/>
					<INPUT TYPE="text" NAME="supports" style="width:600px" value="<?php echo ($Aspp['id'])?$Aspp['supports']:''?>">	
					<INPUT TYPE="hidden" NAME="spid"  value="<?php echo ($Aspp['id'])?$Aspp['spid']:0;?>">
					<input type="submit" name="Submit" value="<?php echo ($Aspp['id'])?'编辑回复':'增加留言'?>" style="background-color: #FFCC66"/>
					<input type="hidden" name="action" value="<?php echo ($Aspp['id'])?'edit':''?>" />        
					<input type="hidden" name="id" value="<?php echo ($Aspp['id'])?$Aspp['id']:'0'?>" /> 
					<br/>
				</TD> 
			</TR>	
		</TABLE> 
	</form>
<?php } ?>
</DIV>
<script type="text/javascript" language="JavaScript">

function selectAll(){
	xx = listForm.check_all.checked
	for(var i=0;i<listForm.length;i++)
	{
		if(listForm.elements[i].type=="checkbox")
			listForm.elements[i].checked=xx;
	}
}

</script>
<?php
include_once( "footer.php");
?>
