<?php
function showMessage($urlstr,$title = 'Power by QQ:16953292,Msn:xufyong@gmail.com')
{ 
	$tS = '<html xmlns="http://www.w3.org/1999/xhtml">';
    $tS .= '<head>';
    $tS .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
    $tS .= '<title>'.$title.'</title>';
	$tS .= '<style>
			body{background-color: #DBE1FD}
			.shadow{  
			   width:98%;  
			   padding:5px;  
			   -moz-box-shadow :3px 3px 4px #ccc; 
			   *filter : progid:DXImageTransform.Microsoft.Shadow(Strength=4, Direction=135, Color="#cccccc"); 
			   -webkit-box-shadow :3px 3px 4px #ccc; 
			   box-shadow :3px 3px 4px #ccc;  
			} 
			.box{
				float:left;margin:100px 50px 50px 50px; padding:10px;width:516px;border:1px solid #cbcbcb;font-size:12px;background-color: #FFFFFF
			}
			</style>';
    $tS .= '</head>';
    $tS .= '<body>';
    $tS .= '<div class="box shadow">';
    $tS .= $urlstr;
    $tS .= '</div>';
    $tS .= '</body>';
    $tS .= '</html>';
    return $tS;
} 

function clean_html($string){
	$search = array ("'<script[^>]*?>.*?</script>'si",
				 "'<[\/\!]*?[^<>]*?>'si",
				 "'([\r\n])[\s]+'",
				 "'&(quot|#34);'i",
				 "'&(amp|#38);'i",
				 "'&(lt|#60);'i",
				 "'&(gt|#62);'i",
				 "'&(nbsp|#160);'i",
				 "'&(iexcl|#161);'i",
				 "'&(cent|#162);'i",
				 "'&(pound|#163);'i",
				 "'&(copy|#169);'i",
				 "'&#(\d+);'e",
				 "'\"'si",
				 "'\''si"); 
	$replace = array ("",
					  "",
					  "",
					  "",
					  "",
					  "",
					  "",
					  "",
					  "",
					  "",
					  "",
					  "",
					  "",
					  "“",
					  "‘");

	$string = preg_replace ($search, $replace, $string);
	return $string;
}

//分页
class ShowPage {  
    var $PageSize; //每页显示的记录数 
    var $Total; //记录总数  
    var $LinkAry; //Url参数数组，对于复合条件查询分页显示情况非常好用 
	var $PHP_SELF;
    
    //取得总页数
    function PageCount() {
        $TotalPage = ($this->Total % $this->PageSize == 0) ? floor($this->Total / $this->PageSize) : floor($this->Total / $this->PageSize)+1;
        return $TotalPage;
    }
    //取得当前页
    function PageNum() {
        $page = ((isset($_GET['page'])) != '') ? $_GET['page'] : $page = 1; 
        if (isset($_POST['page'])) $page = $_POST['page'];
        if ($page < 1 ) $page =1;
        return $page;
    }
    //查询语句定位指针        
    function OffSet() {
        if ($this->PageNum() > $this->PageCount()) {
        //$this->PageNum = $this->PageCount();
        $pagemin = max(0,$this->Total - $this->PageSize - 1);
        }else if ($this->PageNum() == 1){
         $pagemin = 0;
        }else {
         $pagemin = min($this->Total - 1,$this->PageSize * ($this->PageNum() - 1));
        }
        return $pagemin . "," . $this->PageSize;
     }
    //定位首页        
    function FristPage() {
        global $Aconf;
        $LinkAry   =  $this->Url($this->LinkAry);
        if($Aconf['rewrite']){ 
            $A = explode(".",$this->PHP_SELF);
            $preFile = $A[0]; 

            $strurl = ' <a href="'.$preFile.$LinkAry.'-1.html">First</a> ';
            $Frist = ($this->PageNum() <= 1) ? "" : $strurl;
        }else{ 
            $Frist = ($this->PageNum() <= 1) ? "" : "<a href=\"?page=1".$this->Url($this->LinkAry)."\">First</a> ";
        }
        return $Frist;
    }
//定位上一页
    function PrePage() {
        global $Aconf;

        $prepage=$this->PageNum() - 1;
        $LinkAry   =  $this->Url($this->LinkAry);
        if($Aconf['rewrite']){ 
            $A = explode(".",$this->PHP_SELF);
            $preFile = $A[0]; 
            $strurl = ' <a href="'.$preFile.$LinkAry.'-'.$prepage.'.html">Pre</a> ';
            $Previous = ($this->PageNum() >= 2) ? $strurl : "";
        }else{ 
            $Previous = ($this->PageNum() >= 2) ? " <a href=\"?page=".$prepage.$this->Url($this->LinkAry)."\">Pre</a> " : "";
        }
        return $Previous;
    }
//定位下一页
    function NextPage() {
        global $Aconf;
        $nextpage = $this->PageNum() + 1;
        $LinkAry   =  $this->Url($this->LinkAry);
        if($Aconf['rewrite']){  
            $A = explode(".",$this->PHP_SELF);
            $preFile = $A[0]; 
            $strurl = ' <a href="'.$preFile.$LinkAry.'-'.$nextpage.'.html">Next</a> ';
            $Next = ($this->PageNum() <= $this->PageCount()-1) ? $strurl : "";
        }else{
             $Next = ($this->PageNum() <= $this->PageCount()-1) ? " <a href=\"?page=".$nextpage.$this->Url($this->LinkAry)."\">Next</a> " : "";
        }
        return $Next;
    }
//定位最后一页
    function LastPage() {
        global $Aconf;
        $LinkAry   =  $this->Url($this->LinkAry);
        if($Aconf['rewrite']){ 
            $A = explode(".",$this->PHP_SELF); 
            $preFile = $A[0]; 
            $strurl = ' <a href="'.$preFile.$LinkAry.'-'.$this->PageCount().'.html">Last</a> ';
            $Last = ($this->PageNum() >= $this->PageCount()) ?" ": $strurl;
        }else{
            $Last = ($this->PageNum() >= $this->PageCount()) ? " " : " <a href=\"?page=".$this->PageCount().$this->Url($this->LinkAry)."\">Last</a> ";
        }
        return $Last;
    }
//下拉跳转页面
    function JumpPage() {
        $Jump = " 当前第 <b>".$this->PageNum()."</b> 页 共 <b>".$this->PageCount()."</b> 页 跳到 <select name=page onchange=\"javascript:location=this.options[this.selectedIndex].value;\">";
        for ($i=1; $i<=$this->PageCount(); $i++) {
        if ($i==$this->PageNum())
            $Jump .= "<option value=\"?page=".$i.$this->Url($this->LinkAry)."\" selected>$i</option>";
        else 
            $Jump .="<option value=\"?page=".$i.$this->Url($this->LinkAry)."\">$i</option> ";    
        }
        $Jump .= "</select> 页 <b>[".$this->PageSize."条/页]共".$this->Total."条</b>";
        return $Jump;
    }
    function JumpPage2() {
        $Jump = "共 <b>".$this->PageCount()."</b> 页 转到 <select name=page onchange=\"javascript:location=this.options[this.selectedIndex].value;\">";
        for ($i=1; $i<=$this->PageCount(); $i++) {
        if ($i==$this->PageNum())
            $Jump .= "<option value=\"?page=".$i.$this->Url($this->LinkAry)."\" selected>$i</option>";
        else 
            $Jump .="<option value=\"?page=".$i.$this->Url($this->LinkAry)."\">$i</option> ";    
        }
     $Jump .= "</select>";
        return $Jump;
    }
    function JumpPage_num($num = 10) {
        global $Aconf;
        //$num 翻页偏移量
        $nowpage  = $this->PageNum();
        $totalpage = $this->PageCount();
        $totalnum  = $this->Total;
        $PageSize =  $this->PageSize;
        $LinkAry   =  $this->Url($this->LinkAry);
        if($Aconf['rewrite']){ 
            $A = explode(".",$this->PHP_SELF);
            $preFile = $A[0]; 
        }
        
        if($num > 0 ){
            $j = ceil($nowpage /  $num);
        }else{
            return false;
        }

        $num_satrt = ($j - 1) * $num;
        $num_satrt = (!$num_satrt)?1:$num_satrt;

        $num_end = $j * $num;
        $num_end = ($num_end>$totalpage)?$totalpage:$num_end;
 
        $strurl = '';
        for ($i=$num_satrt; $i<=$num_end; $i++) 
        {
              if($nowpage == $i) {
                  if($Aconf['rewrite']){
                        $strurl .= ' <A HREF="'.$preFile.$LinkAry.'-'.$i.'.html" style="background-color:#FFCC00">'.$i.'</A> ';
                  }else{
                        $strurl .= ' <A HREF="'.$this->PHP_SELF.'?page='.$i.$LinkAry.'" style="background-color:#FFCC00">'.$i.'</A> ';
                  }
              } else {
                  if($Aconf['rewrite']){
                        $strurl .= ' <A HREF="'.$preFile.$LinkAry.'-'.$i.'.html">'.$i.'</A> ';
                  }else{
                        $strurl .= ' <A HREF="'.$this->PHP_SELF.'?page='.$i.$LinkAry.'">'.$i.'</A> ';
                  }
              }
        }
       // return $this->FristPage().' '.$this->PrePage().$strurl.' '.$this->NextPage().' '.$this->LastPage().' <a href="#" style="color:#CCC;font-weight: bold" title="[当前第'.$this->PageNum().'页,每页'.$this->PageSize.'条,共'.$this->Total.'条]>共 '.$this->PageCount().' 页</a>';
	    return $this->FristPage().' '.$this->PrePage().$strurl.' '.$this->NextPage().' '.$this->LastPage();
    }    
//URL参数处理
    function Url($ary) {
        global $Aconf;
        $Linkstr = "";
        if (count($ary) > 0) {
             if($Aconf['rewrite']){ 
                foreach ($ary as $key => $val) {
                    $Linkstr .= "-".$val;
                }
             }else{
                foreach ($ary as $key => $val) {
                    $Linkstr .= "&".$key."=".$val;
                }
             }
        }
        return $Linkstr;
    }
   //生成导航条
    function ShowLink() {
        return $this->FristPage().$this->PrePage().$this->NextPage().$this->LastPage().$this->JumpPage();
    }   
    function ShowLink2() {
        return $this->JumpPage2();
    }    
    function ShowLink_num() {
        return $this->JumpPage_num();
    }     
}
//end  

/**
 * 获得用户的真实IP地址
 *
 * @access  public
 * @return  string
 */
function real_ip() {
    static $realip = NULL; 
    if ($realip !== NULL) {
        return $realip;
    } 
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']); 
            /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
            foreach ($arr AS $ip) {
                $ip = trim($ip); 
                if ($ip != 'unknown') {
                    $realip = $ip; 
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $realip = $_SERVER['REMOTE_ADDR'];
            }  else {
                $realip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }

    preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
    $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0'; 
    return $realip;
}

function gzip_enabled() {
    static $enabled_gzip = NULL; 
    if ($enabled_gzip === NULL) {
        $enabled_gzip = ( function_exists('ob_gzhandler'));
    } 
    return $enabled_gzip;
} 
 

/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $length     截取的长度
 * @param   bool        $append     是否附加省略号
 *
 * @return  string
 */
function sub_str($str, $length = 0, $append = true)
{
    $str = trim($str);
    $strlength = strlen($str);

    if ($length == 0 || $length >= $strlength)
    {
        return $str;
    }
    elseif ($length < 0)
    {
        $length = $strlength + $length;
        if ($length < 0)
        {
            $length = $strlength;
        }
    }

    if (function_exists('mb_substr'))
    {
        $newstr = mb_substr($str, 0, $length, 'UTF-8');
    }
    elseif (function_exists('iconv_substr'))
    {
        $newstr = iconv_substr($str, 0, $length, 'UTF-8');
    }
    else
    {
        $newstr = trim_right(substr($str, 0, $length));
    }

    if ($append && $str != $newstr)
    {
        $newstr .= '...';
    }

    return $newstr;
}

/**
 * 去除字符串右侧可能出现的乱码
 *
 * @param   string      $str        字符串
 *
 * @return  string
 */
function trim_right($str)
{
    $length = strlen(preg_replace('/[\x00-\x7F]+/', '', $str)) % 3;

    if ($length > 0)
    {
        $str = substr($str, 0, 0 - $length);
    }

    return $str;
}

/**
 * 计算字符串的长度（汉字按照两个字符计算）
 *
 * @param   string      $str        字符串
 *
 * @return  int
 */
function str_len($str)
{
    $length = strlen(preg_replace('/[\x00-\x7F]/', '', $str));

    if ($length)
    {
        return strlen($str) - $length + intval($length / 3) * 2;
    }
    else
    {
        return strlen($str);
    }
}
/**
 * 验证输入的邮件地址是否合法
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
function is_email($user_email)
{
	$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
	if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
     {
        if (preg_match($chars, $user_email))
        {
            return true;
        } else
        {
            return false;
        }
    } 
	else
    {
       return false;
    }
}

function gmtime()
{
    return time();
}
function local_strtotime($str)
{
	$time = strtotime($str);
    return $time;

}
/**
 * 文件或目录权限检查函数
 *
 * @access          public
 * @param           string  $file_path   文件路径
 * @param           bool    $rename_prv  是否在检查修改权限时检查执行rename()函数的权限
 *
 * @return          int     返回值的取值范围为{0 <= x <= 15}，每个值表示的含义可由四位二进制数组合推出。
 *                          返回值在二进制计数法中，四位由高到低分别代表
 *                          可执行rename()函数权限、可对文件追加内容权限、可写入文件权限、可读取文件权限。
 */
function file_mode_info($file_path)
{
    /* 如果不存在，则不可读、不可写、不可改 */
    if (!file_exists($file_path))
    {
        return false;
    }

    $mark = 0;

    if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
    {
        /* 测试文件 */
        $test_file = $file_path . '/cf_test.txt';

        /* 如果是目录 */
        if (is_dir($file_path))
        {
            /* 检查目录是否可读 */
            $dir = @opendir($file_path);
            if ($dir === false)
            {
                return $mark; //如果目录打开失败，直接返回目录不可修改、不可写、不可读
            }
            if (@readdir($dir) !== false)
            {
                $mark ^= 1; //目录可读 001，目录不可读 000
            }
            @closedir($dir);

            /* 检查目录是否可写 */
            $fp = @fopen($test_file, 'wb');
            if ($fp === false)
            {
                return $mark; //如果目录中的文件创建失败，返回不可写。
            }
            if (@fwrite($fp, 'directory access testing.') !== false)
            {
                $mark ^= 2; //目录可写可读011，目录可写不可读 010
            }
            @fclose($fp);

            @unlink($test_file);

            /* 检查目录是否可修改 */
            $fp = @fopen($test_file, 'ab+');
            if ($fp === false)
            {
                return $mark;
            }
            if (@fwrite($fp, "modify test.\r\n") !== false)
            {
                $mark ^= 4;
            }
            @fclose($fp);

            /* 检查目录下是否有执行rename()函数的权限 */
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
            @unlink($test_file);
        }
        /* 如果是文件 */
        elseif (is_file($file_path))
        {
            /* 以读方式打开 */
            $fp = @fopen($file_path, 'rb');
            if ($fp)
            {
                $mark ^= 1; //可读 001
            }
            @fclose($fp);

            /* 试着修改文件 */
            $fp = @fopen($file_path, 'ab+');
            if ($fp && @fwrite($fp, '') !== false)
            {
                $mark ^= 6; //可修改可写可读 111，不可修改可写可读011...
            }
            @fclose($fp);

            /* 检查目录下是否有执行rename()函数的权限 */
            if (@rename($test_file, $test_file) !== false)
            {
                $mark ^= 8;
            }
        }
    }
    else
    {
        if (@is_readable($file_path))
        {
            $mark ^= 1;
        }

        if (@is_writable($file_path))
        {
            $mark ^= 14;
        }
    }

    return $mark;
} 
//字符类型判断 是否为Utf8
function isUTF8($str)
{
	 $length=strlen($str);
	 for($i=0;$i<$length;$i++){
		  $high=ord($str{$i});
		  if(($high==0xC0)||($high==0xC1)){
			return false;
		  }elseif($high<0x80){
			continue;
		  }elseif($high<0xC0){
			return false;
		  }elseif($high<0xE0){
			if(++$i>=$length)
				return true;
			elseif(($str{$i}&"\xC0")=="\x80")
				continue;
		  }elseif($high<0xF0){
			if(++$i>=$length){
				return true;
			}elseif(($str{$i}&"\xC0")=="\x80"){
				if(++$i>=$length)
					return true;
				elseif(($str{$i}&"\xC0")=="\x80")
					continue;
			}
		  }elseif($high<0xF5){
		   if(++$i>=$length){
			return true;
		   }elseif(($str{$i}&"\xC0")=="\x80"){
			if(++$i>=$length){
			 return true;
			}elseif(($str{$i}&"\xC0")=="\x80"){
			 if(++$i>=$length)
			  return true;
			 elseif(($str{$i}&"\xC0")=="\x80")
			  continue;
			}
		   }
		  }
		  return false;
	 }
	 return true;
} 
/* 
 * js escape 编码，的php->utf8 方法 
 * UnEscapeToUtf8() getUtf8($value= " ")
 */
function UnEscapeToUtf8($ar)
{ 
    $c = "";
    foreach($ar as $val)
	{ 
        $val = intval(substr($val,2),16);
        if ($val < 0x7F){ // 0000-007F
            $c .= chr($val);
        } elseif ($val < 0x800) { // 0080-0800
            $c .= chr(0xC0 | ($val / 64));
            $c .= chr(0x80 | ($val % 64));
        } else { // 0800-FFFF
            $c .= chr(0xE0 | (($val / 64) / 64));
            $c .= chr(0x80 | (($val / 64) % 64));
            $c .= chr(0x80 | ($val % 64));
        }
     } 
     return   $c; 
} 

function getUtf8($value= " ")
{ 
    $text =  preg_replace_callback( "/%u[0-9A-Za-z]{4}/ ","UnEscapeToUtf8",$value); 
    return   urldecode($text); 
}

/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 *
 * @return      bool
 */
function make_dir($folder)
{
    $reval = false;

    if (!file_exists($folder))
    {
        /* 如果目录不存在则尝试创建该目录 */
        @umask(0);

        /* 将目录路径拆分成数组 */
        preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

        /* 如果第一个字符为/则当作物理路径处理 */
        $base = ($atmp[0][0] == '/') ? '/' : '';

        /* 遍历包含路径信息的数组 */
        foreach ($atmp[1] AS $val)
        {
            if ('' != $val)
            {
                $base .= $val;

                if ('..' == $val || '.' == $val)
                {
                    /* 如果目录为.或者..则直接补/继续下一个循环 */
                    $base .= '/';

                    continue;
                }
            }
            else
            {
                continue;
            }

            $base .= '/';

            if (!file_exists($base))
            {
                /* 尝试创建目录，如果创建失败则继续循环 */
                if (@mkdir($base, 0777))
                {
                    @chmod($base, 0777);
                    $reval = true;
                }
            }
        }
    }
    else
    {
        /* 路径已经存在。返回该路径是不是一个目录 */
        $reval = is_dir($folder);
    }

    clearstatcache();

    return $reval;
}


/* 复制目录 
    $target_dir = ROOT_PATH .'templates/user_themes/'.$_SESSION[$un_domain_id'];
    $source_dir = ROOT_PATH.'themes/'.$Aconf['template'].'/';
   结果：文件及目录属性 0777
*/
function dir_copy($source_dir,$target_dir)
{
	if(!is_dir($source_dir))  
		return false;

	if(!is_dir($target_dir))
   {
	   if(!make_dir($target_dir, 0777))
	   {
		   return false;
	   }
   }

   if ($dh = opendir($source_dir))
  {
		while (($file = readdir($dh)) !== false )
		{
			 if(filetype($source_dir . $file) != 'dir')
			{
				  copy($source_dir.$file,$target_dir.'/'.$file);
				  @chmod($target_dir.'/'.$file, 0777);
			 }
			 else if($file != '.' && $file != '..')
			{
				 $target_img_dir = $target_dir.'/'.$file;
				 $source_img_dir = $source_dir.$file.'/';
				 dir_copy($source_img_dir,$target_img_dir);
			 
			}
		}
		closedir($dh);
   }
   return true;
}


/**
 * 递归方式的对变量中的特殊字符进行转义
 *
 * @access  public
 * @param   mix     $value
 *
 * @return  mix
 */
function addslashes_deep($value)
{
    if (empty($value))
    {
        return $value;
    }
    else
    {
        return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
    }
}
/**
 * 递归方式删除指定目录及所属文件
 * @access  public
 * @param   string     $dirName  需要删除的目录名
 * @return             无返回值
**/
function deldirandfile( $dirName )
{
	if ( $handle = opendir( "$dirName" ) ) {
	   while ( false !== ( $item = readdir( $handle ) ) ) {
		   if ( $item != "." && $item != ".." ) {
			   if ( is_dir( "$dirName/$item" ) ) {
					deldirandfile( "$dirName/$item" );
			   } else {
					@unlink( "$dirName/$item" ) ;
			   }
		   }
	   }
	   closedir( $handle );
	   @rmdir( $dirName );
	}
}

/**
 *  清除指定后缀的模板缓存或编译文件
 *
 * @access  public
 * @param  bool       $is_cache  是否清除缓存还是清出编译文件
 * @param  string     $ext       需要删除的文件名，不包含后缀
 *
 * @return int        返回清除的文件个数
 */
function clear_tpl_files($is_cache = true, $ext = '')
{
    global $Aconf;
    $dirs = array();
    if ($is_cache) {
        $dirs[] = ROOT_PATH . 'templates/caches/'.$Aconf['domain_id'].'/';
    }  else {
        $dirs[] = ROOT_PATH . 'templates/compiled/'.$Aconf['domain_id'].'/';
        $dirs[] = ROOT_PATH . 'templates/compiled/admin/'.$Aconf['domain_id'].'/';
    }

    $str_len = strlen($ext);
    $count   = 0; 
    foreach ($dirs AS $dir)
    {
        $folder = @opendir($dir); 
        if ($folder == false) 
		{
            continue;
        }

        while ($file = readdir($folder))
        {
            if ($file == '.' || $file == '..' || $file == 'index.htm' || $file == 'index.html')
            {
                continue;
            }
            if (is_file($dir . $file))
            {
                /* 如果有文件名则判断是否匹配 */
                $pos = ($is_cache) ? strrpos($file, '_') : strrpos($file, '.');

                if ($str_len > 0 && $pos !== false)
                {
                    $ext_str = substr($file, 0, $pos);

                    if ($ext_str == $ext)
                    {
                        if (@unlink($dir . $file))
                        {
                            $count++;
                        }
                    }
                } else {
                    if (@unlink($dir . $file))
                    {
                        $count++;
                    }
                }
            }elseif(is_dir($dir . $file))
			{
				deldirandfile($dir . $file); 
			}

        }
        closedir($folder);
    }

    return $count;
}

/**
 * 清除模版编译文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名， 不包含后缀
 * @return  void
 */
function clear_compiled_files($ext = null)
{
    return clear_tpl_files(false, $ext);
}

/**
 * 清除缓存文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名， 不包含后缀
 * @return  void
 */
function clear_cache_files($ext = null)
{
    return clear_tpl_files(true, $ext);
}

/**
 * 清除模版编译和缓存文件
 *
 * @access  public
 * @param   mix     $ext    模版文件名后缀
 * @return  void
 */
function clear_all_files($ext = null)
{
    return clear_tpl_files(false, $ext) + clear_tpl_files(true,  $ext);
}
 
/* 
取当前分类及所有的下级显示分类 
$id        号
$db_table  数据表名
$idtype    id字段名
$grand     true取得所有下级分类 false只取得一级分类
return  id字串 1,2,3
*/
function next_node_all($id,$db_table,$idtype="acid",$grand=true) {
   global $oPub;
   $sql = "SELECT next_node FROM ".$db_table." where ".$idtype." ='".$id."' AND ifshow = 1";
   $next_node = $oPub->getOne($sql); 
   $Agrad = explode(',',$next_node);
   $strID .= '';
   if(count($Agrad) > 0 ) { 
		while( @list( $k, $v ) = @each( $Agrad ) ) {
			if ($v > 1) 
			{ 
				$sql = "SELECT ".$idtype.",next_node FROM ".$db_table." where ".$idtype." ='".$v."' AND ifshow = 1";
				$Anorm = $oPub->getRow($sql);
				if( $Anorm[$idtype] > 0) {
					$strID .= ','.$Anorm[$idtype];
					if($grand) {
					   $strID .= ','.next_node_all($Anorm[$idtype],$db_table,$idtype,$grand);
					} 
				}
			}
		} 
	} 

	if(empty($strID)){
		return false;
	}else{
		$Agrad = explode(',',$strID);
		$strID = '';
		while( @list( $k, $v ) = @each( $Agrad ) ) {
			if($v > 0 ){
				$strID .= $v.',';
			}
		}
		if(!empty($strID)){  
			return substr($strID,0,-1);
		} 
	} 
}

/* 
取当前分类的所有前置分类
$fid        当前分类的id号
$db_table  数据表名
$idtype    id字段名
return  id字串 1,2,3  (以顶级->一级->二级方式排序)
*/
function pre_node_orders($fid,$db_table,$idtype="acid")
{
	global $oPub; 
	$sql = "SELECT ".$idtype.",fid FROM ".$db_table." where ".$idtype." = '".$fid."'"; 
	$Anorm = $oPub->getRow($sql);
	if( $Anorm['fid'] > 0)
	{ 
		return pre_node_orders($Anorm['fid'],$db_table,$idtype).','.$Anorm[$idtype];
	}else{
		return  $Anorm[$idtype];
	}
}
/* 
取当前分类的所有前置分类 
$fid       当前分类的父id号
$db_table  数据表名
$idtype    id字段名
$grand     true取得所有下级分类 false只取得一级分类
return  id字串 1,2,3
*/
function pre_node($fid,$db_table,$idtype="acid",$navefile='articles.php',$grand=true,$strid="id")
{
   global $oPub,$Aconf;
   if($fid > 0 ){
        $Anorm = $oPub->getRow("SELECT ".$idtype.",fid,name FROM ".$db_table." where ".$idtype." = '".$fid."'"); 
		if( $Anorm['name'] != '') {
			if($Aconf['rewrite']){
				$Atmp = explode(".",$navefile); 
				if($Atmp[0] == 'articles')
				{
					$href = $Atmp[0].'-'.$Anorm[$idtype].'-0.html'; 
				}else
				{
					$href = $Atmp[0].'-'.$Anorm[$idtype].'-0-0-0-0.html'; //产品分类 有搜索
				}
			}else{
				if(strpos($navefile,"?")) {
					$href = $navefile.'&'.$strid.'='.$Anorm[$idtype];
				}else{
					$href = $navefile.'?'.$strid.'='.$Anorm[$idtype];
				}
			}
			$Str = '<li><a href="'.$href.'">'.$Anorm['name'].'</a>'.$Aconf['nav_symbol'].'</li>'; 
			if($grand) {
                  $Stropt = pre_node($Anorm['fid'],$db_table,$idtype,$navefile,$grand).' '.$Str;
			} else {
                  $Stropt = $Str;
			}
		}
	} else {
        $Stropt = false;
	}
	return $Stropt;
}
/* 
取当前分类的顶级分类 
$fid        对应的fid值
$db_table  数据表名
$idtype    id字段名 

return     id 顶级分类id
*/
function pre_node_top($fid,$db_table,$idtype="acid")
{
     global $oPub;
     $sql = "SELECT ".$idtype.",fid  FROM ".$db_table." where ".$idtype." = '".$fid."'";
     $row = $oPub->getRow($sql);
	 if( $row['fid'] > 0 ) {
         pre_node_top($row['fid'],$db_table,$idtype);
	 } else {
         return $row[$idtype];
	 }	
}
/* 得到文章列表 */
function articles_list( $orderby='arid', $limit='',$acid='',$substr=26,$where_ext='') {
	global $oPub,$pre,$_SESSION,$un_domain_id,$Aconf;
	$Strstates = ($Aconf['article'])?" a.states = 2 ":" a.states <> 1 ";
	$Strstates .= (!empty($acid))?" and a.acid in(".$acid.") ":'';
	$Strstates .= (!empty($where_ext))?" and ".$where_ext." ":' ';

	$limit = empty($limit)?'':' limit '.$limit;
    $sql = 'SELECT a.arid,a.acid,a.name,a.subname,a.otherurl,a.colors,a.arti_date,a.dateadd,a.comms,a.min_thumb,a.arti_thumb,a.edit_comm,b.cltion  FROM '.$pre.'artitxt as a,'.$pre.'article as b 
		WHERE a.arid=b.arid and  '.$Strstates .' and a.domain_id='.$Aconf['domain_id']. 
        ' ORDER BY a.'.$orderby.' desc,a.arti_date desc '.$limit;  
	$rowarticles = $oPub->select($sql);
	if($rowarticles){
		foreach ($rowarticles AS $key=>$val) {
			$subname = sub_str($val['subname'],$substr,false);
			if($val['colors']){
				$rowarticles[$key]['subname']       =  '<font style="color:'.$val['colors'].'">'.$subname.'</font>'; 
			}else{
				$rowarticles[$key]['subname'] = $subname;
			}
			$rowarticles[$key]['name']       =  $val['name'];

			if($val['otherurl']) {
				 $rowarticles[$key]['article_url'] =$val['otherurl'];
			}else{
				if($Aconf['rewrite']){
					$rowarticles[$key]['article_url'] = 'article-'.$val['arid'].'-0.html';
				}else{
					$rowarticles[$key]['article_url'] = 'article.php?id='.$val['arid'];
				}
			}

			if(!$val['min_thumb']){
				  $rowarticles[$key]['min_thumb'] = 'images/command/no_imgs.png';
			}

			if(!$val['arti_thumb']){
				  $rowarticles[$key]['arti_thumb'] = 'images/command/no_imgsbig.png';
			}		
			$rowarticles[$key]['edit_comm'] =  $val['edit_comm']; 
			$rowarticles[$key]['dateadd']  = ($val['dateadd'])?date("Y年m月d日", $val['dateadd']):'';
			$rowarticles[$key]['arti_date']  = ($val['arti_date'])?date("m月d日", $val['arti_date']):''; 

			$sql = "SELECT name FROM ".$pre."articat WHERE acid='".$val['acid']."'";
			$row = $oPub->getRow($sql);
			$rowarticles[$key]['acname'] = $row['name'];
			if($Aconf['rewrite']){
				$rowarticles[$key]['acname_url'] = 'articles-'.$val['acid'].'-0.html';
				$rowarticles[$key]['arcomms_url'] = 'acomms-'.$rowarticles[$key]['arid'].'-0.html';
			}else{
				$rowarticles[$key]['acname_url'] = 'articles.php?id='.$val['acid'];
				$rowarticles[$key]['arcomms_url'] = 'acomms.php?id='.$rowarticles[$key]['arid'];
			}
			//关联文章
			if(!empty($val['cltion'])) {
				$strCltion = '';
				$Acltion = explode("{|}",$val['cltion']);
				$n = 1;
				$AstrCltion = array();
				while( @list( $k, $v) = @each($Acltion) ) {
				   $Akeysname = explode("[|]",$v);
				   if($Akeysname[0]) {
						 $n ++ ;
						 $AstrCltion[$n] = '<A HREF="'.$Akeysname[1].'">'.$Akeysname[0].'</A>';
					}
				}
				if(count($AstrCltion) > 0) { 
					 $rowarticles[$key]['cltion'] =$AstrCltion;
				} else {
					 $rowarticles[$key]['cltion'] = false;
				}
			}
			//关联图库
			$rowarticles[$key]['img_list'] = $oPub->select('SELECT thumb_url,filename,descs FROM ' . $pre.'arti_file WHERE arid = "'.$val['arid'].'" limit 3');  
		}
	}else{ 
		$rowarticles = false;
	}
	return $rowarticles;
}
/* 得到产品列表 */
function products_list( $orderby='prid desc ', $limit='',$pcid='',$whereExt='') {

	global $oPub,$pre,  $Aconf;
	$GlobstrPcid_fun = !empty($pcid)?" and  pcid in(".$pcid.") ":''; 
	$GlobstrPcid_fun .= !empty($whereExt)?$whereExt:''; 
	$limit = empty($limit)?'':' limit '.$limit; 
    $rowproduct = $oPub->select('SELECT  prid, pcid, name, edit_comm,shop_sn, shop_number, shop_price, s_discount, s_dis_exp, colors, up_date, dateadd, comms, min_thumb, shop_thumb FROM '.$pre.'producttxt    
		WHERE  states <> 1 ' .$GlobstrPcid_fun.
		' AND  domain_id="'.$Aconf['domain_id']. 
        '" ORDER BY  '.$orderby . $limit);  
    if($rowproduct )
    foreach ($rowproduct AS $key=>$val) { 
		$rowproduct[$key]['alt_name'] = clean_html($val['name']);
		if($val['colors']){
		    $rowproduct[$key]['name'] ='<span style="color:'.$val['colors'].'">'.$val['name'].'</span>'; 
		} 
		if(!$val['min_thumb']){
            $rowproduct[$key]['min_thumb'] = 'images/command/no_imgs.png';
		}

		if(!$val['shop_thumb']){
            $rowproduct[$key]['shop_thumb'] = 'images/command/no_imgsbig.png';
		}	
		$rowproduct[$key]['edit_comm'] =  $val['edit_comm']; 
		$rowproduct[$key]['shop_price'] = ($val['shop_price'] == '0.00')?'':$val['shop_price'];
		$rowproduct[$key]['s_discount'] = ($val['s_discount'] == '0.00')?'':$val['s_discount'];
        $rowproduct[$key]['dateadd']  = ($val['dateadd'])?date("Y年m月d日", $val['dateadd']):'';
		$rowproduct[$key]['up_date']  = ($val['up_date'] > 0)?date("y年n月j日",$val['up_date']):'';  
 
        $row = $oPub->getRow('SELECT name,fid FROM '.$pre.'productcat WHERE pcid="'.$val['pcid'].'"'); 
		$rowproduct[$key]['pcname'] = $row['name']; 
		//找前置分类 id 
		if($row['fid'] > 0)
		{ 
			$products = $Aconf['rewrite']  ?'products.html':'products.php';  
			$rowproduct[$key]['pcname_pre'] = pre_node($row['fid'],$pre."productcat",'pcid',$products,true);
		}else
		{
			$rowproduct[$key]['pcname_pre'] = false;
		}

		if($Aconf['rewrite']){
			$rowproduct[$key]['product_url'] = 'product-'.$val['prid'].'.html';
			$rowproduct[$key]['pcomms_url'] = 'procomms-'.$val['prid'].'-0.html';
			$rowproduct[$key]['pcname_url'] = 'products-'.$val['pcid'].'-0-0-0-0.html';
		}else{
			$rowproduct[$key]['product_url'] = 'product.php?id='.$val['prid'];
			$rowproduct[$key]['pcomms_url'] = 'procomms.php?id='.$val['prid'];
			$rowproduct[$key]['pcname_url'] = 'products.php?id='.$val['pcid'];
		} 
    }
	return $rowproduct;
}
/* 得到当前分类名及导航 */
function catname_url($table='articat',$files='articles.php',$filed='acid',$id=0)
{
	global $oPub,$pre; 
	if(!$id)
	{
		return false;
	} 
	$row = $oPub->getRow('SELECT name FROM '.$pre.$table.' WHERE '.$filed.'="'.$id.'"'); 
	$Arow['catname'] = $row['name'];
	$Arow['catname_url'] = $files.'?'.$filed.'='.$id; 
	return $Arow;
}  
 /* 判断是否为搜索引擎蜘蛛
 *
 * @access  public
 * @return  string
 */
function is_spider($record = true) {
    static $spider = NULL;                            
    if ($spider !== NULL)
	{
        return $spider;
    }       
    if (empty($_SERVER['HTTP_USER_AGENT']))
	{
        $spider = '';                       
        return '';
    }          
    $searchengine_bot = array(
        'googlebot',
        'mediapartners-google',
        'baiduspider+',
        'msnbot',
        'yodaobot',
        'yahoo! slurp;',
        'yahoo! slurp china;',
        'iaskspider',
        'sogou web spider',
        'sogou push spider'
    );

    $searchengine_name = array(
        'GOOGLE',
        'GOOGLE ADSENSE',
        'BAIDU',
        'MSN',
        'YODAO',
        'YAHOO',
        'Yahoo China',
        'IASK',
        'SOGOU',
        'SOGOU'
    ); 

    $spider = strtolower($_SERVER['HTTP_USER_AGENT']);
    foreach ($searchengine_bot AS $key => $value)
    {
        if (strpos($spider, $value) !== false)
        {
            $spider = $searchengine_name[$key]; 
            if ($record === true)
            {
                //数据库记录,暂无
            }
            return $spider;
        }
    } 
    $spider = ''; 
    return '';
}
 

/**
 * 更新用户SESSION,COOKIE及登录时间、登录次数。
 *
 * @access  public
 * @return  void
 */
function update_user_info()
{
	global $oPub,$pre,$_SESSION;
    if (!$_SESSION['user_id'])
    {
        return false;
    }else
	{
		$oPub->query('UPDATE ' .$pre.'users SET visit_count = visit_count + 1, last_ip = "' .real_ip(). '", last_time  = "'.date("Y-m-d H:s:i",gmtime()).'", last_login = "'.gmtime().'" WHERE id ="'. $_SESSION['user_id'].'"'); 
	} 
} 

function set_admin_session($user_id, $user_name, $action_list,$articlecat_list,$praid,$domain_url,$domain_id,$domain_user_id)
{
	global $_SESSION; 
	if($user_id  < 1)
	{
		return false;
	}
	$_SESSION['auser_id']			= $user_id;  
	$_SESSION['auser_name']			= $user_name; 
	$_SESSION['aaction_list']		= $action_list;
	$_SESSION['aarticlecat_list']   = $articlecat_list;
	$_SESSION['apraid']				= $praid;  
	$_SESSION['domain_url']			= $domain_url; 
	$_SESSION['domain_id']			= $domain_id; 
	$_SESSION['domain_user_id']		= $domain_user_id;   
}
/**
 * 取得当前的域名
 *
 * @access  public
 *
 * @return  string      当前的域名
 */
function get_domain()
{
	/* 协议 */
	$protocol = http();

	/* 域名或IP地址 */
	if (isset($_SERVER['HTTP_X_FORWARDED_HOST']))
	{
		$host = $_SERVER['HTTP_X_FORWARDED_HOST'];
	}
	elseif (isset($_SERVER['HTTP_HOST']))
	{
		$host = $_SERVER['HTTP_HOST'];
	}
	else
	{
		/* 端口 */
		if (isset($_SERVER['SERVER_PORT']))
		{
			$port = ':' . $_SERVER['SERVER_PORT'];

			if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol))
			{
				$port = '';
			}
		}
		else
		{
			$port = '';
		}

		if (isset($_SERVER['SERVER_NAME']))
		{
			$host = $_SERVER['SERVER_NAME'] . $port;
		}
		elseif (isset($_SERVER['SERVER_ADDR']))
		{
			$host = $_SERVER['SERVER_ADDR'] . $port;
		}
	}

	return $protocol . $host;
}

/**
 * 获得 当前环境的 URL 地址
 *
 * @access  public
 *
 * @return  void
 */
function url()
{
	$curr = strpos($PHP_SELF, 'admin/') !== false ?
			preg_replace('/(.*)(admin)(\/?)(.)*/i', '\1', dirname($PHP_SELF)) :
			dirname($PHP_SELF);

	$root = str_replace('\\', '/', $curr);

	if (substr($root, -1) != '/')
	{
		$root .= '/';
	}

	return get_domain() . $root;
}

/**
 * 获得当前环境的 HTTP 协议方式
 *
 * @access  public
 *
 * @return  void
 */
function http()
{
	return (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';
}

function daddslashes($string, $force = 0) {
 
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
	}
	return $string;
}
function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', 
		str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}

function StopAttack($StrFiltKey,$StrFiltValue,$ArrFiltReq){ 
	if(is_array($StrFiltValue))
	{
		$StrFiltValue=implode($StrFiltValue);
	}  
	if (preg_match("/".$ArrFiltReq."/is",$StrFiltValue)==1){   
		   $str = "<br><br>QQ:16953292 :".$_SERVER["REMOTE_ADDR"]."<br>time: ".strftime("%Y-%m-%d %H:%M:%S")."<br>php_self:".$_SERVER["PHP_SELF"]."<br>type: ".$_SERVER["REQUEST_METHOD"]."<br>key: ".$StrFiltKey."<br>vaue: ".$StrFiltValue ;
			exit();
	}      
} 

/**
 * 保存某图库及相册图片
 * @param   int     $workid
 * @param   array   $image_files
 * @param   array   $image_descs
 * @return  void
 */ 
function handle_gallery_image($arid, $image_files, $image_descs,$type='')
{
	global $image,$oPub,$pre,$_SESSION,$un_domain_id,$Aconf; 
	$imgType = array(1 => 'image/gif', 2 => 'image/jpeg', 3 => 'image/png',4 => 'image/pjpeg');
	while( @list( $key, $img_desc ) = @each( $image_descs) ) {
        /* 是否成功上传 */
        $flag = false;
        if (isset($image_files['error'])) {
            if ($image_files['error'][$key] == 0) {
                $flag = true;
            }
        } else {
            if ($image_files['tmp_name'][$key] != 'none') {
                $flag = true;
            }
        }

        if ($flag ){
            // 生成缩略图
			if($image->check_img_type($image_files['type'][$key])) { 
               $thumb_url = $image->make_thumb($image_files['tmp_name'][$key], $Aconf['min_thumb_w'],  $Aconf['min_thumb_h']);
			}
            $thumb_url = is_string($thumb_url) ? $thumb_url : '';

            $upload = array(
                'name' => $image_files['name'][$key],
                'type' => $image_files['type'][$key],
                'tmp_name' => $image_files['tmp_name'][$key],
                'size' => $image_files['size'][$key],
            );
            if (isset($image_files['error']))
            {
                $upload['error'] = $image_files['error'][$key];
            }

            $img_original = $image->upload_image($upload);


			$target_file = $filename = ROOT_PATH.$img_original;
			$watermark = ROOT_PATH.'data/weblogo/'.$Aconf['watermark']; 
			if(file_exists($watermark)){ 
				$image->add_watermark($filename, $target_file, $watermark,5,80 ); 
			}
            $img_url = $img_original;

 
			if(empty($img_desc)){
				$A = explode(".",$image_files['name'][$key]);
				$img_desc = $A[0];
			}
            $sql = "INSERT INTO " . $pre."arti_file (arid,type, filename,thumb_url,descs,domain_id) " .
                    "VALUES ('$arid','$type', '$img_url', '$thumb_url','$img_desc',".$Aconf['domain_id'].")";
            $oPub->query($sql);
        }
    }
}

function assign_template($Aconf = array())
{
    global $smarty; 
	while( @list( $k, $v ) = @each( $Aconf) ) { 
 		$smarty->assign($k,   $v );
	}
}
/*
 * 导航
 * @ifbotton 0/1  1为底部导航
 * @top      0/n  0主导航 对应主导航id 的级联导航
 * @access  private
 * @return  array
 */
function get_nav($ifbotton=0) {
	global $oPub,$pre,$Aconf;  
	$Anav = $oPub->select('SELECT id,name,opennew,url,url_logo  FROM '.$pre.'nav WHERE top=0 and ifshow=0 AND ifbotton="'.$ifbotton.'" and domain_id = "'.$Aconf['domain_id'].'" ORDER BY vieworder,id asc '); 
	while(@list( $k, $v ) = @each( $Anav )) {

		if(stristr($v['url'], '://') === FALSE) {
			$Anav[$k]['url'] = $v['url'];
		}

		if(empty($v['url_logo'])){
			$v['url_logo'] = 'no_nav.jpg';	 
		}
 
		$nowFile= substr($Aconf['nowFile'],0,-5);
		$ifNowNave = strpos($v['url'],$nowFile); 

		$Anav[$k]['ifnownave'] =($ifNowNave !== false )?true:false; 

		$Anav[$k]['url_logo'] = 'data/weblogo/'.$v['url_logo'];
		//子导航
		$Anav_sub = $oPub->select('SELECT id,name,opennew,url,url_logo  FROM '.$pre.'nav WHERE top="'.$v['id'].'" and ifshow=0 AND ifbotton="'.$ifbotton.'" and domain_id = "'.$Aconf['domain_id'].'" ORDER BY vieworder,id asc '); 
		while(@list( $ksub, $vsub ) = @each( $Anav_sub )) {
			if(stristr($vsub['url'], '://') === FALSE) {
				$Anav_sub[$ksub]['url'] = $vsub['url'];
			}

			if(empty($vsub['url_logo'])){
				$vsub['url_logo'] = 'no_nav.jpg';	 
			}
	 
			$nowFile= substr($Aconf['nowFile'],0,-5);
			$ifNowNave = strpos($vsub['url'],$nowFile); 

			$Anav_sub[$ksub]['ifnownave'] =($ifNowNave !== false )?true:false; 

			$Anav_sub[$ksub]['url_logo'] = 'data/weblogo/'.$vsub['url_logo'];
		}
		$Anav[$k]['Anav_sub'] = $Anav_sub;


	}
	return $Anav;
}

/* 
关键词过滤 
$message  传入需要过滤的字符 
return     $message
*/
function filter($message)
{
    global $oPub,$pre; 
    $sql = "SELECT ips,words,keysre FROM ".$pre."filter WHERE states=1 ORDER BY fid ASC LIMIT 1";
    $Anorm = $oPub->getRow($sql);  
	if($Anorm) { 

		if($Anorm['ips']) { 
			$ip = real_ip();  
            $pos = strpos($Anorm['ips'], real_ip());
            if ($pos !== false) { 
				$message = '';//禁止发帖的IP'; 
			}
		} 

		if($Anorm['keysre']) {
			$Atmpx = explode("\n",trim($Anorm['keysre']));
			foreach ( $Atmpx AS $key=>$val ) {
				$Atmp = explode("=",$val);
				$works = $Atmp[0]; 
				$num  = $Atmp[1]; 

				$tmpnum = substr_count($message,$works);
				if($tmpnum >= $num){
					$message = '';//超过关键词设定量; 
					return $message;
				}
				unset($Atmp); 
			}
		}

		if($Anorm['words'] && !empty($message)) { 
			$Afind = array();$Areplace = array();
			$Awords = explode("\n",trim($Anorm['words'])); 
            foreach ($Awords AS $val) {
				$Aval = explode("=",$val);
				if(!empty($Aval[0])){
					$Aval[1] = 	!empty($Aval[1])?$Aval[1]:'';
					array_push($Afind,'/'.trim($Aval[0]).'/i');
					array_push($Areplace,trim($Aval[1]));
				}
			} 

			if(count($Afind) >0){  
				$message =  @preg_replace($Afind, $Areplace, $message); 
			} 
		}

	} 
	return $message; 
}


/*
 *调查选项提取
*/
function vote($vtid) {
	global  $oPub,$pre,$Aconf; 
	$vtid = $vtid + 0;
	if($vtid < 1){
		break;
	} 
	$voteurl = $Aconf['rewrite']?'vote-'.$vtid.'.html':'vote.php?id='.$vtid; 
	$rowgroup = $oPub->select("SELECT vgid,vg_name,vg_desc,is_show,thumb_url_w,thumb_url_h,thumb_s_url_w,thumb_s_url_h FROM ".$pre."vote_group WHERE  vtid = '".$vtid."' ORDER BY  orders  desc,vgid ASC ");
 	if($rowgroup){ 
		foreach ($rowgroup AS $kg=>$vg) { 
			//投票数量排序
			$row = $oPub->select("SELECT viid,vi_nums FROM ".$pre."vote_item WHERE  vtid = '".$vtid."' AND  states = 0  AND is_show = 1 AND vgid = '".$vg['vgid']."' AND domain_id='".$Aconf['domain_id']. "' ORDER BY vi_nums desc"); 
			$n = $old_vi_nums = 0;
			$Aorder = array();
			while( @list( $k, $v) = @each($row) )
			{  
				$ifempty = false;
				if($n < 1 && $v['vi_nums'] > 0)
				{
					$n = 1;
				}elseif($old_vi_nums > $v['vi_nums'] && $v['vi_nums']>0)
				{
					$n ++ ;
				}elseif($old_vi_nums == $v['vi_nums']  && $v['vi_nums']>0)
				{
					$n = $n;
				}
				else
				{
					$ifempty = true; 
				}

				$old_vi_nums = $v['vi_nums']; 
				$Aorder[$v['viid']] = $ifempty?0:$n;
			} 

			//自然排序 
			$sql = "SELECT viid,vi_name,vi_type,vi_nums,thumb_url  FROM ".$pre."vote_item WHERE  vtid = '".$vtid."' AND  states = 0  AND is_show = 1 AND vgid = '".$vg['vgid']."' AND domain_id='".$Aconf['domain_id']. "' ORDER BY orders asc,vi_type asc,viid asc"; 
			$rowvote_item = $oPub->select($sql);  
			if($rowvote_item) { 
				foreach ($rowvote_item AS $key=>$val) { 
					$rowvote_item[$key]['toporder'] = $Aorder[$val['viid']]; //组内投票排序序号;
					$vi_name = $val['vi_name'];
					$rowvote_item[$key]['thumb_url'] = (!empty($val['thumb_url']))?'data/vote/'.$val['thumb_url']:'';
					if ($val['vi_type'] < 1) {
						//传统方式投票
						$input = '<INPUT TYPE="radio" NAME="vote_vgid_radio['.$vg['vgid'].']" value="'.$val['viid'].'">';
						//AJAX投票方式类型组合
						$rowvote_item[$key]['name_vi_type']       = 'vote_vgid_radio';
						$rowvote_item[$key]['name_vi_type_key']   = $vg['vgid'];
						$rowvote_item[$key]['name_vi_type_value'] = $val['viid'];

					} elseif($val['vi_type'] == 1) {
						$input = '<INPUT TYPE="checkbox" NAME="vote_item['.$val['viid'].']" value="'.$val['viid'].'">';

						$rowvote_item[$key]['name_vi_type']       = 'vote_item';
						$rowvote_item[$key]['name_vi_type_key']   = $val['viid'];
						$rowvote_item[$key]['name_vi_type_value'] = $val['viid'];

					} elseif($val['vi_type'] == 2){ 
						$selectname = '';
						$Avi_name = explode(",",$val['vi_name']); 
						if(count($Avi_name) > 0){
							$inputselect =  '<SELECT NAME="vote_item['.$val['viid'].']">';
								$n = 0;
								foreach ($Avi_name AS $kt=>$vt) { 
									if($n < 1){
										$selectname = $vt;
									}else{
										$inputselect .= '<OPTION VALUE="'.$vt.'" >'.$vt.'</OPITON>'; 
									}
									$n ++ ;
								}
							$inputselect .= '</SELECT>';
							$input = $inputselect;

							$rowvote_item[$key]['name_vi_type']       = 'vote_item';
							$rowvote_item[$key]['name_vi_type_key']   = $val['viid'];
							$rowvote_item[$key]['name_vi_type_value'] = '';

						}
						$vi_name = $selectname;

					} elseif($val['vi_type'] == 3) { 
						$input = '<INPUT TYPE="text" NAME="vote_item['.$val['viid'].']" value="" style="12">';

						$rowvote_item[$key]['name_vi_type']       = 'vote_item';
						$rowvote_item[$key]['name_vi_type_key']   = $val['viid'];
						$rowvote_item[$key]['name_vi_type_value'] = '';

					} elseif($val['vi_type'] == 4) {
						$input = '<TEXTAREA NAME="vote_item['.$val['viid'].']" style="width:500px;height:200px"></TEXTAREA>';

						$rowvote_item[$key]['name_vi_type']       = 'vote_item';
						$rowvote_item[$key]['name_vi_type_key']   = $val['viid'];
						$rowvote_item[$key]['name_vi_type_value'] = '';

					}
					
					$rowvote_item[$key]['input'] = $input;
					$rowvote_item[$key]['vi_name'] = $vi_name; 
				} 
			}  
			$rowgroup[$kg]['vote_item'] = $rowvote_item; 
		}//foreach ($rowgroup AS $kg=>$vg) 
	}
	//$Ahome['vote'][for_vote_group]= $rowgroup;
	return $rowgroup;
}
/*
 *调查结果
*/
function vote_show($vtid) {
	global $_SESSION,$un_domain_id,$oPub,$pre,$Aconf; 
	$vtid = $vtid + 0;
	if($vtid < 1){
		break;
	} 

	$rowgroup = $oPub->select("SELECT vgid,vg_name,vg_desc,is_show,vg_nums,thumb_url_w,thumb_url_h,thumb_s_url_w,thumb_s_url_h FROM ".$pre."vote_group  WHERE  vtid = '".$vtid."' ORDER BY  orders  desc,vgid ASC "); 
	if($rowgroup)
	{ 
		foreach ($rowgroup AS $kg=>$vg) 
		{ 
			//投票数量排序
			$row = $oPub->select("SELECT viid,vi_nums FROM ".$pre."vote_item WHERE  vtid = '".$vtid."' AND  states = 0  AND is_show = 1 AND vgid = '".$vg['vgid']."' AND domain_id='".$Aconf['domain_id']. "' ORDER BY vi_nums desc"); 
			$n = $old_vi_nums = 0;
			$Aorder = array();
			while( @list( $k, $v) = @each($row) )
			{  
				$ifempty = false;
				if($n < 1 && $v['vi_nums'] > 0)
				{
					$n = 1;
				}elseif($old_vi_nums > $v['vi_nums'] && $v['vi_nums']>0) {
					$n ++ ;
				}elseif($old_vi_nums == $v['vi_nums']  && $v['vi_nums']>0) {
					$n = $n;
				} else {
					$ifempty = true; 
				}

				$old_vi_nums = $v['vi_nums']; 
				$Aorder[$v['viid']] = $ifempty?0:$n;
			} 
			//自然排序 
			$rowvote_item = $oPub->select("SELECT viid,vi_name,vi_type,vi_nums,thumb_s_url  FROM ".$pre."vote_item WHERE  vtid = '".$vtid."' AND  states = 0  AND is_show = 1 AND vgid = '".$vg['vgid']."' AND domain_id='".$Aconf['domain_id']. "' ORDER BY orders asc,vi_type asc,viid asc");  
			if($rowvote_item) {
				$input = '';
				$j = 0;
				foreach ($rowvote_item AS $key=>$val) { 
					$rowvote_item[$key]['toporder'] = $Aorder[$val['viid']]; //组内投票排序序号;
					$inputstr = '';
					if ($val['vi_type'] < 1)
					{ 
						//单选
						$inputstr = $val['vi_name'];
						$rowvote_item[$key]['vi_type_list'] = false;
					} elseif($val['vi_type'] == 1)
					{
						//复选
						$inputstr = $val['vi_name'];
						$rowvote_item[$key]['vi_type_list'] = false;
					} elseif($val['vi_type'] == 2)
					{ 
						//列表
						$Avi_name = explode(",",$val['vi_name']); 
						$Avi_type_list = false;//列表选择 选项标题对应值的投票数量
						if(count($Avi_name) > 0)
						{
							$inputselect =  '';
							$n = 0;
							foreach ($Avi_name AS $kt=>$vt)
							{ 
								if($n < 1){ 
									$selectname = $vt; 
									$rowvote_item[$key]['vi_name'] = $vt;//列表选择 选项标题
								}else{
									$count = $oPub->getOne("SELECT  count(*) as count FROM  ".$pre."vote_poll where viid='".$val['viid']."' and descs LIKE '$vt'"); 
									$count = $count > 0 ?$count :0;
									//列表选择 选项标题对应值的投票数量
									$inputselect .= $vt.'('.$count.')<br/>';
									$Avi_type_list[$n]['vi_name'] = $vt.'('.$count.')';  
								}
								$n ++ ;
							}
							$inputstr =$selectname.'<br/>'.$inputselect;
							$rowvote_item[$key]['vi_type_list'] = $Avi_type_list; 
						}
					} elseif($val['vi_type'] == 3) { 
						$inputstr = $val['vi_name'].'<br/>';
						$row = $oPub->select("SELECT  descs FROM  ".$pre."vote_poll where viid='".$val['viid']."'"); 
						$tmp = '';
						while( @list( $k, $v) = @each($row) ) { 
							$tmp .= $v['descs'].'<br/>';
						}
						$inputstr = $inputstr.$tmp; 
						$rowvote_item[$key]['vi_type_list'] = $row; 
					} elseif($val['vi_type'] == 4) { 
						$inputstr = $val['vi_name'].'<br/>';
						$row = $oPub->select("SELECT  descs FROM  ".$pre."vote_poll where viid='".$val['viid']."'"); 
						$tmp = '';
						while( @list( $k, $v) = @each($row) )
						{ 
							$tmp  .= $v['descs'].'<br/>';
						}
						$inputstr = $inputstr.$tmp; 
						$rowvote_item[$key]['vi_type_list'] = $row; 
					}

					if(!empty($val['thumb_s_url']))
					{
						$rowvote_item[$key]['thumb_s_url'] = 'data/vote/'.$val['thumb_s_url'];
						$rowvote_item[$key]['img']  = '<IMG SRC="data/vote/'.$val['thumb_s_url'].'"  BORDER="0" ALT="'.$val['vi_name'].'" width="'.$vg['thumb_s_url_w'].'" height="'.$vg['thumb_s_url_h'].'" >';
					}else {
						$rowvote_item[$key]['thumb_s_url'] = '';
						$rowvote_item[$key]['img']  =  '';
					}
					//组内选项顺序号 
					$j ++ ;
					$rowvote_item[$key]['orders'] = $j;
					//显示柱状图高度
					$inputstr = $j.'、'.$img.$inputstr;
					//$inputstr = $img.$inputstr;
					//显示柱状图高度
					$mypre = number_format($val['vi_nums'] / $vg['vg_nums'] *100,2); 
					$myprewidth =  $mypre * 2 ;
					$rowvote_item[$key]['myprewidth'] = $myprewidth; 
					//组内百分比
					$rowvote_item[$key]['mypre']      = $mypre;//显示柱状图高度
					//$val[vi_nums] 组内选项票数
					
					$rowvote_item[$key]['bgcolor'] =($j % 2)? '#E4EBF8': '#EFEFEF';
					$rowvote_item[$key]['input']  = '<IMG SRC="images/command/vote.gif" WIDTH="'.$myprewidth.'" HEIGHT="10" BORDER="0" ALT="'.$mypre.'">';
				}
				  
			}  
			//$rowgroup[$kg]['vote_item'] = $rowvote_item['input']; 
			$rowgroup[$kg]['vote_item'] = $rowvote_item; 
		}//foreach ($rowgroup AS $kg=>$vg) 
	}
	//$Ahome['vote'][for_vote_group]= $rowgroup;
	return $rowgroup;
}

/*
 *过滤模版中的PHPD
*/
function delete_php_code($content) {
	if(!empty($content)) {
		$pattern='/\<\?(.|\r\n|\s)*\?\>/U'; 
		return preg_replace($pattern,'',$content);
	}
}
/*
 *过滤模版中的mkmd5
*/
function mkmd5($pass) {
	$pass = trim($pass);
	if(!empty($pass)) {
		$pass = md5("abcdef").md5($pass);
		return md5($pass);
	}else
	{
		return false;
	}
}
//smartr 模版变量  
function insert_exe($op)
{  
	if($op['char'] == 'article_comms'){
		//文章评论数量
		global $oPub,$pre;  
		$comms = $oPub->getOne("SELECT comms FROM ".$pre."artitxt WHERE arid = '".$op['arid']."' LIMIT 1"); 
		return $comms;
	}else{
		//广告
		global $Aads;
		return $Aads[$op['ads']]; 
	}
}

function  log_result($word) {
	@$fp = fopen("log.txt","a");	
	@flock($fp, LOCK_EX) ;
	@fwrite($fp,$word."：power by times：".strftime("%Y%m%d%H%I%S",time())."\r\n");
	@flock($fp, LOCK_UN); 
	@fclose($fp);
}
?>
