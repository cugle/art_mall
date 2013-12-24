<?php
//producttxt.states = 0/1/2 正常/删除/已销售
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}
$Astates = array(0=>'正常normal',1=>'已删除delete',2=>'已售出soldout');
/*------------------------------------------------------ */
//-- 批量删除产品记录
/*------------------------------------------------------ */
if ($_REQUEST['action'] == 'del')
{
	if (isset($_POST['checkboxes']))
	{
		$count = 0;		
		foreach ($_POST['checkboxes'] AS $key => $id)
		{
			$id = $id +0; 
			$condition = "prid=$id AND domain_id='".$Aconf['domain_id']."'"; 
			/* 删除缩图 */
			$rowtmp = $oPub->getRow('SELECT min_thumb,shop_thumb,states  FROM ' . $pre.'producttxt WHERE '.$condition); 
			$states       = $rowtmp['states'];
			if( $states < 1)
			{ 
				if($rowtmp['min_thumb'] ) {
					if (is_file('../' . $rowtmp['min_thumb']))  @unlink('../' . $rowtmp['min_thumb']); 
				}
				if($rowtmp['shop_thumb'] ) {
					if (is_file('../' . $rowtmp['shop_thumb']))  @unlink('../' . $rowtmp['shop_thumb']); 
				}
				/* 删除相册及缩图 */ 
				$row = $oPub->select('SELECT filename,thumb_url FROM '. $pre.'product_file WHERE '.$condition); 
				if($row ) { 
					foreach ($row AS $k=>$v) {
						if (is_file('../' . $v['filename'])) @unlink('../' . $v['filename']);  
						if (is_file('../' . $v['thumb_url'])) @unlink('../' . $v['thumb_url']); 
					}
				}
	 
			   $oPub->delete($pre."producttxt",$condition); 
			   $oPub->delete($pre."product",$condition);	
			   $oPub->delete($pre."product_comms",$condition);  
			}//$states < 1

		}
		$strMessage =  "批量删除成功 Batch elete successful!";
		$tmpID = implode(",",$_POST['checkboxes']);
	} else if(isset($_GET['prid']))
	{
		$id = $_GET['prid'];
		$id = $id +0;

		$condition = "prid=$id AND domain_id='".$Aconf['domain_id']."'"; 
		/* 删除缩图 */
		$sql = "SELECT min_thumb,shop_thumb,states  FROM " . $pre."producttxt WHERE ".$condition;
		$rowtmp = $oPub->getRow($sql);
		$states       = $rowtmp['states'];
		if( $states < 1)
		{ 
			if($rowtmp['min_thumb'] )
			{
				if (is_file('../' . $rowtmp['min_thumb']))  @unlink('../' . $rowtmp['min_thumb']); 
			}

			if($rowtmp['shop_thumb'] )
			{
				if (is_file('../' . $rowtmp['shop_thumb']))  @unlink('../' . $rowtmp['shop_thumb']); 
			}
			/* 删除相册及缩图 */ 
			$row = $oPub->select('SELECT filename,thumb_url FROM '. $pre.'product_file WHERE '.$condition); 
			if($row ) { 
				foreach ($row AS $k=>$v) {
					if (is_file('../' . $v['filename'])) @unlink('../' . $v['filename']);  
					if (is_file('../' . $v['thumb_url'])) @unlink('../' . $v['thumb_url']); 
				}
			} 
			$oPub->delete($pre."producttxt",$condition); 
			$oPub->delete($pre."product",$condition);	
			$oPub->delete($pre."product_comms",$condition);  
		}//$states < 1
		$tmpID = $id; 
		$strMessage =  "删除成功!";
	}  else
	{
		$strMessage =  "没有选择需要删除的信息!nothing to be deleted";
		$tmpID = 0;
	} 
	$db_table = $pre.'account_log';
	$change_desc = real_ip().' |  '.date("m-d h:i").' |  domain_id:'.$Aconf['domain_id'];
	$change_desc .= ' | '.$_SESSION['auser_name'].' 作品删除delete:'.$tmpID;
	$Afields=array('user_id'=>$_SESSION['auser_id'],'type'=>'prodDel','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
	$oPub->install($db_table,$Afields);
}

/* 查询条件 */
$filter['sort_by']    = empty($sort_by)    ? 'dateadd' : trim($sort_by);
$filter['sort_order'] = empty($sort_order) ? 'DESC' : trim($sort_order);
if($pcid) {
	$pcid = $pcid + 0;
	$where = 'pcid="'.$pcid.'" and  states<>1 AND  domain_id = "'.$Aconf['domain_id'].'"';
} else 
{
	$where = 'states<>1 AND domain_id = "'.$Aconf['domain_id'].'"';
}

$count = $oPub->getOne('SELECT COUNT(*) as count FROM '.$pre.'producttxt AS a WHERE 1 AND '. $where);  
$page = new ShowPage;
$page->PageSize = 30;
$page->Total = $count;
$pagenew = $page->PageNum();
$page->LinkAry = array('pcid'=>$pcid,'sort_by'=>$filter['sort_by']); 
$strOffSet = $page->OffSet(); 
$row = $oPub->select('SELECT * FROM '.$pre.'producttxt WHERE  '.$where .' ORDER BY top DESC,'.$filter['sort_by'].' '.$filter['sort_order']. ' LIMIT '. $strOffSet); 
if($row ) {  
    foreach ($row AS $key=>$val) {
        $row[$key]['dateadd']  = ($val['dateadd'])?date("m-d h:i", $val['dateadd']):'';
        $row[$key]['acname'] =$oPub->getOne('SELECT name FROM '.$pre.'productcat WHERE pcid="'.$val['pcid'].'"');  
    }

}
$StrtypeAll = '';
$n = 0;

if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$val['prid'].']" value="'.$val['prid'].'" />';
	   $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<span id="name_'.$val['prid'].'">';
	   $StrtypeAll .= '<INPUT TYPE="text" value="'.$val['name'].'" size="36" onDblClick=pro_list_edit(\'name\',\''.$val['prid'].'\',this.value) />';
	   $StrtypeAll .= '</span>';
	   $StrtypeAll .= '</TD><TD align=left>';
	   $StrtypeAll .= '<span id="price_'.$val['prid'].'">';
	   $StrtypeAll .= '<INPUT TYPE="text" value="'.$val['shop_price'].'" size="10" onDblClick=pro_list_edit(\'price\',\''.$val['prid'].'\',this.value) />';
	   $StrtypeAll .= '</span>';
	   $StrtypeAll .= '</TD><TD align=left>'.$val['acname'].'</TD>';
      
/*       $tmpstr = ($val['top'])?'是':'<font color="CCCCCC">否</font>';
	   $StrtypeAll .= '<TD align=left id="top_'.$val['prid'].'"><span style="cursor:pointer" onmousedown="return pro_list_edit(\'top\',\''.$val['prid'].'\','.$val['top'].')">'.$tmpstr.'</span></TD>';
 
       $tmpstr = ($val['special'])?'是':'<font color="CCCCCC">否</font>';
	   $StrtypeAll .= '<TD align=left id="special_'.$val['prid'].'"><span style="cursor:pointer" onmousedown="return pro_list_edit(\'special\',\''.$val['prid'].'\','.$val['special'].')">'.$tmpstr.'</span></TD>';

	   if($val['colors']=='#00FF00')
	   {
		    $tmpstr = '<font color="#00FF00">绿色</font>';
	   }else if($val['colors']=='#FF0000')
	   {
		   $tmpstr = '<font color="#FF0000">红色</font>';
	   }else if($val['colors']=='#0000FF')
	   {
		   $tmpstr = '<font color="#0000FF">蓝色</font>';
	   }else
	   {
		    $tmpstr = '默认';
	   }

	   $StrtypeAll .= '<TD align=left>'.$tmpstr.'</TD>';*/
	   $StrtypeAll .= '<TD align=left>'.$Astates[$val['states']].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val['dateadd'].'</TD>';
       $StrtypeAll .= '<TD align=center>';
	   $StrtypeAll .= '<a href="../product.php?id='.$val['prid'].'&action=read&page='.$pagenew.'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="阅读read"></a> _ ';
	   $StrtypeAll .= '<a href="productsend.php?prid='.$val['prid'].'&action=edit&page='.$pagenew.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑edit"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER['PHP_SELF'].'?prid='.$val['prid'].'&action=del&page='.$pagenew.'" onclick="return(confirm(\'确定删除are you sure todelete?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除delete"></a>';

	   if($val['comms'] > 0 )
	   {
	       $StrtypeAll .= ' _ <a href="product_comms.php?prid='.$val['arid'].'" target="_blank"><IMG SRC="images/zoo.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="评论管理"></a>';
	   }
       $StrtypeAll .= '</TD></TR>';    
}



/* 找到所有的分类到select start*/ 
$AnormAll = $oPub->select('SELECT * FROM '.$pre.'productcat where fid = 0 AND domain_id="'.$Aconf['domain_id'].'" ORDER BY pcid ASC'); 
$Stropt = '<SELECT NAME="pcid" onchange="chkSearch(this.options[this.options.selectedIndex].value)">';
$tmp = ($filter['pcid'] == 0)?'SELECTED':'';
$Stropt .= '<OPTION VALUE="0" '.$tmp.'>所有分类 all category</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($_GET['pcid'] == $value['pcid'])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value['pcid'].'" '.$selected.' >'.$n.'、'.$value['name'].'</OPTION>';
	   /* 查找儿子 */
       if($value['next_node'] != ''){          
           $Stropt .= get_next_node($value['next_node'],$_GET['pcid'] );
	   }	   
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
	
<TABLE width="100%" border=0>

  <TR class=bg1>
    <TD align=left colspan="9">
	   <span style="float: left">        
	   <?php echo $Stropt;?>
       </span>
	   <span style="float: right"><a href="productsend.php"> [添加新作品add new]</a> </span>
	</TD> 
  </TR>

<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>" name="listForm" target="_self">
  <TR class=bg5>
    <TD align=left>序号</TD>
    <TD align=left>标题[修改后双击鼠标,自动更新]</TD>
	<TD align=left>价格price</TD>
	<TD align=left>分类category</TD>
	<!--<TD align=left>
	<a href="productlist.php?pcid=<?php echo $pcid.'&sort_by=top&page='.$pagenew;?>" target="main">促销</a>
	</TD>
	<TD align=left>
	<a href="productlist.php?pcid=<?php echo $pcid.'&sort_by=special&page='.$pagenew;?>" target="main">畅销</a>
	</TD>
    <TD align=left>颜色</TD>-->
	<TD align=left>状态status</TD>
	<TD align=left>时间time</TD>
	<TD align=center>操作operate</TD>
  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="10">
	<span style="float: left">
	全选删除select all delete:
	<input onclick=selectAll() type="checkbox" name="check_all"/>
	<INPUT TYPE="submit" name="submit" value="确认删除delete" style="background-color: #FF9900">
	<INPUT TYPE="reset" name="reset" value="恢复unselect all" style="background-color: #CCFF99"> 
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
	 
   location="<?php echo $_SERVER['PHP_SELF'];?>?pcid=" + obj;

}

  function pro_list_edit(edit,prid,edit_val)
  {
     obj = edit + "_" + prid;
     var strTemp = "ajax_pro_list_edit.php?op=" + edit + "&prid=" + prid + "&edit_val=" + escape(edit_val);
	 //alert(strTemp);
	 send_request(strTemp);
  }

</script>
<?php
/* OPTION 递归 */
function get_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre.'productcat';
   $Agrad = explode(',',$next_node);
   $Stropt = '';
   if(count($Agrad) > 0 )
	{
	   $str .= '　';
	   $tn = 0;
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		   $sql = "SELECT * FROM ".$db_table." where pcid = $v";
           $Anorm = $oPub->getRow($sql);
		   if( $Anorm['name'] != ''){
			   $tn ++;
			   $selected = ($fid == $v)? 'SELECTED':'';
		      $Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm['name'].'</OPTION>';
              $Stropt .= get_next_node($Anorm['next_node'],$fid,$str);
		   }
		   
	   }
	}
	return $Stropt;
}
/* tbale 递归 */
function tab_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre.'productcat';
   $Agrad = explode(',',$next_node);
   $Strtab = '';
   if(count($Agrad) > 0 )
	{
	   $str .= '　';
	   $tn = 0;
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		    $sql = "SELECT * FROM ".$db_table." where pcid = $v";
            $Anorm = $oPub->getRow($sql);
			if( $Anorm['name'] != ''){
			  $tn ++ ;
	          $tmpstr = ($n % 2 == 0)?"even":"odd";
              $Strtab  .= '<TR class='.$tmpstr.'>';

              $Strtab  .= '<TD align=left>'.$str.$tn.'）'.$Anorm['name'].'</TD>';
			  $Strtab  .= '<TD align=left>'.$Anorm['descs'].'</TD>';
			  $tmp = ($Anorm['ifshow'])?'是yes':'否no';
			  $Strtab  .= '<TD align=left>'.$tmp.'</TD>';
	          $tmp = ($Anorm['ifnav'])?'是yes':'否no';
	          $Strtab .= '<TD align=left>'.$tmp.'</TD>';
              $Strtab  .= '<TD align=left><a href="'.$_SERVER['PHP_SELF'].'?pcid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑edit]"></a> ';
	          $Strtab  .= '<a href="'.$_SERVER['PHP_SELF'].'?pcid='.$v.'&fid='.$fid.'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除delete]"></a></TD>';
              $Strtab  .= '</TR>';  
	          $n ++;
              $Strtab .= tab_next_node($Anorm['next_node'],$v,$str );
			}
	   }
	}
	return $Strtab;
}
?>	
<?php
include_once( "footer.php");
?>
