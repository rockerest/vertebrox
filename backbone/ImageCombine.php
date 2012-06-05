<?php
	class ImageCombine extends ImageBase{
		public function Overlay( $ovr, $dx, $dy, $sx, $sy, $sw, $sh, $alpha = 1 ){
			$alpha *= 100;
			if( $this->imagecopymerge_alpha( $this->handle, $ovr->handle, $dx, $dy, $sx, $sy, $sw, $sh, $alpha ) ){
				return true;
			}
			else{
				return false;
			}
		}
		
		//Fix for imagecopymerge not respecting alpha
		//http://www.php.net/manual/en/function.imagecopymerge.php#102844
		private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct, $trans = NULL){
			$dst_w = imagesx($dst_im);
			$dst_h = imagesy($dst_im);

			// bounds checking
			$src_x = max($src_x, 0);
			$src_y = max($src_y, 0);
			$dst_x = max($dst_x, 0);
			$dst_y = max($dst_y, 0);
			if ($dst_x + $src_w > $dst_w){
				$src_w = $dst_w - $dst_x;
			}
			
			if ($dst_y + $src_h > $dst_h){
				$src_h = $dst_h - $dst_y;
			}

			for($x_offset = 0; $x_offset < $src_w; $x_offset++){
				for($y_offset = 0; $y_offset < $src_h; $y_offset++){
					// get source & dest color
					$srccolor = imagecolorsforindex($src_im, imagecolorat($src_im, $src_x + $x_offset, $src_y + $y_offset));
					$dstcolor = imagecolorsforindex($dst_im, imagecolorat($dst_im, $dst_x + $x_offset, $dst_y + $y_offset));

					// apply transparency
					if (is_null($trans) || ($srccolor !== $trans)){
						$src_a = $srccolor['alpha'] * $pct / 100;
						// blend
						$src_a = 127 - $src_a;
						$dst_a = 127 - $dstcolor['alpha'];
						$dst_r = ($srccolor['red'] * $src_a + $dstcolor['red'] * $dst_a * (127 - $src_a) / 127) / 127;
						$dst_g = ($srccolor['green'] * $src_a + $dstcolor['green'] * $dst_a * (127 - $src_a) / 127) / 127;
						$dst_b = ($srccolor['blue'] * $src_a + $dstcolor['blue'] * $dst_a * (127 - $src_a) / 127) / 127;
						$dst_a = 127 - ($src_a + $dst_a * (127 - $src_a) / 127);
						$color = imagecolorallocatealpha($dst_im, $dst_r, $dst_g, $dst_b, $dst_a);
						// paint
						if (!imagesetpixel($dst_im, $dst_x + $x_offset, $dst_y + $y_offset, $color)){
							return false;
						}
						imagecolordeallocate($dst_im, $color);
					}
				}
			}
			return true;
		}
	}
?>