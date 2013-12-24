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

$db_table = $pre."sysconfig";
if( $action == 'edit' && $scid > 0){ 
		$oPub->query("UPDATE " . $pre."sysconfig SET  notices='$notices' WHERE  scid=".$Aconf['domain_id']); 
		/* 处理相册图片 */
		/* 编辑图片描述 old_img_desc */
		if (isset($_POST['old_img_desc']))
		{
			foreach ($_POST['old_img_desc'] AS $key => $val)
		   {
				$oPub->query("UPDATE " . $pre."arti_file SET `descs` = '$val' WHERE `fileid` =".$key); 
		   }
		}
		$type='notice';$arid =$Aconf['domain_id'];
		handle_gallery_image($arid, $_FILES[img_url], $namedesc,$type); 
		$strMessage = '公告编辑成功'; 
}
/* 网站配置信息 */ 
$Anorm = $oPub->getRow("SELECT scid,notices FROM ".$pre."sysconfig WHERE scid = ".$Aconf['domain_id']." ORDER BY scid ASC LIMIT 1"); 
$img_list = $oPub->select("SELECT * FROM " . $pre."arti_file WHERE type = 'notice' and domain_id=".$Aconf['domain_id']); 
?>
<?php
	include_once( "header.php");
	if ($strMessage != '') {
		 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
	}
?>


<DIV class=content>
  <form name="form1" method="post" action="" enctype="multipart/form-data" style="margin: 0px">
	<TABLE width="800" border=0>
	  <TR > 
		<TD width="800" align="left" > 
			 <textarea name="notices" style="width:750px;height:400px;visibility:hidden;"><?php echo $Anorm['notices'];?></textarea>
		</TD> 
	  </TR>	 
	</TABLE>
	<!-- 附件 start --> 
	<DIV id=tabbar-div></DIV><!-- tab body -->
	<TABLE id=gallery-table width="100%" align=center cellspacing="0" cellpadding="0">
	 <tr>
		<td>
			<B>管理图片列表：</B> 
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
		</TD> 
	  </TR> 
	  <TR>
		<TD>
			<A onclick=addImg(this) href="javascript:;">[+]</A> 
			图片描述:<INPUT TYPE="text" NAME=namedesc[] value="" style="width:160px;"/>
			地址:<INPUT type=file name=img_url[] id="fileToUpload" style="width:160px;">
		</TD>
	  </TR>
	  <tr>
	  <td>
			<input type="hidden" name="action" value="<?php echo ($Anorm['scid'])?'edit':'add';?>" />
			<input type="submit" name="Submit" value="<?php echo ($Anorm['scid'])?'公告编辑提交':'增加';?>" style="margin-left:80px;background-color: #FFCC66"/>
			<input type="button" name="clear" value="清空内容" style="background-color: #FFCC66"/> 
			<input type="hidden" name="scid" value="<?php echo ($Anorm['scid'])?$Anorm['scid']:'0';?>" />  
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
		var strTemp = "ajax_arti_delimg.php?fileid=" + fileid +  "&op=delimg&action=notice";  
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
			uploadJson : '../upload_json.php?jsonop=notice',
			fileManagerJson : '../upload_manager_json.php?jsonop=notice',
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
