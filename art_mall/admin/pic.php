<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '')
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
 
/* 查询条件 */
$db_table = $pre."artitxt";
$filter['sort_by']    = empty($_REQUEST['sort_by'])    ? 'top' : trim($_REQUEST['sort_by']);
$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

/*

*/
$where = " domain_id = '".$Aconf['domain_id']."' and ifpic > 0 ";
  
if($_REQUEST[start_time])
{
	$_REQUEST[start_time] = $_REQUEST[start_time];
	$_REQUEST[end_time] = $_REQUEST[end_time];
    $start_time = local_strtotime("$_REQUEST[start_time] 00:00:00");
    $end_time   = local_strtotime("$_REQUEST[end_time]  23:59:59");
	$where .= " AND dateadd >= '".$start_time."' AND dateadd <= '".$end_time."'";
}

if($_REQUEST[name])
{
	$where .= " AND `name` LIKE '%".$_REQUEST[sear_name]."%'";
}

if($_REQUEST[acid] > 0 )
{
	$acid = $_REQUEST[acid];
	//查询包含的所有子分类
	$db_table = $pre."articat";
    $strAcid_sear = $acid.','.next_node_all($acid,$db_table,'acid',true).',';
    $Aarticlecat_sear = explode(',',$strAcid_sear);
    $Aarticlecat_sear = array_unique($Aarticlecat_sear);
	$articlecat_sear = '';
	foreach ($Aarticlecat_sear AS  $v)
	{
          if($v > 0 )
		  {
              $articlecat_sear .= $v.',';
		  }
	} 
	$articlecat_sear = substr($articlecat_sear,0,-1);
	$where .= ' AND acid in('.$articlecat_sear.') ';
}
else
{
   if ( $_SESSION['aaction_list'] == 'all')
   {}
   else
   {  
       $where .= ' AND acid in('.$_SESSION['aarticlecat_list'].') ';
   }
}


$db_table = $pre."artitxt";


$sql = "SELECT COUNT(*) as count FROM ".$db_table." WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);

$Asum[sum_count]   = $row[count];

$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 12;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('acid'=>$_REQUEST[acid],'sort_by'=>$filter['sort_by'],'start_time'=>$_REQUEST[start_time],'end_time'=>$_REQUEST[end_time],'dateadd'=>$_REQUEST[dateadd],'name'=>$_REQUEST[sear_name]); 

$http_var = '&acid='.$_REQUEST[acid].'&sort_by='.$filter['sort_by'].'&start_time='.$_REQUEST[start_time].'&end_time='.$_REQUEST[end_time].'&dateadd='.$_REQUEST[dateadd].'&sear_name='.$_REQUEST[sear_name];

$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$db_table.
       " WHERE  $where ".
       " ORDER BY ifpic desc,arti_date desc ".
       " LIMIT ". $strOffSet;

$row = $oPub->select($sql);
if($row )
{ 
	$db_table = $pre."articat";
    foreach ($row AS $key=>$val)
    {
        $row[$key]['dateadd']  = ($val['dateadd'])?date("n.j g:i", $val['dateadd']):'';
		$row[$key]['arti_date']  = ($val['arti_date'])?date("n.j g:i", $val['arti_date']):'';
        $sql = "SELECT name FROM ".$db_table." WHERE acid=$val[acid]";
		$row2 = $oPub->getRow($sql);
		$row[$key]['acname'] = $row2[name];
    }
}
$StrtypeAll = '';
$picrow = $row;
unset($row);
 

/* 找到所有的分类到select start*/
$db_table = $pre."articat"; 
$sql = "SELECT * FROM ".$db_table." where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC";
$AnormAll = $oPub->select($sql);

$Stropt = '<SELECT NAME="acid" >';
$tmp = ($filter['acid'] == 0)?'SELECTED':'';
$Stropt .= '<OPTION VALUE="0" '.$tmp.'>所有分类</OPTION>';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   
	   if(is_array($Aarticlecat_list))
	   {
	      if(in_array($value["acid"],$Aarticlecat_list))
	      {
		  $n ++;
          $selected = ($_REQUEST['acid'] == $value["acid"])? 'SELECTED':'';
          $Stropt .= '<OPTION VALUE="'.$value["acid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
	      }
	   }
	   else
	   {
		  $n ++;
          $selected = ($_REQUEST['acid'] == $value["acid"])? 'SELECTED':'';
          $Stropt .= '<OPTION VALUE="'.$value["acid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>';
	   }
	   /* 查找儿子 */
       if($value["next_node"] != ''){          
           $Stropt .= get_next_node($value["next_node"],$_REQUEST['acid'],$str = '　',$Aarticlecat_list);
	   }
	
}
$Stropt .= '</SELECT>';
/* 找到所有的分类到select end*/
?>
<?php
   include_once( "header.php");
?>
<?php
if ($strMessage != '')
{
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<script src="js/calendar/calendar.js"  type="text/javascript" ></script>
<link href="js/calendar/calendar.css" rel="stylesheet" type="text/css">

<TABLE width="100%" border=0>

  <TR class=bg1>
    <TD align=left colspan="6">
	<FORM METHOD=POST ACTION="" style="margin: 0px">
	   <span style="float: left">    
	   <?php echo $Stropt;?> 
        开始日期
		<input style="width: 68px" name="start_time" id="start_time" value="<?php echo ($start_time)?date("Y-m-d",$start_time):'';?>" readonly="readonly" type="text">
		<input name="selbtn1" id="selbtn1" onclick="return showCalendar('start_time', '%Y-%m-%d', false, false, 'selbtn1');" value="选择" type="button" >

        结束日期<input style="width: 68px" name="end_time" id="end_time"  value="<?php echo ($end_time)?date("Y-m-d",$end_time):date("Y-m-d");?>" readonly="readonly" type="text">
		<input name="selbtn2" id="selbtn2" onclick="return showCalendar('end_time', '%Y-%m-%d', false, false, 'selbtn2');" value="选择"  type="button" >
		标题<INPUT TYPE="text" NAME="sear_name" value="<?php echo $_REQUEST[sear_name];?>" >
        <INPUT TYPE="submit" name="submit" value="确定查询">
		<INPUT TYPE="hidden" name="act" value="datesearch">
		<INPUT TYPE="hidden" name="action" value="">
       </span>
	   <span style="float: right"><a href="articlesend.php"> [添加新图库]</a> </span>
	   </FORM>
	</TD> 
  </TR>
</table>
 
<?php
$str = '';
if($picrow)
foreach ($picrow AS $k => $v) {

	$str .= '<div style="float:left; text-align:center; border: 1px solid #FFCC00; margin: 4px; padding:2px;background-color:#FFFFFF">';
	$str .= '<a href="../pic.php?id='.$v["arid"].'" target="_blank"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="阅读"></a>__';
	$str .= '<a href="articlesend.php?arid='. $v["arid"].'&action=edit" target="main"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> '; 
	$str .= '<span id="ifpic_'.$v["arid"].'">';
	$str .= '排序<INPUT TYPE="text" value="'.$v["ifpic"].'" size="2" onDblClick=art_list_edit(\'ifpic\',\''.$v["arid"].'\',this.value) />';
	$str .= '</span>'; 
	$str .=  $v["arti_date"];
	$str .= '<br />'; 
	$str .= sub_str($v["name"],16,true); 
	$str .= '<br />';  
	$str .= '<img src="../'.$v['min_thumb'].'" width="'.$Aconf['min_thumb_w'].'" height="'.$Aconf['min_thumb_h'].'"  />';  
	$str .= '</div>'; 
}
echo $str;
?>
 

<TABLE width="100%" border=0> 
  <TR class=bg5>
    <TD  align=right colspan="15"> 
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR> 
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
   global $oPub,$pre;
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
			   if(is_array($Aarticat))
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
              $Strtab  .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?acid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> ';
	          $Strtab  .= '<a href="'.$_SERVER["PHP_SELF"].'?acid='.$v.'&fid='.$fid.'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
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
