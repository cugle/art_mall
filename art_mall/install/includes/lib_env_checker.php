<?php
if (!defined('IN_OUN')) { 
    die('Hacking attempt');
}

/**
 * 检查目录的读写权限
 *
 * @access  public
 * @param   array     $checking_dirs     目录列表
 * @return  array     检查后的消息数组，
 *    成功格式形如array('result' => 'OK', 'detail' => array(array($dir, $_LANG['can_write']), array(), ...))
 *    失败格式形如array('result' => 'ERROR', 'd etail' => array(array($dir, $_LANG['cannt_write']), array(), ...))
 */
 
function check_dirs_priv($checking_dirs)
{
    include_once(ROOT_PATH . 'includes/funcomm.php'); 
    global $_LANG;
    $msgs = array('result' => 'OK', 'detail' => array());

    foreach ($checking_dirs AS $dir)
    {
        if (!file_exists(ROOT_PATH . $dir))
        {
            $msgs['result'] = 'ERROR';
            $msgs['detail'][] = array($dir, $_LANG['not_exists']);
            continue;
        }

        if (file_mode_info(ROOT_PATH . $dir) < 2)
        {
            $msgs['result'] = 'ERROR';
            $msgs['detail'][] = array($dir, $_LANG['cannt_write']);
        }
        else
        {
            $msgs['detail'][] = array($dir, $_LANG['can_write']);
        }
    }

    return $msgs;
}

/**
 * 检查模板的读写权限
 *
 * @access  public
 * @param   array      $templates_root        模板文件类型所在的根路径数组，形如：array('dwt'=>'', 'lbi'=>'')
 * @return  array      检查后的消息数组，全部可写为空数组，否则是一个以不可写的文件路径组成的数组
 */
function check_templates_priv($templates_root)
{
    global $_LANG;

    $msgs = array();
    $filename = '';
    $filepath = '';

    foreach ($templates_root as $tpl_type => $tpl_root)
    {
        if (!file_exists($tpl_root))
        {
            $msgs[] = str_replace(ROOT_PATH, '', $tpl_root . ' ' . $_LANG['not_exists']);
            continue;
        }

        $tpl_handle = @opendir($tpl_root);
        while (($filename = @readdir($tpl_handle)) !== false)
        {
            $filepath = $tpl_root . $filename;
            if (is_file($filepath)
                    && strrpos($filename, '.' . $tpl_type) !== false
                    && file_mode_info($filepath) < 7)
            {
                $msgs[] = str_replace(ROOT_PATH, '', $filepath . ' ' . $_LANG['cannt_write']);
            }
        }
        @closedir($tpl_handle);
    }

    return $msgs;
}

 
?>