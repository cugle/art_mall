<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$Atype = array(0=>'个人消息',1=>'系统消息',2=>'群发消息');
$Astates = array(0=>'未阅读',1=>'已阅读');
$Anave = array(1=>'未读信息',2=>'个人信息',3=>'系统信息',4=>'群发信息');
$db_table = $pre."messages";
//post
if( $_POST['action'] == 'add' || $_POST['action'] == 'edit' )
{ 
	if($_POST["type"] < 1 && $_POST["usernames"]){
		//个人信息，输入帐号
		$Atousername = explode(",",$_POST["usernames"]); 
		$n = 0;
		while( @list( $key, $value ) = @each(  $Atousername ) ) {
			$value = trim($value);
			$sql = "SELECT user_id   FROM ".$pre."admin_user where user_name='".$value."' limit 1";
			$user_id = $oPub->getOne($sql);
			if($user_id > 0 ){
				$touser_id = $user_id;
				$tousername = $value;
				$Afields=array('touser_id'=>$touser_id,'tousername'=>$tousername,'descs'=>$_POST['descs'],'dateadd'=>gmtime(),'type'=>$_POST['type'],'domain_id'=>$Aconf['domain_id']);
				$oPub->install($db_table,$Afields);
				
				$n ++ ;
			} 
		}
		$strMessage  =$n.'个帐号留言添加成功';
	} else { 
		$Afields=array('descs'=>$_POST['descs'],'dateadd'=>gmtime(),'type'=>$_POST['type'],'domain_id'=>$Aconf['domain_id']);
		$oPub->install($db_table,$Afields);
		$strMessage = '添加成功';
	}
 
} 

if( $_GET['action'] == 'del'){
    $db_table = $pre."messages";
    $condition = 'id='.$_GET['id'].'   and touser_id='.$_SESSION['auser_id'];
    $oPub->delete($db_table,$condition);
    $condition = 'messagesid='.$_GET['id'].'   and touser_id='.$_SESSION['auser_id'];
    $oPub->delete($pre.'messagesre',$condition);
}
 
if ($_POST['action'] == 'del')
{
    if (isset($_POST['checkboxes']))
    {
        $count = 0;		
        foreach ($_POST['checkboxes'] AS $key => $id)
        {
			$id = $id+0; 
			$condition = "id='".$id."' AND  touser_id=".$_SESSION['auser_id']; 
			$oPub->delete($pre."messages",$condition);

			$condition = 'messagesid='.$id .'   and touser_id='.$_SESSION['auser_id'];
			$oPub->delete($pre.'messagesre',$condition);
        } 
        $strMessage =  "批量删除成功!";
   }
}
//短信回复给管理员
if(!empty($redescs)){
	
		$Afields=array('messagesid'=>$_POST[messagesid],'touser_id'=>$_SESSION['auser_id'],'tousername'=>$_SESSION['auser_name'],'descs'=>$_POST['redescs'],'dateadd'=>gmtime(), 'domain_id'=>$Aconf['domain_id']);
		$oPub->install($pre.'messagesre',$Afields);
		$sql = "update ".$pre."messages set restates=1 where id='".$_POST[messagesid]."' and touser_id= '".$_SESSION['auser_id']."' limit 1";
		$oPub->query($sql);
		$strMessage = '短信回复成功';	
}

$strWhere = " WHERE ";
if($nave ==2){ 
	$strWhere .= ' type < 1  and touser_id='.$_SESSION['auser_id'];
}elseif($nave ==3){
	$strWhere .= '  type=1';
}elseif($nave ==4){
	$strWhere .= '  type=2';
}else{
	$strWhere .= '  type < 1 and states<1 and touser_id='.$_SESSION['auser_id'];
}
//page

$sql = "SELECT count( * ) AS count FROM ".$db_table.$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array('nave'=>$nave); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.$strWhere." ORDER BY id desc limit ".$strOffSet;
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   if($nave < 3){
			$StrtypeAll .= '<TD align=left> <input type="checkbox" name="checkboxes['.$value["id"].']" value="'.$value["id"].'" /></td>';
	   }else{
			$StrtypeAll .= '<TD align=left>'.$value["id"].'</td>';
	   }
	     
	   $StrtypeAll .= '<TD align=left><a href="messagesshow.php?id='.$value["id"].'" target="_blank">'.sub_str(clean_html($value["descs"]),30,true).'</a></TD>';
	   $StrtypeAll .= '<TD align=left>'.date("Y/n/j H:i:s",$value["dateadd"]).'</TD>';
	   if($value["restates"] == 1){
			$StrtypeAll .= '<TD align=left>已回复</TD>';
	   }else{
			$StrtypeAll .= '<TD align=left></TD>';
	   }

       $StrtypeAll .= '<TD align=left>';
	   if($nave < 3){
			$StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=del&nave='.$nave.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
	   }else{
			$StrtypeAll .= '';
	   }
       $StrtypeAll .= '</TD></TR>';  	   
}

$Stropt = '<SELECT NAME="type" onchange="chkSearch(this.options[this.options.selectedIndex].value)">'; 
foreach ($Atype AS $key => $value ){ 
       $Stropt .= '<OPTION VALUE="'.$key.'">'.$value.'</OPTION>'; 
}
$Stropt .= '</SELECT>';  
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
   <TR> 
	<TD  colspan="5" align=left >
	<style>
		#navs{ width680px; height:30px; overflow:hidden;  position:relative;  clear:both;} 
		#navs ul{ padding:0px 0 0 20px;}
		#navs ul li{ width:90px; float:left; height:25px; line-height:25px;border:1px solid #FF9900; overflow:hidden; display:inline; text-align:left; font-size:9px; font-family:Verdana, Geneva, sans-serif; }
		#navs ul li a{ font-size:14px; text-indent:0.5em;}
		#navs ul li strong{color:#FF4400; }
		#navs ul li strong a{ color:#FF4400; margin:0px; font-size:14px; height:25px; line-height:25px; }
		#navs ul li a{ margin:0 10px 0 10px; height:25px; line-height:25px; } 
	</style>
	<div id="navs">
	<ul>
	<?php 
	$str = '';
	foreach ($Anave AS $key => $value ){ 
		if($nave == $key){
			$str .=  '<li><a href="'.$_SERVER["PHP_SELF"].'?nave='.$key.'"><strong>'.$value.'</strong></a></li>'; 
		}else{
			$str .=  '<li><a href="'.$_SERVER["PHP_SELF"].'?nave='.$key.'">'.$value.'</a></li>';
		}
		
	}
	echo $str;
	?> 
	</ul>
	</div>
	<div id="messages"></div>
	</TD> 
  </TR>
  <TR class=bg5>
  <form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
	<TD width="5%" align=left>ID</TD> 
	<TD width="30%" align=left>内容</TD>
	<TD width="15%" align=left>时间</TD>
	<TD width="10%" align=left>回复</TD>
    <TD width="10%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="5" align=right>
	<?php
	if($nave < 3){
	?>
		<span style="float: left">
	全选删除:<input onclick=selectAll() type="checkbox" name="check_all"/>
	<INPUT TYPE="submit" name="submit" value="确认删除" style="background-color: #CDE76A">
	<INPUT TYPE="reset" name="reset" value="恢复" style="background-color: #CDE76A"> 
	<INPUT TYPE="hidden" name="action" value="del"> 
    </span>
	<?php } ?>
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span> 
	</TD>
  </TR>
  </form>
</TABLE>  
</DIV>
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<script language=JavaScript>
function chkSearch(obj)
{ 
	if(obj < 1){
		document.getElementById("listuser").style.display='';
	}else{
		document.getElementById("listuser").style.display='none';	
	}

}
function selectAll(){
	xx = listForm.check_all.checked
	for(var i=0;i<listForm.length;i++)
	{
		if(listForm.elements[i].type=="checkbox")
			listForm.elements[i].checked=xx;
	}
}

function messages(id)
{
     obj = 'messages';
     var strTemp = "ajax_messages.php?id=" + id; 
	 send_request(strTemp);
}

function Hidden()
{
	document.getElementById("messages").style.display='none';
}
</script>
<?php
include_once( "footer.php");
?>
