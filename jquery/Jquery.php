<?php
	class jQuery
	{
		private $jquery;
		private $jqueryui;
		public function __construct()
		{
			$v = "1.7.1"; //jQuery version
			$uiv = "1.8.16"; // jQueryUI version
			
			$this->jquery = "<!--    Load Javascript Library - jQuery	-->\n<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jquery/$v/jquery.min.js\"></script>";
			$this->jqueryui = "<!--	Load jQuery User Interface		-->\n<script type=\"text/javascript\" src=\"https://ajax.googleapis.com/ajax/libs/jqueryui/$uiv/jquery-ui.min.js\"></script>";
		}
		
		public function __get($var)
		{
			if( $var == "jquery" )
			{
				return $this->jquery . "\n";
			}
			elseif( $var == "jqueryui" || $var == "ui" )
			{
				return $this->jqueryui . "\n";
			}
			elseif( $var == "both" || $var == "all" || $var == null || !isset($var) )
			{
				return $this->jquery . "\n" . $this->jqueryui . "\n";
			}
		}
	}
?>