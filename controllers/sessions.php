<?php
if (!session_start()) {
	session_start();
}

class sessionsVal{
	public static function is_logged(){
		if (!isset($_SESSION)) {
			return false;
		}else{
			if (isset($_SESSION['page'])) {
				return $_SESSION['page'];
			}
		}
	}


	public static function valLog(){
		$handle = fopen($_SERVER["DOCUMENT_ROOT"] ."\data\users.txt", "r")or die("Unable to open file!");
		if ($handle) {
		    while (($line = fgets($handle)) !== false) {
		        $userdata[]=explode(',', $line);
		    }

		    return $userdata;

		    fclose($handle);
		} else {
		    return 0;
		} 
	}

	public static function logIn($usr,$page){
		$_SESSION['token']=md5(uniqid(rand(), true));
		$_SESSION['page']=trim($page);
		$_SESSION['username']=$usr;
		$_SESSION['time']=time();
		return 1;
	}	

	public static function logOut(){
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
		    $params = session_get_cookie_params();
		    setcookie(session_name(), '', time() - 42000,
		        $params["path"], $params["domain"],
		        $params["secure"], $params["httponly"]
		    );
		}
		session_destroy();
	}
}
