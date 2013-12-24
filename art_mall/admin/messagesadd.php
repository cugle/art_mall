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
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <td align="middle">
   <span style="float: left">
		<a href="<?php echo $_SERVER["PHP_SELF"]?>"> 短信列表 </a>  
   </span>
   <span style="float: right"> <a href="messagesadd.php"> 发布新短信</a> </span>
 </td>
</tr>
</table> 

<TABLE width="100%" border=0> 
  <TR  >
  <form name="form1" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"]?>"> 
    <TD width="13%" align="left" colspan="8">
	<a href="messages.php"> 短信列表 </a> >  发布新短信 <br>   
        <span style="font-weight: bold">短信类型:</span>
     	<?php echo $Stropt;?> 
		<span id="listuser">
		<br/>
         <span style="font-weight: bold">用户帐号:</span> <INPUT TYPE="text" NAME="usernames" value="" size=80><U>注：多个帐号用","半角逗号分开</U><br/>
		</span> 
        <span style="font-weight: bold">短信内容:</span>	
		<div style="width:800px"> 
		<textarea name="descs" style="width:750px;height:400px;visibility:hidden;"><?php echo $work['descs'];?></textarea>
		</div>
        <input type="hidden" name="action" value="<?php echo ($Anorm['id'])?'edit':'add'?>" />
		<br/>
        <input type="submit" name="Submit" value="<?php echo ($Anorm['id'])?' 编辑 ':' 发布短信 ' ?>" style="background-color: #CDE76A;margin-left: 50px"/>
		<input type="hidden" name="id" value="<?php echo ($Anorm['id'])?$Anorm['id']:'0'?>" />  
    </TD>
    </form>
  </TR>	
 
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

function messagesxy(id)
{
     obj = 'messagesxy';
     var strTemp = "ajax_messagesxy.php?id=" + id;  
	 send_request(strTemp);
}

function Hidden()
{
	document.getElementById("messagesxy").style.display='none';
}
</script>
<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script>
<script>
	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="descs"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=messages',
			fileManagerJson : '../upload_manager_json.php?jsonop=messages',
			allowFileManager : false,
            width : '700px',
            height: '500px',
			resizeType: 0,
			items:['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/','formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image','flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak','anchor', 'link', 'unlink'],

			afterCreate : function() {
				var self = this;
				K.ctrl(document, 13, function() {
					self.sync();
					K('form[name=form1]')[0].submit();
				});
				K.ctrl(self.edit.doc, 13, function() {
					self.sync();
					K('form[name=form1]')[0].submit();
				});
			} 
		}); 
		K('input[name=clear]').click(function(e) {
			editor.html('');
		}); 
 
	}); 

	function insertHtml(value,b) {  
		editor.focus();  
		var str = '<IMG SRC="' + value + '"  BORDER="0" ALT="' + b + '">';
		editor.insertHtml( str ); 
	} 
</script>
<?php
include_once( "footer.php");
?>