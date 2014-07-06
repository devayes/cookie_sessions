<?php

$sess = new Session();

class Session {
          
    // Change these
    const cookie_domain = ''; // Leave blank for localhost, otherwise: .domain.com or www.domain.com
    const cookie_name = 'COOKIESESS';
    const cookie_lifetime = 604800; // 1 week;
    
    public function __Construct()
    {
        ini_set('session.use_trans_sid', 0);                        // Stop adding SID to URLs
        ini_set('session.serialize_handler', 'php');                // How to store data
        ini_set('session.use_cookies', 0);                          // Use cookie to store the session ID
        ini_set('session.name', self::cookie_name);                 // Name of our cookie
        ini_set('session.cookie_domain', self::cookie_domain);      // Set above
        ini_set('session.cookie_lifetime', self::cookie_lifetime);  // Set above
        ini_set('session.cookie_path','/');
        ini_set('session.hash_function', 1);                        // SHA1 PHP5+
        ini_set('session.gc_divisor', 1000000);
        ini_set('session.gc_probability', 1);                       //1:1000000 doesn't run anyway.
        ini_set('session.gc_maxlifetime', self::cookie_lifetime);   // Same as cookie lifetime. clean up is browser side

        // use this object as the session handler
        session_set_save_handler(array($this, 'sessionOpen'),
                                 array($this, 'sessionClose'),
                                 array($this, 'sessionRead'),
                                 array($this, 'sessionWrite'),
                                 array($this, 'sessionDestroy'),
                                 array($this, 'sessionGC'));
        
        register_shutdown_function('session_write_close'); 
        
        // start our session
        if (session_id() == '') { 
           session_start();
        }
    }
  
    /**
     * Closes a session.
     * @return boolean true
     */
    public function sessionClose()
    {
        // do nothing
        return true;
    }

    /**
     * Opens a session.
     * @param  string $path  (ignored)
     * @param  string $name  (ignored)
     * @return boolean true
     */
    public function sessionOpen($path = null, $name = null)
    {
        return true;
    }

    /**
     * Destroys a session.
     * @param  string $id  A session ID
     * @return bool true, if the session was destroyed
     */
    public function sessionDestroy($id = null) 
    {
		if(isset($_COOKIE[session_name()])){
			return setcookie(
			    session_name(), 
			    '', 
			    -1, 
			    ini_get('session.cookie_path'), 
			    ini_get('session.cookie_domain')
			);
		}
    }

    /**
     * Cleans up old sessions.
     * @param  int $lifetime  The lifetime of a session
     * @return bool true
     */
    public function sessionGC($lifetime)
    {
        return true;
    }

    /**
     * Reads a session.
     * @param  string $id  A session ID
     * @return mixed null or data
     */
    public function sessionRead($id)
    {
        if(isset($_COOKIE[session_name()])){
            $cookie = unserialize(base64_decode($_COOKIE[session_name()]));
            return $cookie['data'];
        } else {
            $data = array(
                'id' => $id,
                'data' => null,
                'created' => time(),
                'last_active' => time()
            );
            setcookie(
                session_name(),
                base64_encode(serialize($data)), 
                (time() + self::cookie_lifetime), 
                ini_get('session.cookie_path'), 
                ini_get('session.cookie_domain')
            );
            return null;
        }
    }
  
    /**
     * Writes session data.
     * @param  string $id    A session ID
     * @param  string $data  A serialized chunk of session data
     * @return bool true
     */
    public function sessionWrite($id, $data)
    {
        if(isset($_COOKIE[session_name()])){
            $cookie = unserialize(base64_decode($_COOKIE[session_name()]));            
            $cookie['data'] = $data;
            $cookie['last_active'] = time();
            setcookie(
                session_name(),
                base64_encode(serialize($cookie)),
                (time() + self::cookie_lifetime),
                ini_get('session.cookie_path'),
                ini_get('session.cookie_domain')
            );
            unset($cookie);
        }
        
        return true;
    }
  
    /**
	 * Get session data
	 * eg: Session::getData('key');
	 * @access public
	 * @param string $key optional to retrieve single var
	 * @return mixed string or array of session data
	 */
	public static function &getData($key = null)
	{
		if (!is_null($key)) {
			if (isset($_SESSION[$key])) {
				return $_SESSION[$key];
			}
			return null;
		}
		return $_SESSION;
	}
	
	/**
	 * Sets session data
	 * eg: Session::setData($array_of_keys_and_values);
	 * eg: Session::setData('key', 'value');
	 * @access public
	 * @param array keys and values
	 * @param string value
	 * @return true
	 */
	public static function setData($data=array(), $val=null)
	{
	    if (is_string($data)) {
	        $data = array($data=>$val);
	    }

	    if (is_array($data)) {
			foreach($data as $k=>$v) {
		        $_SESSION[$k] = &$v;
			}
		}
		
		return true;
	}
	
	/**
	 * Unset session data
	 * @access public
	 * @param string key to delete
	 * @return bool whether the string was a string
	 */
	public static function unsetData($keys)
	{
	    if (is_array($keys)) {
			foreach ($keys as $key) {
			    unset($_SESSION[$key]);
			}
		} elseif (isset($_SESSION[$keys])) {
			unset($_SESSION[$keys]);
		}		
		
		return true;
	}
	
}
