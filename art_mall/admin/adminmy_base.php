<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if( $action == 'edit')
{
 
 	if(!empty($password)) {
		$Afields=array('password'=>mkmd5($password));
		$condition = 'id='.$_SESSION['auser_id'].' AND domain_id = '.$Aconf['domain_id'];
		$oPub->update($pre."users",$Afields,$condition);
		$strMessage .= " 密码成功修改 ";
	}
} 
 

?>
<?php
include_once( "header.php");
if ($strMessage != '') {
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<style>
.txt{font-weight: bold;margin-left: 20px;} 
.w100{width:100px;}
.w200{width:200px;}
.w300{width:300px;}
.w600{width:600px;}
.w650{width:650px;}
.note{font-size:6px;margin-left:5px;color:#707070}

</style>
<DIV class="content"> 
	<div class="box w650">
		<ul>
		<form name="form1" method="post" action="" enctype="multipart/form-data" > 
		  <div class="odd">
				<span class="txt">登陆帐号user:</span>
				<span class="w400"><?php echo $_SESSION['auser_name'];?> </span>
				<span class="txt">登陆密码password:</span>
				<span class="w400">
			<input name="password" type="text" class="w100" value="" />
			<input type="submit" name="Submit" value="<?php echo ($_SESSION['auser_id'])?'修改提交edit':'增加新用户add'?>" style="clear:left;float:auto;background-color: #FFCC66;"/>
			<br /> 
			</spa><span class="note">(注：如不修改密码，请保持为空。please keep epty if you do not want to change password)</span ></div>
 
			<div class="even">
				<input type="hidden" name="action" value="<?php echo ($_SESSION['auser_id'])?'edit':'add'?>" />   
				
			</div>
		</form>  
	</div>
 
</DIV>
	
<?php
include_once( "footer.php");
?>
