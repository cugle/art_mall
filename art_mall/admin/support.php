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

   $sql = "INSERT INTO " .$pre."support ( supports,  ip , dateadd ,orderdate , states , `domain_id` )" .
           "VALUES ('".$supports."', '".real_ip()."','".gmtime()."','".gmtime()."', '3','".$Aconf['domain_id']."')"; 
   $oPub->query($sql);
   $strMessage =  "添加成功!";
}

if( $action == 'edit')
{ 
	$spid = $spid + 0;   

	$oPub->query($sql = "UPDATE " . $pre."support SET supports = '$supports',ip='".real_ip()."',dateadd='".gmtime()."',orderdate='".gmtime()."',states=3  WHERE domain_id = ".$Aconf['domain_id']." and spid = '".$spid."'");  
	$strMessage = "编辑回复成功";
	$action = false; 
}

//get
if( $op == 'getedit'){
	$spid = $spid + 0;
	$Aspp = $oPub->getRow("SELECT * FROM ".$pre."support WHERE spid = ".$spid. " AND domain_id=".$Aconf['domain_id']); 
}

if( $action == 'del'){
	$spid + 0;
    $condition = 'spid='.$spid;
    $oPub->delete($pre."support",$condition);	
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
			$condition = 'spid='.$id;
			$oPub->delete($pre."support",$condition); 
		}
	}
}


//page
$strWhere = " WHERE states <> 1 AND domain_id=".$Aconf['domain_id'];
$sql = "SELECT count( * ) AS count FROM ".$pre."support ".$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 40;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."support ".$strWhere." ORDER BY spid desc limit ".$strOffSet;
$AsppAll = $oPub->select($sql);
//users_id comms 
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AsppAll) ) {
	$tmpstr = ($n % 2 == 0)?"even":"odd";
	$n ++ ;
	$StrtypeAll .= '<TR class='.$tmpstr.'>';
	$StrtypeAll .= '<TD align=left>';
	$StrtypeAll .= '<input type="checkbox" name="checkboxes['.$value["spid"].']" value="'.$value["spid"].'" />';
	$StrtypeAll .= '</TD>';

	$user_name = $oPub->getOne('SELECT user_name from '.$pre.'users where domain_id = '.$Aconf['domain_id'].' and id="'.$value['users_id'].'"'); 
	$user_name = (empty($user_name))?'匿名':$user_name;  
	$StrtypeAll .= '<TD align=left>'.$user_name.'</TD>';


	$supports = sub_str(clean_html($value["supports"]),20);
	$StrtypeAll .= '<TD align=left><A HREF="support_re.php?spid='.$value['spid'].'">'.$supports.'</a></TD>';

	$row = $oPub->getRow('SELECT supports,dateadd FROM '.$pre.'support_re where spid = "'.$spid.'" order by id desc limit 1');
	$str = '';
	if($row )
	{
		$str = ' <span title="'.date("m.d h:i",$row['dateadd']).'">'.sub_str($row['supports'],10).'</span>';
	}
	$StrtypeAll .= '<TD align=left>('.$value['comms'].')'.$str.'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value["ip"].'</TD>';
	$StrtypeAll .= '<TD align=left>'.date("m.d h:i", $value[dateadd]).'</TD>';
	$states = ($value[states]==3)?'已审':'';
	$StrtypeAll .= '<TD align=left>'.$states.'</TD>';
	$StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?spid='.$value["spid"].'&op=getedit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	$StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?spid='.$value["spid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';

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
	<TD width="10%" align=left>帐号</TD>
    <TD width="25%" align=left>内容</TD>
	<TD width="15%" align=left>回复</TD>
	<TD width="10%" align=left>ip</TD>
	<TD width="15%" align=left>日期</TD>
	<TD width="5%" align=left>状态</TD>
    <TD width="10%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="8" align=right>
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
<?php if($Aspp['spid'] > 0){ ?> 
	<form name="form1" method="post" action=""> 
		<TABLE width="900" border=0>
			<TR class="odd" > 
				<TD width="900" align="left" colspan="7"> 
					<br/> 
					<textarea name="supports" style="width:750px;height:300px;visibility:hidden;"><?php echo $Aspp['supports'];?></textarea>	
		
					<input type="submit" name="Submit" value="<?php echo ($Aspp['spid'])?'编辑回复':'增加留言'?>" style="background-color: #FFCC66"/>
					<input type="hidden" name="action" value="<?php echo ($Aspp['spid'])?'edit':'add'?>" />        
					<input type="hidden" name="spid" value="<?php echo ($Aspp['spid'])?$Aspp['spid']:'0'?>" /> 
					<br/>
				</TD> 
			</TR>	
		</TABLE> 
	</form>
<?php } ?>
</DIV>
<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script> 

<script type="text/javascript" language="JavaScript">

	function selectAll(){
		xx = listForm.check_all.checked
		for(var i=0;i<listForm.length;i++)
		{
			if(listForm.elements[i].type=="checkbox")
				listForm.elements[i].checked=xx;
		}
	}

	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="supports"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=supports',
			fileManagerJson : '../upload_manager_json.php?jsonop=supports',
			allowFileManager : false,
            width : '750px',
            height: '400px',
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
