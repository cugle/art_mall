<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
//include_once($ROOT_PATH.'includes/ckeditor/ckeditor.php'); 
if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

/* 文章标题 */
if($_REQUEST['arid'])
{
   $arid = $_REQUEST['arid'] + 0 ;
   $db_table = $pre."artitxt";
    $sql = "SELECT name FROM ".$db_table." WHERE arid = '".$arid."'";
    $artitxtname = $oPub->getOne($sql);
    if($artitxtname) {
	    $artitxtname = '<A HREF="../article.php?id='.$arid.'" target="_blank">'.$artitxtname.'</A>';
    } else
	{
      $arid = 0;
	}
} else
{
    $arid = 0;
}


$db_table = $pre."arti_comms";
//post
if( $_POST['action'] == 'add' && $arid)
{
   $sql = "INSERT INTO " .$pre."arti_comms ( arid, top,descs , ip , email ,  dateadd , states , `domain_id` )" .
           "VALUES ('$arid','$_POST[top]','$_POST[descs]', '".real_ip()."', '$_POST[email]','".gmtime()."', '3','".$Aconf['domain_id']."')"; 
   $oPub->query($sql);
   $strMessage =  "添加成功!";

}

if( $_POST['action'] == 'edit' && $arid )
{
	$_POST[arcid] = $_POST[arcid] +0; 
	$oPub->query("UPDATE " .$pre."arti_comms SET top='$_POST[top]' ,descs = '$_POST[descs]',email='$_POST[email]',states=3 
	WHERE domain_id = ".$Aconf['domain_id']." and arcid = '".$_POST[arcid]."' and arid='".$arid."'"); 
	$strMessage = "编辑回复成功";
	unset($_GET);
} 
//get
if( $_GET['action'] == 'edit' && $arid){
	$_GET['arcid'] = $_GET['arcid'] +0 ;
	$Acomm = $oPub->getRow("SELECT * FROM ".$pre."arti_comms WHERE arcid = '".$_GET['arcid']."' and arid='".$arid."' AND domain_id='".$Aconf['domain_id']."'"); 
}

if( $_GET['action'] == 'del'){
	$_GET['arcid'] = $_GET['arcid'] + 0;
	$_GET['arid'] = $_GET['arid'] + 0;
    $condition = 'arcid='.$_GET['arcid'];
    $oPub->delete($db_table,$condition);
	/* 文章评论记录 -1 */ 
    $oPub->query("UPDATE " .$pre."artitxt SET comms =comms -1 WHERE domain_id = ".$Aconf['domain_id']." and arid = '".$arid."'"); 
}

/* 批量删除 */
if ($_REQUEST['action'] == 'del_checkbox')
{
    if (isset($_POST['checkboxes']))
    {
        $count = 0;
        foreach ($_POST['checkboxes'] AS $key => $id)
        { 
            $condition = "arcid='".$key."' and arid='".$id."'";
            $oPub->delete($pre."arti_comms",$condition); 
	        /* 文章评论记录 -1 */ 
            $oPub->query("UPDATE " .$pre."artitxt SET comms =comms -1 WHERE domain_id = ".$Aconf['domain_id']." and arid = '".$id."'"); 
		}
	}
} 
//page 
if($arid)
{
     $strWhere = " WHERE arid = '".$arid."' AND domain_id=".$Aconf['domain_id'];
} else
{
   //查找用户分类权限，找到对应文章评论   
   if ( $_SESSION['aaction_list'] == 'all')
   {
       $strWhere = " WHERE  domain_id='".$Aconf['domain_id']."' ";
   } else
   {  
      $strWhere = " WHERE  domain_id='".$Aconf['domain_id']."' ";
      $Aarticlecat_list = false;
      if(!empty($_SESSION['aarticlecat_list']))
      {
			//找到所有的文章分类权限,通过提交的分类查找包含的下级分类 
			$Aarticlecat_list = explode(',',$_SESSION['aarticlecat_list']);
			foreach ($Aarticlecat_list AS  $v)
			{
				$strAcid .= $v.','.next_node_all($v,$pre."articat",'acid',true).',';
			}
			$Aarticlecat_list = explode(',',$strAcid);
			$Aarticlecat_list = array_unique($Aarticlecat_list);
			$articlecat_list = '';
			foreach ($Aarticlecat_list AS  $v)
			{
				if($v > 0 )
				{
					$articlecat_list .= $v.',';
				}
			} 
			$articlecat_list = substr($articlecat_list,0,-1);
			$_SESSION['aarticlecat_list'] = $articlecat_list; 
			$Aarticlecat_list = explode(",",$_SESSION['aarticlecat_list']); //得到分类名权限 
			//查找包含的下级分类 end 
       }

		if($Aarticlecat_list)
		{
			$strWhere  .= ' AND acid in('.$_SESSION['aarticlecat_list'].') ';
		} else
		{
			$strWhere .= ' AND arid in(0) '; 
		}
   }
}
$db_table = $pre."arti_comms";
$sql = "SELECT count( * ) AS count FROM ".$db_table.$strWhere;
$row = $oPub->getRow($sql);
$count = $row['count'];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array('arid'=>$arid); 
$strOffSet = $page->OffSet();

$AcommAll = $oPub->select("SELECT * FROM ".$db_table.$strWhere." ORDER BY top desc,arcid desc limit ".$strOffSet); 
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AcommAll) ) {
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$value["arcid"].']" value="'.$value["arid"].'" />';
	   $StrtypeAll .= '</TD>';
       if(!$artitxtname)
	   {
	       /* 文章标题 */ 
	      $sql = "SELECT name FROM ".$pre."artitxt WHERE arid = ".$value["arid"];
          $row = $oPub->getRow($sql);
	      if($row)
		  {
		     $name = '<A HREF="../article.php?id='.$value["arid"].'" target="_blank">'.$row[name].'</A><br/>';
	      }else{
		    $name = '';
	      }
	   }
	   $descs = $name.sub_str(clean_html($value["descs"]),60); 
       $StrtypeAll .= '<TD align=left>'.$descs.'</TD>'; 
       $tmpstr = ($value["top"])?'是':'<font color="CCCCCC">否</font>';
	   $StrtypeAll .= '<TD align=left id="top_'.$value["arcid"].'"><span style="cursor:pointer" onmousedown="return art_list_edit(\'top\',\''.$value["arcid"].'\','.$value["top"].')">'.$tmpstr.'</span></TD>';

       $StrtypeAll .= '<TD align=left>'.$value["email"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["ip"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.date("m-d H:i", $value[dateadd]).'</TD>';
	   $states = ($value[states]==3)?'已审核':'';
	   $StrtypeAll .= '<TD align=left>'.$states.'</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?arcid='.$value["arcid"].'&arid='.$value["arid"].'&action=edit&page='.$pagenew.'"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑回复]"></a> ';
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?arcid='.$value["arcid"].'&arid='.$value["arid"].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
	   
       $StrtypeAll .= '</TR>';    
}
?>
<?php
include_once( "header.php"); 
if ($strMessage != '') {
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>

<DIV class=content>
<TABLE width="1005" border=0>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
  <TR class=bg5>
    <TD width="4%" align=left>序号</TD>
    <TD width="40%" align=left>内容</TD>
	<TD width="14%" align=left>精华</TD>
	<TD width="10%" align=left>昵称</TD>
	<TD width="5%" align=left>ip</TD>
	<TD width="15%" align=left>日期</TD>
	<TD width="5%" align=left>状态</TD>
    <TD width="7%" align=left>操作</TD>
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
  <?php
  if($Acomm['arcid'])
  {
  ?>
  <TR class="odd" >
  <form name="form1" method="post" action=""> 
    <TD width="13%" align="left" colspan="8">
        <span>昵称:</span>
		<input name="email" type="text" size="10" value="<?php echo ($Acomm['arcid'])?$Acomm['email']:''?>" />
		<?php
		 $Strtopopt = '<b>精华：</b>';
         $Strtopopt .= '<SELECT NAME="top">';
         $selected0 = ($Acomm[top]==0)?'selected':'';
		 $selected1 = ($Acomm[top]==1)?'selected':'';
         $Strtopopt .= '<OPTION VALUE="0" '.$selected0.' >否</OPTION>';
		 $Strtopopt .= '<OPTION VALUE="1" '.$selected1.' >是</OPTION>';
         $Strtopopt .= '</SELECT>';
		 echo $Strtopopt;	
		 ?>
		<br/>
       <?php
		$descs1 = ($Acomm['arcid'] > 0)?$Acomm['descs']:$Acomm['descs'];
	    //echo create_html_ck('descs',$descs1);
	   ?>	
	    <textarea name="descs" style="width:750px;height:400px;visibility:hidden;"><?php echo $descs1;?></textarea>
		<br/>
		<input type="submit" name="Submit" value="<?php echo ($Acomm['arcid'])?'编辑回复':'增加文章评论'?>" style="background-color: #FFCC66"/>
        <input type="hidden" name="action" value="<?php echo ($Acomm['arcid'])?'edit':'add'?>" />        
		<input type="hidden" name="arcid" value="<?php echo ($Acomm['arcid'])?$Acomm['arcid']:'0'?>" />
		<input type="hidden" name="arid" value="<?php echo $Acomm['arid'];?>" />
		<input type="hidden" name="re_arcid" value="<?php echo ($Acomm['arcid'])?$Acomm['arcid']:'0'?>" />
		<br/>
    </TD>
    </form>
  </TR>	
  <?php } ?>
</TABLE> 
</DIV>
<script charset="utf-8" src="../kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="../kindeditor/lang/zh_CN.js"></script> 

<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<script type="text/javascript" language="JavaScript">

  function selectAll(){
	xx = listForm.check_all.checked
	for(var i=0;i<listForm.length;i++)
	{
		if(listForm.elements[i].type=="checkbox")
			listForm.elements[i].checked=xx;
	}
  }

  function art_list_edit(edit,arcid,edit_val)
  {
     obj = edit + "_" + arcid;
     var strTemp = "ajax_art_comm_edit.php?op=" + edit + "&arcid=" + arcid + "&edit_val=" + escape(edit_val);
	 //alert(strTemp);
	 send_request(strTemp);
  }
	var editor;
	KindEditor.ready(function(K) {  
		editor = K.create('textarea[name="descs"]', {
			cssPath : 'plugins/code/prettify.css',
			uploadJson : '../upload_json.php?jsonop=acomms',
			fileManagerJson : '../upload_manager_json.php?jsonop=acomms',
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
