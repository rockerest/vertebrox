<?php
	$action = isset( $_GET['action'] ) ? $_GET['action'] : null;
	
	if( $action == 'phps' ){
		$file = isset( $_GET['file'] ) ? $_GET['file'] : null;
		if( $file != null ){
			phps($file);
		}
	}	
	
	function phps($url){
		if( substr($url, strpos($url, '.')) == '.phps' ){
			highlight_file('../' . $url);
		}
	}
?>