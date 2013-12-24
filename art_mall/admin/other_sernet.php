<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$id = $oPub->getOne('SELECT id FROM '.$pre.'sernet where domain_id='.$Aconf['domain_id'].' limit 1'); 
if($id < 1)
{
	$AnormAll = $oPub->select('SELECT * FROM '.$pre.'sernet  WHERE domain_id='.$Aconf['allow_home'].'  order by id asc'); 
	$strsql = 'INSERT INTO  '.$pre.'sernet(py,name,domain_id)VALUES'; 
	$str = '';
	while( @list( $key, $value ) = @each( $AnormAll) ) { 
		$str .= '("'.$value['py'].'","'.$value['name'].'",'.$Aconf['domain_id'].'),'; 
	}
	if(!empty($str))
	{
		$str = substr($str,0,-1);
		$str = $strsql.$str; 
		$oPub->query($str);
	} 
} 
//post
if( $action == 'add' || $action == 'edit' )
{
   if($action == 'edit' && $id ) {
          $Afields=array('name_desc'=>$_POST['name_desc'],'url'=>$_POST['url'],'stats'=>$_POST['stats']);
	      $condition = "id = ".$id." AND domain_id=".$Aconf['domain_id'];
	      $oPub->update($pre."sernet",$Afields,$condition);
	      
	}
	unset($Anorm);unset($_POST);
} 
//get 
if( $_GET['action'] == 'edit'){
	$Anorm = $oPub->getRow("SELECT * FROM ".$pre."sernet where id = ".$_GET['id']." AND domain_id=".$Aconf['domain_id']); 
} 
//page
$sql = "SELECT * FROM ".$pre."sernet  WHERE domain_id=".$Aconf['domain_id']."  order by py asc";
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>'; 
       $StrtypeAll .= '<TD align=left>'.$value["py"].'</span></TD>'; 
	   $StrtypeAll .= '<TD align=left>'.$value["name"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["name_desc"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["url"].'</TD>'; 
 
	   $StrtypeAll .= '<TD align=left>'.($value["stats"]?'使用':'<span style="color:#F00">禁用</span>').'</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a></TD>';
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
<?php if($Anorm['id'] > 0) { ?>
	<form name="form1" method="post"   action="<?php echo $_SERVER["PHP_SELF"]?>" style="margin: 0">
		<TABLE width="100%" border=0>
		  <TR> 
			<TD align="left"> 
				<span style="font-weight: bold">编码:</span> <?php echo  $Anorm['py'];?> 
				<span style="font-weight: bold">地区:</span> <?php echo  $Anorm['name'];?> 

				<span style="font-weight: bold">描述:</span>	 
				<input name="name_desc" type="text" value="<?php echo  $Anorm['name_desc'];?>" size="45" />
				<span style="font-weight: bold">连接地址:</span>	 
				<input name="url" type="text" value="<?php echo  $Anorm['url']?$Anorm['url']:'';?>"  size="35" />
				<span style="font-weight: bold">状态:</span>
				<INPUT TYPE="radio" NAME="stats" value="1" <?php echo ($Anorm['stats']==1?'checked':'');?>>使用 <INPUT TYPE="radio" NAME="stats" value="0" <?php echo ($Anorm['stats']<1?'checked':'');?>>禁用
	 

				<input type="hidden" name="action" value="<?php echo ($Anorm['id'])?'edit':'add'?>" />
				<input type="submit" name="Submit" value="<?php echo ($Anorm['id'])?' 连接编辑 ':' 连接增加 ' ?>" style="background-color: #FFCC66;margin-left:5px"/>
				<input type="hidden" name="id" value="<?php echo ($Anorm['id'])?$Anorm['id']:'0'?>" />  
			</TD> 
		  </TR>
		</TABLE>  
	</form>
<?php } ?>
 
<TABLE width="100%" border=0>
  <TR class=bg5>
		<TD align=left>编码</TD>
		<TD align=left>地区</TD>
		<TD align=left>描述</TD>
		<TD align=left>连接地址</TD> 
		<TD align=left>状态</TD> 
		<TD align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?> 
</TABLE> 
 
</DIV>
 
<?php
include_once( "footer.php");
?>
