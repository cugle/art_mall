<?php
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  
if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}

/* 检查用户页面目录是否存在 */
$db_table = $pre."sysconfig";
if($action == 'save')
{
	clear_all_files();
	$template = stripslashes($_POST['templates_name']); 
	$oPub->query('UPDATE '. $pre.'sysconfig SET  template="'.$template.'",user_template=0 WHERE  scid="'.$Aconf['domain_id'].'"'); 
	$strMessage =  "页面选择成功！"; 
	//修改初始flash值 $info['flash_1'] $info['flash_2'] //FLASH轮播广告-684*274 FlashSize_2: 企业荣誉-228*171 
	//name,imgwidth,imgheight,limits,showtype,orders,domain_id
	if($flash_1[$template])
	{
		$A = $At = array(); 
		$A = explode("-",$flash_1[$template]);
		$At = explode("*",$A[1]);
		$id = $oPub->getOne('SELECT id FROM '.$pre.'tjcat where showtype = 1 and orders=1 and domain_id="'.$Aconf['domain_id'].'"');   
		if($id > 0)
		{
			$oPub->query('UPDATE '.$pre.'tjcat SET imgwidth="'.$At[0].'",imgheight="'.$At[1].'"  WHERE  id="'.$id.'" limit 1');
		}else
		{
			$oPub->query('INSERT INTO '.$pre.'tjcat(name,imgwidth,imgheight,limits,showtype,orders,domain_id)VALUES("'.$A[0].'","'.$At[0].'","'.$At[1].'",8,1,1,"'.$Aconf['domain_id'].'")'); 
		}
	}

	if($flash_2[$template])
	{
		$A = $At = array();  
		$A = explode("-",$flash_2[$template]);
		$At = explode("*",$A[1]);
		$id = $oPub->getOne('SELECT id FROM '.$pre.'tjcat where showtype = 1 and orders=2 and domain_id="'.$Aconf['domain_id'].'"');   
		if($id > 0)
		{
			$oPub->query('UPDATE '.$pre.'tjcat SET imgwidth="'.$At[0].'",imgheight="'.$At[1].'"  WHERE  id="'.$id.'" limit 1');
		}else
		{
			$oPub->query('INSERT INTO '.$pre.'tjcat(name,imgwidth,imgheight,limits,showtype,orders,domain_id)VALUES("'.$A[0].'","'.$At[0].'","'.$At[1].'",8,1,2,"'.$Aconf['domain_id'].'")'); 
		}
	} 
}
/* 得到当前模版 */

$Anorm = $oPub->getRow('SELECT template,user_template FROM '.$pre.'sysconfig WHERE scid = "'.$Aconf['domain_id'].'" ORDER BY scid ASC LIMIT 1'); 
//页面列表

if($Anorm['user_template'])
{
	$user_template = '<span style="color:#CC0000;font-size: 14px">当前使用自定义页面。如果重新选择,<A HREF="template_edit.php">自定义页面</A>将会被覆盖!</span>';
	$template = '';
} else
{
	$template = $Anorm['template']; //当前模版名
}



/* 获得所有模版信息 */
$available_templates = array(); 
$template_dir        = @opendir(ROOT_PATH . 'themes/');
while ($file = readdir($template_dir))
{
    if ($file != 'cqqswhcb' && $file != '.' && $file != '..' && is_dir(ROOT_PATH. 'themes/' . $file) && $file != '.svn' && $file != 'index.htm')
    {
        $available_templates[] = get_template_info($file);
    }
}

@closedir($template_dir); 

/* 页面列表 */
$strtemplates = '';
while( @list( $k, $v ) = @each($available_templates ) ) {
	$strtemplates .= '<div style="float:left; text-align:center; border: 1px solid #DADADA; margin: 4px; padding:2px;">';
	$strtemplates .= '<IMG SRC="'.$v["screenshot"].'" WIDTH="200" HEIGHT="150" BORDER="0" TITLE="'. $v["desc"].'">';
	$checked = ($v["code"] == $template )?'checked':'';
    $strtemplates .= '<br/><INPUT TYPE="radio" NAME="templates_name" value="'.$v["code"].'" '.$checked.' />';
	$strtemplates .= $v['name'].' '.$v['version']. '<br/><A HREF="'.$v['author_uri'].'" target="_blank">'.$v['author'] .'</A>';
    $strtemplates .= '</div>';
	$strtemplates .= '<INPUT TYPE="hidden" NAME="flash_1['.$v["code"].']" value="'.$v['flash_1'].'" />';
	$strtemplates .= '<INPUT TYPE="hidden" NAME="flash_2['.$v["code"].']" value="'.$v['flash_2'].'" />'; 
}
?>
<?php
include_once( "header.php");
if ($strMessage != ''){
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}
?>
<DIV class=content>
<TABLE width="100%" border=0>
  <TR  >
    <FORM METHOD=POST NAME="TEMPLATE_EDIT" ACTION="">      
      <TD width="13%" align="left" >
	    
           <?php  echo $strtemplates; ?>
		    <div style="clear:left"></div>
			<?php echo $user_template;?>
		    <input type="submit" name="Submit" value="确定选择" style="background-color: #FFCC66;font-size: 14px"/>
			<INPUT TYPE="hidden" name="action" value="save">			
      </TD>
	</FORM>
   </TR>	
</TABLE> 
</DIV>
<?php
/**
 * 获得模版的信息
 *
 * @access  private
 * @param   string      $template_name      模版名
 * @return  array
 */
function get_template_info($template_name)
{
    $info = array();
    $ext  = array('png', 'gif', 'jpg', 'jpeg');

    $info['code']       = $template_name;
    $info['screenshot'] = '';

    foreach ($ext AS $val)
    {
        if (file_exists('../themes/' . $template_name . "/screenshot.$val"))
        {
            $info['screenshot'] = '../themes/' . $template_name . "/screenshot.$val"; 
            break;
        }
    }

    if (file_exists('../themes/' . $template_name . '/style.css') && !empty($template_name))
    {
        $arr = array_slice(file('../themes/'. $template_name. '/style.css'), 0, 11);

        $template_name      = explode(': ', $arr[1]);
        $template_uri       = explode(': ', $arr[2]);
        $template_desc      = explode(': ', $arr[3]);
        $template_version   = explode(': ', $arr[4]);
        $template_author    = explode(': ', $arr[5]);
        $author_uri         = explode(': ', $arr[6]);
        $logo_filename      = explode(': ', $arr[7]);
		$FlashSize_1        = explode(': ', $arr[8]);
		$FlashSize_2        = explode(': ', $arr[9]);

        $info['name']       = isset($template_name[1]) ? trim($template_name[1]) : '';
        $info['uri']        = isset($template_uri[1]) ? trim($template_uri[1]) : '';
        $info['desc']       = isset($template_desc[1]) ? trim($template_desc[1]) : '';
        $info['version']    = isset($template_version[1]) ? trim($template_version[1]) : '';
        $info['author']     = isset($template_author[1]) ? trim($template_author[1]) : '';
        $info['author_uri'] = isset($author_uri[1]) ? trim($author_uri[1]) : '';
        $info['logo']       = isset($logo_filename[1]) ? trim($logo_filename[1]) : '';
		
		$info['flash_1']         = isset($FlashSize_1[1]) ? trim($FlashSize_1[1]) : '';
		$info['flash_2']         = isset($FlashSize_2[1]) ? trim($FlashSize_2[1]) : '';

    } else
    {
        $info['name']       = '';
        $info['uri']        = '';
        $info['desc']       = '';
        $info['version']    = '';
        $info['author']     = '';
        $info['author_uri'] = '';
        $info['logo']       = '';
		$info['flash_1'] = $info['flash_2'] = '';
    }

    return $info;
}

?>
<?php
include_once( "footer.php");
?>
