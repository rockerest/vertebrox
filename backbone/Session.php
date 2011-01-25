<?php
	function 	setSession($expire, $root)
	{
		if( !isset( $_COOKIE['identifier'] ) )
		{
			$myTempVar = uniqid(TRUE);
			$sesswhirl = substr( hash('whirlpool', $myTempVar), 0, 68 );
		
			setcookie('identifier', $sesswhirl, $expire, $root);
			session_name($sesswhirl);
			session_start();
		}
		else
		{
			session_name($_COOKIE['identifier']);
			session_start();
		}
	}
	
	function 	setSessionVar($name, $value)
	{
		$_SESSION[$name] = $value;
	}
?>
