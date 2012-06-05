<?php
	class ImageWrite extends ImageBase{
		public function Normal( $x, $y, $string, $size, $color, $alpha = 1, $vert = false ){
			$rgba = Color::HexToRGBA($color, $alpha);
			if( $size > 5 || $size < 1 ){
				return false;
			}
			else{
				if( $vert ){
					$str = "imagestringup";
				}
				else{
					$str = "imagestring";
				}
				
				if( $str( $this->handle, $size, $x, $y, $string, $this->AllocateColor($rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) ){
					return true;
				}
				else{
					return false;
				}
			}
		}
		
		public function Font( $x, $y, $string, $textSize, $color, $angle, $font, $alpha = 1, $bg = "#000000", $bgA = 0, $padding = 0){
			$rgba = Color::HexToRGBA($color, $alpha);
			$bgrgba = Color::HexToRGBA($bg, $bgA);
			
			$size = imagettfbbox($textSize, 0, $font, $string);
			
			$text = new Image( (abs($size[2]) + abs($size[0])) + (2 * $padding), (abs($size[7]) + abs($size[1])) + (2 * $padding) );
			imagefill($text->handle, 0, 0, $this->AllocateColor($bgrgba[0]['r'], $bgrgba[0]['g'], $bgrgba[0]['b'], $bgrgba[0]['alpha']));
			
			imagealphablending($text->handle, true);
			$bool = imagettftext($text->handle, $textSize, 0, $padding, abs($size[5]) + $padding, $this->AllocateColor($rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']), $font, $string);
			imagealphablending($text->handle, false);
			if( $bool ){
				$textRot = $text->Manipulate->Rotate($angle);
				if( $textRot ){
					//get dimensions
					$Ox = $textRot->width;
					$Oy = $textRot->height;
					$tx = $text->width;
					$ty = $text->width;
					
					//make angle safe
					$a = $angle;
					while( $a < 0 || $a >= 360 ){
						if( $a < 0 ){
							$a += 360;
						}
						
						if( $a >= 360 ){
							$a -= 360;
						}
					}
					
					$deltaX = 0;
					$deltaY = 0;
					
					//calculate re-anchor point based on quadrant
					if( $a >= 0 && $a <= 90 ){
						$deltaX = 0;
						$deltaY = -(sin(deg2rad($a)) * $tx);
					}
					elseif( $a > 90 && $a <= 180 ){
						$deltaX = -(sin(deg2rad(90 - (180 - $a))) * $tx);
						$deltaY = -$Oy;
					}
					elseif( $a > 180 && $a <= 270 ){
						$deltaX = -$Ox;
						$deltaY = -(sin(deg2rad(270 - $a)) * $ty);
					}
					elseif( $a > 270 && $a < 360 ){
						$deltaX = -(sin(deg2rad(360 - $a)) * $ty);
						$deltaY = 0;
					}
					
					//if the offset places the image outside of the original, crop until it fits
					//if cropped, reset the coordinate to 0
					$newx = $x + round($deltaX);
					$newy = $y + round($deltaY);
					
					$left = $newx < 0 ? abs($newx) : 0;
					$top = $newy < 0 ? abs($newy) : 0;
					
					$xp = $newx < 0 ? 0 : $newx;
					$yp = $newy < 0 ? 0 : $newy;

					$cropFit = $textRot->Manipulate->Crop($top, 0, 0, $left);
					
					if( $this->caller->Combine->Overlay( $cropFit, $xp, $yp, 0, 0, $cropFit->width, $cropFit->height ) ){
						return true;
					}
					else{
						return false;
					}
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
	}
?>