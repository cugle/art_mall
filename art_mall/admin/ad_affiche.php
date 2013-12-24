<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   //echo showMessage($Aconf['priveMessage']);
   //exit;
}

/*------------------------------------------------------ */
//-- 批量删除产品记录
/*------------------------------------------------------ */



/* 查询条件 */
$ad_id = $_GET[ad_id] + 0;
$db_table = $pre."ad_affiche";
$where = " AND ad_id = '".$ad_id."'";

if($_GET["to"] != 'excel')
{
    $sql = "SELECT COUNT(*) as count FROM ".$db_table."  WHERE 1 ". $where;
    $row = $oPub->getRow($sql);
    $filter['record_count'] = $row[count];
    unset($row);
    $page = new ShowPage;
    $page->PageSize = 30;
    $page->Total = $filter['record_count'];
    $pagenew = $page->PageNum();
    $page->LinkAry = array('ad_id'=>$_GET[ad_id]); 
    $strOffSet = $page->OffSet();

    $sql = "SELECT * FROM ".$db_table.
       " WHERE 1 $where ".
       " ORDER BY adddate desc " .
       " LIMIT ". $strOffSet;
    $row = $oPub->select($sql);
 
    if($row)
    foreach ($row AS $key=>$val)
    {
	      $tmpstr = ($n % 2 == 0)?"even":"odd";
	      $n ++ ;
          $StrtypeAll .= '<TR class='.$tmpstr.'>';
	      $StrtypeAll .= '<TD align=left>'.$val["ip"].'</TD>';

	      $StrtypeAll .= '<TD align=left>'.date("m月d日H:i",$val["adddate"]).'</TD>';
	      $StrtypeAll .= '</TR>';    
    }
}
else
{
    //生成 excel报表 生成统计数据：月份 每天
	$sql = "SELECT y,count(*)as count FROM ".$db_table."  WHERE 1 ". $where.
		" group by y desc";
    $row = $oPub->select($sql);
	$strlist = $strlist_d = '';
    if($row)
    foreach ($row AS $key=>$val)
    {
	      $strlist = $val["y"].','.$val["count"]."\r\n";   
		  //按月统计
		  $where_y = $where." AND y='".$val["y"]."'";
	      $sql = "SELECT m,count(*)as count FROM ".$db_table."  WHERE 1 ". $where_y.
		  " group by m asc";
		  $row_m = $oPub->select($sql);		  
		  if($row_m)
		  foreach ($row_m AS $key_m=>$val_m)
          {
			  $strlist .= $val_m["m"].'月,'.$val_m["count"]."\r\n"; 
			  //记录每一天的统计
			  $where_d = $where." AND y='".$val["y"]."' AND m='".$val_m["m"]."'";
	          $sql = "SELECT d,count(*)as count FROM ".$db_table."  WHERE 1 ". $where_d.
		      " group by d asc";
			  $row_d = $oPub->select($sql);	
			  $strlist_d .= "\r\n\r\n".$val_m["m"].'月,'.$val_m["count"];
			  if($row_d)
			  foreach ($row_d AS $key_d=>$val_d)
			  {
				  $strlist .=   $val_d["d"].'日,'.$val_d["count"]."\r\n"; //月记录到每天
				  $strlist_d .= "\r\n".$val_d["d"].'日,'.$val_d["count"]."\r\n"; //月记录到每天
				  //按天详细清单
				  $where_list = $where." AND y='".$val["y"]."' AND m='".$val_m["m"]."' AND d='".$val_d["d"]."'";
				  $sql = "SELECT adddate,ip  FROM ".$db_table."  WHERE 1 ". $where_list.
		          " order by adddate asc";
				  $row_list = $oPub->select($sql);	
				  if($row_list)
				  foreach ($row_list AS $key_list=>$val_list)
			      {

					  $strlist_d .= date("m月d日H:i",$val_list["adddate"]) .",".$val_list["ip"]."\r\n";

			      }
			  }

		  }

    }
    $str = $strlist."\r\n\r\n".$strlist_d;
	$str = iconv("UTF-8","GBK", $str);
	header("Content-type:application/txt; charset=GBK");
    header("Content-Disposition:attachment;filename=total_shudoo_ad.csv");
	echo $str;
	exit;
}




?>
<?php
include_once( "header.php");
if ($strMessage != '')
{
 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
	
<TABLE width="99%" border=0>

  <TR class=bg1>
    <TD align=left colspan="2">
	   <A HREF="<?php echo PHP_SELF;?>?to=excel&ad_id=<?php echo $ad_id;?>" target="_blank">生成统计数据</A>
	</TD> 
  </TR>
  <TR class=bg5 style=" font-weight:bold ">
    <TD width="50%" align=left>IP</TD>
	<TD width="50%" align=left>日期</TD>
  </TR>

 <?php echo $StrtypeAll;?>

  <TR class=bg5>
    <TD  align=right colspan="2">
	<span style="float: right">
	<?php echo $showpage = $page->ShowLink();?>
	</span>
	</TD>
  </TR>
 </table>
<?php
include_once( "footer.php");
?>