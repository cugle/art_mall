<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
} 

if( $_SESSION['apraid'] < 1)
{
   $strMessage = '此账号没有绑定经销商，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">多管理员权限->管理员权限设置 ->指定管理经销商</a>';
   echo  showMessage($strMessage);
   exit;
}

 $praid = $oPub->getOne("SELECT praid FROM ".$pre."pravail WHERE praid = ".$_SESSION['apraid']." ORDER BY praid ASC LIMIT 1"); 
 if( $praid < 1)
 {
	$strMessage = '此经销商已不存在，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">多管理员权限->管理员权限设置 ->指定管理经销商</a>';
	echo  showMessage($strMessage);
	exit;
}

$db_table = $pre."pravail_productcat"; 
if( $_POST['action'] == 'add'  )
{
	$Afields=array('praid'=>$_SESSION['apraid'],'name'=>$_POST['name'],'descs'=>$_POST['descs'],'ifshow'=>1,'domain_id'=>$Aconf['domain_id']);
    $tprapcid= $oPub->install($db_table,$Afields);
}

if( $_POST['action'] == 'edit'){
	$db_table = $pre."pravail_productcat";
	$_POST['prapcid'] = $_POST['prapcid'] + 0;

	$Afields=array('name'=>$_POST['name'],'descs'=>$_POST['descs'],'ifshow'=>1);
	$condition = 'prapcid='.$_POST['prapcid'].' AND domain_id='.$Aconf['domain_id'];
    $oPub->update($db_table,$Afields,$condition);
	unset($_GET);
}

//get
$db_table = $pre."pravail_productcat";
if( $_GET['action'] == 'edit'){
	$_GET['prapcid'] = $_GET['prapcid'] +0;
	$sql = "SELECT * FROM ".$db_table." where prapcid= ".$_GET['prapcid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	/*还有子分类将不能删除*/
	$_GET['prapcid'] = $_GET['prapcid'] + 0;

	$condition = 'prapcid='.$_GET['prapcid'].' AND domain_id='.$Aconf['domain_id'];
    $oPub->delete($db_table,$condition);
}



/* 找到所有的分类到select start*/
$db_table = $pre."pravail_productcat";
$sql = "SELECT * FROM ".$db_table." 
    where praid  = '".$_SESSION['apraid']."' 
	AND domain_id=".$Aconf['domain_id']." 
	ORDER BY prapcid ASC";
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	  $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$value["name"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["descs"].'</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?prapcid='.$value["prapcid"].'&fid=0&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?prapcid='.$value["prapcid"].'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';
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
<table width="800" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <form name="form1" method="post" action=""> 
    <TD align="left" colspan="3">
   
        <span style="font-weight: bold">分类名:</span>
     	<input name="name" type="text" value="<?php echo ($Anorm['prapcid'])?$Anorm['name']:''?>" />
		<span style="font-weight: bold">分类描述:</span>
     	<input name="descs" type="text" value="<?php echo ($Anorm['prapcid'])?$Anorm['descs']:''?>" size="50"/>		
        <input type="hidden" name="action" value="<?php echo ($Anorm['prapcid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['prapcid'])?'编辑':'增加' ?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="prapcid" value="<?php echo ($Anorm['prapcid'])?$Anorm['prapcid']:'0'?>" />  
    </TD>
    </form>
</tr>
</table>

<TABLE width="800" border=0>
  <TR class=bg5>
    <TD width="30%" align=left>分类</TD>
	<TD width="60%" align=left>描述</TD>
    <TD width="10%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="3" align=right><?php //echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
 
</DIV>
<?php
include_once( "footer.php");
?>
