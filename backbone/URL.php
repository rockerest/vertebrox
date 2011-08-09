<?php
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	
	if( $action == 'phps' )
	{
		$file = isset( $_GET['file'] ) ? $_GET['file'] : null;
		if( $file != null )
		{
			phps($file);
		}
	}	
	
	function phps($url)
	{
		if( substr($url, strpos($url, '.')) == '.phps' )
		{
			highlight_file('../' . $url);
		}
	}
	
	function getQueryString($level = 2)
	{
		$variables = array(
							'post'	=>	array(),
							'get'	=>	array()
							);
							
		if( isset($_POST) )
		{
			foreach( $_POST as $key => $val )
			{
				if( $level > 0 )
				{
					$cleaned = strip_tags($val);
					if( $level > 1 )
					{
						$cleaned = htmlentities($cleaned);
					}
				}
				else
				{
					$cleaned = $val;
				}
				$variables['post'][$key] = $cleaned;
			}
		}
		
		if( isset($_GET) )
		{
			foreach( $_GET as $key => $val )
			{
				if( $level > 0 )
				{
					$cleaned = strip_tags($val);
					if( $level > 1 )
					{
						$cleaned = htmlentities($cleaned);
					}
				}
				else
				{
					$cleaned = $val;
				}
				$variables['get'][$key] = $cleaned;
			}
		}
		
		return $variables;
	}
?>