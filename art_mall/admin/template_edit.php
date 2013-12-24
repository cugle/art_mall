<?php	
define('IN_OUN', true);
include_once( "../includes/command.php");
include_once( "./includes/admincommand.php");  
if($Aconf['priveMessage'] != '')
{
   echo showMessage($Aconf['priveMessage']);
   exit;
}
 
if(!$Aconf['user_template'])
{
   $strMessage =  "启用自定义模板后才能操作,<A HREF=\"sysconfig.php\">系统管理->系统属性设置</A>->启用自定义模板【是】";
   echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
   echo  showMessage($strMessage);
   exit;
}
 

/* 检查用户模板目录是否存在 */ 
$target_dir = ROOT_PATH .'templates/user_themes/'.$Aconf['domain_id']; 
if(!is_dir($target_dir)) 
{
	$source_dir = ROOT_PATH.'themes/'.$Aconf['template'].'/';
	if (!dir_copy($source_dir,$target_dir))
	 {
         $strMessage =  "自定义模板目录创建失败,\\n请与管理员联系";
	 }

}

$curr_template = $Aconf['domain_id'];

if($_POST[action] == 'save')
{

    $template = stripslashes($_POST['template']);
	$template =delete_php_code($template);  
	$template_file = ($_POST['template_file'] == 'style')?'style.css':$_POST['template_file'].'.html';
    $lib_file = '../templates/user_themes/' . $curr_template . '/' . $template_file;
    $lib_file = str_replace("0xa", '', $lib_file); // 过滤 0xa 非法字符 

    $org_html = str_replace("\xEF\xBB\xBF", '', file_get_contents($lib_file));

    if (@file_exists($lib_file) === true && @file_put_contents($lib_file, $template))
    {
        @file_put_contents('../templates/backup/' . $curr_template . '-' . $template_file , $org_html);

        $strMessage =  "模板修改成功！";
    }
    else
    {
        $strMessage =  "模板修改错误！".$template_file;
    }

}

/* 可以修改的模板 */
$Atemplate_files = array(
    'header'        => '页面顶部 -- header.html', 
    'footer'        => '页面底部 -- footer.html',
    'index'         => '首页主体 -- index.html', 

    'about'         => '关于我们 -- about.html', 
	'links'         => '友情连接 -- links.html',
	'style'         => '样式表修改 -- style.css',
);

/* 得到选择列表 */
$template_fileopt = '<SELECT NAME="template_file" id="template_file_id" onchange="selecttemplate(this.options[this.options.selectedIndex].value)">';
$template_fileopt .= '<OPTION VALUE="">模板文件选择</OPTION>';
while( @list( $k, $v ) = @each( $Atemplate_files) ) {
	$tmp = ($_REQUEST[template_file] == $k)?'selected':'';
    $template_fileopt .= '<OPTION VALUE="'.$k.'" '.$tmp.'>'.$v.'</OPTION>';
}
$template_fileopt .= '</SELECT>';

/* 得到页面代码类容 */
$lib_name = ($_REQUEST[template_file])?$_REQUEST[template_file]:'header';
$Aconnect = load_library($curr_template, $lib_name);
?>
<?php
include_once( "header.php"); 
if ($strMessage != '') {
	echo  '<SCRIPT language="javascript"> alert( "'.$strMessage.'");</script>';
}

?>
<DIV class=content>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="button">
<tr>
  <td align="left">
 <?php echo $template_fileopt;?>
 </td>
</tr>
</table>
<TABLE width="100%" border=0>
  <TR >
    <FORM METHOD=POST NAME="TEMPLATE_EDIT" ACTION="">      
      <TD width="13%" align="left" >
	     <TEXTAREA NAME="template"  style="clear:all;margin-left:0px;padding:0;height:550px;width:100%"><?php echo $Aconnect[html];?>
	     </TEXTAREA>
		 <div style="clear:left">
		    <input type="submit" name="Submit" value="确定保存" style="background-color: #FFCC66"/>
            <INPUT TYPE="reset" name="reset" value="恢复当前操作" style="background-color: #CCFF99"> 
			<INPUT TYPE="hidden" name="template_file" value="<?php echo $lib_name;?>">
			<INPUT TYPE="hidden" name="action" value="save">
		 </div>
      </TD>
	</FORM>
   </TR>	
 
</TABLE>
 
</DIV>
<SCRIPT language=JavaScript>
  function  selecttemplate(a)
  {
     location="<?php echo $_SERVER["PHP_SELF"];?>?template_file=" + a;   
  }
</SCRIPT>
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
        $arr = array_slice(file('../themes/'. $template_name. '/style.css'), 0, 9);

        $template_name      = explode(': ', $arr[1]);
        $template_uri       = explode(': ', $arr[2]);
        $template_desc      = explode(': ', $arr[3]);
        $template_version   = explode(': ', $arr[4]);
        $template_author    = explode(': ', $arr[5]);
        $author_uri         = explode(': ', $arr[6]);
        $logo_filename      = explode(': ', $arr[7]);

        $info['name']       = isset($template_name[1]) ? trim($template_name[1]) : '';
        $info['uri']        = isset($template_uri[1]) ? trim($template_uri[1]) : '';
        $info['desc']       = isset($template_desc[1]) ? trim($template_desc[1]) : '';
        $info['version']    = isset($template_version[1]) ? trim($template_version[1]) : '';
        $info['author']     = isset($template_author[1]) ? trim($template_author[1]) : '';
        $info['author_uri'] = isset($author_uri[1]) ? trim($author_uri[1]) : '';
        $info['logo']       = isset($logo_filename[1]) ? trim($logo_filename[1]) : '';
    }
    else
    {
        $info['name']       = '';
        $info['uri']        = '';
        $info['desc']       = '';
        $info['version']    = '';
        $info['author']     = '';
        $info['author_uri'] = '';
        $info['logo']       = '';
    }

    return $info;
}

/**
 * 载入库项目内容
 *
 * @access  public
 * @param   string  $curr_template  模版名称
 * @param   string  $lib_name       库项目名称
 * @return  array
 */
function load_library($curr_template, $lib_name)
{
    $lib_name = str_replace("0xa", '', $lib_name); // 过滤 0xa 非法字符
    if($lib_name == 'style')
	{
		$lib_file    = '../templates/user_themes/' . $curr_template . '/'.$lib_name.'.css';
	}
	else
	{
        $lib_file    = '../templates/user_themes/' . $curr_template . '/'.$lib_name.'.html';
	}
    $arr['html'] = str_replace("\xEF\xBB\xBF", '', file_get_contents($lib_file));

    return $arr;
}


?>
<?php
include_once( "footer.php");
?>