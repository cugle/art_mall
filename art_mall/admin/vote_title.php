<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
if($Aconf['priveMessage'] != '') {
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."vote_title";
//post
if( ($_POST['action'] == 'add' || $_POST['action'] == 'edit') && $_POST['vt_name'] )
{
     $_POST['vtid'] = $_POST['vtid'] + 0;
	if($_POST['action'] == 'add')
	{

	    $Afields=array('vt_name'=>$_POST['vt_name'],'vt_desc'=>$_POST['vt_desc'],'add_time'=>gmtime(),'is_show'=>$_POST['is_show'],'xianz'=>$_POST['xianz'],'xianz_num'=>$_POST['xianz_num'],'showtype'=>$_POST['showtype'], 'top'=>$_POST['top'],'arid'=>$arid,'domain_id'=>$Aconf['domain_id']);
        $vtid = $tlkid = $oPub->install($db_table,$Afields);
		if($arid > 0){
			$Afields=array('vtid'=>$vtid );
			$condition = "arid = ".$arid;
			$oPub->update($pre."artitxt",$Afields,$condition); 
		}
		$strMessage = '添加成功';
	    
	} else if($_POST['action'] == 'edit' && $_POST['vtid'] ) {
        $Afields=array('vt_name'=>$_POST['vt_name'],'vt_desc'=>$_POST['vt_desc'],'is_show'=>$_POST['is_show'],'xianz'=>$_POST['xianz'],'xianz_num'=>$_POST['xianz_num'],'showtype'=>$_POST['showtype'],'top'=>$_POST['top'],'arid'=>$arid);
	    $condition = "vtid = ".$_POST['vtid']." AND domain_id=".$Aconf['domain_id'];
	    $oPub->update($db_table,$Afields,$condition); 
		if($arid > 0){
			$Afields=array('vtid'=>$vtid );
			$condition = "arid = ".$arid;
			$oPub->update($pre."artitxt",$Afields,$condition); 
		}
	}

	if($arid > 0 ){
		$sql = "UPDATE ".$pre."artitxt SET vtid=$vtid  WHERE arid= '".$arid."'";
		$oPub->query($sql);
	}
	unset($Anorm);unset($_POST);
}

//get 
if( $_GET['action'] == 'edit'){
	$_GET['vtid'] = $_GET['vtid'] + 0;
	$sql = "SELECT * FROM ".$pre."vote_title where vtid = ".$_GET['vtid']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	$_GET['vtid'] = $_GET['vtid'] + 0; 
    $sql = "UPDATE ".$pre."vote_title SET states='1' ".
            " WHERE vtid= '".$_GET['vtid']."' AND domain_id='".$Aconf['domain_id']."'";
    $oPub->query($sql);
 
    $sql = "UPDATE ".$pre."vote_item SET states='1' ".
            " WHERE vtid='".$_GET['vtid']."' AND domain_id='".$Aconf['domain_id']."'";
    $oPub->query($sql);

}

/* 是否显示 */
$db_table = $pre."vote_title";

if($Anorm["vtid"]){
	$is_show_1 = ($Anorm[is_show] == 1)? 'SELECTED':'';
	$is_show_0 = ($Anorm[is_show] == 0)? 'SELECTED':'';
}else{ 
	$is_show_1 = 'SELECTED';
}

$Stris_showopt = '<SELECT name="is_show">';
$Stris_showopt .= '<OPTION VALUE="1" '.$is_show_1.'>是</OPTION>';
$Stris_showopt .= '<OPTION VALUE="0" '.$is_show_0.'>否</OPTION>';
$Stris_showopt .= '</SELECT>';

//page
$strWhere = " WHERE states = 0 AND domain_id=".$Aconf['domain_id'];
$sql = "SELECT count( * ) AS count FROM ".$pre."vote_title".$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."vote_title".$strWhere." ORDER BY top desc,vtid desc limit ".$strOffSet;
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>'.$value["vt_name"];
	   $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=right> '.$value["vt_nums"].'  | <a href="vote_group.php?vtid='.$value["vtid"].'">可选组编辑</a>';
	   $StrtypeAll .= '  | <a href="vote_item.php?vtid='.$value["vtid"].'"> 选项编辑</A></TD>';
	   $StrtypeAll .= '<TD align=left>'.sub_str(clean_html($value["vt_desc"]),20).'</TD>';
	   $tmp = ($value["is_show"])?'是':'否';
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["top"].'</TD>';

		if($value["xianz"] <1)
		{
			$tmp = '不限制';	   
		}elseif($value["xianz"] == 1)
		{
			$tmp = '启用IP限制 ['.$value["xianz_num"].']';
		}elseif($value["xianz"] == 2)
		{
			$tmp = '启用手机号限制['.$value["xianz_num"].']';
		}  
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
		if($value["showtype"] <1)
		{
			$tmp = '不在同一页';
		}else
		{
			$tmp = '在同一页';
		}

	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
	   //所属文章
	   $name = '';
	   if($value["arid"]){
			$sql = "SELECT name FROM ".$pre."artitxt where arid=".$value["arid"];  
			$name = $oPub->getOne($sql);
			$name = '<a href="articlesend.php?arid='.$value["arid"].'&action=edit">'.$name.'</a>';
	   }
	   $StrtypeAll .= '<TD align=left>'.$name.'</TD>';
       $StrtypeAll .= '<TD align=left><a href="../vote.php?vtid='.$value["vtid"].'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a> ';
       $StrtypeAll .= ' __ <a href="'.$_SERVER["PHP_SELF"].'?vtid='.$value["vtid"].'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	   $StrtypeAll .= ' __ <a href="'.$_SERVER["PHP_SELF"].'?vtid='.$value["vtid"].'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';
       $StrtypeAll .= '</TR>';  	   
}

//是否为文章调查
$articelname = '';
if($arid > 0 ){
	$sql = "SELECT name FROM ".$pre."artitxt where arid=$arid";  
    $articelname = $oPub->getOne($sql);
	$articelname = '<span style="color:#f00">[<a href="articlesend.php?arid='.$arid.'&action=edit">'.$articelname.'</a>]</span>';
}

?>

<?php
include_once( "header.php"); 
if ($strMessage != '') {
 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
} 
?>
<DIV class=content>
<table width="100%" border="0" cellspacing="1" cellpadding="1" class="button">
<tr>
	<td align="right">
	<?php
	if($vtid){
		$str =  '调查项编辑';
		$str .=  ' _ <a href="vote_item.php?vtid='.$vtid.'">选项编辑</A>';
		$str .= ' _ <a href="vote_group.php?vtid='.$vtid.'">可选组编辑</a>';
		echo $str;
	}
	?>
	</td>
</tr>
</table>
<TABLE width="100%" border=0>
  <TR> 
    <TD align="left" colspan="9"> 
		<form name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>"> 
			<span style="font-weight: bold">调查项目标题:</span><?php echo $articelname;?>
				<input name="vt_name" type="text" value="<?php echo ($Anorm['vtid'])?$Anorm['vt_name']:''?>" size="50" />
			<span style="font-weight: bold">是否显示:</span>
				<?php echo $Stris_showopt;?>
			<span style="font-weight: bold">排序:</span>
				<input name="top" type="text" value="<?php echo ($Anorm['vtid'])?$Anorm['top']:''?>" size="2" />
			<br/>
			<span style="font-weight: bold">限制投票:</span>  
				<INPUT TYPE="radio" NAME="xianz" value="0" <?php echo ($Anorm['xianz'] <1?'checked':'');?>>不限制
				<INPUT TYPE="radio" NAME="xianz" value="1" <?php echo ($Anorm['xianz'] ==1?'checked':'');?>>启用IP限制
				<INPUT TYPE="radio" NAME="xianz" value="2" <?php echo ($Anorm['xianz'] ==2?'checked':'');?>>只允许登录帐号投票   
	 
			<B>启用限制后，24小时允许投票次数:</B><INPUT TYPE="text" NAME="xianz_num" value="<?php echo ($Anorm['xianz_num'] < 1?1:$Anorm['xianz_num'])?>" style="width:20px">
			<br/>
			<span style="font-weight: bold">选项与投票与结果显示在同一页:</span>
				<INPUT TYPE="radio" NAME="showtype" value="0" <?php echo ($Anorm['showtype'] < 1?'checked':'');?>>不在同一页
				<INPUT TYPE="radio" NAME="showtype" value="1" <?php echo ($Anorm['showtype'] == 1?'checked':'');?>>在同一页显示

			<br/>
			<span style="font-weight: bold">描述:</span>
  
			<textarea name="vt_desc" style="width:750px;height:400px;visibility:hidden;"><?php echo $Anorm["vt_desc"];?></textarea>
			<br/>
			<input type="hidden" name="action" value="<?php echo ($Anorm['vtid'])?'edit':'add'?>" />
			<input type="submit" name="Submit" value="<?php echo ($Anorm['vtid'])?' 调查编辑修改 ':' 调查增加 ' ?>" style="background-color: #FFCC66"/>
			<input type="hidden" name="vtid" value="<?php echo ($Anorm['vtid'])?$Anorm['vtid']:'0'?>" />  
			<input type="hidden" name="arid" value="<?php echo ($Anorm['vtid'])?$Anorm['arid']:$arid;?>" /> 
		</form>
    </TD>
    
  </TR>	
  <TR class=bg5>
    <TD align=left>标题</TD>
    <TD align=right width="200">合计票数 | 可选组编辑 | 选项编辑  </TD>
	<TD align=left >描述</TD>
	<TD align=left>显示</TD>
	<TD align=left>排序</TD>
	<TD align=left>投票限制</TD>
	<TD align=left>显示方式</TD>
	<TD align=left >所属文章</TD>
    <TD align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="9" align=right><?php echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
 
</DIV>

<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script>
<script>
	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="vt_desc"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=vote_title',
			fileManagerJson : '../upload_manager_json.php?jsonop=vote_title',
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