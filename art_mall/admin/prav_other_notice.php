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

if( $_SESSION['apraid'] < 1)
{
   $strMessage = '此账号没有绑定经销商，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">多管理员权限->管理员权限设置 ->指定管理经销商</a>';
   echo  showMessage($strMessage);
   exit;
}

 $praid = $oPub->getOne("SELECT praid FROM ".$pre."pravail WHERE praid = ".$_SESSION['apraid']." ORDER BY praid ASC LIMIT 1"); 
 if( $praid < 1)
 {
	$strMessage = '此经销商已不存在，不能操作！请通过管理员设置.<br/><br/><a href="adminuser.php">多管理员权限->管理员权限设置 ->指定管理经销商</a>';
	echo  showMessage($strMessage);
	exit;
}

$db_table = $pre."pravail";
if( $_POST['action'] == 'edit' && $_POST['praid'] > 0){

        $sql = "UPDATE " . $db_table . " SET  notices='$_POST[notices]' WHERE  praid='".$_SESSION['apraid']."'";
        $oPub->query($sql);
		/* 处理相册图片 */
		/* 编辑图片描述 old_img_desc */
		if (isset($_POST['old_img_desc']))
		{
			foreach ($_POST['old_img_desc'] AS $key => $val)
		   {
				$sql = "UPDATE " . $pre."arti_file SET `descs` = '$val' WHERE `fileid` =".$key;
				$oPub->query($sql);              
		   }
		}
		$type='pra_notices';$arid =$_SESSION['apraid'];
		handle_gallery_image($arid, $_FILES['img_url'], $_POST['namedesc'],$type); 
		$strMessage = '公告编辑成功'; 
}
/* 网站配置信息 */
$db_table = $pre."pravail";
$sql = "SELECT praid,pra_name,notices FROM ".$db_table." WHERE praid = '".$_SESSION['apraid']."' ORDER BY praid ASC LIMIT 1";
$Anorm = $oPub->getRow($sql);
$sql = "SELECT * FROM " . $pre."arti_file WHERE type = 'pra_notices' and  arid=".$_SESSION['apraid'];
$img_list = $oPub->select($sql); 
?>
<?php
include_once( "header.php");
if ($strMessage != '')
{
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<DIV class=content>
<form name="form1" method="post" action="" enctype="multipart/form-data" style="margin: 0px">
<TABLE width="800" border=0>
  <TR> 
    <TD  align="left" > 
        <span style="font-weight:bold"><?php echo $Anorm['pra_name'];?> 公告：</span> 
		<textarea name="notices" style="width:750px;height:400px;visibility:hidden;"><?php echo $Anorm['notices'];?></textarea>  
    </TD> 
  </TR>	 
</TABLE>
<!-- 附件 start --> 
<DIV id=tabbar-div></DIV><!-- tab body -->
<TABLE id=gallery-table width="100%" align=center cellspacing="0" cellpadding="0">
 <tr>
   <td>
    文章相册：(<U>1.点“加号”可以批量上传多张图片；2.点提交按钮提交图片；3.单击相册列表中的缩图，可把原图添加到编辑器中</U>) 
		<div id="delimg_show" style="margin: 0">
			<?php while( @list( $k, $v ) = @each( $img_list) ) { ?>
				<div id="gallery_<?php echo $v['fileid'];?>" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;width:122px;height:130px">
					<a href="javascript:;" onclick="if (confirm('删除')) dropImg('<?php echo $v['fileid'];?>','<?php echo $v['arid'];?>')" title="删除">[-]</a>
					<a href="../<?php echo $v['filename'];?>" target="_blank" title="查看原信息:<?php echo $v['descs'];?>">[>]</a>  
					<br />  
					<?php if($v['thumb_url'] != '') { ?>
						 <img src="../<?php  echo $v['thumb_url'];?>" width="120" height="90"  border="0" title="插入编辑器" onclick="insertHtml('<?php  echo $Aconf['domain_url'].$v['filename'];?>','<?php  echo $v['descs'];?>')" />
					<?php } else {?>
						 <div style="width:120px;height:90px;background-color:#E4E4E4"><br/><br/><a href="../<?php  echo $v['filename'];?>" target="_blank">查阅>></a></div>
					<?php  }?> 
					<input type="text" value="<?php echo $v['descs'];?>" size="15" name="old_img_desc[<?php echo $v['fileid'];?>]" />
				</div>
			<?php } ?>
		</div>
	 </div>
    </TD> 
  </TR> 
  <TR>
  <TD>
	<A onclick=addImg(this) href="javascript:;">[+]</A> 
	图片描述:<INPUT TYPE="text" NAME=namedesc[] value="" size="30"/>
	地址:<INPUT type=file name=img_url[] id="fileToUpload" >
	</TD>
  </TR>
  <tr>
	<td>
		<input type="hidden" name="action" value="<?php echo ($Anorm['praid'])?'edit':'add';?>" />
		<input type="submit" name="Submit" value="<?php echo ($Anorm['praid'])?'公告编辑提交':'增加';?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="praid" value="<?php echo ($Anorm['praid'])?$Anorm['praid']:'0';?>" />  		
	</td>
  </tr>
</TABLE> 
</form>
  <!-- 附件 end --> 
</DIV>
<SCRIPT src="js/tab.js" type="text/javascript"></SCRIPT>
<SCRIPT src="js/utils.js" type="text/javascript"></SCRIPT>	
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<!--以下的两个script为添加的ajax上传-->
<SCRIPT language=JavaScript>
//<![CDATA[ 
  function addImg(obj)
  {
	  id="fileid"+document.getElementsByTagName('input').length;
	  iid=document.getElementsByTagName('input').length+1;
	  kid="imgid"+iid;
      var src  = obj.parentNode.parentNode;
      var idx  = rowindex(src);
      var tbl  = document.getElementById('gallery-table');
      var row  = tbl.insertRow(idx + 1);
      var cell = row.insertCell(-1);

      cell.innerHTML = src.cells[0].innerHTML.replace(/(.*)(addImg)(.*)(\[)(\+)/i, "$1removeImg$3$4-");
  }

  /**
   * ~{I>3}M<F,IO4+~}
   */
  function removeImg(obj)
  {
      var row = rowindex(obj.parentNode.parentNode);
      var tbl = document.getElementById('gallery-table');

      tbl.deleteRow(row);
  }

  /**
   * ~{I>3}M<F,~}
   */
  function dropImg(fileid,arid)
  {
    obj = "delimg_show";
	var strTemp = "ajax_arti_delimg.php?fileid=" + fileid +  "&op=delimg&action=pra_notices";  
	send_request(strTemp);	
  }
 
 
  function style_display(a)
  {   obj = a; 
       document.getElementById(obj).innerHTML="";
  }
   
	function countChar(textareaName,spanName) 
	{   
		document.getElementById(spanName).innerHTML =  document.getElementById(textareaName).value.length; 
	}   
	 
	   
//]]> 
</SCRIPT>

<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script>
<script>
	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="notices"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=pra_notices&arid=<?php echo $_SESSION['apraid'];?>',
			fileManagerJson : '../upload_manager_json.php?jsonop=pra_notices&arid=<?php echo $_SESSION['apraid'];?>',
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
