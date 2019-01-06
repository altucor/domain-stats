<?php

// full pathes to avoid including from local domain folder
require "DataBaseWrapper.php";
require "browser.php";
require "config.php";

class DomainStats
{	
	function __construct()
	{
		$this->userData = array();
		
		$this->userData['scheme'] = $_SERVER['REQUEST_SCHEME'];
		$this->userData['domain'] = $_SERVER['HTTP_HOST'];
		$this->userData['uri'] = $_SERVER['REQUEST_URI'];
		$this->userData['method'] = $_SERVER['REQUEST_METHOD'];
		$this->userData['ip'] = '';
		$this->userData['port'] = $_SERVER['REMOTE_PORT'];
		$this->userData['browserName'] = '';
		$this->userData['browserVersion'] = '';
		$this->userData['time'] = $_SERVER['REQUEST_TIME'];
		$this->userData['operationSystem'] = '';
		$this->userData['userFingerPrint'] = '';
		$this->userData['uniqueRowHash'] = '';
		
		
		$this->captureAllData();
		$this->saveToDataBase();
	}
	
	function __destruct()
	{
		
	}
	
	private function captureAllData()
	{
		$this->getUserIp();
		$this->getOs();
		$this->getUserBrowser();
		$this->generateUserFingerPrint();
		$this->generateHashWithTime();
	}
	
	function debugGetAllData()
	{
		return $this->userData;
	}
	
	private function generateUserFingerPrint()
	{
		$line = '';
		$line .= $this->userData['ip'];
		$line .= $this->userData['browserName'];
		$line .= $this->userData['browserVersion'];
		$line .= $this->userData['operationSystem'];
		
		$this->userData['userFingerPrint'] = md5($line);
	}
	
	private function generateHashWithTime()
	{
		$line = '';
		foreach($this->userData as $key => $elem)
			$line .= $elem;
		$this->userData['uniqueRowHash'] = md5($line);
	}
	
	private function saveToDataBase()
	{
		global $g_dbName;
		global $g_login;
		global $g_password;
		$db = new DataBaseWrapper($g_dbName, $g_login, $g_password);
		$db->insertArray($this->userData);
	}
	
	private function getUserIp()
	{
		$user_ip = '';
		if ( getenv('REMOTE_ADDR') ){
			$user_ip = getenv('REMOTE_ADDR');
		}elseif ( getenv('HTTP_FORWARDED_FOR') ){
			$user_ip = getenv('HTTP_FORWARDED_FOR');
		}elseif ( getenv('HTTP_X_FORWARDED_FOR') ){
			$user_ip = getenv('HTTP_X_FORWARDED_FOR');
		}elseif ( getenv('HTTP_X_COMING_FROM') ){
			$user_ip = getenv('HTTP_X_COMING_FROM');
		}elseif ( getenv('HTTP_VIA') ){
			$user_ip = getenv('HTTP_VIA');
		}elseif ( getenv('HTTP_XROXY_CONNECTION') ){
			$user_ip = getenv('HTTP_XROXY_CONNECTION');
		}elseif ( getenv('HTTP_CLIENT_IP') ){
			$user_ip = getenv('HTTP_CLIENT_IP');
		}
	
		$user_ip = trim($user_ip);
		if ( empty($user_ip) ){
			return '';
		}
		if ( !preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $user_ip) ){
			return '';
		}
		
		$this->userData['ip'] = $user_ip;
	}
	
	private function getOS() 
	{
		// Create list of operating systems with operating system name as array key 
		$oses = array (
			'iPhone'            => '(iPhone)',
			'Windows 3.11'      => 'Win16',
			'Windows 95'        => '(Windows 95)|(Win95)|(Windows_95)',
			'Windows 98'        => '(Windows 98)|(Win98)',
			'Windows 2000'      => '(Windows NT 5.0)|(Windows 2000)',
			'Windows XP'        => '(Windows NT 5.1)|(Windows XP)',
			'Windows 2003'      => '(Windows NT 5.2)',
			'Windows Vista'     => '(Windows NT 6.0)|(Windows Vista)',
			'Windows 7'         => '(Windows NT 6.1)|(Windows 7)',
			'Windows NT 4.0'    => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
			'Windows ME'        => 'Windows ME',
			'Open BSD'          => 'OpenBSD',
			'Sun OS'            => 'SunOS',
			'Linux'             => '(Linux)|(X11)',
			'Safari'            => '(Safari)',
			'Mac OS'            => '(Mac_PowerPC)|(Macintosh)',
			'QNX'               => 'QNX',
			'BeOS'              => 'BeOS',
			'OS/2'              => 'OS/2',
			'Search Bot'        => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp/cat)|(msnbot)|(ia_archiver)'
		);
		
		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		$osName = 'n/a';
		
		// Loop through $oses array
		foreach($oses as $os => $preg_pattern) {
			// Use regular expressions to check operating system type
			if ( preg_match('@' . $preg_pattern . '@', $userAgent) ) {
				// Operating system was matched so return $oses key
				$osName = $os;
				break;
			}
		}
		
		if($osName == 'n/a')
		{
			$osName = $_SERVER['HTTP_USER_AGENT']; // if cannot be detected, add whole line for futher manual analysis
		}
		
		$this->userData['operationSystem'] = $osName;
	}
	
	private function getUserBrowser()
	{
		$browser = new Browser($_SERVER['HTTP_USER_AGENT']);
		$this->userData['browserName'] = $browser->getBrowser();
		$this->userData['browserVersion'] = $browser->getVersion();
	}
}

?>