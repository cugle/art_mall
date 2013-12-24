<?php

/**
 * 会员数据处理类
 */

if (!defined('IN_OUN'))
{
    die('Hacking attempt');
}

class osunit
{ 
    var $db     = NULL;
    var $table  = ''; 

    function __construct($db, $table)
    {
        $this->osunit($db, $table);
    }

    /**
     * osunit初始化
     *
     * @access  public
     *
     * @return void
     */
    function osunit($db, $table)
    {
        $this->db	 = $db;
		$this->table = $table;
    }

    /**
     *  用户登录函数
     *
     * @access  public
     * @param   string  $username
     * @param   string  $password
     *
     * @return void
     */

    function login($username, $password)
    {
		$username = addslashes($username);
		$password = mkmd5($password);
		$user_exist = $this->db->getOne('SELECT id FROM ' . $this->table . ' WHERE user_name="'.$username.'" AND password="'.$password.'" limit 1');
		if ($user_exist > 0)
		{  
			$this->set_session($user_exist);
			$this->set_cookie($user_exist);
			return true;
		} else
		{
			$this->error = '用户名或者密码错误';
			return false;
		}
    }
    /**
     * 用户退出
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function logout()
    {
        $this->set_cookie();  //清除cookie
        $this->set_session(); //清除session
        return true;
    }

    /**
     * 检测Email是否合法
     *
     * @access  public
     * @param   string  $email   邮箱
     *
     * @return  blob
     */
    function check_email($email)
    {
        if (!empty($email))
        {
          /* 检查email是否重复 */
            $sql = 'SELECT id FROM ' . $this->table. ' WHERE email = "'.$email.'"';
            if ($this->db->getOne($sql, true) > 0)
            {
                $this->error = '邮箱已存在！';
                return true;
            }
            return false;
        }
        return true;
    }

    /* 编辑用户信息 */
    function edit_user($cfg, $forget_pwd = '0')
    { 
        return true;
    }

    /**
     *  获取指定用户的信息
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function get_profile_by_name($username)
    {
        //$username = addslashes($username);

        $sql = 'SELECT * FROM '. $this->table. ' WHERE user_name="'.$username.'"';
        $row = $this->db->getRow($sql);
        return $row;
    }

    /**
     *  检查cookie是正确，返回用户名
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function check_cookie()
    {
 		$id = '';
		if ( isset($_COOKIE['OUN'])  && isset($_COOKIE['OUN']['user_id'])  && isset($_COOKIE['OUN']['password']))
		{  
			$id = $this->db->getOne('SELECT id FROM '. $this->table. ' WHERE id="'.$_COOKIE['OUN']['user_id'].'" and password="'.$_COOKIE['OUN']['password'].'" LIMIT 1');  
		} 
		return $id; 
    }

    /**
     *  根据登录状态设置cookie
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function get_cookie()
    {
        $id = $this->check_cookie();
        if ($id)
        {
            $this->set_session($id);
            return true;
        } else
        {
            return false;
        }
    }

    /**
     *  设置cookie
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function set_cookie($id='')
    {
        if ( $id < 1)
        {
            /* 摧毁cookie */
            $time = time() - 3600;
            setcookie('OUN[user_id]',  '', $time); 
			setcookie('OUN[password]', '', $time); 
			setcookie('OUN[auser_id]', '', $time);
        } else
        {
            /* 设置cookie */
            $time = time() + 3600 * 24 * 30; 
            $row = $this->db->getRow('SELECT id,password FROM '. $this->table. ' WHERE id="'.$id.'" LIMIT 1'); 
            if ($row)
            {
                setcookie("OUN[user_id]", $row['id'], $time, $this->cookie_path, $this->cookie_domain); 
				setcookie("OUN[password]", $row['password'], $time, $this->cookie_path, $this->cookie_domain); 
            }
        }
    }  

    /**
     *  设置指定用户SESSION
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function set_session($id=0)
    {
        if  ($id < 1) 
        {
			$GLOBALS['sess']->destroy_session();
            return false;
        } else
        { 
            $row = $this->db->getRow('SELECT id, user_name, sex,ifmanger,avatar FROM ' . $this->table. ' WHERE id="'.$id.'" LIMIT 1');  
            if ($row)
            {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['user_name'] = $row['user_name'];
				$_SESSION['sex']       = $row['sex']==1?'w':'m';  
				$_SESSION['ifmanger']  = $row['ifmanger']; 
				$_SESSION['avatar']    = $row['avatar']; 
            } 
        }
    }

    /**
     *  获取指定用户的信息
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function get_profile_by_id($id)
    {
        $sql = "SELECT * FROM " . $this->table. " WHERE id='$id'";
        $row = $this->db->getRow($sql); 
        return $row;
    }

    function get_user_info($username)
    {
        return $this->get_profile_by_name($username);
    }

    /**
     * 删除用户
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function remove_user($id)
    {
       return false;
    }
}

?>
