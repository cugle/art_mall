<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  

if($Aconf['priveMessage'] != '') {
   echo showMessage($Aconf['priveMessage']);
   exit;
}
 
if( $_GET[action] == 'del') {  
	 if(in_array($_SESSION['auser_id'],$Adminconf['allowManage'])){
		 $condition = 'id='.$_GET['id'] ; 
		 $oPub->delete($pre."feibao",$condition);  
	 }  
} 
 
if(  $_GET['action'] == 're'){ 
	$id = $_REQUEST[id] + 0;
	$sql = "SELECT a.*     
	        FROM ".$pre."feibao  as a 
			where  a.id = '$id'
			AND a.domain_id=".$Aconf['domain_id'];
    $work = $oPub->getRow($sql);  
} 
$allowpre = false;
if(in_array($_SESSION['auser_id'],$Adminconf['allowManage'])){
	$allowpre = true;	
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
if($_REQUEST["shenhecwstats"])
{
	$where .= " AND `shenhecwstats` = '".$_REQUEST["shenhecwstats"]."'";
}
if($_REQUEST["chunastatsuserid"] >0 )
{
	if($_REQUEST["chunastatsuserid"] == 1){
		$where .= " AND `chunastatsuserid` < 1 ";
	}else{
		$where .= " AND `chunastatsuserid` >= 1 ";
	}
	
}

//$_SESSION["minipriv"] == 1 && !empty($_POST['miniprivdescs']
if($_SESSION["minipriv"] <> 1 ){
	$where .= " and miniprivdescs=''";
} 

$sql = "SELECT COUNT(*) as count FROM ".$pre."feibao  AS a WHERE 1 AND ". $where;
$row = $oPub->getRow($sql);
$filter['record_count'] = $row[count];
unset($row);
$page = new ShowPage;
$page->PageSize = 40;
$page->Total = $filter['record_count'];
$pagenew = $page->PageNum();
$page->LinkAry = array('user_name'=>$_REQUEST["user_name"],'shenhename'=>$_REQUEST["shenhename"],'shenhestats'=>$_REQUEST["shenhestats"],'shenhecwstats'=>$_REQUEST["shenhecwstats"],'chunastatsuserid'=>$_REQUEST["chunastatsuserid"],'start_time'=>$_REQUEST[start_time],'end_time'=>$_REQUEST[end_time]); 
$strOffSet = $page->OffSet();

$sql = "SELECT * FROM ".$pre."feibao  WHERE  $where ".
       " ORDER BY dateadd DESC ".
       " LIMIT ". $strOffSet;
$row = $oPub->select($sql);
if($row ) { 
    foreach ($row AS $key=>$val) { 
        $row[$key]['dateadd_show']  = ($val['dateadd'])?date("y/n/j H:i", $val['dateadd']):'';  
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
	    $StrtypeAll .= '<TD align=middle><span style="color:#0080C0">'.$val["premoney"].'</span></TD>';
	   $StrtypeAll .= '<TD align=middle><span style="color:#FF0000">'.$val["money"].'</span></TD>';  
	   $StrtypeAll .= '<TD align=middle title="'.$val["descs"].'">'.sub_str($val["descs"],20).'</TD>'; 
	$strstates = '';
	   if($val["prestats"] == 1 ){
			$strstates = '未通过';
			if($allowpre){
				$states = '【未通过,by '.$val["preuserid"].' '.$val["prename"].'】'.$val["predescs"];
			}else{
				$states = '【未通过 】'.$val["predescs"];
			}
	   }
	   elseif($val["prestats"] == 2){
			 $strstates = '已通过'; 
			if($allowpre){
				$states = '【已通过,by '.$val["preuserid"].' '.$val["prename"].'】'.$val["predescs"];
			}else{
				$states = '【已通过 】'.$val["predescs"];
			}

	   }
	   else{
			$strstates = $states = '';
	   }

		 if($_SESSION["minipriv"] == 1 && !empty($val["miniprivdescs"])){
			$strstates .= '<span style="color:#F00">注</span>';
			$states .= $val["miniprivdescs"];
		 }

	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.' '.$val["predate"].'">'.$strstates.'</a></TD>';
	   if($val["shennwstats"] == 1 ){
			$strstates = '未通过';
			if($allowpre){
				$states = '【未通过,by '.$val["shennwuserid"].' '.$val["shennwdate"].'】'.$val["shennwdescs"];
			}else{
				$states = '【未通过 '.$val["shennwdate"].'】'.$val["shennwdescs"];
			}
	   }
	   elseif($val["shennwstats"] == 2){
			 $strstates = '已通过';
			 if($allowpre){
				 $states = '【已通过,by '.$val["shennwuserid"].' '.$val["shennwdate"].'】'.$val["shennwdescs"];
			 }else{
				$states = '【已通过 '.$val["shennwdate"].'】'.$val["shennwdescs"];
			 }
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>';
		$strstates = '';
	   if($val["shenhestats"] == 1 ){
			$strstates = '未通过';
			 if($allowpre){
				$states = '【未通过,by '.$val["shenheuserid"].' '.$val["shenhedate"].'】'.$val["shenhedescs"];
			 }else{
				$states = '【未通过'.$val["shenhedate"].'】'.$val["shenhedescs"];
			 }
	   }
	   elseif($val["shenhestats"] == 2){
			 $strstates = '已通过';
			 if($allowpre){
				 $states = '【已通过,by '.$val["shenheuserid"].' '.$val["shenhedate"].'】'.$val["shenhedescs"];
			 }else{
				$states = '【已通过'.$val["shenhedate"].'】'.$val["shenhedescs"];
			 }
			 
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states.'">'.$strstates.'</a></TD>'; 

		$strstates = '';
	   if($val["shenhecwstats"] == 1 ){
			$strstates = '<span style="color:#FF0000">未通过</span>'; 
			if($allowpre){
				$states = '【未通过,by '.$val["shenhecwuserid"].' '.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
			}else{
				$states = '【未通过 '.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
			}
			
	   }
	   elseif($val["shenhecwstats"] == 2){
			 $strstates = '已通过';
			if($allowpre){
				$states = '【已通过,by '.$val["shenhecwuserid"].' '.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
			}else{
				$states = '【已通过'.$val["shenhecwdate"].'】'.$val["shenhecwdescs"];
			}
			 
	   }
	   else{
			$strstates = $states = '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states .'">'.$strstates.'</a></TD>'; 

		$strstates = '';
	   if($val["chunastatsuserid"] >0 ){
			$strstates = '已付款';
			if($allowpre){
				$states = '已付款'.$val["chunadescs"];
			}else{
				$states = '已付款';
			}
			
	   }
	   else{
			$strstates = $states =  '';
	   }
	   $StrtypeAll .= '<TD align=middle ><a title="'.$states .'">'.$strstates.'</a></TD>'; 

		$StrtypeAll .= '<TD align=center>';
 
		$StrtypeAll .= '<a href="'.$_SERVICE["PHP_SELF"].'?id='.$val["id"].'&action=re&page='.$pagenew.'" target="main"><IMG SRC="images/icon_view.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="查阅"></a>';
 
        if($allowpre){
			$StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?id='.$val["id"].'&action=del&page='.$pagenew.'" onclick="return(confirm(\'确定删除?\'))"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除" onclick="return(confirm(\'确定删除?\'))"></a>'; 
		} 
		$StrtypeAll .= '</TD>';

 
		$StrtypeAll .= '</TR>';   
} 
$sql = "SELECT sum(money) as money FROM ".$pre."feibao  WHERE  $where ";
$summoney = $oPub->getOne($sql);
$StrtypeAll .= '<TR >';  
$StrtypeAll .= '<TD align=middle> </TD>'; 
$StrtypeAll .= '<TD align=middle> </TD>'; 
$StrtypeAll .= '<TD align=middle><span style="color:#FF0000">资金流量:</span></TD>';
$StrtypeAll .= '<TD align=middle><span style="color:#FF0000">'.$summoney.'</span></TD>'; 
$StrtypeAll .= '<TD align=middle > </TD>'; 
 
$StrtypeAll .= '<TD align=middle > </TD>'; 
$StrtypeAll .= '<TD align=middle > </TD>'; 
$StrtypeAll .= '<TD align=middle > </TD>';  
$StrtypeAll .= '<TD align=middle > </TD>'; 


$StrtypeAll .= '</TR>';
?>

<?php
   include_once( "header.php");
   if ($strMessage != ''){
     echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
   }

?>
<?php if($work["id"] >0 ){?> 
<TABLE width="100%" border=0 cellspacing="1" cellpadding="0"> 
  <TR class=bg1>
    <TD align=left > 
 
		  <TABLE width=96%>
		  <TR>
			<TD width="120" align="right"><b>姓名:</b></TD>
			<TD>
				<span style="color:#0000ff"><?php echo ($work["id"] > 0)?$work["user_name"]:'';?></span>   
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>申请金额:</b></TD>
			<TD> 
			 <span style="color:#0000ff"><?php echo ($work["id"] > 0)?$work["premoney"].'元':'';?></span> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>实报金额:</b></TD>
			<TD> 
			 <span style="color:#ff0000"><?php echo ($work["id"] > 0)?$work["money"].'元':'';?></span> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>用途说明:</b></TD>
			<TD>
				 <span style="color:#0000ff"><?php echo ($work["id"] > 0)?$work["descs"]:'';?> </span>
			</TD>
		  </TR>  
		  <TR>
			<TD width="120" align="right"><b>申报时间:</b></TD>
			<TD>
				  <span style="color:#0000ff"><?php echo ($work["id"] > 0)?date("y-n-j H:i", $val['dateadd']):'';?> </span> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>请款预备审核:</b></TD>
			<TD>
				  <span style="color:#0000ff">
				  <?php
						$strstates = ''; //predate preuserid prename predescs 
					   if($work["prestats"] == 1 ){
						   if($allowpre){
								$strstates = '<span style="color:#FF0000">【未通过,by '.$work["prename"].' '.$work["predate"].'】</span>'.$work["predescs"];
						   }else{
								$strstates = '<span style="color:#FF0000">【未通过 '.$work["predate"].'】</span>'.$work["predescs"];
						   }
					   }
					   elseif($work["prestats"] == 2){
						   if($allowpre){
								$strstates = '【已通过,by '.$work["prename"].' '.$work["predate"].'】'.$work["predescs"];
						   }else{
								$strstates = '【已通过 '.$work["predate"].'】'.$work["predescs"];
						   }
					   }
					   else{
							$strstates = '暂未审核';
					   }
					   echo $strstates;
				   ?> 
				   </span> 
			</TD>
		  </TR> 
		  <TR>
			<TD width="120" align="right"><b>内务审核:</b></TD>
			<TD>
				  <span style="color:#0000ff">
				  <?php
						$strstates = '';
					   if($work["shennwstats"] == 1 ){
						   if($allowpre){
								$strstates = '<span style="color:#FF0000">【未通过,by '.$work["shennwname"].' '.$work["shennwdate"].'】</span>'.$work["shennwdescs"];
						   }else{
								$strstates = '<span style="color:#FF0000">【未通过'.$work["shennwdate"].'】</span>'.$work["shennwdescs"];
						   }
					   }
					   elseif($work["shennwstats"] == 2){
						   if($allowpre){
							    $strstates = '【已通过,by '.$work["shennwname"].' '.$work["shennwdate"].'】'.$work["shennwdescs"];
						   }else{
								$strstates = '【已通过 '.$work["shennwdate"].'】'.$work["shennwdescs"];
						   }
					   }
					   else{
							$strstates = '暂未审核';
					   }
					   echo $strstates;
				   ?> 
				   </span> 
			</TD>
		  </TR> 

		  <TR>
			<TD width="120" align="right"><b>财务审核:</b></TD>
			<TD>
				  <span style="color:#0000ff">
				  <?php
						$strstates = '';
					   if($work["shenhestats"] == 1 ){
						   if($allowpre){
								$strstates = '<span style="color:#FF0000">【未通过,by '.$work["shenhename"].' '.$work["shenhedate"].'】</span>'.$work["shenhedescs"];
						   }else{
								$strstates = '<span style="color:#FF0000">【未通过 '.$work["shenhedate"].'】</span>'.$work["shenhedescs"];
						   }
					   }
					   elseif($work["shenhestats"] == 2){
						   if($allowpre){
							   $strstates = '【已通过,by '.$work["shenhename"].' '.$work["shenhedate"].'】'.$work["shenhedescs"];
						   }else{
							   $strstates = '【已通过 '.$work["shenhedate"].'】'.$work["shenhedescs"];
						   }
					   }
					   else{
							$strstates = '暂未审核';
					   }
					   echo $strstates;
				   ?> 
				   </span> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>董事审核:</b></TD>
			<TD>
				  <span style="color:#0000ff">
				  <?php

						$strstates = '';
					   if($work["shenhecwstats"] == 1 ){
						   if($allowpre){
								$strstates = '<span style="color:#FF0000">【未通过,by '.$work["shenhecwname"].' '.$work["shenhecwdate"].'】</span>'.$work["shenhecwdescs"];
						   }else{
								$strstates = '<span style="color:#FF0000">【未通过 '.$work["shenhecwdate"].'】</span>'.$work["shenhecwdescs"];
						   }
					   }
					   elseif($work["shenhecwstats"] == 2){
						   if($allowpre){
								$strstates = '【已通过,by '.$work["shenhecwname"].' '.$work["shenhecwdate"].'】'.$work["shenhecwdescs"];
						   }else{
								$strstates = '【已通过 '.$work["shenhecwdate"].'】'.$work["shenhecwdescs"];
						   }
					   }
					   else{
							$strstates = '未执行';
					   }
					   echo $strstates;
				   ?> 
				   </span> 
			</TD>
		  </TR>
		  <TR>
			<TD width="120" align="right"><b>出纳审核:</b></TD>
			<TD>
				  <span style="color:#0000ff">
				  <?php

						$strstates = '';
					   if($work["chunastatsuserid"] >0 ){
						   if($allowpre){
								$strstates = '已付款'.$work["chunadescs"]; 
						   }else{
								$strstates = '已付款';
						   }
					   }
					   else{
							$strstates = '未付款';
					   }
					   echo $strstates;
				   ?> 
				   </span>
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
		财务审核：
		<SELECT NAME="shenhestats">
			<OPTION VALUE="0" <?php echo $_REQUEST["shenhestats"]<1?'SELECTED':''?>>所有</option>
			<OPTION VALUE="1" <?php echo $_REQUEST["shenhestats"]==1?'SELECTED':''?>>没通过</option>
			<OPTION VALUE="2" <?php echo $_REQUEST["shenhestats"]==2?'SELECTED':''?>>已审核</option>
		</SELECT> 
		董事状态：
		<SELECT NAME="shenhecwstats">
			<OPTION VALUE="0" <?php echo $_REQUEST["shenhestats"]<1?'SELECTED':''?>>所有</option>
			<OPTION VALUE="1" <?php echo $_REQUEST["shenhestats"]==1?'SELECTED':''?>>没通过</option>
			<OPTION VALUE="2" <?php echo $_REQUEST["shenhestats"]==2?'SELECTED':''?>>已审核</option>
		</SELECT> 
		出纳审核：
		<SELECT NAME="chunastatsuserid">
			<OPTION VALUE="0" <?php echo $_REQUEST["chunastatsuserid"]<1?'SELECTED':''?>>所有</option>
			<OPTION VALUE="1" <?php echo $_REQUEST["chunastatsuserid"]==1?'SELECTED':''?>>未付款</option>
			<OPTION VALUE="2" <?php echo $_REQUEST["chunastatsuserid"]==2?'SELECTED':''?>>已付款</option>
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
	<TD align=middle width="8%">姓名</TD> 
	<TD align=middle width="10%">时间</TD>
	<TD align=middle width="8%">申请金额</TD>
	<TD align=middle width="8%">实报金额</TD>
	<TD align=middle width="22%">用途说明</TD>
	<TD align=middle width="10%">请款预备审核</TD>
	<TD align=middle width="6%">内务审核</TD>
	<TD align=middle width="6%">财务审核</TD>
	<TD align=middle width="8%">董事审核</TD>
	<TD align=middle width="8%">出纳审核</TD> 
	<TD align=middle width="8%">操作</TD> 
  </TR>
   <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=middle colspan="11">
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

