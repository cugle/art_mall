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

if($_POST['act'] == 'insert' || $_POST['act'] == 'update' )
{
	$is_insert   = $_POST['act'] == 'insert';
	$myuser_id   = $_SESSION['auser_id'];
    if(trim($name) == '' )
	{
		$strMessage = '标题及内容不能为空';
	} else
	{

 	    /* 处理主图 */
		$arti_thumb = $_POST['old_arti_thumb'];
		$min_thumb = $_POST['old_min_thumb'];
	    if($_FILES["arti_thumb"]["size"] > 0 )
	    {
		    /* 判断图像类型 */
            if (!$image->check_img_type($_FILES['arti_thumb']['type']))
            {
                $strMessage =  '图片类型错误,只支持 .jpg,.gif,.png格式.\n';
            } else
		    {
			    if($_FILES["arti_thumb"]["size"] > settype($_POST['MAX_FILE_SIZE'], "integer"))
			    {
				    $strMessage =  '文件太大，不能上传：最大为2M.\n';
			    } else
			    {		
					$img_width = ($_POST['img_width'] > 0 )?$_POST['img_width']:$Aconf['big_thumb_w'];
					$img_height = ($_POST['img_height'] > 0 )?$_POST['img_height']:$Aconf['big_thumb_h'];

					if($img_width >= $img_height ) {
					 $img_width_big  = $img_width;
					 $img_height_big = intval($img_width * $img_height/$img_width);
					}  else {
					 $img_width_big  = intval($img_height * $img_width/$img_height);
					 $img_height_big = $img_height; 
					}

					if($Aconf['min_thumb_w'] >= $Aconf['min_thumb_h'] ) {
					 $img_width_min  = $Aconf['min_thumb_w'];
					 $img_height_min = intval($Aconf['min_thumb_w'] * $Aconf['min_thumb_h']/$Aconf['min_thumb_w']);
					}  else {
					  $img_width_min  = intval($Aconf['min_thumb_h'] * $Aconf['min_thumb_w']/$Aconf['min_thumb_h']);
					  $img_height_min = $Aconf['min_thumb_h']; 
					}
					/* 生成缩略图 */
					$arti_thumb = $image->make_thumb($_FILES["arti_thumb"]['tmp_name'], $img_width_big , $img_height_big);
					/* 像册 */
					$thumb_url = $image->make_thumb($_FILES["arti_thumb"]['tmp_name'],  $img_width_min , $img_height_min);
					$min_thumb = $thumb_url;
					/* 原图 */
					$filename = $image->upload_image($_FILES["arti_thumb"]); 
			     }
		     }		  
	     } 

	     /* 像册图片处理 star */
         /* 检查图片：如果有错误，检查尺寸是否超过最大值；否则，检查文件类型 */
         if (isset($_FILES['img_url']['error'][0])) // php 4.2 版本才支持 error
         {
             // 最大上传文件大小
             $php_maxsize = ini_get('upload_max_filesize');
             $htm_maxsize = '2M';
             // 相册图片
             foreach ($_FILES['img_url']['error'] AS $key => $value)
             {				
				if ($value == 0)
				{		
					if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
					{
					   $strMessage = '文件类型错误:'.$key;
					   break;
					}
				} elseif ($value == 1)
				{
					$strMessage = '文件太大:'.$key.' '.$php_maxsize;
					break;
				} elseif ($_FILES['img_url']['error'] == 2)
				{
					$strMessage = '文件太大:'.$key.' '. $htm_maxsize;
					break;
				}
             }
	      }
          /* 4。1版本 */
          else
          {
              // 相册图片
		      while( @list( $key, $value ) = @each( $_FILES['img_url']['tmp_name']) )
              {			
                  if ($value != 'none')
                  {				
                      if (!$image->check_img_type($_FILES['img_url']['type'][$key]))
                      {
                          $strMessage = '文件无效:'. $key + 1;
					      break;
                       }
                    }
                }
           }
	      /* 像册图片处理 end */

      //数据添加
	  if($is_insert)
	  {
	    /* 入库 */ 
		$sql = 'INSERT INTO '.$pre.'pravail_artitxt( user_id,praid, name,dateadd ,min_thumb,arti_thumb, states , domain_id )VALUES ("'.$myuser_id.'","'.$_SESSION['apraid'].'","'.$name.'","'.gmtime().'","'.$min_thumb.'","'.$arti_thumb.'",0,"'.$Aconf['domain_id'].'")'; 
        $oPub->query($sql);
        /* 编号 */
        $arid = $is_insert ? $oPub->insert_id() : $arid;
 
		$sql = 'INSERT INTO '. $pre.'pravail_article( arid , user_id , praid, name , descs ,dateadd , domain_id  )VALUES ("'.$arid.'","'.$myuser_id.'","'.$_SESSION['apraid'].'","'.$name.'","'.$descs.'","'.gmtime().'","'.$Aconf['domain_id'].'")'; 
        $oPub->query($sql);
		/* tag */

		/* 相册 */
		if($filename && $thumb_url)
		{ 
		     $oPub->query('INSERT INTO '.$pre.'pravail_arti_file( arid,filename,thumb_url,domain_id )VALUES ("'.$arid.'","'.$filename.'", "'.$thumb_url.'", "'.$Aconf['domain_id'].'")');  
		}
		$strMessage = '添加成功!';

	  } else if($_POST['act'] == 'update' && $arid > 0)
	  {
 
		$arid = $arid+0;
        $oPub->query('UPDATE '.$pre.'pravail_artitxt SET user_id="'.$myuser_id.'", name="'.$name.'",min_thumb="'.$min_thumb.'",arti_thumb="'.$arti_thumb.'" WHERE arid ="'.$arid.'" and domain_id="'.$Aconf['domain_id'].'"');  
        $oPub->query('UPDATE '.$pre.'pravail_article SET user_id="'.$myuser_id.'",name ="'.$name.'",descs="'.$descs.'" WHERE arid ="'.$arid.'" and domain_id="'.$Aconf['domain_id'].'"'); 
		/* 相册 */
		if($filename && $thumb_url)
		{ 
		     $oPub->query('INSERT INTO '.$pre.'pravail_arti_file( arid ,  filename , thumb_url,domain_id  )VALUES ("'.$arid.'","'.$filename.'","'.$thumb_url.'","'.$Aconf['domain_id'].'")');  
		}
		/* 编辑图片描述 old_img_desc */
		if (isset($_POST['old_img_desc']))
        {
			foreach ($_POST['old_img_desc'] AS $key => $val)
			{ 
				$oPub->query('UPDATE '. $pre.'pravail_arti_file SET descs = "'.$val.'" WHERE fileid ="'.$key.'"');  
			}
		}
        $strMessage = '修改成功！';
	  }
 	  /* 处理相册图片 */
      handle_gallery_imageprav($arid, $_FILES['img_url'], $_POST['namedesc']);
    }//if(trim($name) == '')

	if($arid > 0)
	{
		$oPub->query( "update " . $pre."pravail_arti_file set arid=$arid where user_id=".$_SESSION['auser_id']." and arid=".$Aconf['domain_id']."  and domain_id =".$Aconf['domain_id']);
	}  

	$_REQUEST['arid'] = $arid;
	$_REQUEST['action'] = 'edit';

}


if($_REQUEST['action'] == 'edit' && $_REQUEST['arid'])
{ 
	$arid = $_REQUEST['arid'];
	$work = $oPub->getRow('SELECT a.*,b.descs FROM '.$pre.'pravail_artitxt as a,'.$pre.'pravail_article as b 
			where a.arid = b.arid AND a.arid = "'.$arid.'" AND a.domain_id="'.$Aconf['domain_id'].'"');  
	/* 像册列表 */
    $img_list = $oPub->select('SELECT * FROM '. $pre.'pravail_arti_file WHERE arid = "'.$arid.'"'); 
}

?>
<?php
include_once( "header.php"); 
if ($strMessage != '') {
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>

 
<table width="800" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <td align="middle">
	       <span style="float: left"><a href="prav_articlesend.php"> [添加促销信息]</a> </span>
	   <span style="float: right"> <a href="prav_articlelist.php"> [促销信息文章列表]</a></span>
 </td>
</tr>
</table>
<form action="" method="post" name="theForm" enctype="multipart/form-data" onsubmit="return validate();">
<TABLE width="800" border=0> 
  <TR class=bg1>
    <TD align=left> 
         <b>标题:</b><input type="text" name="name" value="<?php echo ($work["arid"] > 0)?$work["name"]:'';?>" size="40"/>
 
		 <b>缩图:</b>
		 <input type="file" name="arti_thumb"  size="20"/>
		 <span id="arti_thumb_show">
         <?php 
		 if($work["arti_thumb"])
		 {
			 $tmp = '<A HREF="../'.$work["arti_thumb"].'" target="_blank">';
			 $tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="显示缩图"></A> ';
			 $tmp .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) drop_pravail_artitxtImg(\''.$work["arid"].'\',\''.$work["arti_thumb"].'\')">';
			 $tmp .= '<IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除缩图"></A> ';
		     $tmp .= '<input type="hidden" name="old_arti_thumb" value="'.$work["arti_thumb"].'" />';
			 $tmp .= '<input type="hidden" name="old_min_thumb" value="'.$work["min_thumb"].'" />';
		 } else
		 {
             $tmp = '<input type="hidden" name="old_arti_thumb" value="" />';
			 $tmp .= '<input type="hidden" name="old_min_thumb" value="" />';
		 }
		 echo $tmp;
		 ?>
		 </span>
		 (注：缩图尺寸<?php echo $Aconf['big_thumb_w'].' × '.$Aconf['big_thumb_h'];?>)
 
	</TD> 
  </TR>
  <TR class=bg1>
    <TD align=left>
	  <textarea name="descs" style="width:750px;height:450px;visibility:hidden;"><?php echo $work["descs"];?></textarea> 
    </TD> 
  </TR>
</TABLE>
<!-- 附件 start -->
<DIV id=tabbar-div></DIV><!-- tab body -->
<TABLE id=gallery-table width="100%" align=center cellspacing="0" cellpadding="0">
 <tr>
   <td>
   相册：(<U>1.点“加号”可以批量上传多张图片；2.点提交按钮提交图片；3.单击相册列表中的缩图，可把原图添加到编辑器中</U>) 
     <div id="delimg_show" style="margin: 0"> 

		<?php while( @list( $k, $v ) = @each( $img_list) ) { ?>
			<div id="gallery_<?php echo $v['fileid'];?>" style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;width:122px;height:130px">
				<a href="javascript:;" onclick="if (confirm('删除')) dropImg('<?php echo $v['fileid'];?>','<?php echo $v['arid'];?>')" title="删除">[-]</a>
				<a href="../<?php echo $v['filename'];?>" target="_blank" title="查看原信息:<?php echo $v['descs'];?>">[>]</a>  
				<br />  
				<?php if($v['thumb_url'] != '') { ?>
					 <img src="../<?php  echo $v['thumb_url'];?>" width="120" height="90"  border="0" title="插入编辑器" onclick="insertHtml('<?php  echo  '../'.$v['filename'];?>','<?php  echo $v['descs'];?>')" />
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
	描述:<INPUT TYPE="text" NAME=namedesc[] value="" size="30"/>
	地址:<INPUT type=file name=img_url[]> 
	</TD>
  </TR>
</TABLE>
  <!-- 附件 end -->
<TABLE width="96%" border=0>
  <TR class=even>
    <TD align=left>
	 <div id="cltion">
	 <?php echo $strCltion;?>
	 </div>
	 <div id="cltion_product">
	 <?php echo $strCltion_product;?>
	 </div>	
    </TD> 
  </TR>
  <TR class=bg1>
    <TD align=left>
       <input type="submit" value="<?php echo ($work["arid"] > 0)?'修改文章':'提交新文章';?>" style="background-color: #FFCC66;margin-left: 100px"/>
	   <input type="hidden" name="arid" value="<?php echo ($work["arid"] > 0)?$work["arid"]:0;?>" id="arid" />
       <input type="hidden" name="act" value="<?php echo ($work["arid"] > 0)?'update':'insert';?>" /> 
    </TD> 
  </TR> 
 </table>
</form>
<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script> 

<SCRIPT src="js/tab.js" type="text/javascript"></SCRIPT>
<SCRIPT src="js/utils.js" type="text/javascript"></SCRIPT>	
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<SCRIPT language=JavaScript>
  function addImg(obj)
  {
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
	var strTemp = "ajax_prav_arti_delimg.php?fileid=" + fileid + "&arid=" + arid + "&op=delimg&action=edit";
	//alert(strTemp);
	//document.getElementById('gallery_' + fileid).style.display = 'none';
	send_request(strTemp);	
  }
  function drop_pravail_artitxtImg(arid,arti_thumb_file)
  {
    obj = "arti_thumb_show";
	var strTemp = "ajax_prav_arti_delimg.php?arid=" + arid + "&arti_thumb_file=" + arti_thumb_file;
	//alert(strTemp);
	//document.getElementById('gallery_' + fileid).style.display = 'none';
	send_request(strTemp);	
  }
 
function validate()
{
  var frm          = document.forms['theForm'];
  var name = document.getElementById("name").value;  

  var msg = '';
  var reg = null;

  if( name.length < 4) {
	msg += '请输入文章标题' + '\n';
  } 
  if (msg.length > 0) {
	alert(msg);
	return false;
  }  else
  {
	return true;
  }
} 

 	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="descs"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=particle&arid=<?php echo $arid;?>',
			fileManagerJson : '../upload_manager_json.php?jsonop=particle&arid=<?php echo $arid;?>',
			allowFileManager : false,
			width : '750px',
			height: '450px',
			resizeType: 0,
			items:['source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste','plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright','justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript','superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/','formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold','italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image','flash', 'media', 'insertfile', 'table', 'hr', 'emoticons', 'baidumap', 'pagebreak','anchor', 'link', 'unlink'],

			afterCreate : function() {
				var self = this;
				K.ctrl(document, 13, function() {
					self.sync();
					K('form[name=theForm]')[0].submit();
				});
				K.ctrl(self.edit.doc, 13, function() {
					self.sync();
					K('form[name=theForm]')[0].submit();
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

</SCRIPT>
<?php 
/*
 * 保存某商品的相册图片
 * @param   int     $workid
 * @param   array   $image_files
 * @param   array   $image_descs
 * @return  void
 */
function handle_gallery_imageprav($arid, $image_files, $image_descs)
{
	global $image,$oPub,$pre,$Aconf;
	$imgType = array(1 => 'image/gif', 2 => 'image/jpeg', 3 => 'image/png',4 => 'image/pjpeg');
	while( @list( $key, $img_desc ) = @each( $image_descs) )
    {
        /* 是否成功上传 */
        $flag = false;
        if (isset($image_files['error']))
        {
            if ($image_files['error'][$key] == 0)
            {
                $flag = true;
            }
        } else
        {
            if ($image_files['tmp_name'][$key] != 'none')
            {
                $flag = true;
            }
        }

        if ($flag)
        {
            // 生成缩略图
			//if( in_array($image_files['type'][$key],$imgType) )
			if($image->check_img_type($image_files['type'][$key]))
			{
				
               $thumb_url = $image->make_thumb($image_files['tmp_name'][$key],$Aconf['min_thumb_w'],$Aconf['min_thumb_h']);
			}
            $thumb_url = is_string($thumb_url) ? $thumb_url : '';

            $upload = array(
                'name' => $image_files['name'][$key],
                'type' => $image_files['type'][$key],
                'tmp_name' => $image_files['tmp_name'][$key],
                'size' => $image_files['size'][$key],
            );
            if (isset($image_files['error']))
            {
                $upload['error'] = $image_files['error'][$key];
            }
            $img_original = $image->upload_image($upload);
            $img_url = $img_original; 
            $oPub->query('INSERT INTO '.$pre.'pravail_arti_file(arid, filename,thumb_url,descs,domain_id)VALUES ("'.$arid.'","'.$img_url.'","'.$thumb_url.'","'.$img_desc.'","'.$Aconf['domain_id'].'")'); 
        }
    }
}

?>
<?php
include_once( "footer.php");
?>

