<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if(!empty($Aconf['priveMessage']))
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

if ( $_SESSION['aaction_list'] != 'all' and empty($_SESSION['aarticlecat_list']))
{
   echo showMessage("文章分类权限没有指定，不能查阅文章列表，请与管理员联系");
   exit;  
}
$Aarticlecat_list = false;
if(!empty($_SESSION['aarticlecat_list']))
{
  //找到所有的文章分类权限,通过提交的分类查找包含的下级分类
	$db_table = $pre."articat";
	$Aarticlecat_list = explode(',',$_SESSION['aarticlecat_list']);
	foreach ($Aarticlecat_list AS  $v)
	{
         $strAcid .= $v.','.next_node_all($v,$db_table,'acid',true).',';
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

/*------------------------------------------------------ */
//-- 批量删除文章记录
/*------------------------------------------------------ */

if ($action == 'del')
{
    if (isset($_POST['checkboxes']))
    {
        $count = 0;
		$strid = '';
        foreach ($_POST['checkboxes'] AS $key => $id)
        {	
			$id = $id+0;
			$condition = "arid='".$id."' AND domain_id='".$Aconf['domain_id']."'"; 
			/* 删除缩图 */
			$sql = "SELECT min_thumb, arti_thumb,states  FROM " . $pre."artitxt WHERE ".$condition;
			$rowtmp = $oPub->getRow($sql);
			$states       = $rowtmp["states"];
		  
			if( $states < 1)
			{
 
				if($rowtmp["arti_thumb"]) {
					if (is_file('../' . $rowtmp["arti_thumb"]))  @unlink('../' . $rowtmp["arti_thumb"]); 
					if (is_file('../' . $rowtmp["min_thumb"]))  @unlink('../' . $rowtmp["min_thumb"]); 
				}
				/* 删除相册及缩图 */ 
				$sql = "SELECT filename,thumb_url FROM " . $pre."arti_file WHERE ".$condition;
				$row = $oPub->select($sql);
				if($row )
				{ 
					foreach ($row AS $k=>$v)
					{
						if (is_file('../' . $v["filename"])) @unlink('../' . $v["filename"]);  
						if (is_file('../' . $v["thumb_url"])) @unlink('../' . $v["thumb_url"]); 
					}
				}
 
			   $oPub->delete($pre."artitxt",$condition);
			   $oPub->delete( $pre."article",$condition);	
			   $oPub->delete($pre."arti_comms",$condition);
			   $oPub->delete($pre."arti_comms_re",$condition);
			   $strid .= $id.',';
		 }//if( $states < 1)
        }
        $tmpID = ($strid)?substr($strid,0,-1):'';
        $strMessage =  "批量删除成功!";
   }
   else if(isset($_GET['arid'])) {
	     $_GET['arid'] = $_GET['arid']+0;
         $id = $_GET['arid'];

		  $condition = "arid='".$id."' AND domain_id='".$Aconf['domain_id']."'"; 
		  /* 删除缩图 */
          $sql = "SELECT min_thumb,arti_thumb,states  FROM " . $pre."artitxt WHERE ".$condition;
          $rowtmp = $oPub->getRow($sql);
		  if(!isset($rowtmp["states"])) $rowtmp["states"]=false;
		  $states       = $rowtmp["states"];
		  if( $states < 1)
		 {
				if($rowtmp["arti_thumb"]) {
					if (is_file('../' . $rowtmp["arti_thumb"]))  @unlink('../' . $rowtmp["arti_thumb"]); 
					if (is_file('../' . $rowtmp["min_thumb"]))  @unlink('../' . $rowtmp["min_thumb"]); 
				}
				/* 删除相册及缩图 */ 
				$sql = "SELECT filename,thumb_url FROM " . $pre."arti_file WHERE ".$condition;
				$row = $oPub->select($sql);
				if($row )
				{ 
					foreach ($row AS $k=>$v)
					{
						if (is_file('../' . $v["filename"])) @unlink('../' . $v["filename"]);  
						if (is_file('../' . $v["thumb_url"])) @unlink('../' . $v["thumb_url"]); 
					}
				}

				$oPub->delete($pre."artitxt",$condition);
				$oPub->delete($pre."article",$condition);	
				$oPub->delete($pre."arti_comms",$condition);
				$oPub->delete($pre."arti_comms_re",$condition);

				$tmpID = $id;

				$strMessage =  "删除成功!";
		 }// if( $states < 1)

   }
   else
   {
      $strMessage =  "没有选择需要删除的信息!";
	  $tmpID = 0;
   }

   $db_table = $pre.'account_log';
   $change_desc = real_ip().' |  '.date("m月d日 h:i").' |  domain_id:'.$Aconf['domain_id'];
   $change_desc .= ' | '.$_SESSION['auser_name'].' 文章删除:'.$tmpID;
   $Afields=array('user_id'=>$_SESSION['auser_id'],'type'=>'artiDel','change_desc'=>$change_desc,'domain_id'=>$Aconf['domain_id']);
   $oPub->install($db_table,$Afields);
}

/* 查询条件 */
$db_table = $pre."artitxt";
$filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'top' : trim($_REQUEST['sort_by']);
$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

/*

*/
$where = " domain_id = '".$Aconf['domain_id']."'";
if(!isset($start_time)) $start_time=0;  
if(!isset($sear_name)) $sear_name=false;
if(!empty($start_time)) { 
    $start_time = local_strtotime($start_time." 00:00:01");
    $end_time   = local_strtotime($end_time."  23:59:59");
	$where .= " AND dateadd >= '".$start_time."' AND dateadd <= '".$end_time."'";
}  
if(empty($end_time)){
	$end_time=time();
}

if(!empty($sear_name)) {
	$where .= " AND `name` LIKE '%". $sear_name ."%'";
}
if(!isset($acid)) $acid=false;
if($acid > 0 ) {
	$acid = $acid;
	//查询包含的所有子分类
	$db_table = $pre."articat";
    $strAcid_sear = $acid.','.next_node_all($acid,$db_table,'acid',true).',';
    $Aarticlecat_sear = explode(',',$strAcid_sear);
    $Aarticlecat_sear = array_unique($Aarticlecat_sear);
	$articlecat_sear = '';
	foreach ($Aarticlecat_sear AS  $v) {
	  if($v > 0 ) {
		  $articlecat_sear .= $v.',';
	  }
	} 
	$articlecat_sear = substr($articlecat_sear,0,-1);
	$where .= ' AND acid in('.$articlecat_sear.') ';
} else {
   if ( $_SESSION['aaction_list'] == 'all')
   {}  else {  
       $where .= ' AND acid in('.$_SESSION['aarticlecat_list'].') ';
   }
}


$db_table = $pre."artitxt";


$sql = "SELECT COUNT(*) as count,sum(hots) as sum_hots,sum(support) as sum_support,sum(against) as sum_against,sum(comms) as sum_comms FROM ".$db_table." WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);

$Asum["sum_count"]   = $row["count"];
$Asum["sum_hots"]    = $row["sum_hots"];
$Asum["sum_support"] = $row["sum_support"];
$Asum["sum_against"] = $row["sum_against"];
$Asum["sum_sa"]      = $row["sum_support"] + $row["sum_against"];
$Asum["sum_comms"]   = $row["sum_comms"];

if(!isset($dateadd)) $dateadd=false;
$filter['record_count'] = $row["count"];
unset($row);
$page = new ShowPage;
$page->PageSize = $Aconf['set_pagenum'];
$page->PHP_SELF = PHP_SELF;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('acid'=>$acid,'sort_by'=>$filter['sort_by'],'start_time'=>$start_time,'end_time'=>$end_time,'dateadd'=>$dateadd); 

$http_var = '&acid='.$acid.'&sort_by='.$filter['sort_by'].'&start_time='.$start_time.'&end_time='.$end_time.'&dateadd='.$dateadd;

$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.
       " WHERE  $where ".
       " ORDER BY ".$filter['sort_by']." ".$filter['sort_order']. ",arid desc ".
       " LIMIT ". $strOffSet;

$row = $oPub->select($sql);
if($row )
{ 
	$db_table = $pre."articat";
    foreach ($row AS $key=>$val)
    {
        $row[$key]['dateadd']  = ($val['dateadd'])?date("n.j H:i", $val['dateadd']):'';
		$row[$key]['arti_date']  = ($val['arti_date'])?date("n.j H:i", $val['arti_date']):'';
        $sql = "SELECT name FROM ".$db_table." WHERE acid=$val[acid]";
		$row2 = $oPub->getRow($sql);
		$row[$key]['acname'] = $row2["name"];
    }
}
$StrtypeAll = '';
$n = 0;
$Astates = array(0=>'未审',1=>'隐藏',2=>'已审');
if($row)
foreach ($row AS $key=>$val)
{
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
	   $n ++ ;
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<input type="checkbox" name="checkboxes['.$val["arid"].']" value="'.$val["arid"].'" />';
	   $StrtypeAll .= '</TD>';

	   /* 找到所有的分类到select start*/

		$Stropt = '<span id="states_'.$val["arid"].'">';
		$Stropt .= '<SELECT NAME="states" onchange="art_list_edit(\'states\',\''.$val["arid"].'\',this.options[this.options.selectedIndex].value)">';   
		foreach ($Astates AS $k=>$value)
		{
			$selected = ($val['states'] == $k)? 'SELECTED':'';
			$Stropt .= '<OPTION VALUE="'.$k.'" '.$selected.' >'.$value.'</OPTION>'; 
		}
		$Stropt .= '</SELECT>';
		$Stropt .= '</span>';
	  /* 找到所有的分类到select end*/ 

	   $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= $Stropt;
	   $StrtypeAll .= '</TD>'; 


       $StrtypeAll .= '<TD align=left>';
	   $StrtypeAll .= '<a href="../articleverify.php?id='.$val["arid"].'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="阅读"></a> ';

	   $StrtypeAll .= ' _ <a href="articlesend.php?arid='.$val["arid"].'&action=edit&page='.$pagenew.$http_var.'" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a>';
       $StrtypeAll .= ' _ <a href="'.$PHP_SELF.'?arid='.$val["arid"].'&action=del&page='.$pagenew.$http_var.'" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a> ';
 

	   if($val["comms"] > 0 )
	   {
	       $StrtypeAll .= ' _ <a href="article_comms.php?arid='.$val["arid"].'" target="_blank"><IMG SRC="images/zoo.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="评论管理"></a>';
	   }

       $StrtypeAll .= '</TD>';
       $StrtypeAll .= '<TD align=left>'.sub_str($val["name"],12,true).'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["acname"].'</TD>';


       $tmpstr = ($val["focus"])?'是':'<font color="CCCCCC">否</font>';
	   $StrtypeAll .= '<TD align=left id="focus_'.$val["arid"].'"><span style="cursor:pointer" onmousedown="return art_list_edit(\'focus\',\''.$val["arid"].'\','.$val["focus"].')">'.$tmpstr.'</span></TD>';

       $tmpstr = ($val["top"])?'是':'<font color="CCCCCC">否</font>';
	   $StrtypeAll .= '<TD align=left id="top_'.$val["arid"].'"><span style="cursor:pointer" onmousedown="return art_list_edit(\'top\',\''.$val["arid"].'\','.$val["top"].')">'.$tmpstr.'</span></TD>';

       $tmpstr = ($val["trundle"])?'是':'<font color="CCCCCC">否</font>';
	   $StrtypeAll .= '<TD align=left id="trundle_'.$val["arid"].'"><span style="cursor:pointer" onmousedown="return art_list_edit(\'trundle\',\''.$val["arid"].'\','.$val["trundle"].')">'.$tmpstr.'</span></TD>';

       $tmpstr = ($val["ifpic"])?'是':'<font color="CCCCCC">否</font>';
	   $StrtypeAll .= '<TD align=left id="ifpic_'.$val["arid"].'"><span style="cursor:pointer" onmousedown="return art_list_edit(\'ifpic\',\''.$val["arid"].'\','.$val["ifpic"].')">'.$tmpstr.'</span></TD>';

	   $StrtypeAll .= '<TD align=left>'.$val["arti_date"].'</TD>';


	   if($val["colors"]=='#00FF00')
	   {
		    $tmpstr = '<font color="#00FF00">绿色</font>';
	   }else if($val["colors"]=='#FF0000')
	   {
		   $tmpstr = '<font color="#FF0000">红色</font>';
	   }else if($val["colors"]=='#0000FF')
	   {
		   $tmpstr = '<font color="#0000FF">蓝色</font>';
	   }else
	   {
		    $tmpstr = '默认';
	   }
	   $StrtypeAll .= '<TD align=left>'.$tmpstr.'</TD>'; 
		$StrtypeAll .= '<TD align=left>'; 
		$StrtypeAll .= '<span id="hots_'.$val["arid"].'">';
		$StrtypeAll .= '<INPUT TYPE="text" value="'.$val["hots"].'" size="3" onDblClick=art_list_edit(\'hots\',\''.$val["arid"].'\',this.value) />';
		$StrtypeAll .= '</span>'; 
		$StrtypeAll .= '</TD>';

	   $tmp = $val["support"] + $val["against"];
	   $StrtypeAll .= '<TD align=left>';
	   if($_SESSION['auser_name'] == 'admin')
	  {	   
	   $StrtypeAll .= '<span id="support_'.$val["arid"].'">';
	   $StrtypeAll .= '<INPUT TYPE="text" value="'.$val["support"].'" size="2" onDblClick=art_list_edit(\'support\',\''.$val["arid"].'\',this.value) />';
	   $StrtypeAll .= '</span>';
      }
	  else
	  {
      $StrtypeAll .=$val["support"];
	  }
	   $StrtypeAll .= '+';
	   if($_SESSION['auser_name'] == 'admin')
	  {	        
	   $StrtypeAll .= '<span id="against_'.$val["arid"].'">';
	   $StrtypeAll .= '<INPUT TYPE="text" value="'.$val["against"].'" size="2" onDblClick=art_list_edit(\'against\',\''.$val["arid"].'\',this.value) />';
	   $StrtypeAll .= '</span>';
	  }
	  else
	  {
        $StrtypeAll .=$val["against"];
	  }

	   $StrtypeAll .= '=';
	   $StrtypeAll .= $tmp;

	   $StrtypeAll .= '</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["comms"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$val["dateadd"].'</TD>';

	   $StrtypeAll .= '</TR>';    
}

$StrtypeAll .= '<TR class=even>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td>';
$StrtypeAll .= '<B>合计</B>:'.$Asum["sum_count"];
$StrtypeAll .= '</td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td></td>';
$StrtypeAll .= '<td>';
$StrtypeAll .= $Asum["sum_hots"];
$StrtypeAll .= '</td>';
$StrtypeAll .= '<td>';
$StrtypeAll .= $Asum["sum_support"].'+'.$Asum["sum_against"].'='.$Asum["sum_sa"];
$StrtypeAll .= '</td>';
$StrtypeAll .= '<td>';
$StrtypeAll .= $Asum["sum_comms"];
$StrtypeAll .= '</td>';
$StrtypeAll .= '<td></td>';

$StrtypeAll .= '</TR>';

/* 找到所有的分类到select start*/
$db_table = $pre."articat"; 
$sql = "SELECT * FROM ".$db_table." where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC";
$AnormAll = $oPub->select($sql);


if(!isset($filter['acid'])) $filter['acid']=false;


$Stropt = '<SELECT NAME="acid" >';
$tmp = ($filter['acid'] == 0)?'SELECTED':'';
$Stropt .= '<OPTION VALUE="0" '.$tmp.'>所有分类</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   
	   if(is_array($Aarticlecat_list)  && $_SESSION['aaction_list'] != 'all')
	   {
	      if(in_array($value["acid"],$Aarticlecat_list))
	      {
		  $n ++;
          $selected = ($acid == $value["acid"])? 'SELECTED':'';
          $Stropt .= '<OPTION VALUE="'.$value["acid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
	      }
	   } else {
		  $n ++;
          $selected = ($acid == $value["acid"])? 'SELECTED':'';
          $Stropt .= '<OPTION VALUE="'.$value["acid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
	   }
	   /* 查找儿子 */
       if($value["next_node"] != ''){          
           $Stropt .= get_next_node($value["next_node"],$acid,$str = '　',$Aarticlecat_list);
	   }
	
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/
?>
<?php
   include_once( "header.php");
?>
<?php
if (!empty($strMessage))
{
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<script src="js/calendar/calendar.js"  type="text/javascript" ></script>
<link href="js/calendar/calendar.css" rel="stylesheet" type="text/css">

<TABLE width="100%" border=0 title="审核机制：在系统设置里，开启新闻审核功能后才有效。">

  <TR class=bg1>
    <TD align=left colspan="14">
	<FORM METHOD=POST ACTION="" style="margin: 0px">
	   <span style="float: left">    
	   <?php echo $Stropt;?> 
        开始日期
		<input style="width: 68px" name="start_time" id="start_time" value="<?php echo ($start_time)?date("Y-m-d",$start_time):'';?>" readonly="readonly" type="text">
		<input name="selbtn1" id="selbtn1" onclick="return showCalendar('start_time', '%Y-%m-%d', false, false, 'selbtn1');" value="选择" type="button" >
		<?php
			if(!empty($end_time)){
				$end_time  =time();
			}
		?>
        结束日期<input style="width: 68px" name="end_time" id="end_time"  value="<?php echo !empty($end_time)?date("Y-m-d",$end_time):date("Y-m-d");?>" readonly="readonly" type="text">
		<input name="selbtn2" id="selbtn2" onclick="return showCalendar('end_time', '%Y-%m-%d', false, false, 'selbtn2');" value="选择"  type="button" >
		标题<INPUT TYPE="text" NAME="sear_name" value="<?php echo $sear_name;?>" >
        <INPUT TYPE="submit" name="submit" value="确定查询">
		<INPUT TYPE="hidden" name="act" value="datesearch">
		<INPUT TYPE="hidden" name="action" value="">
       </span>
	   <span style="float: right"><a href="articlesend.php"> [添加新文章]</a> </span>
	   </FORM>
	</TD> 
  </TR>
</table>
<form method="POST" action="<?php echo $PHP_SELF;?>" name="listForm" target="_self">
<TABLE width="100%" border=0> 
  <TR class=bg5>
    <TD width="3%" align=left></TD>
	<TD width="6%" align=left>审核</TD>
	<TD width="9%" align=left>操作</TD>
    <TD width="17%" align=left>标题</TD>
	<TD width="7%" align=left>分类</TD>
	<TD width="4%" align=left><a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=focus&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">焦点</a></TD>

	<TD width="4%" align=left><a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=top&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">
	置顶</a></TD>

	<TD width="4%" align=left><a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=trundle&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">
	滚动</a></TD>
	<TD width="4%" align=left><a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=ifpic&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">
	图库</a></TD>

	<TD width="10%" align=left>
	<a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=arti_date&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">文章时间</a>
	</TD>
    <TD width="4%" align=left>颜色</TD>
	<TD width="5%" align=left><a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=hots&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">PV</a></TD>
	<TD width="10%" align=left><a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=support&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">顶</a>/
	<a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=against&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">踩</a>/合计
	</TD>
	<TD width="4%" align=left><a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=comms&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">评论</a></TD>
	<TD width="13%" align=left>
	<a href="articlelist.php?acid=<?php echo $acid;?>&sort_by=dateadd&page=<?php echo $pagenew;?>&start_time=<?php echo $start_time;?>&end_time=<?php echo $end_time;?>" target="main">添加时间</a>
	</TD>

  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="15">
	<span style="float: left">
	全选删除:<input onclick=selectAll() type="checkbox" name="check_all"/>
	<INPUT TYPE="submit" name="submit" value="确认删除" >
	<INPUT TYPE="reset" name="reset" value="恢复"> 
	<INPUT TYPE="hidden" name="action" value="del"> 
    </span>
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR> 
 </table>
  </form>
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

 function art_list_edit(edit,arid,edit_val)
  {
     obj = edit + "_" + arid;
     var strTemp = "ajax_art_list_edit.php?op=" + edit + "&arid=" + arid + "&edit_val=" + escape(edit_val);
	 //alert(strTemp);
	 send_request(strTemp);
  }
</script>
<?php
/* OPTION 递归 */
function get_next_node($next_node,$fid,$str = '　',$Aarticat = false)
{
   global $oPub,$pre,$_SESSION,$un_aaction_list;
   $db_table = $pre.'articat';
   $Agrad = explode(',',$next_node);
   $Stropt = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   $str .= '　';
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		   $sql = "SELECT * FROM ".$db_table." where acid = $v";
           $Anorm = $oPub->getRow($sql);
		   if( $Anorm["name"] != ''){
			   
			   $selected = ($fid == $v)? 'SELECTED':'';
			   if(is_array($Aarticat)  && $_SESSION['aaction_list'] != 'all')
			   {
                  if(in_array($v,$Aarticat))
				   {
					  $tn ++;
                      $Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["name"].'</OPTION>';
				   }
			   }
			   else
			   {
				   $tn ++;
		          $Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["name"].'</OPTION>';
			   }
              $Stropt .= get_next_node($Anorm["next_node"],$fid,$str,$Aarticat);
		   }
		   
	   }
	}
	return $Stropt;
}
/* tbale 递归 */
function tab_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre;
   $db_table = $pre.'articat';
   $Agrad = explode(',',$next_node);
   $Strtab = '';
   if(count($Agrad) > 0 )
	{
	   $tn = 0;
	   while( @list( $k, $v ) = @each( $Agrad ) ) {
           if ($v == 0 && $v =='')
		   {
              break;
		   }		   
		    $sql = "SELECT * FROM ".$db_table." where acid = $v";
            $Anorm = $oPub->getRow($sql);
			if( $Anorm["name"] != ''){
			  $tn ++ ;
	          $tmpstr = ($n % 2 == 0)?"even":"odd";
              $Strtab  .= '<TR class='.$tmpstr.'>';

              $Strtab  .= '<TD align=left>'.$str.$tn.'）'.$Anorm["name"].'</TD>';
			  $Strtab  .= '<TD align=left>'.$Anorm["descs"].'</TD>';
			  $tmp = ($Anorm["ifshow"])?'是':'否';
			  $Strtab  .= '<TD align=left>'.$tmp.'</TD>';
	          $tmp = ($Anorm["ifnav"])?'是':'否';
	          $Strtab .= '<TD align=left>'.$tmp.'</TD>';
              $Strtab  .= '<TD align=left><a href="'.$PHP_SELF.'?acid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> ';
	          $Strtab  .= '<a href="'.$PHP_SELF.'?acid='.$v.'&fid='.$fid.'&action=del" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]" ></a></TD>';
              $Strtab  .= '</TR>';  
	          $n ++;
              $Strtab .= tab_next_node($Anorm["next_node"],$v,$str .= '　');
		      $str = '　';
			}
	   }
	}
	return $Strtab;
}
?>	
<?php
include_once( "footer.php");
?>
