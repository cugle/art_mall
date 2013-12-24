<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  
if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."product_comms";
//post
if( $_POST['action'] == 'add' )
{
   $sql = "INSERT INTO " . $db_table . " ( descs ,  ip , email ,  dateadd , states , `domain_id` )" .
           "VALUES ('$_POST[descs]', '".real_ip()."', '$_POST[email]','".gmtime()."', '3','".$Aconf['domain_id']."')"; 
   $oPub->query($sql);
   $strMessage =  "添加成功!";

}

if( $_POST['action'] == 'edit')
{
	$_POST[prcid] = $_POST[prcid] +0;

	$sql = "UPDATE " . $db_table . " SET descs = '$_POST[descs]',email='$_POST[email]',states=3 
	WHERE domain_id = ".$Aconf['domain_id']." and prcid = ".$_POST[prcid];
    $oPub->query($sql);  
	$strMessage = "编辑回复成功";
	unset($_GET);
}

//get
if( $_GET['action'] == 'edit'){
	$_GET['prcid'] = $_GET['prcid'] + 0;
	$sql = "SELECT * FROM ".$db_table." 
	       WHERE prcid = ".$_GET['prcid'].
		   " AND domain_id=".$Aconf['domain_id'];
	$Acomm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	$_GET['prcid'] = $_GET['prcid'] + 0;
    $condition = 'prcid='.$_GET['prcid'];
    $oPub->delete($db_table,$condition);
	/* 文章评论记录 -1 */
	$db_table = $pre."producttxt";
    $sql = "UPDATE " . $db_table . " SET comms =comms -1 
	        WHERE domain_id = ".$Aconf['domain_id']." and prid = ".$_GET['prid'];
    $oPub->query($sql);

}

;
/* 批量删除 */
if ($_REQUEST['action'] == 'del_checkbox')
{
    if (isset($_POST['checkboxes']))
    {
        $count = 0;
        foreach ($_POST['checkboxes'] AS $key => $id)
        {
			$id = $id +1;
			$db_table = $pre."product_comms";
            $sql = "UPDATE " . $db_table . " SET states=1 
	         WHERE domain_id = ".$Aconf['domain_id']." and prcid = ".$key;
            $tmp = $oPub->query($sql);
	        /* 文章评论记录 -1 */
	        $db_table = $pre."producttxt";
            $sql = "UPDATE " . $db_table . " SET comms =comms -1 
	                WHERE domain_id = ".$Aconf['domain_id']." and prid = ".$id;
            $oPub->query($sql);
		}
	}
}


//page
$db_table = $pre."product_comms";
$strWhere = " WHERE states <> 1 AND domain_id=".$Aconf['domain_id'];
$sql = "SELECT count( * ) AS count FROM ".$db_table.$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array(); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.$strWhere." ORDER BY prcid desc limit ".$strOffSet;
$AcommAll = $oPub->select($sql);

$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AcommAll) ) {
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$value["prcid"].']" value="'.$value["prid"].'" />';
	   $StrtypeAll .= '</TD>';
	   /* 文章标题 */
	   $db_table = $pre."producttxt";
	   $sql = "SELECT name FROM ".$db_table." WHERE prid = ".$value["prid"];
       $row = $oPub->getRow($sql);
	   if($row){
		   $name = '文章:<A HREF="../product.php?prid='.$value["prid"].'" target="_blank">'.$row[name].'</A><br/>';
	   }else{
		   $name = '无对应文章<br/>';
	   }
	   $descs = $name .sub_str(clean_html($value["descs"]),60);

       $StrtypeAll .= '<TD align=left>'.$descs.'</TD>';
       $StrtypeAll .= '<TD align=left>'.$value["email"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["ip"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.date("Y-m-d h:i", $value[dateadd]).'</TD>';
	   $states = ($value[states]==3)?'已审核':'';
	   $StrtypeAll .= '<TD align=left>'.$states.'</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?prcid='.$value["prcid"].'&prid='.$value["prid"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑回复]"></a> ';
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?prcid='.$value["prcid"].'&prid='.$value["prid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
	   
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
    <TD width="40%" align=left>内容</TD>
	<TD width="5%" align=left>EMAIL</TD>
	<TD width="5%" align=left>ip</TD>
	<TD width="20%" align=left>日期</TD>
	<TD width="10%" align=left>状态</TD>
    <TD width="15%" align=left>操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="7" align=right>
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
  <?php
  if($Acomm['prcid'])
  {
  ?>
  <TR class="odd" >
  <form name="form1" method="post" action=""> 
    <TD width="13%" align="left" colspan="7">
        <span>EMAIL:</span>
		<input name="email" type="text" size="50" value="<?php echo ($Acomm['prcid'])?$Acomm['email']:''?>" /><br/>
       <?php
		$descs1 = ($Acomm[states]==0 AND $Acomm['prcid'] > 0)?$Acomm['descs'].'<hr><B>回复:</B>':$Acomm['descs']; 
	   ?> 
	    <textarea name="descs" style="width:750px;height:300px;visibility:hidden;"><?php echo $descs1;?></textarea>	   
		<br/>
		<input type="submit" name="Submit" value="<?php echo ($Acomm['prcid'])?'编辑回复':'增加文章评论'?>" style="background-color: #FFCC66"/>
        <input type="hidden" name="action" value="<?php echo ($Acomm['prcid'])?'edit':'add'?>" />        
		<input type="hidden" name="prcid" value="<?php echo ($Acomm['prcid'])?$Acomm['prcid']:'0'?>" />
		<input type="hidden" name="re_prcid" value="<?php echo ($Acomm['prcid'])?$Acomm['prcid']:'0'?>" />
		<br/>
    </TD>
    </form>
  </TR>	
  <?php } ?>
</TABLE> 
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
		editor = K.create('textarea[name="descs"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=pcomms',
			fileManagerJson : '../upload_manager_json.php?jsonop=pcomms',
			allowFileManager : true,
            width : '750px',
            height: '300px',
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
