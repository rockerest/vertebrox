<?php
	class ImageDraw extends ImageBase
	{
		public function Circle($x, $y, $r, $color, $filled = false, $alpha = 1)
		{
			$rgba = Color::HexToRGBA($color, $alpha);
			if( $filled )
			{
				$ell = "imagefilledellipse";
			}
			else
			{
				$ell = "imageellipse";
			}
			
			if( $ell( $this->handle, $x, $y, $r, $r, $this->AllocateColor($rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function Rectangle($x, $y, $w, $h, $color, $filled = false, $alpha = 1)
		{
			$rgba = Color::HexToRGBA($color, $alpha);
			if( $filled )
			{
				$rec = "imagefilledrectangle";
			}
			else
			{
				$rec = "imagerectangle";
			}
			
			if( $rec( $this->handle, $x, $y, $x + $w, $y + $h, $this->AllocateColor($rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		public function Line($x1, $y1, $x2, $y2, $color, $alpha = 1)
		{
			$rgba = Color::HexToRGBA($color, $alpha);
			if( imageline( $this->handle, $x1, $y1, $x2, $y2, $this->AllocateColor($rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) )
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
?>