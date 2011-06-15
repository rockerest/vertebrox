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
		
		public function Font( $x, $y, $string, $textSize, $color, $angle, $font, $alpha = 1, $bg = "#000000", $bgA = 0, $padding = 0)
		{
			$rgba = Color::HexToRGBA($color, $alpha);
			$bgrgba = Color::HexToRGBA($bg, $bgA);
			
			$size = imagettfbbox($textSize, $angle, $font, $string);
			
			$text = new Image( (abs($size[2]) + abs($size[0])) + (2 * $padding), (abs($size[7]) + abs($size[1])) + (2 * $padding) );
			imagefill($text->handle, 0, 0, $this->AllocateColor($bgrgba[0]['r'], $bgrgba[0]['g'], $bgrgba[0]['b'], $bgrgba[0]['alpha']));
			
			if( imagettftext($text->handle, $textSize, $angle, $padding, abs($size[5]) + $padding, $this->AllocateColor($rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']), $font, $string) )
			{
				if( $this->caller->Combine->Overlay( $text, $x, $y, 0, 0, $text->width, $text->height ) )
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
	}
?>