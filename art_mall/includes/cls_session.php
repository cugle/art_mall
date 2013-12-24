<?php

if (!defined('IN_OUN'))
{
    die('Hacking attempt');
}

class cls_session
{
    var $db             = NULL;
    var $session_table  = '';

    var $max_life_time  = 1800; // SESSION 过期时间

    var $session_name   = '';
    var $session_id     = '';

    var $session_expiry = '';
    var $session_md5    = '';

    var $session_cookie_path   = '/';
    var $session_cookie_domain = '';
    var $session_cookie_secure = false;

	var $login = false;

	//var $domain_user_id = 0;  //当前域名的id

    var $_ip   = '';
    var $_time = 0;

    function __construct(&$db, $session_table, $session_data_table, $session_name = 'OUN_ID', $session_id = '')
    {
        $this->cls_session($db, $session_table, $session_data_table, $session_name, $session_id);
    }

    function cls_session(&$db, $session_table, $session_data_table, $session_name = 'OUN_ID', $session_id = '')
    {
        $GLOBALS['_SESSION'] = array();

        if (!empty($GLOBALS['cookie_path']))
        {
            $this->session_cookie_path = $GLOBALS['cookie_path'];
        } else {
            $this->session_cookie_path = '/';
        }

        if (!empty($GLOBALS['cookie_domain']))
        {
            $this->session_cookie_domain = $GLOBALS['cookie_domain'];
        } else {
            $this->session_cookie_domain = '';
        }

        if (!empty($GLOBALS['cookie_secure']))
        {
            $this->session_cookie_secure = $GLOBALS['cookie_secure'];
        } else {
            $this->session_cookie_secure = false;
        }

        $this->session_name       = $session_name;
        $this->session_table      = $session_table;
        $this->session_data_table = $session_data_table;

        $this->db  = &$db;
        $this->_ip = real_ip();

        if ($session_id == '' && !empty($_COOKIE[$this->session_name]))
        {
            $this->session_id = $_COOKIE[$this->session_name];
        } else {
            $this->session_id = $session_id;
        }

        if ($this->session_id)
        {
            $tmp_session_id = substr($this->session_id, 0, 32);
            if ($this->gen_session_key($tmp_session_id) == substr($this->session_id, 32))
            {
                $this->session_id = $tmp_session_id;
            } else {
                $this->session_id = '';
            }
        }

        $this->_time = gmtime();

        if ($this->session_id)
        {  		
            $this->load_session();
        } else {
            $this->gen_session_id(); 
            setcookie($this->session_name, $this->session_id . $this->gen_session_key($this->session_id), 0, $this->session_cookie_path, $this->session_cookie_domain, $this->session_cookie_secure);
        } 
        register_shutdown_function(array(&$this, 'close_session'));
    }

    function gen_session_id()
    {
        $this->session_id = md5(uniqid(mt_rand(), true)); 
        return $this->insert_session();
    }

    function gen_session_key($session_id)
    {
        static $ip = ''; 
        if ($ip == '')
        {
            $ip = substr($this->_ip, 0, strrpos($this->_ip, '.'));
        } 
        return sprintf('%08x', crc32(!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] . ROOT_PATH . $ip . $session_id : ROOT_PATH . $ip . $session_id));
    }

    function insert_session()
    {
        return $this->db->query('INSERT INTO ' . $this->session_table . " (sesskey, expiry, ip, data) VALUES ('" . $this->session_id . "', '". $this->_time ."', '". $this->_ip ."', 'a:0:{}')");
    }

    function load_session()
    { 
        $session = $this->db->getRow('SELECT userid,adminid,data, expiry FROM ' . $this->session_table . " WHERE sesskey = '" . $this->session_id . "'");  
        if ( !$session ) {  
            $this->insert_session(); 
            $this->session_expiry = 0;
            $this->session_md5    = '40cd750bba9870f18aada2478b24840a';
            $GLOBALS['_SESSION']  = array();  
        } else {  
            if (!empty($session['data']) && $this->_time - $session['expiry'] <= $this->max_life_time) {  
                $this->session_expiry = $session['expiry'];
                $this->session_md5    = md5($session['data']);
                $GLOBALS['_SESSION']  = unserialize($session['data']);  
				//$GLOBALS['_SESSION']['user_id']   = $session['userid'];
				//$GLOBALS['_SESSION']['auser_id']  = $session['adminid'];  
            } else {
                $session  = $this->db->getRow('SELECT expiry,data  FROM ' . $this->session_data_table . " WHERE sesskey = '" . $this->session_id . "'");   
                if (!empty($session['data']) && $this->_time - $session['expiry'] <= $this->max_life_time)
                {
                    $this->session_expiry = $session['expiry'];
                    $this->session_md5    = md5($session['data']);
                    $GLOBALS['_SESSION']  = unserialize($session['data']); 
					$GLOBALS['_SESSION']['user_id']  = $GLOBALS['_SESSION']['user_id'];
					$GLOBALS['_SESSION']['auser_id'] = $GLOBALS['_SESSION']['auser_id'];

                } else {
                    $this->session_expiry = 0;
                    $this->session_md5    = '40cd750bba9870f18aada2478b24840a';
                    $GLOBALS['_SESSION']  = array();
                }
            }
        }
    }

    function update_session()
    { 
		$auser_id    =  $GLOBALS['_SESSION']['auser_id']>0 ? intval($GLOBALS['_SESSION']['auser_id']) : 0;
		$user_id     =  $GLOBALS['_SESSION']['user_id']  >0 ? intval($GLOBALS['_SESSION']['user_id'])  : 0;  
		//$data      = ($user_id > 0 || $auser_id > 0 )?serialize($GLOBALS['_SESSION']):'';  
		$data      = serialize($GLOBALS['_SESSION']);   
		unset($GLOBALS['_SESSION']['auser_id']);
		unset($GLOBALS['_SESSION']['user_id']);  
        $this->_time = time(); 
        if ($this->session_md5 == md5($data) && $this->_time < $this->session_expiry + 10) {
            return true;
        } 

        $data = addslashes($data); 
        if (isset($data{255})) {
            $this->db->autoReplace($this->session_data_table, array('sesskey' => $this->session_id, 'expiry' => $this->_time, 'data' => $data), array('data' => $data));
            $data = '';
        }

		$sesskey = $this->db->getOne('SELECT sesskey  FROM ' . $this->session_data_table . " WHERE sesskey = '" . $this->session_id . "'");
		if($sesskey > 0) {
			$this->db->query('UPDATE ' . $this->session_data_table . " SET expiry = '" . $this->_time . "' WHERE sesskey = '" . $this->session_id . "' LIMIT 1");
		}

        return $this->db->query('UPDATE ' . $this->session_table . " SET expiry = '" . $this->_time . "', ip = '" . $this->_ip . "', userid = '" . $user_id . "', adminid = '" .$auser_id . "', data = '$data' WHERE sesskey = '" . $this->session_id . "' LIMIT 1");
    }

    function close_session()
    {
        $this->update_session();

        /* 随机对 sessions_data 的库进行删除操作 */
        if (mt_rand(0, 2) == 2)
        {
            $this->db->query('DELETE FROM ' . $this->session_data_table . ' WHERE expiry < ' . ($this->_time - $this->max_life_time));
        } 
        if ((time() % 2) == 0)
        {
            return $this->db->query('DELETE FROM ' . $this->session_table . ' WHERE expiry < ' . ($this->_time - $this->max_life_time));
        } 
        return true;
    }

    function delete_spec_admin_session($adminid)
    {
        if (!empty($GLOBALS['_SESSION']['auser_id']) && $adminid)
        {
            return $this->db->query('DELETE FROM ' . $this->session_table . " WHERE adminid = '$adminid'");
        } else
        {
            return false;
        }
    }

    function destroy_session()
    {
        $GLOBALS['_SESSION'] = array();
		$GLOBALS['_SESSION']['auser_id']   = 0;
        $GLOBALS['_SESSION']['user_id']   = 0; 

        setcookie($this->session_name, $this->session_id, 1, $this->session_cookie_path, $this->session_cookie_domain, $this->session_cookie_secure);
         /*  自定义执行部分 */
		$this->db->query('DELETE FROM ' . $this->session_data_table . " WHERE sesskey = '" . $this->session_id . "' LIMIT 1");
		return $this->db->query('DELETE FROM ' . $this->session_table . " WHERE sesskey = '" . $this->session_id . "' LIMIT 1");
    }

    function get_session_id()
    {
        return $this->session_id;
    } 

    function get_users_count($login=false)
    {
		if($login)
		{
			$count = $this->db->getOne('SELECT count(*) FROM ' . $this->session_table.' where userid > 0'); 
		}else
		{
			$count = $this->db->getOne('SELECT count(*) FROM ' . $this->session_table.' where userid < 1');
		}
		return $count;
    }
}

?>