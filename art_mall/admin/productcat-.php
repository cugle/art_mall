<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php"); 

if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$db_table = $pre."productcat";
//post ifhot
if( $_POST['action'] == 'add'  )
{
    $acids = '';
	$i = count($_POST["pro_acids"]);
    while( @list( $k, $v ) = @each( $_POST["pro_acids"]) ) 
	{
       $j = $k + 1;
       $acids .= ($i == $j )?$v:$v.',';
	}
	$Afields=array('fid'=>$_POST['fid'],'next_node'=>'','name'=>$_POST['name'],'descs'=>$_POST['descs'],'ifnav'=>$_POST['ifnav'],'ifhot'=>$_POST['ifhot'],'pro_interval'=>$_POST['pro_interval'],'acids'=>$acids,'domain_id'=>$Aconf['domain_id']);
    $tpcid = $oPub->install($db_table,$Afields);
    if($tpcid)
	{
		$_POST['fid'] = $_POST['fid'] + 0;
        $sql = 'SELECT pcid FROM '.$db_table.' where fid = "'.$_POST['fid'].'" AND domain_id="'.$Aconf['domain_id'].'"';
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["pcid"].',';
		}
		if(!empty($next_node )){
			$next_node  = substr($next_node ,0,-1);
			$Afields=array('next_node'=>$next_node);
			$condition = "pcid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
			$oPub->update($db_table,$Afields,$condition);
		}

	    /* 导航条显示 */
	    if($_POST['ifnav'])
	    {
			$db_table = $pre."nav"; 
			$url = 'products.php?pcid='.$tpcid;			$Afields=array('name'=>$_POST['name'],'ifshow'=>0,'url'=>$url,'domain_id'=>$Aconf['domain_id']);
            $oPub->install($db_table,$Afields);
	    }
	}
	unset($Anorm);
}

if( $_POST['action'] == 'edit' && $_POST['pcid'] != $_POST['fid']){
	$db_table = $pre."productcat";
	$_POST['pcid'] = $_POST['pcid'] + 0;
	$_POST['fid'] = $_POST['fid'] + 0;

    $acids = '';
	$i = count($_POST["pro_acids"]);
    while( @list( $k, $v ) = @each( $_POST["pro_acids"]) ) 
	{
       $j = $k + 1;
       $acids .= ($i == $j )?$v:$v.',';
	}

	$sql = 'SELECT fid FROM '.$pre.'productcat where pcid="'.$_POST["pcid"].'" AND domain_id="'.$Aconf['domain_id'].'"';
	$old_fid = $oPub->getOne($sql); 
	$condition =' pcid='.$_POST["pcid"].' AND domain_id='.$Aconf['domain_id'];
	$Afields=array('fid'=>$_POST['fid'],'name'=>$_POST['name'],'descs'=>$_POST['descs'],'ifnav'=>$_POST['ifnav'],'ifhot'=>$_POST['ifhot'],'pro_interval'=>$_POST['pro_interval'],'acids'=>$acids);
	$oPub->update($pre."productcat",$Afields,$condition); 

	if($old_fid > 0){ 
        $sql = "SELECT pcid FROM ".$pre."productcat where fid = ".$old_fid." AND domain_id=".$Aconf['domain_id'];
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["pcid"].',';
		} 
		$next_node  = substr($next_node ,0,-1);
		$Afields=array('next_node'=>$next_node);
		$condition = "pcid = ".$old_fid." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($pre."productcat",$Afields,$condition); 
	} 

	if($_POST['fid'] > 0){
        $sql = "SELECT pcid FROM ".$pre."productcat where fid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
	    $row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
			$next_node .=  $value["pcid"].',';
		} 
		$next_node  = substr($next_node ,0,-1);
		$Afields=array('next_node'=>$next_node);
		$condition = "pcid = ".$_POST['fid']." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($pre."productcat",$Afields,$condition);

	}

	 /* 导航条显示 */ 
	 if($Anorm['ifnav'])
	 {
		 if(!$_POST['ifnav'])
		 {
			 $url = "products.php?pcid=".$_POST['pcid'];
	         $condition = 'url="'.$url.'" AND domain_id='.$Aconf['domain_id'];
             $oPub->delete($pre."nav",$condition);
	      }
	 } else
	 {
		 if($_POST['ifnav'])
		 {
			$url = "products.php?pcid=".$_POST['pcid'];
			$Afields=array('name'=>$_POST['name'],'ifshow'=>0,'url'=>$url,'domain_id'=>$Aconf['domain_id']);
            $oPub->install($pre."nav",$Afields);
	      }
	 }

	 unset($Anorm);
	 unset($_GET);
}

//get
if( $_GET['action'] == 'edit'){
	$pcid = $pcid +0;
	$Anorm = $oPub->getRow('SELECT * FROM '.$pre.'productcat where pcid = "'.$pcid.'" AND domain_id="'.$Aconf['domain_id'].'"'); 
}

if( $_GET['action'] == 'del'){
	/*还有子分类将不能删除*/
	$pcid = $pcid + 0;
	$fid = $fid + 0;
	$strwhere = " where pcid = ".$pcid." AND domain_id=".$Aconf['domain_id'];
	$sql = "SELECT next_node FROM ".$pre."productcat".$strwhere;
	$Anorm = $oPub->getRow($sql);
	if(empty($Anorm[next_node]) && $fid > 0)
	{
		$condition = 'pcid='.$pcid." AND domain_id=".$Aconf['domain_id'];
		$oPub->delete($pre."productcat",$condition);

		$sql = "SELECT pcid FROM ".$pre."productcat where fid = ".$fid." AND domain_id=".$Aconf['domain_id'];
		$row = $oPub->select($sql);
		$next_node  = '';
		while( @list( $key, $value ) = @each( $row) ) {
		$next_node .=  $value["pcid"].',';
		} 
		$next_node  = substr($next_node ,0,-1);
		$Afields=array('next_node'=>$next_node);
		$condition = "pcid = ".$fid." AND domain_id=".$Aconf['domain_id'];
		$oPub->update($pre."productcat",$Afields,$condition);  
 
	}elseif($fid < 1 && empty($Anorm[next_node]))
	{
		  $condition = 'pcid='.$pcid.' AND domain_id='.$Aconf['domain_id'];
		  $oPub->delete($pre."productcat",$condition);
	}else
	{
       $strMessage = '存在下级分类，不能删除。';
	}
} 

$ifnav_1 = ($Anorm[ifnav] == 1)? 'SELECTED':'';
$ifnav_0 = ($Anorm[ifnav] == 0)? 'SELECTED':'';

$ifhot_1 = ($Anorm[ifhot] == 1)? 'SELECTED':'';
$ifhot_0 = ($Anorm[ifhot] == 0)? 'SELECTED':'';  

$Strifnavopt = '<SELECT NAME="ifnav">';
$Strifnavopt .= '<OPTION VALUE="1" '.$ifnav_1.'>yes是</OPTION>';
$Strifnavopt .= '<OPTION VALUE="0" '.$ifnav_0.'>no否</OPTION>';
$Strifnavopt .= '</SELECT>';

$Strifhotopt = '<SELECT NAME="ifhot">';
$Strifhotopt .= '<OPTION VALUE="1" '.$ifhot_1.'>yes是</OPTION>';
$Strifhotopt .= '<OPTION VALUE="0" '.$ifhot_0.'>no否</OPTION>';
$Strifhotopt .= '</SELECT>';
/* 找到所有的分类到select start*/  
$whereStr = ($pcid > 0)?' and pcid !='.$pcid:'';
$AnormAll = $oPub->select('SELECT * FROM '.$pre.'productcat where fid = 0 '.$whereStr.' AND domain_id="'.$Aconf['domain_id'].'" ORDER BY pcid ASC');  
$Stropt = '<SELECT NAME="fid">';
$Stropt .= '<OPTION VALUE="0" >顶级分类</OPTION>';
$n = 0; 
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $selected = ($fid == $value["pcid"])? 'SELECTED':'';
       $Stropt .= '<OPTION VALUE="'.$value["pcid"].'" '.$selected.' >'.$n.'、'.$value["name"].'</OPTION>'; 
       if($value["next_node"] != ''){          
           $Stropt .= get_next_node($value["next_node"],$fid,'--',$whereStr );
	   }	   
}
$Stropt .= '</SELECT>';  
/* 找到所有的分类到select end*/
/* 关联文章分类 start */ 
 
$sql = "SELECT acid,name FROM ".$pre."articat where fid = 0 AND ifshow = 1 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC";
$AnormAll = $oPub->select($sql);
$strPro_acids_check = '';
$Aproacids = explode(",",$Anorm["acids"]);
while( @list( $k, $v ) = @each( $AnormAll) ) 
{
    $strChecked = (in_array($v['acid'],$Aproacids))?'checked':'';
	$strPro_acids_check  .= ' <INPUT TYPE="checkbox"  name="pro_acids[]" value="'.$v['acid'].'" '.$strChecked.'><span style="color:#cc0000;font-weight:bold">'.$v['name'].'</span>';    
	$strAcid = next_node_all($v['acid'],$pre."articat",'acid',true);
	if($strAcid)
	{
	   $Aacids = explode(",",$strAcid);
       while( @list( $key, $value ) = @each( $Aacids ) )
       {  
		   if($value)
		   { 
			         $strChecked = (in_array($value,$Aproacids))?'checked':'';
			         $sql = "SELECT fid,name FROM ".$pre."articat  
			                 where acid = '".$value."'
					         AND domain_id=".$Aconf['domain_id']." limit 1";
                     $acidtmp = $oPub->getRow($sql); 
					 $fid = $acidtmp[fid];
					 $name = $acidtmp[name];
					 $fidname = '';
					 if($fid > 0)
					  {
			             $sql = "SELECT  name FROM ".$pre."articat  
			                     where acid = '".$fid."'
					             AND domain_id=".$Aconf['domain_id']." limit 1";
                         $fidname = $oPub->getOne($sql);
						 $fidname = '<span style="color: #6E6E6E">'.$fidname.":</span>";
					  }              
		       $strPro_acids_check  .=' <INPUT TYPE="checkbox"  name="pro_acids[]" value="'.$value.'" '.$strChecked.'>'.$fidname.$name;
		   } 
	   }
	}
	$strPro_acids_check .='<br/>';
}
 
/* 关联文章分类 end */
$sql = "SELECT * FROM ".$pre."productcat where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY pcid ASC";
$AnormAll = $oPub->select($sql);
$StrtypeAll = '';
$n = 0;
while( @list( $key, $value ) = @each( $AnormAll) ) {
	  $n ++;
	   $tmpstr = ($n % 2 == 0)?"even":"odd";
       $StrtypeAll .= '<TR class='.$tmpstr.'>';
       $StrtypeAll .= '<TD align=left>'.$n.'、'.$value["name"].'</TD>';
	   $StrtypeAll .= '<TD align=left>'.$value["descs"].'</TD>'; 
	   $tmp = ($value["ifnav"])?'yes是':'no否';
	   $StrtypeAll .= '<TD align=left>'.$tmp.'</TD>';
	   $tmp = ($value["ifhot"])?'yes是':'no否';
	   //$StrtypeAll .= '<TD align=left>'.$tmp.'</TD>'; 
	   $StrtypeAll .= '<TD align=left>'.$value["pro_interval"].'</TD>'; 
	   $acidname = ' ';
	   if($value["acids"])
	   { 
           $sql = "SELECT name FROM ".$pre."articat where ifshow = 1 AND domain_id=".$Aconf['domain_id']." AND acid in(".$value["acids"].") ORDER BY acid ASC";
           $row = $oPub->select($sql);
		   while( @list( $k, $v ) = @each( $row) ) 
		   {
               $acidname .= $v['name'].',';
		   } 
	   }

	   //$StrtypeAll .= '<TD align=left>'.$acidname.'</TD>';
       $StrtypeAll .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?pcid='.$value["pcid"].'&fid=0&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="编辑"></a> _ ';
	   $StrtypeAll .= '<a href="'.$_SERVER["PHP_SELF"].'?pcid='.$value["pcid"].'&fid=0&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="删除"></a></TD>';
       $StrtypeAll .= '</TR>';   
       if($value["next_node"] != ''){          
           $StrtypeAll .= tab_next_node($value["next_node"],$value["pcid"]);
	   } 	   
}
?>

<?php
include_once( "header.php");
if ($strMessage != '')
{
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<DIV class=content>
<TABLE width="100%" border=0>
  <TR>
  <form name="form1" method="post" action=""> 
    <TD width="13%" align="left" colspan="7">
        <span style="font-weight: bold">category english name 英文分类名称:</span>
     	<input name="name" type="text" value="<?php echo ($Anorm['pcid'])?$Anorm['name']:''?>" />

		 <span style="font-weight: bold"> Parent category 上级分类:</span><?php echo $Stropt;?> 
		<span style="font-weight: bold">display 导航条显示:</span>
		<?php echo $Strifnavopt;?>
		<br />
		 <span style="font-weight: bold"> <span style="font-weight: bold">chinese category name中文分类名称:</span>
     	<input name="descs" type="text" value="<?php echo ($Anorm['pcid'])?$Anorm['descs']:''?>" size="50"/>
		<br/>
		<span style="font-weight: bold">category pic 分类导航图片:</span>
		<input name="pro_interval" type="text" value="<?php echo ($Anorm['pcid'])?$Anorm['pro_interval']:''?>" size="50"/>
		<br/>
		<!--<span style="font-weight: bold">关联文章分类:</span>[此分类自动关联的文章分类]
		<p style="margin-left: 60px;margin-top: 0px;margin-bottom: 0px">-->

				
        <input type="hidden" name="action" value="<?php echo ($Anorm['pcid'])?'edit':'add'?>" />
        <input type="submit" name="Submit" value="<?php echo ($Anorm['pcid'])?'Edit编辑':'Add增加' ?>" style="background-color: #FFCC66"/>
		<input type="hidden" name="pcid" value="<?php echo ($Anorm['pcid'])?$Anorm['pcid']:'0'?>" />  
    </TD>
    </form>
  </TR>	
  <TR class=bg5>
    <TD width="28%" align=left>category english name 产品分类</TD>
	<TD width="28%" align=left>chinese category name 中文名称</TD> 
	<TD width="16%" align=left>display导航条显示</TD>
	 
	<TD width="18%" align=left>category pic 导航图片</TD>

    <TD width="10%" align=left>operate操作</TD>
  </TR>
  <?php echo $StrtypeAll?>
  <TR class=bg5>
    <TD colspan="7" align=right><?php //echo $showpage = $page->ShowLink();?></TD>
  </TR>
</TABLE>
 
</DIV>
<?php
/* OPTION 递归 */
function get_next_node($next_node,$fid,$str = '--',$whereStr='')
{
	global $oPub,$pre; 
	$Agrad = explode(',',$next_node);
	$Stropt = '';
	if(count($Agrad) > 0 )
	{
		$str .= '--';
		$tn = 0;
		while( @list( $k, $v ) = @each( $Agrad ) ) {
			$v = $v + 0;
			if($v < 1)
			{
				break;
			}		   
			$Anorm = $oPub->getRow('SELECT * FROM '.$pre.'productcat where pcid = "'.$v.'"'.$whereStr); 
			if( !empty($Anorm["name"])){
				$tn ++;
				$selected = ($fid == $v)? 'SELECTED':'';
				$Stropt .=  '<OPTION VALUE="'.$v.'" '.$selected.'>'.$str.$tn.'）'.$Anorm["name"].'</OPTION>';
				$Stropt .= get_next_node($Anorm["next_node"],$fid,$str,$whereStr);
			} 
		}
	}
	return $Stropt;
}
/* tbale 递归 */
function tab_next_node($next_node,$fid,$str = '　')
{
   global $oPub,$pre,$Aconf;
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
		    $db_table = $pre.'productcat';
		    $sql = "SELECT * FROM ".$db_table." where pcid = $v";
            $Anorm = $oPub->getRow($sql);
			if( $Anorm["name"] != ''){
			  $tn ++ ;
	          $tmpstr = ($n % 2 == 0)?"even":"odd";
              $Strtab  .= '<TR class='.$tmpstr.'>';

              $Strtab  .= '<TD align=left>'.$str.$tn.'）'.$Anorm["name"].'</TD>';
			  $Strtab  .= '<TD align=left>'.$Anorm["descs"].'</TD>'; 
	          $tmp = ($Anorm["ifnav"])?'yes是':'no否';
	          $Strtab .= '<TD align=left>'.$tmp.'</TD>';
	          $tmp = ($Anorm["ifhot"])?'yes是':'no否';
	         // $Strtab .= '<TD align=left>'.$tmp.'</TD>';
	          $Strtab .= '<TD align=left>'.$Anorm["pro_interval"].'</TD>';

	           $acidname = ' ';
	           if($Anorm["acids"])
	           {
	               $db_table = $pre."articat";
                   $sql = "SELECT name FROM ".$db_table." where ifshow = 1 AND domain_id=".$Aconf['domain_id']." AND acid in(".$Anorm["acids"].") ORDER BY acid ASC";
                   $row = $oPub->select($sql);
		           while( @list( $key, $value ) = @each( $row) ) 
		           {
                      $acidname .= $value[name].',';
		           }

	           }

	         // $Strtab .= '<TD align=left>'.$acidname.'</TD>';
              $Strtab  .= '<TD align=left><a href="'.$_SERVER["PHP_SELF"].'?pcid='.$v.'&fid='.$fid.'&action=edit"><IMG SRC="images/b_edit.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[编辑]"></a> _ ';
	          $Strtab  .= '<a href="'.$_SERVER["PHP_SELF"].'?pcid='.$v.'&fid='.$fid.'&action=del"><IMG SRC="images/b_drop.png" WIDTH="16" HEIGHT="16" BORDER="0" ALT="[删除]"></a></TD>';
              $Strtab  .= '</TR>';  
	          $n ++;
              $Strtab .= tab_next_node($Anorm["next_node"],$v,$str );
			}
	   }
	}
	return $Strtab;
}
?>	
<?php
include_once( "footer.php");
?>