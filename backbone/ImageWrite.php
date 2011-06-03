<?php
	class ImageWrite extends ImageBase
	{
		public function Normal( $x, $y, $string, $size, $color, $alpha = 1, $vert = false )
		{
			$rgba = Color::HexToRGBA($color, $alpha);
			if( $size > 5 || $size < 1 )
			{
				return false;
			}
			else
			{
				if( $vert )
				{
					$str = "imagestringup";
				}
				else
				{
					$str = "imagestring";
				}
				
				if( $str( $this->handle, $size, $x, $y, $string, $this->AllocateColor($rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) )
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
	}
?>