<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 
//include_once($ROOT_PATH.'includes/ckeditor/ckeditor.php'); 
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
} 
$Avi_type = array(0=>'单选',1=>'复选',2=>'下拉列表选择框',3=>'单行文本输入',4=>'多行文本输入');
if(( $_POST['action'] == 'add' || $_POST['action'] == 'edit') && $_POST['vi_name']) { 
	/*处理图片*/
	if($_FILES['vote_img']['size'] > 0 ) {
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['vote_img']['type'])) {
            $strMessage =  '图片类型错误';
			$img_name = $_POST['old_vote_img'];
        } else
		{ 
	       if(!empty($_POST['old_vote_img'])) {
               $img_name = basename($image->upload_image($_FILES['vote_img'],'vote',$_POST['old_vote_img']));
	       } else {
		       $img_name = basename($image->upload_image($_FILES['vote_img'],'vote')); 
	       }
		}
	} else {
		$img_name = $_POST['old_vote_img'];
	}

	if($_FILES['vote_s_img']['size'] > 0 ) {
		/* 判断图像类型 */
        if (!$image->check_img_type($_FILES['vote_s_img']['type'])) {
            $strMessage =  '图片类型错误';
			$img_s_name = $_POST['old_vote_s_img'];
        } else
		{ 
	       if(!empty($_POST['old_vote_s_img'])) {
               $img_s_name = basename($image->upload_image($_FILES['vote_s_img'],'vote',$_POST['old_vote_s_img']));
	       } else {
		       $img_s_name = basename($image->upload_image($_FILES['vote_s_img'],'vote')); 
	       }
		}
	} else {
		$img_s_name = $_POST['old_vote_s_img'];
	}

    $_POST['vtid'] = $_POST['vtid'] + 0;
	$_POST['viid'] = $_POST['viid'] + 0;
	$_POST['vgid'] = $_POST['vgid'] + 0;
    $db_table = $pre."vote_item";
	if($_POST['action'] == 'add' && $_POST['vtid'] ) {
	    $Afields=array('vtid'=>$_POST['vtid'],'vgid'=>$_POST['vgid'],'vi_name'=>$_POST['vi_name'],'vi_type'=>$_POST['vi_type'],'is_show'=>$_POST['is_show'],'orders'=>$_POST['orders'],'vi_nums'=>$_POST['vi_nums'],'thumb_url'=>$img_name,'thumb_s_url'=>$img_s_name,'domain_id'=>$Aconf['domain_id']);
        $tlkid = $oPub->install($db_table,$Afields);
		$strMessage = '添加成功';
	    
	} else if($_POST['action'] == 'edit' && $_POST['vtid'] && $_POST['viid'] ) {
        $Afields=array('vtid'=>$_POST['vtid'],'vgid'=>$_POST['vgid'],'vi_name'=>$_POST['vi_name'],'vi_type'=>$_POST['vi_type'],'is_show'=>$_POST['is_show'],'orders'=>$_POST['orders'],'vi_nums'=>$_POST['vi_nums'],'thumb_url'=>$img_name,'thumb_s_url'=>$img_s_name);
	    $condition = "viid = ".$_POST['viid']." AND domain_id=".$Aconf['domain_id'];
	    $oPub->update($db_table,$Afields,$condition);
		$strMessage = '修改成功';
	      
	} else {
       $strMessage = '请选择调查标题';
	}

	/* 计算总票数 */
    if($_POST['vtid']) {
		$_POST['vtid'] = $_POST['vtid'] + 0;
		$db_table = $pre."vote_item";
        
	    $sql = "SELECT sum( `vi_nums` ) AS sumnums FROM ".$db_table." 
		       where vtid = ".$_POST['vtid']." 
			   AND is_show = 1 
			   AND states = 0 
			   AND domain_id=".$Aconf['domain_id'];
		 $Anum = $oPub->getRow($sql);
         $sumnums = $Anum['sumnums'];
		
		 $db_table = $pre."vote_title";
		 $Afields=array('vt_nums'=>$sumnums);
	     $condition = "vtid = ".$_POST['vtid']." AND domain_id=".$Aconf['domain_id'];
	     $oPub->update($db_table,$Afields,$condition);
		  unset($Anum);
	}
}

/*------------------------------------------------------ */
//-- 批量删除记录
/*------------------------------------------------------ */

if ($_REQUEST['action'] == 'del') {
	$_GET['viid'] = $_GET['viid'] + 0;
    if (isset($_POST['checkboxes'])) {
        $count = 0;		
        foreach ($_POST['checkboxes'] AS $key => $id) {
		 $id = $id + 0;

          $condition = "viid=$id AND domain_id='".$Aconf['domain_id']."'";
          $row = $oPub->getRow("SELECT thumb_url, thumb_s_url FROM " . $pre."vote_item WHERE ".$condition);
          $thumb_url   = $row['thumb_url'];
		  $thumb_s_url = $row['thumb_s_url'];
	      if(!empty($thumb_url)) {
              if (is_file('../data/vote/' . $thumb_url)) {
                  @unlink('../data/vote/' . $thumb_url);
              }
          }

	      if(!empty($thumb_s_url)) {
              if (is_file('../data/vote/' . $thumb_s_url)) {
                  @unlink('../data/vote/' . $thumb_s_url);
              }
          }

		  $sql = "delete  FROM " .$pre."vote_item WHERE ".$condition;
          $oPub->query($sql);

        }
		$tmpID = implode(",",$_POST['checkboxes']);
        $strMessage =  "批量删除成功!";
   } else if(isset($_GET['viid'])) {
		$id = $_GET['viid']; 
		$condition = "viid=$id AND domain_id='".$Aconf['domain_id']."'";
		$sql = "SELECT thumb_url  FROM " . $pre."vote_item WHERE ".$condition;
		$thumb_url = $oPub->getOne($sql);
		if($thumb_url ) {
			if (is_file('../data/vote/' . $thumb_url)) {
			  @unlink('../data/vote/' . $thumb_url);
			}
		} 
		$sql = "delete  FROM " .$pre."vote_item WHERE ".$condition;
		$oPub->query($sql); 
		$tmpID = $id;

		$strMessage =  "删除成功!";
   } else {
      $strMessage =  "没有选择需要删除的信息!";
	  $tmpID = 0;
   }
}

/* 查询条件 */

if($_REQUEST[vtid]) {
	$_REQUEST[vtid] = $_REQUEST[vtid] + 0;
	$_GET['viid'] = $_GET['viid'] + 0;
	$db_table = $pre."vote_title";
	$sql = "SELECT * FROM ".$db_table." where vtid = ".$_REQUEST['vtid']." AND domain_id=".$Aconf['domain_id'];
	$Avtitle = $oPub->getRow($sql);

    if( $_GET['action'] == 'edit'){
		$db_table = $pre."vote_item"; 
	    $sql = "SELECT * FROM ".$db_table." where viid = ".$_GET['viid']." AND domain_id=".$Aconf['domain_id'];
	    $Anorm = $oPub->getRow($sql);
    }
	$where = " vtid='$_REQUEST[vtid]' and  states=0 and domain_id = '".$Aconf['domain_id']."'";
} else {
     $where = " states=0 and domain_id = '".$Aconf['domain_id']."'";
}

if($viid){
	$is_show_1 = ($Anorm[is_show] == 1)? 'SELECTED':'';
	$is_show_0 = ($Anorm[is_show] == 0)? 'SELECTED':'';
}else{
	$is_show_1 = 'SELECTED'; 
}

$Stris_showopt = '<SELECT name="is_show">';
$Stris_showopt .= '<OPTION VALUE="1" '.$is_show_1.'>是</OPTION>';
$Stris_showopt .= '<OPTION VALUE="0" '.$is_show_0.'>否</OPTION>';
$Stris_showopt .= '</SELECT>';


$Strvi_typeopt = '<SELECT name="vi_type">';
foreach ($Avi_type AS $key=>$val) {
	$selected =  $key == $Anorm[vi_type] ?'selected':'';
	$Strvi_typeopt .= '<OPTION VALUE="'.$key.'" '.$selected.'>'.$Avi_type[$key].'</OPTION>';
}
$Strvi_typeopt .= '</SELECT>';

$db_table = $pre."vote_item";
$sql = "SELECT COUNT(*) as count FROM ".$db_table."  WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('vtid'=>$_REQUEST[vtid]); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.
       " WHERE  $where ".
	   " ORDER BY vtid desc,vgid asc,orders asc " .
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
$db_table = $pre."vote_group";
$StrtypeAll = '';
$n = 0;
if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$val["viid"].']" value="'.$val["viid"].'" />';
	   $StrtypeAll .= '</TD>';
 
		$sql = "SELECT vt_name FROM ".$pre."vote_title where vtid=".$val["vtid"];  
		$vt_name = $oPub->getOne($sql);

	   $StrtypeAll .= '<TD align=left>'.sub_str($vt_name,0,20).'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["vi_name"].'</TD>';

	   $tmp = ($val["thumb_url"] != '')?'<A HREF="../data/vote/'.$val["thumb_url"].'" target="_blank"><IMG SRC="../data/vote/'.$val["thumb_url"].'" WIDTH="25" HEIGHT="25" BORDER="0" ALT="'.$val["vi_name"].'"></A>':''; 
       $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';

	   $tmp = ($val["thumb_s_url"] != '')?'<A HREF="../data/vote/'.$val["thumb_s_url"].'" target="_blank"><IMG SRC="../data/vote/'.$val["thumb_s_url"].'" WIDTH="25" HEIGHT="25" BORDER="0" ALT="'.$val["vi_name"].'"></A>':''; 
       $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';

       $sql = "SELECT vg_name FROM ".$db_table." where vgid='".$val["vgid"]."'  limit 1";
       $vg_name = $oPub->getone($sql);
	   $vg_name = ($vg_name)?$vg_name:'默认组';

	   $StrtypeAll .= '<TD align=left>'.sub_str($vg_name,0,20).'</TD>'; 
	   $StrtypeAll .= '<TD align=left>'.$Avi_type[$val["vi_type"]].'</TD>';

		$StrtypeAll .= '<TD align=left>';
		$StrtypeAll .= '<span id="vi_nums_'.$val["viid"].'">';
		$StrtypeAll .= '<INPUT TYPE="text" value="'.$val["vi_nums"].'" size="2" onDblClick=vote_edit(\'vi_nums\',this.value,\''.$val["viid"].'\') />'; 
		$StrtypeAll .= '</span>'; 
		$StrtypeAll .= '</TD>';

		$tmpstr = ($val["is_show"])?'是':'否';
		$StrtypeAll .= '<TD align=left>'.$tmpstr.'</TD>'; 

		$StrtypeAll .= '<TD align=left>';
		$StrtypeAll .= '<span id="orders_'.$val["viid"].'">';
		$StrtypeAll .= '<INPUT TYPE="text" value="'.$val["orders"].'" size="2" onDblClick=vote_edit(\'orders\',this.value,\''.$val["viid"].'\') />'; 
		$StrtypeAll .= '</span>';
		$StrtypeAll .= '</TD>';


       $StrtypeAll .= '<TD align=center>';	
	   $StrtypeAll .= '<a href="../vote.php?vtid='.$val["vtid"].'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?viid='.$val["viid"].'&vtid='.$val['vtid'].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?viid='.$val["viid"].'&vtid='.$val['vtid'].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
       $StrtypeAll .= '</TD></TR>';    
}


/* 找到所有的vote_title 到select start*/
$db_table = $pre."vote_title"; 
$sql = "SELECT vtid,vt_name FROM ".$db_table." where states = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY vtid DESC";
$AnormAll = $oPub->select($sql);
$Stropt = '<SELECT NAME="vtid" onchange="chkSearch(this.options[this.options.selectedIndex].value)">';
$tmp = ($_REQUEST['vtid'] == 0)?'SELECTED':'';
$Stropt .= '<OPTION VALUE="0" '.$tmp.'>调查选项</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($_REQUEST['vtid'] == $value["vtid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["vtid"].'" '.$selected.' >'.$value["vt_name"].'</OPTION>';   
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/

/* 找到所属可选组 start */
$vtid = $_REQUEST['vtid'] + 0 ;
if($vtid ) {
    $db_table = $pre."vote_group";  
    $sql = "SELECT vgid,vg_name FROM ".$db_table." where vtid = '".$vtid."' AND domain_id=".$Aconf['domain_id']." ORDER BY orders asc,vgid ASC";
    $AnormAll = $oPub->select($sql);
	if(!$AnormAll){
	   $strMessage = '还没添加可选组，至少要添加不在前台显示的默认可选组.<br/><br/><a href="vote_group.php?vtid='.$vtid.'">添加默认可选组</a>';
	   echo  showMessage($strMessage);
	   exit;
	}
    $Strvg_opt = '<SELECT NAME="vgid">';
    $tmp = ($Anorm['vgid'] == 0)?'SELECTED':''; 
    $n = 0;
    while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($value['vgid'] == $Anorm["vgid"])? 'SELECTED':'';
       $Strvg_opt.= '<OPTION VALUE="'.$value["vgid"].'" '.$selected.' >'.$value["vg_name"].'</OPTION>';   
    }
    $Strvg_opt .= '</SELECT>';

}
/* 找到所属可选组 end */

?>
<?php
include_once( "header.php"); 
if ($strMessage != '') {
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?> 
<table width="100%" border="0" cellspacing="1" cellpadding="1" class="button">
  <TR class=bg1>
    <TD align=left colspan="9">
	   <span style="float: left">       
	   <?php echo $Stropt;?>
       </span>
	   <span style="float: right">
		<?php
		if($vtid){
			$str =  '<a href="vote_title.php?vtid='.$vtid.'&action=edit">调查项编辑</a>';
			$str .=  ' _  选项编辑';
			$str .= ' _ <a href="vote_group.php?vtid='.$vtid.'">可选组编辑</a>';
			echo $str;
		}
		?>
		</span>
	</TD> 
  </TR>
</table>
<TABLE width="100%" border=0>
  <TR class=bg1>
    <form name="form1" method="post" enctype="multipart/form-data" action="<?php echo $_SERVER["PHP_SELF"]?>" > 
    <TD align=left colspan="11"> 
        <span style="color:#c00;">
	     <?php if($Avtitle["vt_name"]) echo $Avtitle["vt_name"].' 【票数'.$Avtitle["vt_nums"].'】';?> 
		</span>
		<br/>
		<span style="font-weight: bold">选项类型:</span>
         <?php echo $Strvi_typeopt;?>
        <span style="font-weight: bold">选项名称:</span>
     	<input name="vi_name" type="text" value="<?php echo ($Anorm['viid'])?$Anorm['vi_name']:''?>" size="60" />
		<br/><span style="color:#000066;margin-left: 200px">(注:如果选项类型为<b>下拉列表选择框</b>,选项名称中<b>第一个值</b>为选项名称，其它值为<b>可选项</b>，每个值之间用 半角逗号 分割","。)</span>
		<br/>
		<span style="font-weight: bold">所属可选组:</span>
		<?php echo $Strvg_opt;?>


		<span style="font-weight: bold">票数:</span>
		<input name="vi_nums" type="text" value="<?php echo ($Anorm['viid'])?$Anorm['vi_nums']:''?>" size="3" />
		<span style="font-weight: bold">是否显示:</span>
		<?php echo $Stris_showopt;?>
		<span style="font-weight: bold">排序:</span>
		<input name="orders" type="text" value="<?php echo ($Anorm['viid'])?$Anorm['orders']:''?>" size="2" />

		<br/>
		<span style="font-weight: bold">点击投票图标:</span>
		<INPUT type="file" name="vote_img" size="20" />  
		<?php
		 if($Anorm["thumb_url"])
		 {
			 $tmp = '<A HREF="../data/vote/'.$Anorm["thumb_url"].'" target="_blank">';
			 $tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="显示图像"></A> ';
			 echo $tmp;
		 }
		 ?>		
		(注：支持：.jpg .gif .png 格式,默认尺寸:请在组内修改)
		<INPUT type="hidden" name="old_vote_img"  value="<?php echo ($Anorm['viid'])?$Anorm['thumb_url']:'';?>" />

		<br/>
		<span style="font-weight: bold">显示结果图标:</span>
		<INPUT type="file" name="vote_s_img" size="20" />  
		<?php
		 if($Anorm["thumb_s_url"])
		 {
			 $tmp = '<A HREF="../data/vote/'.$Anorm["thumb_s_url"].'" target="_blank">';
			 $tmp .= '<IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="显示图像"></A> ';
			 echo $tmp;
		 }
		 ?>		
		(注：支持：.jpg .gif .png 格式,默认尺寸:请在组内修改)
		<INPUT type="hidden" name="old_vote_s_img"  value="<?php echo ($Anorm['viid'])?$Anorm['thumb_s_url']:'';?>" />

        <input type="hidden" name="action" value="<?php echo ($Anorm['viid'])?'edit':'add'?>" />
		<br/>
        <input type="submit" name="Submit" value="<?php echo ($Anorm['viid'])?' 编辑修改 ':' 增加选项 ' ?>" style="background-color: #FFCC66;margin:10px 0 10px 36px"/>
		<input type="hidden" name="viid" value="<?php echo ($Anorm['viid'])?$Anorm['viid']:'0';?>" /> 
		<input type="hidden" name="vtid" value="<?php echo $_REQUEST['vtid'];?>" />
    </TD>
    </form>
  </TR>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
  <TR class=bg5>
    <TD align=left>序号</TD>
	<TD align=left>调查项名称</TD>
    <TD align=left>选项名称</TD>
	<TD align=left>投票图标</TD>
	<TD align=left>结果图标</TD>
	<TD align=left>所属可选组</TD>
	<TD align=left>类型</TD>
	<TD align=left>票数</TD>
	<TD align=left>显示 </TD>
	<TD align=left>排序 </TD>
	<TD align=center>操作</TD>
  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="11">
	<span style="float: left">
	全选删除:<input onclick=selectAll() type="checkbox" name="check_all"/>
	<INPUT TYPE="submit" name="submit" value="确认删除" style="background-color: #FF9900">
	<INPUT TYPE="reset" name="reset" value="恢复" style="background-color: #CCFF99"> 
	<input type="hidden" name="vtid" value="<?php echo $_REQUEST['vtid'];?>" />
	<INPUT TYPE="hidden" name="action" value="del"> 
    </span>
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
  </form>
 </table>
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

	function chkSearch(obj)
	{ 
		location="<?php echo $_SERVER["PHP_SELF"];?>?vtid=" + obj; 
	}

	function vote_edit(edit,edit_val,viid)
	{
		obj = edit + "_" + viid;
		var strTemp = "ajax_vote_edit.php?op=" + edit + "&viid=" + viid  + "&edit_val=" + escape(edit_val);  
		//alert( strTemp );
		send_request(strTemp); 

	}
</script>
<?php
include_once( "footer.php");
?>