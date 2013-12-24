<?php

/**
 * 获得数据库列表
 *
 * @access  public
 * @param   string      $db_host        主机
 * @param   string      $db_port        端口号
 * @param   string      $db_user        用户名
 * @param   string      $db_pass        密码
 * @return  mixed       成功返回数据库列表组成的数组，失败返回false
 */
function get_db_list($db_host, $db_port, $db_user, $db_pass)
{
    global $err, $_LANG;
    $databases = array();
    $filter_dbs = array('information_schema', 'mysql');
    $db_host = construct_db_host($db_host, $db_port);
    $conn = @mysql_connect($db_host, $db_user, $db_pass);

    if ($conn === false)
    {
        $err->add($_LANG['connect_failed']);
        return false;
    }
    keep_right_conn($conn);

    $result = mysql_query('SHOW DATABASES', $conn);
    if ($result !== false)
    {
        while (($row = mysql_fetch_assoc($result)) !== false)
        {
            if (in_array($row['Database'], $filter_dbs))
            {
                continue;
            }
            $databases[] = $row['Database'];
        }
    }
    else
    {
        $err->add($_LANG['query_failed']);
        return false;
    }
    @mysql_close($conn);

    return $databases;
}


/**
 * 创建指定名字的数据库
 *
 * @access  public
 * @param   string      $db_host        主机
 * @param   string      $db_port        端口号
 * @param   string      $db_user        用户名
 * @param   string      $db_pass        密码
 * @param   string      $db_name        数据库名
 * @return  boolean     成功返回true，失败返回false
 */
function create_database($db_host, $db_port, $db_user, $db_pass, $db_name)
{
    $db_host = construct_db_host($db_host, $db_port);
    $conn = @mysql_connect($db_host, $db_user, $db_pass);

    if ($conn === false)
    { 
        return 3; //数据库连接失败
    }

    $mysql_version = mysql_get_server_info($conn);
    keep_right_conn($conn, $mysql_version);
    if (mysql_select_db($db_name, $conn) === false)
    {
        $sql = $mysql_version >= '4.1' ? "CREATE DATABASE $db_name DEFAULT CHARACTER SET utf8" : "CREATE DATABASE $db_name"; 
        if (mysql_query($sql, $conn) === false)
        { 
            return 4; //数据库创建失败
        }
    }
    @mysql_close($conn);

    return 1;
}

/**
 * 保证进行正确的数据库连接（如字符集设置）
 *
 * @access  public
 * @param   string      $conn                      数据库连接
 * @param   string      $mysql_version        mysql版本号
 * @return  void
 */
function keep_right_conn($conn, $mysql_version='')
{
    if ($mysql_version === '')
    {
        $mysql_version = mysql_get_server_info($conn);
    }

    if ($mysql_version >= '4.1')
    {
        mysql_query('SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary', $conn);

        if ($mysql_version > '5.0.1')
        {
            mysql_query("SET sql_mode=''", $conn);
        }
    }
}

/**
 * 创建配置文件
 *
 * @access  public
 * @param   string      $db_host        主机
 * @param   string      $db_port        端口号
 * @param   string      $db_user        用户名
 * @param   string      $db_pass        密码
 * @param   string      $db_name        数据库名
 * @param   string      $prefix         数据表前缀
 * @param   string      $timezone       时区
 * @return  boolean     成功返回true，失败返回false
 */
function create_config_file($db_host, $db_port, $db_user, $db_pass, $db_name, $prefix,$mail_url)
{
    global $err, $_LANG;
    $db_host = construct_db_host($db_host, $db_port);

    $content = '<?' ."php\n";
	$content .= "if (__FILE__ == '')\n";
    $content .= "{\n";
    $content .= "die('Fatal error code: 0');\n";
    $content .= "}\n"; 
    $content .= "// database host\n";
    $content .= "\$dbhost   = \"$db_host\";\n\n";
    $content .= "// database name\n";
    $content .= "\$dbname  = \"$db_name\";\n\n";
    $content .= "// database username\n";
    $content .= "\$dbuser   = \"$db_user\";\n\n";
	$content .= "\$charset = \"utf-8\";\n\n";
    $content .= "// database password\n";
    $content .= "\$dbpw   = \"$db_pass\";\n\n";
    $content .= "// table prefix\n";
    $content .= "\$pre    = \"$prefix\";\n\n";
	$content .= "\$Aconf['mail_url']  = \"$mail_url\";\n\n"; 
	$content .= "\$Aconf['allow_multi']  = true;\n\n";  
	$content .= "\$Aconf['allow_home']  = 1;\n\n"; 
	$content .= "\$Aconf['manage_dir']  = \"admin/\";\n\n";  
    $content .= '?>';
 

    @chmod(ROOT_PATH . 'data/config.inc.php', 0777);

    $fp = @fopen(ROOT_PATH . 'data/config.inc.php', 'wb+');
    if (!$fp)
    {
        return false;
    }
    if (!@fwrite($fp, trim($content)))
    {
        return false;
    }
    @fclose($fp);
    @chmod(ROOT_PATH . 'data/config.inc.php', 0744);
	//$fp = @fopen(ROOT_PATH . 'data/install.lock', 'a');
	//@fclose($fp);
    return true;
}

/**
 * 把host、port重组成指定的串
 *
 * @access  public
 * @param   string      $db_host        主机
 * @param   string      $db_port        端口号
 * @return  string      host、port重组后的串，形如host:port
 */
function construct_db_host($db_host, $db_port)
{
    return $db_host . ':' . $db_port;
}

/**
 * 安装数据
 *
 * @access  public
 * @param   array         $sql_files        SQL文件路径组成的数组
 * @return  boolean       成功返回true，失败返回false
 */
function install_data($sql_files)
{ 
	 global $mail_url;
     include_once( ROOT_PATH."data/config.inc.php");
     include_once( ROOT_PATH."includes/mydb.php");
     include_once( "./cls_sql_executor.php");

     $db  = new mydb($dbhost,$dbuser,$dbpw,$dbname);
     $se  = new sql_executor($db, 'utf8', 'oun_', $pre); 
 
    $result = $se->run_all($sql_files);
     if ($result === false)
     {
         return 3;  //数据导入失败
     }
	/* 修改主站链接地址 */
	 $main_domin = $main_http;
     $db->query("UPDATE oun_sysconfig SET  main_domin='$mail_url',user_id=1,user_name='admin',states=2   where scid=1"); 
	 $db->query("UPDATE oun_sysconfigfast SET  main_domin='$mail_url',user_name='admin',states=2  where scid=1");   

    /* 初始化友情链接   */
    $sql = "INSERT INTO oun_links ".
                "(lk_name , lk_logo, lk_desc, site_url,sort_order,domain_id )".
            "VALUES ".
                "('行业之星', 'osunit_logo.gif','免费开源多用户建站系统','http://www.osunit.com/', '0','1')";
     $db->query($sql);  
    return true;
}
function update_data($sql_files)
{
	 global $mail_url;
     include_once( ROOT_PATH."data/config.inc.php");
     include_once( ROOT_PATH."includes/mydb.php");
     include_once( "./cls_sql_executor.php");

     $db  = new mydb($dbhost,$dbuser,$dbpw,$dbname);
     $se  = new sql_executor($db, 'utf8', 'oun_', $pre);

    $result = $se->run_all($sql_files);
     if ($result === false)
     {
         return 3;  //数据导入失败
     }
    return true;
}
/**
 * 获得GD的版本号
 *
 * @access  public
 * @return  string     返回版本号，可能的值为0，1，2
 */
function get_gd_version()
{
    include_once(ROOT_PATH . 'includes/cls_image.php');

    return cls_image::gd_version();
}
/**
 * 获得系统的信息
 *
 * @access  public
 * @return  array     系统各项信息组成的数组
 */
function get_system_info()
{
    global $_LANG;

    $system_info = array();

    /* 检查系统基本参数 */
    $system_info[] = array($_LANG['php_os'], PHP_OS);
    $system_info[] = array($_LANG['php_ver'], PHP_VERSION);

    /* 检查MYSQL支持情况 */
    $mysql_enabled = function_exists('mysql_connect') ? $_LANG['support'] : $_LANG['not_support'];
    $system_info[] = array($_LANG['does_support_mysql'], $mysql_enabled);
	
    /* 检查图片处理函数库 */
    $gd_ver = get_gd_version();
    $gd_ver = empty($gd_ver) ? $_LANG['not_support'] : $gd_ver;
    if ($gd_ver > 0)
    {
        if (PHP_VERSION >= '4.3' && function_exists('gd_info'))
        {
            $gd_info = gd_info();
            $jpeg_enabled = ($gd_info['JPG Support']        === true) ? $_LANG['support'] : $_LANG['not_support'];
            $gif_enabled  = ($gd_info['GIF Create Support'] === true) ? $_LANG['support'] : $_LANG['not_support'];
            $png_enabled  = ($gd_info['PNG Support']        === true) ? $_LANG['support'] : $_LANG['not_support'];
        }
        else
        {
            if (function_exists('imagetypes'))
            {
                $jpeg_enabled = ((imagetypes() & IMG_JPG) > 0) ? $_LANG['support'] : $_LANG['not_support'];
                $gif_enabled  = ((imagetypes() & IMG_GIF) > 0) ? $_LANG['support'] : $_LANG['not_support'];
                $png_enabled  = ((imagetypes() & IMG_PNG) > 0) ? $_LANG['support'] : $_LANG['not_support'];
            }
            else
            {
                $jpeg_enabled = $_LANG['not_support'];
                $gif_enabled  = $_LANG['not_support'];
                $png_enabled  = $_LANG['not_support'];
            }
        }
    }
    else
    {
        $jpeg_enabled = $_LANG['not_support'];
        $gif_enabled  = $_LANG['not_support'];
        $png_enabled  = $_LANG['not_support'];
    }
    $system_info[] = array($_LANG['gd_version'], $gd_ver);
    $system_info[] = array($_LANG['jpeg'], $jpeg_enabled);
    $system_info[] = array($_LANG['gif'],  $gif_enabled);
    $system_info[] = array($_LANG['png'],  $png_enabled); 
 

    /* 服务器是否安全模式开启 */
    $safe_mode = ini_get('safe_mode') == '1' ? $_LANG['safe_mode_on'] : $_LANG['safe_mode_off'];
    $system_info[] = array($_LANG['safe_mode'], $safe_mode);

    return $system_info;
}

?>