<?php
	function setSession($expire = 0, $root = '/', $dom = null, $secure = false, $httponly = true){
		if( $dom == null ){
			$dom = '.' . $_SERVER['HTTP_HOST'];
		}
		
		session_set_cookie_params( $expire, $root, $dom, $secure, $httponly );

		if( !isset( $_COOKIE['identifier'] ) ){
			$myTempVar = uniqid(TRUE);
			$sesswhirl = substr( hash('whirlpool', $myTempVar), 0, 68 );

			setcookie('identifier', $sesswhirl);
			session_name($sesswhirl);
			session_start();
		}
		else{
			session_name($_COOKIE['identifier']);
			session_start();
		}
	}
	
	function setSessionVar($name, $value){
		$_SESSION[$name] = $value;
	}
?>
