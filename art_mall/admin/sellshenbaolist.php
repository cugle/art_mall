<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");

if($Aconf['priveMessage'] != '') {
   echo showMessage($Aconf['priveMessage']);
   exit;
} 

if( $_GET['action'] == 'edit' || $_GET['action'] == 'show'){ 
	$id = $_REQUEST[id] + 0;
	$sql = "SELECT a.*     
	        FROM ".$pre."shenbao  as a 
			where  a.id = '$id'
			AND a.domain_id=".$Aconf['domain_id'];
    $work = $oPub->getRow($sql); 
 
	$work['nowend']  = $work['dateadd'] + 1800;  
 
}
if( $_GET[action] == 'del') {  
	 if(in_array($_SESSION[$un_auser_id],$Adminconf['allowManage'])){
		 $condition = 'id='.$_GET['id'] ; 
		 $oPub->delete($pre."shenbao",$condition);  
	 }  
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
	if($_REQUEST["shenhestats"] == 3)
	{
		$where .= " AND `shenhestats` < 1 ";
	}else
	{
		$where .= " AND `shenhestats` = '".$_REQUEST["shenhestats"]."'";
	}
}

$sql = "SELECT COUNT(*) as count FROM ".$pre."shenbao  AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$Aconf['set_pagenum'] = 40;
$page->PageSize = $Aconf['set_pagenum'];
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('action'=>$_GET["action"]); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."shenbao  WHERE  $where ".
       " ORDER BY dateadd DESC ".
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row ) { 
    foreach ($row AS $key=>$val) { 
        $row[$key]['dateadd_show']  = ($val['dateadd'])?date("y/n/j H:i", $val['dateadd']):'';  
		$row[$key]['nowend']  = $val['dateadd'] + 1800; 
    } 
}
$StrtypeAll = ''; 

if($row)
foreach ($row AS $key=>$val)
{
		$n ++ ;
		$tmpstr = ($n % 2 == 0)?"even_m":"odd_m";
       $StrtypeAll .= '<TR class="'.$tmpstr.'" onMouseOver="this.style.backgroundColor=\'#FFFFFF\';" onMouseOut="this.style.backgroundColor=\'#e6e6e6\'; ">';  
	   $StrtypeAll .= '<TD align=middle>'.$val["user_name"].'</TD>'; 
	   $StrtypeAll .= '<TD align=middle>'.$val["dateadd_show"].'</TD>';  
	   $kf = $val["khname"].'|联系人：'.$val["linksnames"]; 
	   $kf2 = '客户名:'.$val["khname"].'|客户地址:'.$val["khaddress"].'|联系人：'.$val["linksnames"].'|电话：'.$val["tel"];
	   $StrtypeAll .= '<TD align=middle><a title="'.$kf2.'">'.$kf.'</a></TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["proname"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["proguige"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["promoney"].'<br/><span style="color:#FF0000">'.$val["ifhanshui"].'</span> <span style="color:#0000FF">'.$val["ifhanyunfei"].'</span></TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["nums"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.($val["nums"] * $val["promoney"]) .'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.$val["shoukuanmoshi"].'</TD>';
	   $StrtypeAll .= '<TD align=middle>'.date("y-m-d",$val["profukandate"]).'</TD>'; 
	   $StrtypeAll .= '<TD align=middle title="'.$val["descs"].'">'.sub_str($val["descs"],5).'</TD>'; 

		$strstates = '';
	   if($val["shenhestats"] == 1 ){
			$strstates = '<span style="color:#FF0000">未通过</span>';
			$states = '【未通过,by '.$val["shenhename"].' '.$val["shenhedate"].'】'.$val["shenhedescs"];
	   }
	   elseif($val["shenhestats"] == 2){
			 $strstates = '已通过';
			 $states = '【已通过,by '.$val["shenhename"].' '.$val["shenhedate"].'】'.$val["shenhedescs"];
	   }
	   else{
			$strstates =  $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>'; 
 
	 
		$StrtypeAll .= '</TD>';

		$StrtypeAll .= '<TD align=center>'; 

		$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=show&page='.$pagenew.'" target="main"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a> _ ';
        if(in_array($_SESSION[$un_auser_id],$Adminconf['allowManage'])){
			$StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?id='.$val["id"].'&action=del&page='.$pagenew.'" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除" onclick="return(confirm(\'确定删除?\'))"></a>'; 
		} 
		$StrtypeAll .= '</TD>';
		$StrtypeAll .= '</TR>';   
} 
?>

<?php
   include_once( "header.php");
   if ($strMessage != ''){
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
   }

?>
<?php if($_GET['action'] == 'show') {?> 
<TABLE width="900" border=0 cellspacing="5" cellpadding="0"> 
  <TR class=bg1>
    <TD align=left >  

		  <TABLE width=96%> 
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">客户名称:</b></TD>
			<TD> 
				<?php echo ($work["id"] > 0)?$work["khname"]:'';?> 
				<b style="color:#FF0000">客户地址:</b>
				 <?php echo ($work["id"] > 0)?$work["khaddress"]:'';?> 
				<b style="color:#FF0000">联系人:</b>
				<?php echo ($work["id"] > 0)?$work["linksnames"]:'';?>
				<b style="color:#FF0000">联系电话:</b>
				<?php echo ($work["id"] > 0)?$work["tel"]:'';?>
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">产品名称:</b></TD>
			<TD> 
				<?php echo ($work["id"] > 0)?$work["proname"]:'';?> 
					<b style="color:#FF0000">规格:</b>
				<?php echo ($work["id"] > 0)?$work["proguige"]:'';?>
				<b style="color:#FF0000">数量:</b>
				 <?php echo ($work["id"] > 0)?$work["nums"]:'';?> 
				<b style="color:#FF0000">单价:</b>
				 <?php echo ($work["id"] > 0)?$work["promoney"]:'0.00';?> 元；
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">收款模式:</b></TD>
			<TD>
				 <?php echo ($work["id"] > 0)?$work["shoukuanmoshi"]:'';?> 
				<b style="color:#FF0000">收款时间</b>  
				 <?php echo ($work["id"] > 0)?date("Y-m-d",$work["profukandate"]):date("Y-m-d");?> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">备注说明:</b></TD>
			<TD>
		    	   <?php echo ($work["id"] > 0)?$work["descs"]:'';?> 
				   
			</TD>
		  </TR> 
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">税 运费:</b></TD>
			<TD>
					<span style="color:#00FF00"> <? echo ($work["ifhanshui"] == '含税')?'含税':'未税';?>  
 					 <? echo ($work["ifhanyunfei"] == '含运费')?'含运费':'不含运费';?> </span>

			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">申报时间:</b></TD>
			<TD>
		    	   <?php echo ($work["id"] > 0)?date("y-n-j H:i", $work['dateadd']):'';?> 
				   
			</TD>
		  </TR> 
		  <TR>
			<TD width="120" align="right"><b style="color:#FF0000">审核状态:</b></TD>
			<TD> 
				  <?php 
			if($work["shenhestats"] == 2){	
				echo '【已通过,by '.$work["shenhename"].' '.$work["shenhedate"].'】'.$work["shenhedescs"];
			}elseif($work["shenhestats"] == 1){
				echo '未通过'.'【已通过,by '.$work["shenhename"].' '.$work["shenhedate"].'】'.$work["shenhedescs"];
			}else{
				echo '未处理';
			}
			?>
				   
			</TD>
		  </TR>
		  </TABLE>
    </TD> 
  </TR> 
</table>  
<?php } ?>
<TABLE width="100%" border=0 cellspacing="1" cellpadding="0">
  <form action="" method="post" name="theForm" style="margin: 0">
  <TR class=bg1>
    <TD align=left > 
		姓名：<INPUT TYPE="text" NAME="user_name" value="<?php echo $_REQUEST["user_name"];?>" size="10">
		审核者：<INPUT TYPE="text" NAME="shenhename" value="<?php echo $_REQUEST["shenhename"];?>" size="10">
		审核状态：
		<SELECT NAME="shenhestats">
			<OPTION VALUE="0" <?php echo $_REQUEST["shenhestats"]<1?'SELECTED':''?>>所有</option>
			<OPTION VALUE="3" <?php echo $_REQUEST["shenhestats"]==3?'SELECTED':''?>>未审核</option>
			<OPTION VALUE="1" <?php echo $_REQUEST["shenhestats"]==1?'SELECTED':''?>>没通过</option>
			<OPTION VALUE="2" <?php echo $_REQUEST["shenhestats"]==2?'SELECTED':''?>>已审核</option>
		</SELECT> 
       开始日期
		<input style="width: 76px" name="start_time" id="start_time" value="<?php echo ($start_time)?date("Y-m-d",$start_time):'';?>" readonly="readonly" type="text">
		<input name="selbtn1" id="selbtn1" onclick="return showCalendar('start_time', '%Y-%m-%d', false, false, 'selbtn1');" value="选择" type="button" > 
        结束日期<input style="width: 76px" name="end_time" id="end_time"  value="<?php echo ($end_time)?date("Y-m-d",$end_time):date("Y-m-d");?>" readonly="readonly" type="text">
		<input name="selbtn2" id="selbtn2" onclick="return showCalendar('end_time', '%Y-%m-%d', false, false, 'selbtn2');" value="选择"  type="button" >
	 
         <input type="submit" value="查询提交" style="background-color: #FFCC66;margin-left: 10px"/>
         <input type="hidden" name="act" value="search" />   
 
    </TD> 
  </TR>
 </form> 
</table> 

<TABLE width="100%" border=0 cellspacing="1" cellpadding="0"> 
  <TR class=bg5> 
	<TD align=middle width="5%">姓名</TD> 
	<TD align=middle width="10%">申报时间</TD>
	<TD align=middle width="13%">客户信息</TD>
	<TD align=middle width="10%">产品名</TD>
	<TD align=middle width="5%">规格</TD>
	<TD align=middle width="7%">单价</TD> 
	<TD align=middle width="5%">数量</TD>
	<TD align=middle width="5%">合计</TD>
	<TD align=middle width="10%">收款方式</TD> 
	<TD align=middle width="10%">收款日期</TD>
	<TD align=middle width="10%">备注</TD>
	<TD align=middle width="5%">审核状态</TD>
	<TD align=middle width="5%">操作</TD>   
  </TR>
   <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=middle colspan="13">
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

