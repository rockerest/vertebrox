<?php
class Template
{
    public function __construct()
	{
    }       
    
    function build($templateFilePath)
	{
        $tmpl = $this;
        // suppress non-existent variable warnings
        error_reporting(E_ALL - E_NOTICE);
        ob_start();     
        include($templateFilePath);
        return ob_get_clean();
    }
}
?>
