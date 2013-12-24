<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."userstype";
//post
if( $_POST['action'] == 'add' || $_POST['action'] == 'edit' )
{
	$_POST['orders']  =  $_POST['orders'] < 1 ? 1:$_POST['orders'];
	if($_POST['action'] == 'add' && $_POST['name'])
	{ 
		$Afields=array('name'=>$_POST['name'], 'orders'=>$_POST['orders'],'domain_id'=>$Aconf['domain_id']);
		$tid = $oPub->install($db_table,$Afields);
		$strMessage = '添加成功';  
	} else if($_POST['action'] == 'edit' && $_POST['id'] ) {
		$Afields=array('name'=>$_POST['name'], 'orders'=>$_POST['orders'] );
		$condition = "id = ".$_POST['id']." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($db_table,$Afields,$condition); 
	}
	unset($Anorm);unset($_POST);
}

if(isset($_GET['id']) && $_POST['action'] == 'del' )
{ 
	$condition = 'id='.$_GET['id'].' AND domain_id='.$Aconf['domain_id']; 
	$oPub->delete($db_table,$condition);  
}
//get
 
if( $_GET['action'] == 'edit'){
	$Anorm = $oPub->getRow("SELECT * FROM ".$pre."userstype where id = ".$_GET['id']." AND domain_id=".$Aconf['domain_id']); 
} 
 
//page
$sql = "SELECT * FROM ".$pre."userstype  WHERE domain_id=".$Aconf['domain_id']."  order by orders asc";
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';  
	   $StrtypeAll .= '<TD align=left>'.$value["name"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["orders"].'</TD>'; 
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a>'; 
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';
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
 
<form name="form1" method="post"   action="<?php echo $_SERVER["PHP_SELF"]?>" style="margin: 0">
	<TABLE width="100%" border=0>
	  <TR> 
		<TD align="left">  
			<span style="font-weight: bold">类型:</span> 
			<input name="name" type="text" value="<?php echo  $Anorm['name'];?>" size="10" />

			<span style="font-weight: bold">序号:</span>	 
			<input name="orders" type="text" value="<?php echo  $Anorm['orders'];?>" size="2" /> 
			<span style="color:#F00">特别注意：序号不能重复，权限序号以1开始累加。</span>

			<input type="hidden" name="action" value="<?php echo ($Anorm['id'])?'edit':'add'?>" />
			<input type="submit" name="Submit" value="<?php echo ($Anorm['id'])?' 编辑 ':' 增加 ' ?>" style="background-color: #FFCC66;margin-left:5px"/>
			<input type="hidden" name="id" value="<?php echo ($Anorm['id'])?$Anorm['id']:'0'?>" />  
		</TD> 
	  </TR>
	</TABLE>  
</form>
 
 
<TABLE width="100%" border=0>
  <TR class=bg5>
		<TD align=left>类型</TD>
		<TD align=left>顺序</TD> 
		<TD align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>


</TABLE> 
 
</DIV>
 
<?php
include_once( "footer.php");
?>
