<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}
$Ashowtype = array(0=>'图文列表',1=>'FLAS轮播',2=>'优酷视频');
//第一个分类为固定的FLASH广告轮播分类
$db_table = $pre."tjcat";
//post
if( $action && !empty($name)){
	//如果是第一条则固定为广告轮播分类
	if($id < 1 )
	{ 
		//新加类别的调用排序
		$orders = $oPub->getOne('SELECT orders  FROM '.$pre.'tjcat where showtype="'.$showtype.'" and domain_id="'.$Aconf['domain_id'].'" order by orders desc limit 1');
		if($orders > 0)
		{
			$orders = $orders + 1;
		}else
		{
			$orders = 1;
		} 

	}else
	{
		//假如分类不变，则序号不变
		$row = $oPub->getRow('SELECT showtype,orders  FROM '.$pre.'tjcat where id='.$id.' and domain_id="'.$Aconf['domain_id'].'" limit 1');
		$showtype_old = $row['showtype'];
		$orders       = $row['orders'];
		if($showtype_old <> $showtype)
		{ 
			if($orders > 0)
			{
				$orders = $orders + 1;
			}else
			{
				$orders = 1;
			}
		} 

	}
	
	//优酷视频只允许1条记录
	if($showtype == 2)
	{
		$_POST['limits'] = 1;
	}

	if( $action == 'add'  ) {
		$Afields=array('name'=>$name,'imgwidth'=>$_POST['imgwidth'],'imgheight'=>$_POST['imgheight'],'limits'=>$_POST['limits'],'showtype'=>$showtype,'orders'=>$orders,'domain_id'=>$Aconf['domain_id']);
		$id = $oPub->install($db_table,$Afields);
		unset($Anorm);
	}

	if( $action == 'edit'){
		 $id = $id + 0; 
		 $Afields=array('name'=>$name,'imgwidth'=>$_POST['imgwidth'],'imgheight'=>$_POST['imgheight'],'limits'=>$_POST['limits'],'showtype'=>$showtype,'orders'=>$orders);
		 $condition = 'id='.$id.' AND domain_id='.$Aconf['domain_id'];
		 $oPub->update($db_table,$Afields,$condition);
		 unset($Anorm);
		 unset($_GET);
	}

}
//get
if( $_GET['action'] == 'edit'){
	$sql = "SELECT * FROM ".$db_table." where id = ".$_GET['id']." AND domain_id=".$Aconf['domain_id'];
	$Anorm = $oPub->getRow($sql);
}

if( $_GET['action'] == 'del'){
	/*还有子属性将不能删除*/
	$condition = 'id='.$_GET['id'].' AND domain_id='.$Aconf['domain_id'];
    $oPub->delete($db_table,$condition);
}
 
$sql = "SELECT * FROM ".$db_table." WHERE showtype=1 and domain_id='".$Aconf['domain_id']."' ORDER BY orders ASC "; 
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) )
{
	  $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
	   $StrtypeAll .= '<TD align=left>'.$value['id'].'</TD>';
       $StrtypeAll .= '<TD align=left>'.$value['name'].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value['imgwidth'].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value['imgheight'].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value['limits'].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$Ashowtype[$value['showtype']].'</TD>';

		$tjkey = "show_".$value['orders'];  
		$showtype  = '<B>标签:</B>{$home.tj_title.'.$tjkey.'}<br/><B>FLASH内容:</B>';
		$showtype .= '{$home.tj.'.$tjkey.'} '; 

	   $StrtypeAll .= '<TD align=left>'.$showtype.'</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&fid=0&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	   $StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&fid=0&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>'; 
       $StrtypeAll .= '</TR>';     
}
//图文列表
$sql = "SELECT * FROM ".$db_table." WHERE showtype<1 and  domain_id='".$Aconf['domain_id']."' ORDER BY id ASC "; 
$AnormAll = $oPub->select($sql); 
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) )
{
	$n ++;
	$tmpstr = ($n % 2 == 0)?"even":"odd";
	$StrtypeAll .= '<TR class='.$tmpstr.'>';
	$StrtypeAll .= '<TD align=left>'.$value['id'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value['name'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value['imgwidth'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value['imgheight'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value['limits'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$Ashowtype[$value['showtype']].'</TD>'; 

	$tjkey = "showli_".$n; 
	$showtype = '<B>标签:</B>{$home.tj_title.'.$tjkey.'} <br/><B>内容:</B><br/>';  
	$showtype .= '<ul>{foreach from=$home.tj.'.$tjkey.' item=show}<br/>';  
	$showtype .= '<li class="showli"><span style="float:right">标题</span>{$show.name}</li>'; 
	$showtype .= '<li class="showli"><span style="float:right">连接地址</span>{$show.url}</li>';
	$showtype .= '<li class="showli"><span style="float:right">图片URL地址</span>{$show.img}</li>';
	$showtype .= '<li class="showli"><span style="float:right">标题颜色,如红色：#FF0000</span>{$show.colors}</li>'; 
	$showtype .= '<br/>{/foreach}</ul>';  

	$StrtypeAll .= '<TD align=left>'.$showtype.'</TD>';
	$StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&fid=0&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	$StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&fid=0&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>'; 
	$StrtypeAll .= '</TR>';     
}
//优酷视频
$sql = "SELECT * FROM ".$db_table." WHERE showtype=2 and  domain_id='".$Aconf['domain_id']."' ORDER BY id ASC "; 
$AnormAll = $oPub->select($sql); 
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) )
{
	$n ++;
	$tmpstr = ($n % 2 == 0)?"even":"odd";
	$StrtypeAll .= '<TR class='.$tmpstr.'>';
	$StrtypeAll .= '<TD align=left>'.$value['id'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value['name'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value['imgwidth'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value['imgheight'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$value['limits'].'</TD>';
	$StrtypeAll .= '<TD align=left>'.$Ashowtype[$value['showtype']].'</TD>';

	$tjkey = "showyk_".$n; 
	$showtype  = '<B>标签:</B>{$home.tj_title.'.$tjkey.'}<br/><B>FLASH内容:</B>';
	$showtype .= '{$home.tj.'.$tjkey.'} '; 

	$StrtypeAll .= '<TD align=left>'.$showtype.'</TD>';
	$StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&fid=0&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> ';
	$StrtypeAll .= ' _ <a href="'.$_SERVER["PHP_SELF"].'?id='.$value["id"].'&fid=0&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>'; 
	$StrtypeAll .= '</TR>';     
}
//显示方式
$Stroptshowtype = '<SELECT NAME="showtype">'; 
while( @list( $key, $value ) = @each( $Ashowtype) ) {    
    $selected = ($Anorm['showtype'] == $key)? 'SELECTED':'';
    $Stroptshowtype .= '<OPTION VALUE="'.$key.'" '.$selected.' >'.$value.'</OPTION>';
}
$Stroptshowtype .= '</SELECT>';
/* 找到所有的属性到select end*/

?>

<?php
include_once( "header.php");
if ($strMessage != '')
{
	 echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<DIV class=content>
<style>
.showli{margin-left:30px;list-style-type:none;width:90%}
</style>
<TABLE width="99%" border=0>
  <TR   >
  <form name="form1" method="post" action=""> 
    <TD width="13%" align="left" colspan="8">
        <span style="font-weight: bold">分类名:</span>
     	<input name="name" type="text" value="<?php echo ($Anorm['id'])?$Anorm['name']:''?>" />	
		显示方式<?php echo $Stroptshowtype;?>
		<span>宽度:</span>
		<input name="imgwidth" type="text" value="<?php echo ($Anorm['id'])?$Anorm['imgwidth']:''?>" size="2"/>px
		<span>高度:</span>
		<input name="imgheight" type="text" value="<?php echo ($Anorm['id'])?$Anorm['imgheight']:''?>" size="2" />px	
		<span>前台推荐显示的条数:</span>
		<input name="limits" type="text" value="<?php echo ($Anorm['id'])?$Anorm['limits']:5;?>" size="2"/>	
        <input type="hidden" name="action" value="<?php echo ($Anorm['id'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['id'])?'编辑':'增加' ?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="id" value="<?php echo ($Anorm['id'])?$Anorm['id']:'0'?>" />  
		<br/>
		<span style="color:##8A8A8A">注：如果用优酷视频，只允许：http://v.youku.com/v_show/id_XNDgzOTI1NDk2.html 最终页面的显示地址</span>
    </TD>
    </form>
  </TR>	
  <TR class=bg5>
    <TD align=left width=5%>ID</TD>
    <TD align=left width=10%>推荐分类</TD>
	<TD align=left width=8%>宽度</TD>
	<TD align=left width=8%>高度</TD>
	<TD align=left width=8%>显示条数</TD>
	<TD align=left width=10%>显示方式</TD>
	<TD align=left width=40%>模版调用代码</TD>
    <TD align=left width=11%>操作</TD> 
  </TR>
  <?php echo $StrtypeAll;?>
 
</TABLE>
 
</DIV>
<BR/> 

<?php
include_once( "footer.php");
?>
