<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
} 
//post 
if( $action == 'add' )
{  
	$Afields=array('notices'=>$notices);
	$oPub->install($pre."sysnotice",$Afields);
	$strMessage =  "添加成功!"; 
}

if( $action == 'edit')
{ 
	$id = $id + 0;  
	$condition = ' id = '.$id;
	$Afields=array('notices'=>$notices);
	$oPub->update($pre."sysnotice",$Afields,$condition);
	$strMessage = "编辑成功";
	$action = false; 
}

//get
if( $op == 'getedit'){
	$id = $id + 0;
	$Aspp = $oPub->getRow("SELECT id,notices FROM ".$pre."sysnotice WHERE id = '".$id. "'"); 
}

if( $action == 'del'){
	$id + 0;
    $condition = 'id='.$id;
    $oPub->delete($pre."sysnotice",$condition);	
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
			$oPub->delete($pre."sysnotice",$condition); 
		}
	}
}


//page
$strWhere = "";
$sql = "SELECT count( * ) AS count FROM ".$pre."sysnotice ".$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 20;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."sysnotice ".$strWhere." ORDER BY id desc limit ".$strOffSet;
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


	$notices = sub_str(clean_html($value["notices"]),30);
	$StrtypeAll .= '<TD align=left>'.$notices.'</TD>'; 
 
	$StrtypeAll .= '<TD align=left>'.$row['dateadd'].'</TD>'; 

	$StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&op=getedit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	$StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';

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
	<form name="form1" method="post" action=""> 
		<TABLE width="900" border=0>
			<TR class="odd" > 
				<TD width="900" align="left" colspan="7"> 
					<div style="margin: 5px;color:#CC0000">注：此处添加的最新公告,将统一显示到子站默认后台首页！</div>
 		
					 <textarea name="notices" style="width:750px;height:400px;visibility:hidden;"><?php echo $Aspp['notices'];?></textarea>
					<input type="submit" name="Submit" value="<?php echo ($Aspp['id'])?'编辑回复':'增加留言'?>" style="background-color: #FFCC66"/>
					<input type="hidden" name="action" value="<?php echo ($Aspp['id'])?'edit':'add'?>" />        
					<input type="hidden" name="id" value="<?php echo ($Aspp['id'])?$Aspp['id']:'0'?>" />  
				</TD> 
			</TR>	
		</TABLE> 
	</form>

<TABLE width="100%" border=0>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
  <TR class=bg5>
    <TD width="10%" align=left>序号</TD> 
    <TD width="60%" align=left>内容</TD> 
	<TD width="20%" align=left>日期</TD> 
    <TD width="10%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="4" align=right>
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
<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script>
<script>
	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="notices"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=sysnotice',
			fileManagerJson : '../upload_manager_json.php?jsonop=sysnotice',
			allowFileManager : false,
            width : '700px',
            height: '200px',
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
