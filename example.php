<?php
	set_include_path('.:backbone:global:jquery');
	
	require_once('Template.php');
	require_once('Color.php');
	require_once('Image.php');
	require_once('RedirectBrowserException.php');
	require_once('Session.php');
	require_once('URL.php');
	
	$tmpl = new Template();
	$tmpl->passedvar = "This string was passed through the Template object to be rendered in the HTML file.";
	
	$tmpl->hex = Color::RGBToHex(60, 120, 60);
	$tmpl->alpha = Color::HexToRGBA($tmpl->hex, .5);
	$tmpl->rgb = Color::HexToRGB($tmpl->hex);
	
	$img = new Image();
	$img->source = 'portrait.png';
	$img->Write->Normal(20, 20, "A Self Portrait of Me", 5, "#000000", 1);
	$img->destination = 'portrait2.png';
	$img->output();
	$img->clean();
	unset($img);
	
	//start a session and store a variable;
	setSession(0,'/'); // expires with browser session, the root is '/'
	setSessionVar('foo', 'bar'); //there's no retrieval function, so this is kind of stupid
	if( !isset($_SESSION['foo']) ){
		throw new RedirectBrowserException("example.php");
	}
	
	//Database calls
	/*
	$db = new Database("username", "password", "database name", "location of database", "type of database"); // currently only supports "mysql"
	$sql = "SELECT * FROM mytable WHERE myid=?";
	$values = array(4); // myid
	
	$result = $db->qwv($sql, $values); // query with values, returns array of rows (can be empty)
	
	if( $db->stat() ) // <-- the boolean representing whether the last query was successful{
		foreach( $result as $row ){
			print $row['myid'] . "<br />";
		}
	}
	
	$sql = "INSERT INTO mytable VALUES (4)";
	$db->q($sql); // query (no values), returns array of rows (can be empty)
	
	Database.php is fully top-commented
	*/	
	
	print $tmpl->build('example.html');
?>