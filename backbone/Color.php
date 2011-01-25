<?php
	class Color
	{
		private $hex;
		private $r;
		private $g;
		private $b;
		
		public function __construct( $a, $b = null, $c = null )
		{
			if( $b == null || $c == null )
			{
				//hex, or unknown input
			}
			elseif( $a == null )
			{
				//bad input
			}
		}
	}
?>