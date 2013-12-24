<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}
//keysre

$db_table = $pre."filter";
if( $action == 'add' || $action == 'edit' )
{

	$_POST["words"] = @preg_replace(array('/\//i'), array(""), $words); 
    if( $_POST['action'] == 'add' && $_POST['scid'] == 0){
		$oPub->query('INSERT INTO ' .$pre.'filter(ips,words,keysre,states) VALUES ("'.$ips.'","'.$words.'","'.$keysre.'","'.$states.'")'); 
    }

   if( $_POST['action'] == 'edit' && $_POST['fid'] > 0){
	   $_POST['fid'] = $_POST['fid'] + 0; 
       $oPub->query('UPDATE ' .$pre.'filter SET  ips="'.$ips.'",words="'.$words.'",keysre="'.$keysre.'",states="'.$states.'"  WHERE  fid="'.$fid.'"');  
   }

}
/* 网站配置信息 */ 
$Anorm = $oPub->getRow("SELECT * FROM ".$pre."filter ORDER BY fid ASC LIMIT 1"); 
?>
<?php
   include_once( "header.php"); 
?>

<DIV class=content>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <td align="left">
 <span style="font-weight:bold">用户留言,关键词及IP过滤设置:</span>
 </td>
</tr>
</table>
<TABLE width="100%" border=0>
  <TR>
  <form name="form1" method="post" action=""> 
    <TD width="13%" align="left"> 
		<p style="margin-left: 20px;margin-top:0px;"> 
		<span style="font-weight:bold">是否启用过滤:</span>
		<?PHP
		 $temChecked1 = ($Anorm['states'])?'CHECKED':'';
		 $temChecked0 = ($Anorm['states'])?'':'CHECKED';
		?>
		是<INPUT TYPE="radio" NAME="states" value="1" <?php echo $temChecked1;?>>
		否<INPUT TYPE="radio" NAME="states" value="0" <?php echo $temChecked0;?>>
		<br/>
		<span style="font-weight:bold">过滤关键词:</span>[<U>1.每一个关键词一行;2.需要贴换的词在 = 符号后.3.在 = 后如果为空，则贴换为空。如: 手枪=**</U>]<br/>
		<TEXTAREA NAME="words" style="height:200px;width: 600px" ><?php echo $Anorm['words'];?></TEXTAREA>
        <br/> <br/> 
        <span style="font-weight:bold">重复关键词:</span>[<U>1.规则方式：关键词=重复次数；2.每一个关键词一行。如：日本AV=3</U>],超过次数则不允许录入。<br/>
		<TEXTAREA NAME="keysre" style="height:100px;width: 600px" ><?php echo $Anorm['keysre'];?></TEXTAREA> 
		<br/> <br/> 
        <span style="font-weight:bold">过滤IP:</span>[<U>IP之间用","号分隔</U>],以下IP不能留言。<br/>
		<TEXTAREA NAME="ips" style="height:100px;width: 600px" ><?php echo $Anorm['ips'];?></TEXTAREA> 
		<br/>
		<input type="hidden" name="action" value="<?php echo ($Anorm['fid'])?'edit':'add';?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['fid'])?'编辑修改':'增加';?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="fid" value="<?php echo ($Anorm['fid'])?$Anorm['fid']:'0';?>" />  
		</p>

    </TD>
    </form>
  </TR>	
 
</TABLE>
 
</DIV>
<?php
include_once( "footer.php");
?>