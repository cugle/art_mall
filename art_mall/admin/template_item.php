<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");
include_once( $ROOT_PATH.'includes/cls_image.php');
$image = new cls_image();
if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

$strMessage = '';
$db_table = $pre."sysconfig";
$template_file = ($_REQUEST['template_file'])?$_REQUEST['template_file']:'home';
//系统定义标签名,是否显示，记录数，分类，自定义标签名
if($_POST['action'] == 'save')
{
		$str = 'one|';
        foreach( $_POST[one] as $k => $v ) {
			if( $k <> 'oldlogo')
			{
			   $str .= $k.':'.$v[0].','.$v[1].','.$v[2].','.$v[3].',';
				//上传图标 start   
				$logo = '';
				if($_FILES['one']["size"]['logo'][$k] > 0 ) {
					/* 判断图像类型 */
					if (!$image->check_img_type($_FILES['one']['type']['logo'][$k])) {
						$strMessage .= $_FILES['one']['type']['logo'][$k].'图片类型错误';
						$logo = $_POST['one']['oldlogo'][$k];
					} else 
					{
					   /* 删除原有的 LOGO */ 
						$upload = array(
							'name' => $_FILES['one']['name']['logo'][$k],
							'type' => $_FILES['one']['type']['logo'][$k],
							'tmp_name' => $_FILES['one']['tmp_name']['logo'][$k],
							'size' => $_FILES['one']["size"]['logo'][$k],
						);
					   if(!empty($_POST['one']['oldlogo'][$k])) {
						   @unlink('../data/weblogo/' . $_POST['one']['oldlogo'][$k]);
					   }
					   $logo = basename($image->upload_image($upload,'weblogo')); 
					}
				} else {
					$logo = $_POST['one']['oldlogo'][$k]; 
				}

				if (!file_exists('../data/weblogo/' . $logo))
				{
					$logo = '';
				}
				//上传图标 end
				$str .=$logo.";"; 
			}
	    }
        $str .= ']';
		$str .= 'two|';
        foreach( $_POST[two] as $k => $v ) {
			if( $k <> 'oldlogo')
			{
				$str .= $k.':'.$v[0].','.$v[1].','.$v[2].','.$v[3].','; 
				//上传图标 start   
				$logo = '';
				if($_FILES['two']["size"]['logo'][$k] > 0 ) {
					/* 判断图像类型 */
					if (!$image->check_img_type($_FILES['two']['type']['logo'][$k])) {
						$strMessage .= $_FILES['two']['type']['logo'][$k].'图片类型错误';
						$logo = $_POST['two']['oldlogo'][$k];
					} else 
					{
					   /* 删除原有的 LOGO */ 
						$upload = array(
							'name' => $_FILES['two']['name']['logo'][$k],
							'type' => $_FILES['two']['type']['logo'][$k],
							'tmp_name' => $_FILES['two']['tmp_name']['logo'][$k],
							'size' => $_FILES['two']["size"]['logo'][$k],
						);
					   if(!empty($_POST['two']['oldlogo'][$k])) {
						   @unlink('../data/weblogo/' . $_POST['two']['oldlogo'][$k]);
					   }
					   $logo = basename($image->upload_image($upload,'weblogo')); 
					}
				} else {
					$logo = $_POST['two']['oldlogo'][$k];
				}
				if (!file_exists('../data/weblogo/' . $logo))
				{
					$logo = '';
				}
				//上传图标 end
				$str .=$logo.";"; 
			}
		}
        $str .= ']';
		$str .= 'three|';
 
        foreach( $_POST[three] as $kx => $vx ) {
		   if($kx == 'acids_s'){ 
			    $n = 1;
				foreach( $vx as $k => $v) {
					if($_POST[three][acids_n][$k] > 0){
						$str .='acids'.$n.':';
						$acid =  str_replace(',','~',$_POST[three][acids_id][$k]);
						$str .=$v.','.$_POST[three][acids_n][$k].','.$acid.','.$_POST[three][acids_t][$k].",";
						//上传图标 start   
						$acids_logo = '';
						if($_FILES['three']["size"]['acids_logo'][$k] > 0 ) {
							/* 判断图像类型 */
							if (!$image->check_img_type($_FILES['three']['type']['acids_logo'][$k])) {
								$strMessage .= $_FILES['three']['type']['acids_logo'][$k].'图片类型错误';
								$acids_logo = $_POST['three']['acids_oldlogo'][$k];
							} else 
							{
							   /* 删除原有的 LOGO */ 
								$upload = array(
									'name' => $_FILES['three']['name']['acids_logo'][$k],
									'type' => $_FILES['three']['type']['acids_logo'][$k],
									'tmp_name' => $_FILES['three']['tmp_name']['acids_logo'][$k],
									'size' => $_FILES['three']["size"]['acids_logo'][$k],
								); 
							   if(!empty($_POST['three']['acids_oldlogo'][$k])) {
								   @unlink('../data/weblogo/' . $_POST['three']['acids_oldlogo'][$k]);
							   }
							   $acids_logo = basename($image->upload_image($upload,'weblogo')); 
							}
						} else {
							$acids_logo = $_POST['three']['acids_oldlogo'][$k];
						}
						//上传图标 end
						if (!file_exists('../data/weblogo/' . $acids_logo))
						{
							$acids_logo = '';
						}
						$str .=$acids_logo.";";
						$n ++ ;
					}
				}
		   }
		   break;
		}
        $sql = "UPDATE " . $pre."sysconfig SET ".$_POST['template_file']."='$str' WHERE  scid=".$Aconf['domain_id'];
        $oPub->query($sql);
		$strMessage = '提交成功！'; 
}


/* 可以修改的模板 */
$Atemplate_files = array(
    'home'          => '首页     -- index.html',
);

/* 得到选择列表 */
$template_fileopt = '<SELECT NAME="template_file" id="template_file_id" onchange="selecttemplate(this.options[this.options.selectedIndex].value)">';
$template_fileopt .= '<OPTION VALUE="">模板文件选择</OPTION>';
foreach( $Atemplate_files as $k => $v ) {
	$tmp = ($_REQUEST['template_file'] == $k)?'selected':'';
    $template_fileopt .= '<OPTION VALUE="'.$k.'" '.$tmp.'>'.$v.'</OPTION>';
}
$template_fileopt .= '</SELECT>';

$strTitle = '';
if($template_file == 'home') {
	$strTitle = '首页';    
}
if($template_file == 'articles') {
   $strTitle  = '文章列表';
} 


$sql = "SELECT ".$template_file." FROM ".$pre."sysconfig WHERE scid='".$Aconf['domain_id']."'";
$Arow = $oPub->getRow($sql);
if( $Arow )
{
	/* 基本配置信息 */
	$n = 0;
	$Asets = explode("]",$Arow[$template_file]);
    foreach ($Asets as $v)
    {
		$At = array();
		$At = explode("|",$v);

		if($At[0] == 'one') {
		   $Atv = explode(";",$At[1]);
		   foreach($Atv as $val) {
				$Atmp = explode(":",$val);
				if($Atmp[0]) { 
				   $Aitem[one][$Atmp[0]] = explode(",",$Atmp[1]); 
				}
			}
		}

		if($At[0] == 'two') {
			$Atv = explode(";",$At[1]);
			foreach($Atv as $val) {
			$Atmp = explode(":",$val);
				if($Atmp[0]) {
				   $Aitem[two][$Atmp[0]] = explode(",",$Atmp[1]);  
				}
			}
		}

		if($At[0] == 'three') {
			$Atv = explode(";",$At[1]);
			foreach($Atv as $val) {
			$Atmp = explode(":",$val);
				if($Atmp[0]) {
				   $Aitem[three][$Atmp[0]] = explode(",",$Atmp[1]);  
				}
			}
		}
	} 
} 
 
if(!$Aitem && $template_file){
	$Aitem[one][notices]          = 1;
	$Aitem[one][descs]            = 1;
	$Aitem[one][articat]          = 1;
	$Aitem[one][productcat]       = 1;
	$Aitem[one][vote]             = 1; 
	$Aitem[one][qq]               = 1;
	$Aitem[one][sesspro]          = 0;  

	$Aitem[two][vip]              = array(1,10);
	$Aitem[two][articles]         = array(1,5);
	$Aitem[two][articles_top]     = array(0,6);
	$Aitem[two][articles_focus]   = array(0,20); 
	$Aitem[two][articles_trundle] = array(0,10);
	$Aitem[two][articles_ifpic]   = array(0,20);
	$Aitem[two][articles_comms]   = array(0,20);

	$Aitem[two][products]         = array(0,6);  
	$Aitem[two][products_top]     = array(0,20); 
	$Aitem[two][products_special] = array(0,20); 
	$Aitem[two][probrand]         = array(0,20); 
	$Aitem[two][pravail]          = array(0,20); 
 
	$Aitem[two][votes]            = array(1,6);
	$Aitem[two][keytj]            = array(1,10);
	$Aitem[two][links]            = array(1,10);  
	$Aitem[two][users]            = array(1,10);
	$Aitem[two][lineusers]        = array(1,10);
}
//模版调用代码数组：start
$Aitemtemp[notices]          = '{$notices_title}-{$notices_logo}-{$notices}';
$Aitemtemp[descs]            = '{descs_title}-{$descs_logo}-{$descs}';
$Aitemtemp[articat]          = '{$home.Articat_title}--{$home.Articat_logo}--{foreach from=$home.Articat item=articat} {/foreach}';
$Aitemtemp[productcat]       = '{$home.Productcat_title}--{$home.Productcat_logo}--{foreach from=$home.Productcat item=productcat} {/foreach}';
$Aitemtemp[vote]             = '{$home.vote_title}--{$home.vote_logo}--{foreach from=$home.vote item=vote} {/foreach}'; 
$Aitemtemp[qq]               = '{$home.qq_title}--{$home.qq_logo}--{foreach from=$home.qq item=qq} {/foreach}';

$Aitemtemp[sesspro]          = '{$home.Sesspro_title}--{foreach from=$home.sesspro item=products}<br/>{$products.sesspro.min_thumb} {$products.sesspro.s_discount} {$products.sesspro.name} {/foreach}';

$Aitemtemp[vip]              = '{$home.Vip_title}-{$home.Vip_logo}-{foreach from=$home.Vip item=vip} {/foreach}';
$Aitemtemp[articles]         = '{$home.Tarticles_title}-{$home.Tarticles_logo}-{foreach from=$home.Tarticles item=articles} {/foreach}';
$Aitemtemp[articles_top]     = '{foreach from=$home.Tarticles_top item=articles} {/foreach}';
$Aitemtemp[articles_focus]   = '{foreach from=$home.Tarticles_focus item=articles} {/foreach}';
$Aitemtemp[articles_trundle] = '{foreach from=$home.Tarticles_trundle item=articles} {/foreach}';
$Aitemtemp[articles_ifpic]   = '{foreach from=$home.Tarticles_ifpic item=articles} {/foreach}';
$Aitemtemp[articles_comms]   = '{foreach from=$home.Tarticles_comms item=articles} {/foreach}';

$Aitemtemp[products]         = '{foreach from=$home.Tproducts item=products} {/foreach}';  
$Aitemtemp[products_top]     = '{foreach from=$home.Tproducts_top item=products} {/foreach}'; 
$Aitemtemp[products_special] = '{foreach from=$home.Tproducts_special item=products} {/foreach}';  
$Aitemtemp[probrand]         = '{foreach from=$home.Tprobrand item=probrand} {/foreach}'; 
$Aitemtemp[pravail]          = '{foreach from=$home.Tpravail item=pravail} {/foreach}'; 

$Aitemtemp[votes]            = '{foreach from=$home.Tvotes item=votes} {/foreach}';
$Aitemtemp[keytj]            = '{$home.keytj_title}--{foreach from=$home.Tkeytj item=articles} {/foreach}';
$Aitemtemp[links]            = '{foreach from=$home.links item=links} {/foreach}'; 
$Aitemtemp[users]            = '{foreach from=$home.users item=users} {/foreach}'; 
$Aitemtemp[lineusers]        = '{$home.Lineusers_title}--{foreach from=$home.Lineusers item=users}{/foreach}';
//模版调用代码数组：end
/* 得到公共选项 */

$strItem = '';
$n = 0;
if($Aitem){
	foreach( $Aitem[one] as $k => $v ) 
	{
		$tmpstr = ($n % 2 == 0)?"even":"odd";
		$strItem .= '<TR class="'.$tmpstr.'">';

		$strItem .= '<TD align="left">';
		$strItem .= $Aitname[$k];
		$strItem .= '</TD><TD align="left">';
		$strItem .= '<SELECT NAME="one['.$k.'][0]">';
		$selected_1 = ($v[0])?'SELECTED':''; 
		$selected_0 = (!$v[0])?'SELECTED':'';
		$strItem .= '<OPTION VALUE="1" '.$selected_1.'>是</OPTION>';
		$strItem .= '<OPTION VALUE="0" '.$selected_0.'> 否</OPTION>';
		$strItem .= '</SELECT>';
		$strItem .= '</TD><TD align="left"><INPUT TYPE="hidden" NAME="one['.$k.'][1]" value="" size="8"></TD>';
		$strItem .= '<TD align="left"><INPUT TYPE="hidden" NAME="one['.$k.'][2]" value=""></TD>';
		$strItem .= '<TD align="left">';
		$strItem .= '<INPUT TYPE="text" NAME="one['.$k.'][3]" value="'.($v[3]?$v[3]:$Aitname[$k]).'" style="width:100px">'; 
		$strItem .= '</TD>';
		$strItem .= '<TD align="left">';
		//$strItem .= $Aitemtemp[$k];
		$strItem .= '<INPUT type="file" name="one[logo]['.$k.']" style="width:120px" />';
		$strItem .= '<INPUT type="hidden" name="one[oldlogo]['.$k.']"  value="'.($v[4]?$v[4]:'').'" />'; 
		if(!empty($v[4]))
		{
			$strItem .= '<span id="one_show_'.$n.'">'; 
			$strItem .= '<IMG SRC="../data/weblogo/'.$v[4].'" WIDTH="'.($Aconf['nav_w']/4).'" HEIGHT="'.($Aconf['nav_h']/4).'" BORDER="0" >'; 
			$strItem .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) delnavlogo(\''.$v[4].'\',\'one_show_'.$n.'\')">'; 
			$strItem .= '<IMG SRC="images/b_drop.png" WIDTH="12" HEIGHT="12" BORDER="0" ALT="删除缩图"></A>';  
			$strItem .= '</span>';

		}
		$strItem .= '</TD>';
		$strItem .= '<TD align="left">';
		$strItem .= $Aitemtemp[$k];
		$strItem .= '</TD>';
		$strItem .= '</TR>';
		$n ++;
	}
 
	foreach( $Aitem[two] as $k => $v ) {
		$tmpstr = ($n % 2 == 0)?"even":"odd";
		$strItem .= '<TR class="'.$tmpstr.'">';

		$strItem .= '<TD align="left">';
		$strItem .= $Aitname[$k];
		$strItem .= '</TD><TD align="left">';
		$strItem .= '<SELECT NAME="two['.$k.'][0]">';
		$selected_1 = ($v[0])?'SELECTED':''; 
		$selected_0 = (!$v[0])?'SELECTED':'';
		$strItem .= '<OPTION VALUE="1" '.$selected_1.'>是</OPTION>';
		$strItem .= '<OPTION VALUE="0" '.$selected_0.'> 否</OPTION>';
		$strItem .= '</SELECT>';
		$strItem .= '</TD><TD align="left">';
		$strItem .= '<INPUT TYPE="text" NAME="two['.$k.'][1]" value="'.$v[1].'" size="5">';
		$strItem .= '</TD>';
		$strItem .= '<TD align="left"><INPUT TYPE="hidden" NAME="two['.$k.'][2]" value="" size="8"></TD>';
		$strItem .= '<TD align="left">';
		$strItem .= '<INPUT TYPE="text" NAME="two['.$k.'][3]" value="'.($v[3]?$v[3]:$Aitname[$k]).'" style="width:100px">'; 
		$strItem .= '</TD>';
		$strItem .= '<TD align="left">';
		//$strItem .= $Aitemtemp[$k];
		//$strItem .= '{foreach from=$home.acids.'.$k.'.list item=articles}{/foreach}'; 
		$strItem .= '<INPUT type="file" name="two[logo]['.$k.']" style="width:120px" />';
		$strItem .= '<INPUT type="hidden" name="two[oldlogo]['.$k.']"  value="'.($v[4]?$v[4]:'').'" />'; 
		if(!empty($v[4]))
		{
			$strItem .= '<span id="two_show_'.$n.'">'; 
			$strItem .= '<IMG SRC="../data/weblogo/'.$v[4].'" WIDTH="'.($Aconf['nav_w']/4).'" HEIGHT="'.($Aconf['nav_h']/4).'" BORDER="0" >'; 
			$strItem .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) delnavlogo(\''.$v[4].'\',\'two_show_'.$n.'\')">'; 
			$strItem .= '<IMG SRC="images/b_drop.png" WIDTH="12" HEIGHT="12" BORDER="0" ALT="删除缩图"></A>';  
			$strItem .= '</span>';  
		}
		$strItem .= '</TD>';
		$strItem .= '<TD align="left">';
		$strItem .= $Aitemtemp[$k];
		$strItem .= '</TD>';
		$strItem .= '</TR>';
		$n ++;
	}

	if($Aitem[three])
	foreach( $Aitem[three] as $k => $v ) {
		$tmpstr = ($n % 2 == 0)?"even":"odd";
		$strItem .= '<TR class="'.$tmpstr.'">';
		
		$strItem .= '<TD align="left">';
		$strItem .= $Aitname["acids"];
		$strItem .= '</TD><TD align="left">';
		$strItem .= '<SELECT NAME="three[acids_s]['.$n.']">';
		$selected_1 = ($v[0])?'SELECTED':''; 
		$selected_0 = (!$v[0])?'SELECTED':'';
		$strItem .= '<OPTION VALUE="1" '.$selected_1.'>是</OPTION>';
		$strItem .= '<OPTION VALUE="0" '.$selected_0.'> 否</OPTION>';
		$strItem .= '</SELECT>';
		$strItem .= '</TD><TD align="left">';
		$strItem .= '<INPUT TYPE="text" NAME="three[acids_n]['.$n.']" value="'.$v[1].'" size="5">';
		$strItem .= '</TD>';
		$strItem .= '<TD align="left"><INPUT TYPE="text" NAME="three[acids_id]['.$n.']" value="'.(str_replace('~',',',$v[2])).'" style="width:80px"></TD>';
		$strItem .= '<TD align="left">';
		$strItem .= '<INPUT TYPE="text" NAME="three[acids_t]['.$n.']" value="'.($v[3]?$v[3]:$Aitname[$k]).'" style="width:100px">'; 
		$strItem .= '</TD>';
		$strItem .= '<TD align="left">';
			//$strItem .= '{foreach from=$home.acids.'.$k.'.list item=articles}{/foreach}'; 
			$strItem .= '<INPUT type="file" name="three[acids_logo]['.$n.']" style="width:120px" />';
			$strItem .= '<INPUT type="hidden" name="three[acids_oldlogo]['.$n.']"  value="'.($v[4]?$v[4]:'').'" />'; 
			if(!empty($v[4]))
			{
				$strItem .= '<span id="three_show_'.$n.'">'; 
				$strItem .= '<IMG SRC="../data/weblogo/'.$v[4].'" WIDTH="'.($Aconf['nav_w']/4).'" HEIGHT="'.($Aconf['nav_h']/4).'" BORDER="0" >'; 
				$strItem .= '<a href="javascript:;" onclick="if (confirm(\'删除\')) delnavlogo(\''.$v[4].'\',\'three_show_'.$n.'\')">'; 
				$strItem .= '<IMG SRC="images/b_drop.png" WIDTH="12" HEIGHT="12" BORDER="0" ALT="删除缩图"></A>';  
				$strItem .= '</span>';  
			}
		$strItem .= '</TD>';
		$strItem .= '<TD align="left">';
		$strItem .= '{$home.acids.'.$k.'.title}-{$home.acids.'.$k.'.logo}-{foreach from=$home.acids.'.$k.'.list item=articles}{/foreach}';
		$strItem .= '</TD>';

		$strItem .= '</TR>';
		$n ++;
	}
}
//新闻一级分类
$sql = "SELECT acid,name FROM ".$pre."articat where fid = 0 AND domain_id=".$Aconf['domain_id']." ORDER BY acid ASC";
$AnormAll = $oPub->select($sql);
$strAcid ='';
while( @list( $key, $value ) = @each( $AnormAll) ) {
	   $n ++;
       $strAcid .= '<b>'.$value["name"]."</b>( ".$value["acid"]." ) &nbsp;&nbsp;&nbsp;" ; 
}  
?>
<?php
include_once( "header.php");
if ($strMessage != '') {
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <td align="left">
	<?php echo ($strTitle)?'>>'.$strTitle:'';?>    
    <?php echo $template_fileopt;?>
 </td>
</tr>
</table>
<form action="" method="post" name="theForm" enctype="multipart/form-data">
<TABLE width="100%" border=0> 
  <TR class="odd">
     <TD><B>项目</B></TD>
	 <TD><B>显示</B></TD>
	 <TD><B>记录数</B></TD>
	 <TD><B>分类ID号</B></TD>
	 <TD><B>前台标签名</B></TD>
	 <TD><B>图标</B><span class="note" title=".jpg .gif .png">(尺寸:<?php echo $Aconf['nav_w'].'px*'.$Aconf['nav_h'].'px';?>)</span></TD>
	 <TD><B>模版调用代码</B></TD>
  </TR> 
  <?PHP echo $strItem;?>
</TABLE>
<DIV class=content id=tabbar-div>
<TABLE width="100%" border=0 id=gallery-table> 
  <TR>
	<TD>
		<A onclick=addImg(this) href="javascript:;">[+]</A> 
		新闻子类调用: 
		<B>显示</B><SELECT NAME="three[acids_s][]"> 
			 <OPTION VALUE="1" >是</OPTION> 
			 <OPTION VALUE="0" > 否</OPTION> 
			 </SELECT> 
		<B>记录数</B><INPUT TYPE="text" NAME=three[acids_n][] value="" size="5"> 
		<B>新闻分类ID号</B><INPUT TYPE="text" NAME=three[acids_id][] value="" style="width:100px;"> 
		<B>在前台显示的标签标签名</B><INPUT TYPE="text" NAME=three[acids_t][] value="" size="10">
		<B>图标</B><INPUT type="file" name="three[acids_logo][]" style="width:150px;"/>
	</TD> 
  </TR> 
</TABLE>
<span style="margin-left: 356px;font-size: 9px">注：多个ID号，请用","逗号分割。</span>
<br/><b>新闻顶级分类(ID号):</b><?php echo $strAcid;?>
</DIV>
<TABLE width="100%" border=0> 
  <TR class="odd" >         
      <TD width="13%" align="left" colspan="6">
		 <div style="clear:left">
		    <input type="submit" name="Submit" value="确定保存" style="background-color: #FFCC66"/>
			<INPUT TYPE="hidden" name="template_file" value="<?php echo $template_file;?>">
			<INPUT TYPE="hidden" name="action" value="save">
		 </div>
      </TD>
   </TR> 
</TABLE>
</FORM>
<SCRIPT src="js/tab.js" type="text/javascript"></SCRIPT>
<SCRIPT src="js/utils.js" type="text/javascript"></SCRIPT>	
<SCRIPT src="../js/ajax.js" type="text/javascript"></SCRIPT>
<!--以下的两个script为添加的ajax上传-->
<SCRIPT language=JavaScript> 
  function addImg(obj)
  {
      var src  = obj.parentNode.parentNode;
      var idx  = rowindex(src);
      var tbl  = document.getElementById('gallery-table');
      var row  = tbl.insertRow(idx + 1);
      var cell = row.insertCell(-1);
      cell.innerHTML = src.cells[0].innerHTML.replace(/(.*)(addImg)(.*)(\[)(\+)/i, "$1removeImg$3$4-");
  } 
  /**
   * ~{I>3}M<F,IO4+~}
   */
  function removeImg(obj)
  {
      var row = rowindex(obj.parentNode.parentNode);
      var tbl = document.getElementById('gallery-table');

      tbl.deleteRow(row);
  } 
  /**
   * ~{I>3}M<F,~}
   */

  function  selecttemplate(a)
  {
     location="<?php echo $PHP_SELF;?>?template_file=" + a;   
  }

function delnavlogo(a,b) {  
	obj = b;
	var strTemp = "ajax_nav_delimg.php?op=del&avalue=" + escape(a); 
	send_request(strTemp);
}  
</SCRIPT>
<?php
include_once( "footer.php");
?>