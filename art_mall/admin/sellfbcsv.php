<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '') {
   echo showMessage($Aconf['priveMessage']);
   exit;
} 
 
//列表
$where = " domain_id = '".$Aconf['domain_id']."'";  
  
if($_REQUEST[start_time])
{
	$_REQUEST[start_time] = $_REQUEST[start_time];
	$_REQUEST[end_time] = $_REQUEST[end_time];
    $start_time = local_strtotime("$_REQUEST[start_time] 00:00:00");
    $end_time   = local_strtotime("$_REQUEST[end_time]  23:59:59");
	$where .= " AND dateadd >= '".$start_time."' AND dateadd <= '".$end_time."'";
}
else{
	$end_time=gmtime();
}

if($_REQUEST["user_name"])
{
	$where .= " AND `user_name` LIKE '".$_REQUEST["user_name"]."'";
}
if($_REQUEST["shenhename"])
{
	$where .= " AND `shenhename` LIKE '".$_REQUEST["shenhename"]."'";
}
if($_REQUEST["shenhestats"])
{
	$where .= " AND `shenhestats` = '".$_REQUEST["shenhestats"]."'";
}

if($_SESSION["minipriv"] <> 1 ){
	$where .= " and miniprivdescs=''";
} 

$sql = "SELECT COUNT(*) as count FROM ".$pre."feibao  AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = $Aconf['set_pagenum'];
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('user_name'=>$_REQUEST["user_name"],'shenhename'=>$_REQUEST["shenhename"],'shenhestats'=>$_REQUEST["shenhestats"],'start_time'=>$_REQUEST[start_time],'end_time'=>$_REQUEST[end_time]); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."feibao  WHERE  $where ".
       " ORDER BY chunastatsuserid asc,dateadd DESC ".
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row ) { 
    foreach ($row AS $key=>$val) { 
        $row[$key]['dateadd_show']  = ($val['dateadd'])?date("y/n/j H:i", $val['dateadd']):'';  
    } 
}
$StrtypeAllcsv  = $StrtypeAll = ''; 

if($row)
foreach ($row AS $key=>$val)
{
		$n ++ ;
		$tmpstr = ($n % 2 == 0)?"even_m":"odd_m";
       $StrtypeAll .= '<TR class="'.$tmpstr.'" onMouseOver="this.style.backgroundColor=\'#FFFFFF\';" onMouseOut="this.style.backgroundColor=\'#e6e6e6\'; ">';  
	   $StrtypeAll .= '<TD align=middle>'.$val["user_name"].'</TD>'; 
	   $StrtypeAllcsv .= $val["user_name"].',';
	   $StrtypeAll .= '<TD align=middle>'.$val["dateadd_show"].'</TD>';
	   $StrtypeAllcsv .= $val["dateadd_show"].',';
	   $StrtypeAll .= '<TD align=middle><span style="color:#FF0000">'.$val["money"].'</span></TD>'; 
	   $StrtypeAllcsv .= $val["money"].',';
	   $StrtypeAll .= '<TD align=middle title="'.$val["descs"].'">'.sub_str($val["descs"],20).'</TD>';
	   $StrtypeAllcsv .=str_replace(",","，",$val["descs"]).',';

		$strstates = '';
	   if($val["shennwstats"] == 1 ){
			$strstates = '<span style="color:#FF0000">【未通过,by '.$val["shennwname"].' '.$val["shennwdate"].'】</span>'.$val["shennwdescs"];
			$states = '<span style="color:#FF0000">【未通过】</span>';
	   }
	   elseif($val["shennwstats"] == 2){
			 $strstates = '【已通过,by '.$val["shennwname"].' '.$val["shennwdate"].'】'.$val["shennwdescs"];
			 $states = '【已通过】';
	   }
	   else{
			$strstates = $states ='';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$strstates.'">'.$states.'</a></TD>'; 
		$StrtypeAllcsv .=str_replace(",","，",$strstates).',';

		$strstates = '';
	   if($val["shenhestats"] == 1 ){
			$strstates = '<span style="color:#FF0000">【未通过,by '.$val["shenhename"].' '.$val["shenhedate"].'】</span>'.$val["shenhedescs"];
			$states = '<span style="color:#FF0000">【未通过】</span>';
	   }
	   elseif($val["shenhestats"] == 2){
			 $strstates = '【已通过,by '.$val["shenhename"].' '.$val["shenhedate"].'】'.$val["shenhedescs"];
			 $states = '【已通过】';
	   }
	   else{
			$strstates = $states ='';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$strstates.'">'.$states.'</a></TD>'; 
		$StrtypeAllcsv .=str_replace(",","，",$strstates).',';

		$strstates = '';
	   if($val["shenhecwstats"] == 1 ){
			$strstates = '<span style="color:#FF0000">【未通过,by '.$val["shenhecwname"].' '.$val["shenhecwdate"].'】</span>'.$val["shenhecwdescs"];
			$states = '<span style="color:#FF0000">【未通过】</span>';
	   }
	   elseif($val["shenhecwstats"] == 2){
			 $strstates = '【已通过,by '.$val["shenhecwname"].' '.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
			  $states = '【已通过】';
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$strstates.'">'.$states.'</a></TD>'; 
		$StrtypeAllcsv .=str_replace(",","，",$strstates).',';


		$strstates = '';
	   if($val["chunastatsuserid"] >0 ){
			$strstates = '已付款'.$val["chunadescs"]; 
			 $states ='已付款';
	   }
	   else{
			$strstates =  $states ='';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$strstates.'">'. $states.'</a></TD>'; 
		 $StrtypeAllcsv .= $strstates."\r\n";
 
		$StrtypeAll .= '</TR>';   
} 
$sql = "SELECT sum(money) as money FROM ".$pre."feibao  WHERE  $where ";
$summoney = $oPub->getOne($sql);
$StrtypeAll .= '<TR  >';  
$StrtypeAll .= '<TD align=middle> </TD>'; 
 $StrtypeAllcsv .=  ',';
$StrtypeAll .= '<TD align=middle>合计：</TD>';
$StrtypeAllcsv .=  '合计：,';
$StrtypeAll .= '<TD align=middle><span style="color:#FF0000">'.$summoney.'</span></TD>'; 
$StrtypeAllcsv .=  $summoney.',';
$StrtypeAll .= '<TD align=middle > </TD>'; 
 $StrtypeAllcsv .=  ','; 
$StrtypeAll .= '<TD align=middle > </TD>'; 
 $StrtypeAllcsv .=  ','; 
$StrtypeAll .= '<TD align=middle > </TD>'; 
 $StrtypeAllcsv .=  ','; 
$StrtypeAll .= '<TD align=middle > </TD>'; 
 $StrtypeAllcsv .=  ',';
 $StrtypeAll .= '<TD align=middle > </TD>'; 
 $StrtypeAllcsv .=  ',';

$StrtypeAll .= '</TR>';
?>

<?php
 
if($_POST["xact"]==1){
    $filename = iconv("UTF-8","GBK",'费用报销导出')."_".date("Y-m-d-H-i").".csv"; 
	header("Content-type:application/txt; charset=GBK");
    header("Content-Disposition:attachment;filename=".$filename); 
	$StrtypeAllcsv = "姓名,时间,金额,用途说明,内务审核,财务审核,董事状态,出纳审核\r\n".$StrtypeAllcsv;
    echo  iconv("UTF-8","GBK",$StrtypeAllcsv );
    exit;
}

   include_once( "header.php");
   if ($strMessage != ''){
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
   }

?>
 
<TABLE width="100%" border=0 cellspacing="1" cellpadding="0">
  <form action="" method="post" name="theForm" style="margin: 0">
  <TR class=bg1>
    <TD align=left > 
		姓名：<INPUT TYPE="text" NAME="user_name" value="<?php echo $_REQUEST["user_name"];?>" size="10">
		审核者：<INPUT TYPE="text" NAME="shenhename" value="<?php echo $_REQUEST["shenhename"];?>" size="10">
		审核状态：
		<SELECT NAME="shenhestats">
			<OPTION VALUE="0" <?php echo $_REQUEST["shenhestats"]<1?'SELECTED':''?>>所有</option>
			<OPTION VALUE="1" <?php echo $_REQUEST["shenhestats"]==1?'SELECTED':''?>>没通过</option>
			<OPTION VALUE="2" <?php echo $_REQUEST["shenhestats"]==2?'SELECTED':''?>>已审核</option>
		</SELECT> 
       开始日期
		<input style="width: 76px" name="start_time" id="start_time" value="<?php echo ($start_time)?date("Y-m-d",$start_time):'';?>" readonly="readonly" type="text">
		<input name="selbtn1" id="selbtn1" onclick="return showCalendar('start_time', '%Y-%m-%d', false, false, 'selbtn1');" value="选择" type="button" > 
        结束日期<input style="width: 76px" name="end_time" id="end_time"  value="<?php echo ($end_time)?date("Y-m-d",$end_time):date("Y-m-d");?>" readonly="readonly" type="text">
		<input name="selbtn2" id="selbtn2" onclick="return showCalendar('end_time', '%Y-%m-%d', false, false, 'selbtn2');" value="选择"  type="button" >
		<input type="radio" name="xact" value="0" <?php echo $act < 1?'checked':'';?>/>本页显示 
		<input type="radio" name="xact" value="1" <?php echo $act == 1?'checked':'';?>/>保存为CSV	 
         <input type="submit" value="查询提交" style="background-color: #FFCC66;margin-left: 10px"/>
         <input type="hidden" name="act" value="search" />   
	 
    </TD> 
  </TR>
 </form> 
</table> 
 
<TABLE width="100%" border=0 cellspacing="1" cellpadding="0"> 
  <TR class=bg5> 
	<TD align=middle width="10%">姓名</TD> 
	<TD align=middle width="10%">时间</TD>
	<TD align=middle width="10%">金额</TD>
	<TD align=middle width="20%">用途说明</TD>
	<TD align=middle width="10%">内务审核</TD>
	<TD align=middle width="10%">财务审核</TD>
	<TD align=middle width="10%">董事审核</TD>
	<TD align=middle width="10%">出纳审核</TD>  
  </TR>
   <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=middle colspan="8">
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
 </table>
<script src="js/calendar/calendar.js"  type="text/javascript" ></script>
<link href="js/calendar/calendar.css" rel="stylesheet" type="text/css">
<?php
include_once( "footer.php");
?>

