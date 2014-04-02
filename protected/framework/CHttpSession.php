<?php
/**
 * 重新定义session，便于以后进行重载
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
class CHttpSession extends CApplicationComponents
{
	/**
	 * @var boolean whether the session should be automatically started when the session application component is initialized, defaults to true.
	 */
	public $autoStart=true;

	/**
	 * Initializes the application component.
	 * This method is required by IApplicationComponent and is invoked by application.
	 */
	public function init()
	{
		parent::init();

		if($this->autoStart)
			$this->open();
		register_shutdown_function(array($this,'close'));
	}

	/**
	 * Returns a value indicating whether to use custom session storage.
	 * This method should be overriden to return true if custom session storage handler should be used.
	 * If returning true, make sure the methods {@link openSession}, {@link closeSession}, {@link readSession},
	 * {@link writeSession}, {@link destroySession}, and {@link gcSession} are overridden in child
	 * class, because they will be used as the callback handlers.
	 * The default implementation always return false.
	 * @return boolean whether to use custom storage.
	 */
	public function getUseCustomStorage()
	{
		return false;
	}

	/**
	 * Starts the session if it has not started yet.
	 */
	public function open()
	{
		if($this->getUseCustomStorage())
			@session_set_save_handler(array($this,'openSession'),array($this,'closeSession'),array($this,'readSession'),array($this,'writeSession'),array($this,'destroySession'),array($this,'gcSession'));

		@session_start();
		if(NBT_DEBUG && session_id()=='')
		{
			$message="打开SESSION失败";
			if(function_exists('error_get_last'))
			{
				$error=error_get_last();
				if(isset($error['message']))
					$message=$error['message'];
			}
		}
	}

	/**
	 * Ends the current session and store session data.
	 */
	public function close()
	{
		if(session_id()!=='')
			@session_write_close();
	}

	/**
	 * Frees all session variables and destroys all data registered to a session.
	 */
	public function destroy()
	{
		if(session_id()!=='')
		{
			@session_unset();
			@session_destroy();
		}
	}

	/**
	 * @return boolean whether the session has started
	 */
	public function getIsStarted()
	{
		return session_id()!=='';
	}

	/**
	 * @return string the current session ID
	 */
	public function getSessionID()
	{
		return session_id();
	}

	/**
	 * @param string $value the session ID for the current session
	 */
	public function setSessionID($value)
	{
		session_id($value);
	}

	/**
	 * Updates the current session id with a newly generated one .
	 * Please refer to {@link http://php.net/session_regenerate_id} for more details.
	 * @param boolean $deleteOldSession Whether to delete the old associated session file or not.
	 * @since 1.1.8
	 */
	public function regenerateID($deleteOldSession=false)
	{
		session_regenerate_id($deleteOldSession);
	}

	/**
	 * @return string the current session name
	 */
	public function getSessionName()
	{
		return session_name();
	}

	/**
	 * @param string $value the session name for the current session, must be an alphanumeric string, defaults to PHPSESSID
	 */
	public function setSessionName($value)
	{
		session_name($value);
	}

	/**
	 * @return string the current session save path, defaults to {@link http://php.net/session.save_path}.
	 */
	public function getSavePath()
	{
		return session_save_path();
	}

	/**
	 * @param string $value the current session save path
	 * @throws CException if the path is not a valid directory
	 */
	public function setSavePath($value)
	{
		if(is_dir($value))
			session_save_path($value);
		else
			throw new CException("设置SESSION保存路径失败，该路径{$value}不是一个正确的路径");
	}

	/**
	 * @return array the session cookie parameters.
	 * @see http://us2.php.net/manual/en/function.session-get-cookie-params.php
	 */
	public function getCookieParams()
	{
		return session_get_cookie_params();
	}

	/**
	 * Sets the session cookie parameters.
	 * The effect of this method only lasts for the duration of the script.
	 * Call this method before the session starts.
	 * @param array $value cookie parameters, valid keys include: lifetime, path,
	 * domain, secure, httponly. Note that httponly is all lowercase.
	 * @see http://us2.php.net/manual/en/function.session-set-cookie-params.php
	 */
	public function setCookieParams($value)
	{
		$data=session_get_cookie_params();
		extract($data);
		extract($value);
		if(isset($httponly))
			session_set_cookie_params($lifetime,$path,$domain,$secure,$httponly);
		else
			session_set_cookie_params($lifetime,$path,$domain,$secure);
	}

	/**
	 * @return string how to use cookie to store session ID. Defaults to 'Allow'.
	 */
	public function getCookieMode()
	{
		if(ini_get('session.use_cookies')==='0')
			return 'none';
		elseif(ini_get('session.use_only_cookies')==='0')
			return 'allow';
		else
			return 'only';
	}

	/**
	 * @param string $value how to use cookie to store session ID. Valid values include 'none', 'allow' and 'only'.
	 */
	public function setCookieMode($value)
	{
		if($value==='none')
		{
			ini_set('session.use_cookies','0');
			ini_set('session.use_only_cookies','0');
		}
		elseif($value==='allow')
		{
			ini_set('session.use_cookies','1');
			ini_set('session.use_only_cookies','0');
		}
		elseif($value==='only')
		{
			ini_set('session.use_cookies','1');
			ini_set('session.use_only_cookies','1');
		}
		else
			throw new CException('CHttpSession.cookieMode can only be "none", "allow" or "only".');
	}

	/**
	 * @return float the probability (percentage) that the gc (garbage collection) process is started on every session initialization, defaults to 1 meaning 1% chance.
	 */
	public function getGCProbability()
	{
		return (float)(ini_get('session.gc_probability')/ini_get('session.gc_divisor')*100);
	}

	/**
	 * @param float $value the probability (percentage) that the gc (garbage collection) process is started on every session initialization.
	 * @throws CException if the value is beyond [0,100]
	 */
	public function setGCProbability($value)
	{
		if($value>=0 && $value<=100)
		{
			// percent * 21474837 / 2147483647 ≈ percent * 0.01
			ini_set('session.gc_probability',floor($value*21474836.47));
			ini_set('session.gc_divisor',2147483647);
		}
		else
			throw new CException("CHttpSession.gcProbability '{$value}' is invalid. It must be a float between 0 and 100.");
	}

	/**
	 * @return boolean whether transparent sid support is enabled or not, defaults to false.
	 */
	public function getUseTransparentSessionID()
	{
		return ini_get('session.use_trans_sid')==1;
	}

	/**
	 * @param boolean $value whether transparent sid support is enabled or not.
	 */
	public function setUseTransparentSessionID($value)
	{
		ini_set('session.use_trans_sid',$value?'1':'0');
	}

	/**
	 * @return integer the number of seconds after which data will be seen as 'garbage' and cleaned up, defaults to 1440 seconds.
	 */
	public function getTimeout()
	{
		return (int)ini_get('session.gc_maxlifetime');
	}

	/**
	 * @param integer $value the number of seconds after which data will be seen as 'garbage' and cleaned up
	 */
	public function setTimeout($value)
	{
		ini_set('session.gc_maxlifetime',$value);
	}

	/**
	 * Session open handler.
	 * This method should be overridden if {@link useCustomStorage} is set true.
	 * Do not call this method directly.
	 * @param string $savePath session save path
	 * @param string $sessionName session name
	 * @return boolean whether session is opened successfully
	 */
	public function openSession($savePath,$sessionName)
	{
		return true;
	}

	/**
	 * Session close handler.
	 * This method should be overridden if {@link useCustomStorage} is set true.
	 * Do not call this method directly.
	 * @return boolean whether session is closed successfully
	 */
	public function closeSession()
	{
		return true;
	}

	/**
	 * Session read handler.
	 * This method should be overridden if {@link useCustomStorage} is set true.
	 * Do not call this method directly.
	 * @param string $id session ID
	 * @return string the session data
	 */
	public function readSession($id)
	{
		return '';
	}

	/**
	 * Session write handler.
	 * This method should be overridden if {@link useCustomStorage} is set true.
	 * Do not call this method directly.
	 * @param string $id session ID
	 * @param string $data session data
	 * @return boolean whether session write is successful
	 */
	public function writeSession($id,$data)
	{
		return true;
	}

	/**
	 * Session destroy handler.
	 * This method should be overridden if {@link useCustomStorage} is set true.
	 * Do not call this method directly.
	 * @param string $id session ID
	 * @return boolean whether session is destroyed successfully
	 */
	public function destroySession($id)
	{
		return true;
	}

	/**
	 * Session GC (garbage collection) handler.
	 * This method should be overridden if {@link useCustomStorage} is set true.
	 * Do not call this method directly.
	 * @param integer $maxLifetime the number of seconds after which data will be seen as 'garbage' and cleaned up.
	 * @return boolean whether session is GCed successfully
	 */
	public function gcSession($maxLifetime)
	{
		return true;
	}

	/**
	 * Returns the number of items in the session.
	 * @return integer the number of session variables
	 */
	public function getCount()
	{
		return count($_SESSION);
	}

	/**
	 * Returns the number of items in the session.
	 * This method is required by Countable interface.
	 * @return integer number of items in the session.
	 */
	public function count()
	{
		return $this->getCount();
	}

	/**
	 * @return array the list of session variable names
	 */
	public function getKeys()
	{
		return array_keys($_SESSION);
	}

	/**
	 * Returns the session variable value with the session variable name.
	 * This method is very similar to {@link itemAt} and {@link offsetGet},
	 * except that it will return $defaultValue if the session variable does not exist.
	 * @param mixed $key the session variable name
	 * @param mixed $defaultValue the default value to be returned when the session variable does not exist.
	 * @return mixed the session variable value, or $defaultValue if the session variable does not exist.
	 * @since 1.1.2
	 */
	public function get($key,$defaultValue=null)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
	}

	/**
	 * Returns the session variable value with the session variable name.
	 * This method is exactly the same as {@link offsetGet}.
	 * @param mixed $key the session variable name
	 * @return mixed the session variable value, null if no such variable exists
	 */
	public function itemAt($key)
	{
		return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
	}

	/**
	 * Adds a session variable.
	 * Note, if the specified name already exists, the old value will be removed first.
	 * @param mixed $key session variable name
	 * @param mixed $value session variable value
	 */
	public function add($key,$value)
	{
		$_SESSION[$key]=$value;
	}
	
	/**
	 * 与add方法一致的功能，取个别名
	 *
	 * @param unknown_type $key
	 * @param unknown_type $value
	 */
	public function set( $key,$value )
	{
		$_SESSION[$key]=$value;
	}

	/**
	 * Removes a session variable.
	 * @param mixed $key the name of the session variable to be removed
	 * @return mixed the removed value, null if no such session variable.
	 */
	public function remove($key)
	{
		if(isset($_SESSION[$key]))
		{
			$value=$_SESSION[$key];
			unset($_SESSION[$key]);
			return $value;
		}
		else
			return null;
	}

	/**
	 * Removes all session variables
	 */
	public function clear()
	{
		foreach(array_keys($_SESSION) as $key)
			unset($_SESSION[$key]);
	}

	/**
	 * @param mixed $key session variable name
	 * @return boolean whether there is the named session variable
	 */
	public function contains($key)
	{
		return isset($_SESSION[$key]);
	}
	
	/**
	 * 返回所有的SESSION信息
	 *
	 * @return array
	 */
	public function getAll()
	{
		return $_SESSION;
	}
	
//end class
}
