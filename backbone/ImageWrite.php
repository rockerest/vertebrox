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
			
			$size = imagettfbbox($textSize, 0, $font, $string);
			
			$text = new Image( (abs($size[2]) + abs($size[0])) + (2 * $padding), (abs($size[7]) + abs($size[1])) + (2 * $padding) );
			imagefill($text->handle, 0, 0, $this->AllocateColor($bgrgba[0]['r'], $bgrgba[0]['g'], $bgrgba[0]['b'], $bgrgba[0]['alpha']));
			
			imagealphablending($text->handle, true);
			$bool = imagettftext($text->handle, $textSize, 0, $padding, abs($size[5]) + $padding, $this->AllocateColor($rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']), $font, $string);
			imagealphablending($text->handle, false);
			if( $bool )
			{
				$textRot = $text->Manipulate->Rotate($angle);
				if( $textRot )
				{
					if( $this->caller->Combine->Overlay( $textRot, $x, $y, 0, 0, $textRot->width, $textRot->height ) )
					{
						$this->caller->Draw->Rectangle($x, $y, $textRot->width, $textRot->height, "#FF0000");
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
			else
			{
				return false;
			}
		}
	}
?>