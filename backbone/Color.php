<?php
	class Color
	{
		public static function HexToRGB($hex)
		{
			if( $hex[0] == '#' )
			{
				$hex = substr($hex, 1);
			}
			
			$r = intval(substr($hex, 0, 2), 16);
			$g = intval(substr($hex, 2, 2), 16);
			$b = intval(substr($hex, 4, 2), 16);
			
			return array(
							array(
									'r' => $r,
									'g' => $g,
									'b' => $b
							),
							"rgb($r, $g, $b)"
						);
		}
		
		public static function RGBToHex($r, $g, $b)
		{
			if( $r < 256 && $r > -1 )
			{
				$h = dechex($r);
			}
			else
			{
				$h = "00";
			}
			
			if( $g < 256 && $g > -1 )
			{
				$e = dechex($g);
			}
			else
			{
				$e = "00";
			}
			
			if( $b < 256 && $b > -1 )
			{
				$x = dechex($b);
			}
			else
			{
				$x = "00";
			}
			
			return $h . $e . $x;
		}
		
		public static function HexToRGBA($hex, $alpha)
		{
			$res = Color::HexToRGB($hex);
			
			if( $alpha > 1 )
			{
				$alpha = $alpha / 100;
			}
			elseif( $alpha < 0 )
			{
				$alpha = 0;
			}
			
			$res[0]['alpha'] = $alpha;
			$res[1] = "rgba(" . substr($res[1], 3, -1) . ", $alpha)";
			
			return $res;
		}
	}
?>