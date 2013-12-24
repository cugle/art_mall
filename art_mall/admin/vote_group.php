<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}


if(( $_POST['action'] == 'add' || $_POST['action'] == 'edit') && $_POST['vg_name'])
{
    $_POST['vtid'] = $_POST['vtid'] + 0;
	$_POST['vgid'] = $_POST['vgid'] + 0;
    $db_table = $pre."vote_group";
	if($_POST['action'] == 'add' && $_POST['vtid'] )
	{
	    $Afields=array('vtid'=>$_POST['vtid'],'vg_name'=>$_POST['vg_name'],'vg_desc'=>$_POST['vg_desc'],'is_show'=>$_POST['is_show'],'orders'=>$_POST['orders'],'thumb_url_w'=>$_POST['thumb_url_w'],'thumb_url_h'=>$_POST['thumb_url_h'],'thumb_s_url_w'=>$_POST['thumb_s_url_w'],'thumb_s_url_h'=>$_POST['thumb_s_url_h'],'domain_id'=>$Aconf['domain_id']);
        $tlkid = $oPub->install($db_table,$Afields);
		$strMessage = '添加成功';
	    
	}
	else if($_POST['action'] == 'edit' && $_POST['vtid'] && $_POST['vgid'] )
	{
        $Afields=array('vtid'=>$_POST['vtid'],'vg_name'=>$_POST['vg_name'],'vg_desc'=>$_POST['vg_desc'],'is_show'=>$_POST['is_show'],'orders'=>$_POST['orders'],'thumb_url_w'=>$_POST['thumb_url_w'],'thumb_url_h'=>$_POST['thumb_url_h'],'thumb_s_url_w'=>$_POST['thumb_s_url_w'],'thumb_s_url_h'=>$_POST['thumb_s_url_h']);
	    $condition = "vgid = ".$_POST['vgid']." AND domain_id=".$Aconf['domain_id'];
	    $oPub->update($db_table,$Afields,$condition);
		$strMessage = '修改成功';
	      
	}
	else
	{
       $strMessage = '请选择调查标题';
	}
}

/*------------------------------------------------------ */
//-- 批量删除记录
/*------------------------------------------------------ */

if ($_REQUEST['action'] == 'del')
{
	$_GET['vgid'] = $_GET['vgid'] + 0;


    if (isset($_POST['checkboxes']))
    {
        $count = 0;		
        foreach ($_POST['checkboxes'] AS $key => $id) {
		  $id = $id + 0; 
          $sql = "delete from ".$pre."vote_group WHERE vgid=$id AND domain_id='".$Aconf['domain_id']."'";
          $oPub->query($sql);
 
          $sql = "delete from  ".$pre."vote_item WHERE vgid=$id AND domain_id='".$Aconf['domain_id']."'";
          $oPub->query($sql);
		  $tempvgid = $id; 
        }
       
	    if($tempvgid) {
			$db_table = $pre."vote_group";
	        $sql = "SELECT vtid FROM ".$db_table." where 
	      vgid  = ".$tempvgid." AND domain_id=".$Aconf['domain_id'];
	        $vtid = $oPub->getone($sql);
		} 
        $strMessage =  "批量删除成功!";
   } else if(isset($_GET['vgid']))
   {
        $id = $_GET['vgid']; 
         $sql = "delete from ".$pre."vote_group WHERE vgid=$id AND domain_id='".$Aconf['domain_id']."'";
         $oPub->query($sql); 
         $sql = "delete from  ".$pre."vote_item WHERE vgid=$id AND domain_id='".$Aconf['domain_id']."'";
         $oPub->query($sql); 
		 $strMessage =  "删除成功!";
   }  else
   {
      $strMessage =  "没有选择需要删除的信息!";
	  $tmpID = 0;
   }

    /* 计算总票数 */

    $vtid = ($vtid)?$vtid:$_GET['vgid'] + 0;
	if($vtid) { 
		$sql = "SELECT sum( `vi_nums` ) AS sumnums FROM ".$pre."vote_item 
				   where vtid = ".$vtid." 
				   AND is_show = 1 
				   AND states = 0 
				   AND domain_id=".$Aconf['domain_id'];
		$Anum = $oPub->getRow($sql);
		$sumnums = $Anum['sumnums']; 
		$Afields=array('vt_nums'=>$sumnums);
		$condition = "vtid = ".$vtid." AND domain_id=".$Aconf['domain_id'];
		$oPub->update( $pre."vote_title",$Afields,$condition);
		unset($Anum);
	}
}

/* 查询条件 */
if($_REQUEST[vtid])
{
	$_REQUEST[vtid] = $_REQUEST[vtid] + 0;
	$_GET['vgid'] = $_GET['vgid'] + 0;
	$db_table = $pre."vote_title";
	$sql = "SELECT * FROM ".$db_table." where vtid = ".$_REQUEST['vtid']." AND domain_id=".$Aconf['domain_id'];
	$Avtitle = $oPub->getRow($sql);

    if( $_GET['action'] == 'edit'){
		$db_table = $pre."vote_group"; 
	    $sql = "SELECT * FROM ".$db_table." where vgid = ".$_GET['vgid']." AND domain_id=".$Aconf['domain_id'];
	    $Anorm = $oPub->getRow($sql);
    }
	$where = "vtid='$_REQUEST[vtid]' and   domain_id = '".$Aconf['domain_id']."'";
}
else
{
     $where = " domain_id = '".$Aconf['domain_id']."'";
}

$Stris_showopt = '<SELECT name="is_show">';
$Stris_showopt .= '<OPTION VALUE="1" '.($Anorm[is_show] == 1 ?'selected':'').'>是</OPTION>';
$Stris_showopt .= '<OPTION VALUE="0" '.($Anorm[is_show] < 1 ?'selected':'').'>否</OPTION>';
$Stris_showopt .= '</SELECT>';

$db_table = $pre."vote_group";
$sql = "SELECT COUNT(*) as count FROM ".$db_table." AS a WHERE 1 AND ". $where;
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
	   " ORDER BY vtid desc,orders asc,vgid ASC " .
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$val["vgid"].']" value="'.$val["vgid"].'" />';
	   $StrtypeAll .= '</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["vg_name"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["orders"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["vg_desc"].'</TD>'; 
       $tmpstr = ($val["is_show"])?'是':'否';
	   $StrtypeAll .= '<TD align=left>'.$tmpstr.'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["thumb_url_w"].' - '.$val["thumb_url_h"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["thumb_s_url_w"].' - '.$val["thumb_s_url_h"].'</TD>';
       $StrtypeAll .= '<TD align=center>';	
	   $StrtypeAll .= '<a href="../vote.php?vtid='.$val["vtid"].'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a> ';
	   $StrtypeAll .= ' __ <a href="'.$_SERVER["PHP_SELF"].'?vgid='.$val["vgid"].'&vtid='.$val['vtid'].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	   $StrtypeAll .= ' __ <a href="'.$_SERVER["PHP_SELF"].'?vgid='.$val["vgid"].'&vtid='.$val['vtid'].'&action=del&page='.$pagenew.'"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a>';
       $StrtypeAll .= '</TD></TR>';    
}

/* 找到所有的vote_title 到select start*/
$db_table = $pre."vote_title"; 
$sql = "SELECT vtid,vt_name FROM ".$db_table." where states = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY vtid ASC";
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
?>
<?php
include_once( "header.php"); 
if ($strMessage != '')
{
 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?> 

<table width="100%" border="0" cellspacing="1" cellpadding="1" class="button">
  <TR class=bg1>
    <TD align=left colspan="8">
	   <span style="float: left">       
	   <?php echo $Stropt;?>
       </span>
	   <span style="float: right">
		<?php
		if($vtid){
			$str =  '<a href="vote_title.php?vtid='.$vtid.'&action=edit">调查项编辑</a>';
			$str .=  ' _  <a href="vote_item.php?vtid='.$vtid.'">选项编辑</a>';
			$str .= ' _  可选组编辑';
			echo $str;
		}
		?>
		</span>
	</TD> 
  </TR>
</table>

<TABLE width="100%" border=0>
  <TR class=bg1>
    <form name="form1" method="post" action="<?php echo $_SERVER["PHP_SELF"]?>"> 
    <TD align=left colspan="2"> 
       <span style="color:#c00;">
	     <?php if($Avtitle["vt_name"]) echo $Avtitle["vt_name"].' 【票数'.$Avtitle["vt_nums"].'】';?> (<span><a href="vote_item.php?vtid=<?php echo $_REQUEST['vtid'];?>">[选项添加]</a></span>)
	</span>
	<br/>
		<span style="font-weight: bold">可选组名称:</span>
		<input name="vg_name" type="text" value="<?php echo ($Anorm['vgid'])?$Anorm['vg_name']:'';?>" size="50" />  
		<span style="font-weight: bold">顺序:</span>
		<input name="orders" type="text" value="<?php echo ($Anorm['vgid'])?$Anorm['orders']:'';?>" size="1" /> 
		<span style="font-weight: bold">是否显示:</span>
		<?php echo $Stris_showopt;?>(注：在前台是否显示组名称、描述，不影响可选项的显示)
		<br/>
		<span style="font-weight: bold">投票图标尺寸:</span>
		宽:<input name="thumb_url_w" type="text" value="<?php echo ($Anorm['vgid'])?$Anorm['thumb_url_w']:'30';?>" style="width:40px" />px 
		高:<input name="thumb_url_h" type="text" value="<?php echo ($Anorm['vgid'])?$Anorm['thumb_url_h']:'30';?>" style="width:40px" />px 
		<span style="font-weight: bold">结果图标尺寸:</span>
		宽:<input name="thumb_s_url_w" type="text" value="<?php echo ($Anorm['vgid'])?$Anorm['thumb_s_url_w']:'30';?>" style="width:40px" />px 
		高:<input name="thumb_s_url_h" type="text" value="<?php echo ($Anorm['vgid'])?$Anorm['thumb_s_url_h']:'30';?>" style="width:40px" />px 
		<br/>
		<span style="font-weight: bold">可选组描述:</span>
		<br/>
		<TEXTAREA NAME="vg_desc" style="width:566px;height=40px"><?php echo ($Anorm['vgid'])?$Anorm['vg_desc']:'';?></TEXTAREA>
		<br/>
        <input type="hidden" name="action" value="<?php echo ($Anorm['vgid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['vgid'])?' 编辑修改 ':' 增加选项 ' ?>" style="background-color: #FFCC66;margin-left: 30px"/>
		<input type="hidden" name="vgid" value="<?php echo ($Anorm['vgid'])?$Anorm['vgid']:'0';?>" /> 
		<input type="hidden" name="vtid" value="<?php echo $_REQUEST['vtid'];?>" />
    </TD>
    </form>
  </TR>
</table>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"];?>" name="listForm" target="_self">
	<TABLE width="100%" border=0>

		<TR class=bg5>
			<TD align=left>序号</TD>
			<TD align=left>名称</TD>
			<TD  align=left>排序</TD>
			<TD align=left>描述</TD>
			<TD align=left>显示 </TD>
			<TD align=left>投票图标宽-高 </TD>
			<TD align=left>结果图标宽-高 </TD>
			<TD align=center>操作</TD>
		</TR>

		<?php echo $StrtypeAll;?>

		<TR class=bg5>
			<TD  align=right colspan="8">
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

	</table>
</form>
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
</script>
<?php
include_once( "footer.php");
?>