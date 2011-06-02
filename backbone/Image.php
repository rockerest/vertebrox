<?php
	require_once('Color.php');
	class Image
	{
		private $log = array();
		private $status = array();
		
		private $source = null;
		private $srcFh;
		private $destination = null;
		
		private $origXW;
		private $origYH;
		private $origType;
		
		private $newIm;
		
		public function __construct($src = null, $dest = null)
		{
			$this->setBoth($src, $dest);
			$this->size();
			$this->createHandle();
			$this->makeImg($this->origXW, $this->origYH);
		}
		
		public function setSrc($src = null)
		{
			if( $src == null )
			{
				$this->logStat("SRC_!set", false);
			}
			else
			{
				$this->source = $src;
				$this->logStat("SRC_SET", true);
			}
		}
		
		public function setDest($dest = null)
		{
			if( $dest == null )
			{
				$this->logStat("DEST_!set", false);
			}
			else
			{
				$this->destination = $dest;
				$this->logStat("DEST_SET", true);
			}
		}
		
		public function setBoth($src = null, $dest = null)
		{
			$this->setSrc($src);
			$this->setDest($dest);
		}
		
		public function copySrc()
		{
			imagecopy($this->newIm, $this->srcFh, 0, 0, 0, 0, $this->origXW, $this->origYH);
		}
		
		public function getHandle()
		{
			return $this->newIm;
		}
		
		public function __get($var)
		{
			if( $var == "width" )
			{
				return $this->origXW;
			}
			elseif( $var == "height" )
			{
				return $this->origYH;
			}
		}
		
		public function resize($newWidth, $newHeight, $stretch = true)
		{
			//This function will NOT return an image with the exact dimensions specified if $stretch is false
			//it will return an image with the LARGEST dimension matching the size indicated, and the other dimension scaled appropriately.
			if( $this->source == null )
			{
				$this->logStat("CANNOT_RESIZE_NON-SRC", false);
				return false;
			}
			else
			{
				$this->size();
				if( $stretch )
				{
					$this->makeImg($newWidth, $newHeight);
					$this->createHandle();
					imagecopyresampled($this->newIm, $this->srcFh, 0, 0, 0, 0, $newWidth, $newHeight, $this->origXW, $this->origYH);
					$this->logStat("RSZ_IMG_TO_X:".$newWidth."_Y:".$newHeight,true);
					return true;
				}
				else
				{
					if( $this->origXW > $this->origYH )
					{
						$perc = ($newWidth / $this->origXW) * 100;
					}
					else
					{
						$perc = ($newHeight / $this->origYH) * 100;
					}
					return $this->scale($perc);
				}
				
			}
		}
		
		public function scale($percent)
		{
			if( $percent < 1 )
			{
				$this->logStat("CANNOT_SCALE_BELOW_1%", false);
				return false;
			}
			
			if( $this->source == null )
			{
				$this->logStat("CANNOT_SCALE_NON-SRC", false);
				return false;
			}
			else
			{
				$scale = $percent / 100;
				$this->size();
				$this->newXW = $this->origXW * $scale;
				$this->newYH = $this->origYH * $scale;
				$this->makeImg($this->newXW, $this->newYH);
				$this->createHandle();
				imagecopyresampled($this->newIm, $this->srcFh, 0, 0, 0, 0, $this->newXW, $this->newYH, $this->origXW, $this->origYH);
				$this->logStat("SCL_IMG_TO_X:".$this->newXW."_Y:".$this->newYH,true);
				return true;
			}
		}
		
		public function DrawCircle($x, $y, $r, $color, $filled, $alpha = 1)
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
			
			if( $ell( $this->newIm, $x, $y, $r, $r, $this->allocColor($this->newIm, $rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) )
			{
				$this->logStat("DRAW:CIRCLE_X:".$x."_Y:".$y."_R:".$r."_C:".$rgba[1], true);
			}
			else
			{
				$this->logStat("DRAW:CIRCLE_X:".$x."_Y:".$y."_R:".$r."_C:".$rgba[1].";FAILED", false);
			}
		}
		
		public function DrawRectangle($x, $y, $w, $h, $color, $filled, $alpha = 1)
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
			
			if( $rec( $this->newIm, $x, $y, $x + $w, $y + $h, $this->allocColor($this->newIm, $rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) )
			{
				$this->logStat("DRAW:RECT_X:".$x."_Y:".$y."_W:".$w."_H:".$h."_C:".$rgba[1], true);
			}
			else
			{
				$this->logStat("DRAW:RECT_X:".$x."_Y:".$y."_W:".$w."_H:".$h."_C:".$rgba[1].";FAILED", false);
			}
		}
		
		public function DrawLine($x1, $y1, $x2, $y2, $color, $alpha = 1)
		{
			$rgba = Color::HexToRGBA($color, $alpha);
			if( imageline( $this->newIm, $x1, $y1, $x2, $y2, $this->allocColor($this->newIm, $rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) )
			{
				$this->logStat("DRAW:LINE:".$x1.",".$y1."-".$x2.",".$y2."_C:".$rgba[1], true);
			}
			else
			{
				$this->logStat("DRAW:LINE:".$x1.",".$y1."-".$x2.",".$y2."_C:".$rgba[1].";FAILED", false);
			}
		}
		
		public function DrawOverlay( $src, $dx, $dy, $sx, $sy, $sw, $sh, $alpha = 1 )
		{
			$alpha *= 100;
			if( $this->imagecopymerge_alpha($this->newIm, $src->getHandle(), $dx, $dy, $sx, $sy, $sw, $sh, $alpha) )
			{
				$this->logStat("DRAW:IMG_OVERLAY", true);
			}
			else
			{
				$this->logStat("DRAW:IMG_OVERLAY;FAILED", true);
			}
		}
		
		public function WriteDefault( $x, $y, $string, $size, $color, $alpha = 1, $vert = false )
		{
			$rgba = Color::HexToRGBA($color, $alpha);
			if( $size > 5 || $size < 1 )
			{
				$this->logStat("WRITE:".$string.":".$x.",".$y."_S:".$size."_C:".$rgba[1]."_V:".$vert.";FAILED", false);
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
				
				if( $str( $this->newIm, $size, $x, $y, $string, $this->allocColor($this->newIm, $rgba[0]['r'], $rgba[0]['g'], $rgba[0]['b'], $rgba[0]['alpha']) ) )
				{
					$this->logStat("WRITE:".$string.":".$x.",".$y."_S:".$size."_C:".$rgba[1]."_V:".$vert, true);
				}
				else
				{
					$this->logStat("WRITE:".$string.":".$x.",".$y."_S:".$size."_C:".$rgba[1]."_V:".$vert.";FAILED", false);
				}
			}
		}
		
		public function size($source = null)
		{
			if( $source == null && $this->source == null )
			{
				$this->logStat("CANNOT_SIZE_NON-SRC", false);
				return false;
			}
			elseif( $source != null )
			{
				$info = getimagesize($source);
				$this->logStat("SIZED_".$source, true);
				return array('x' => $info[0], 'y' => $info[1]);				
			}
			else
			{
				$info = getimagesize($this->source);
				if( $info[0] != 0 && $info[1] != 0 )
				{
					$this->origXW = $info[0];
					$this->origYH = $info[1];
					$this->origType = $info[2];
					$this->logStat("GOT_IMG_SZ_X:".$info[0]."_Y:".$info[1]."_T:".$info[2],true);
					return true;
				}
				else
				{
					$this->logStat("READ_ERROR", false);
					return false;
				}
			}		
		}
		
		public function output()
		{
			if( isset($this->destination) && $this->destination != null )
			{
				switch( $this->origType )
				{
					case 1:
							imagegif($this->newIm, $this->destination);
							break;
					case 2:
							imagejpeg($this->newIm, $this->destination);
							break;
					case 3:
							imagepng($this->newIm, $this->destination);
							break;
					default:
							$this->logStat("OUTPUT_FAIL_UNKNOWN_TYPE_".$this->origType,true);
							return false;
							break;
				}
			}
			else
			{
				switch( $this->origType )
				{
					case 1:
							imagegif($this->newIm);
							break;
					case 2:
							imagejpeg($this->newIm);
							break;
					case 3:
							imagepng($this->newIm);
							break;
					default:
							$this->logStat("OUTPUT_FAIL_UNKNOWN_TYPE_".$this->origType,true);
							return false;
							break;
				}
			}
			$this->logStat("OUTPUT_TO_TYPE:".$this->origType,true);
		}
		
		//You don't have to explicitly set a destination as a parameter to this function.
		//However, if you don't explicitly set one, you may (read will) run into issues,
		//unless you've set one previously through any one of the numerous methods to do so.		
		public function check($dest = null)
		{
			if( $dest == null && $this->destination == null)
			{
				$this->logStat("CANNOT_CHECK_NON-DEST", false);
				return false;
			}
			else
			{
				if( $dest != null )
				{
					$res = file_exists( $dest );
					$this->logStat("CHECK_FILE_".$dest, $res );
					return $res;
				}
				else
				{
					$res = file_exists( $this->destination );
					$this->logStat("CHECK_FILE_".$this->destination, $res );
					return $res;
				}
			}
		}
		
		//If you send an actual #, the function will return that log index (not recommended)
		//If you send true, the function will return the last log entry
		//[If you send nothing, it does that too]
		//If you send the string 'yes', the function will return the whole damn log to you.
		public function log($command = null)
		{
			$low = -1;
			$high = count( $this->log );
			if( is_int( $command ) && $low < $command && $command < $high)
			{
				return $this->log[$command];
			}
			elseif( (is_bool( $command ) && $command == true) || $command == null )
			{
				return $this->log[$high - 1];
			}
			elseif( is_string( $command ) && $command == 'yes' )
			{
				return $this->log;
			}
			else
			{
				return "Bad input parameter.  Index out of scope or command unknown.";
			}			
		}
		
		//If you send an actual #, the function will return that status index (not recommended)
		//If you send true, the function will return the last status entry
		//[If you send nothing, it does that too]
		//If you send the string 'yes', the function will return the whole damn status array to you. (REALLY not recommended)
		public function stat( $command = null )
		{
			$low = -1;
			$high = count( $this->status );
			if( is_int( $command ) && $low < $command && $command < $high)
			{
				return $this->status[$command];
			}
			elseif( (is_bool( $command ) && $command == true) || $command == null )
			{
				return $this->status[$high - 1];
			}
			elseif( is_string( $command ) && $command == 'yes' )
			{
				return $this->status;
			}
			else
			{
				return "Bad input parameter.  Index out of scope or command unknown.";
			}
		}
		
		//if you resize a 15Mpixel image and then don't call clean(), don't come
		//crying to me when PHP decides that you've run out of memory.
		public function clean()
		{
			imagedestroy($this->srcFh);
			imagedestroy($this->newIm);
			$this->logStat("CLEANED_HOUSE", true );
			return true;
		}
		
		private function allocColor($fh, $r, $g, $b, $a = 1)
		{
			$alp = 1 - $a;
			$alpha = $alp * 127;
			
			return imagecolorallocatealpha($fh, $r, $g, $b, $alpha);
		}
		
		private function logStat($msg, $bool)
		{
			//this updates the error and status arrays
			array_push($this->log, $msg);
			array_push($this->status, $bool);
		}
		
		private function makeImg($x, $y)
		{
			$this->newIm = imagecreatetruecolor($x, $y);
			imagealphablending($this->newIm, false);
			imagesavealpha($this->newIm, true);
			$this->logStat("CRT_EMPT_IMG_W/ALPHA", true );
		}
		
		private function createHandle()
		{
			switch( $this->origType )
			{
				case 1:
						$this->srcFh = imagecreatefromgif($this->source);
						break;
				case 2:
						$this->srcFh = imagecreatefromjpeg($this->source);
						break;
				case 3:
						$this->srcFh = imagecreatefrompng($this->source);
						break;
				default:
						$this->logStat("UNHANDLED_IMG_TYPE",false);
						break;
			}
			$this->logStat("CRT_IMG_FRM_".$this->origType, true );
		}
		
		//Fix for imagecopymerge not respecting alpha
		//http://www.php.net/manual/en/function.imagecopymerge.php#102844
		function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct, $trans = NULL)
		{
			$dst_w = imagesx($dst_im);
			$dst_h = imagesy($dst_im);

			// bounds checking
			$src_x = max($src_x, 0);
			$src_y = max($src_y, 0);
			$dst_x = max($dst_x, 0);
			$dst_y = max($dst_y, 0);
			if ($dst_x + $src_w > $dst_w)
			{
				$src_w = $dst_w - $dst_x;
			}
			
			if ($dst_y + $src_h > $dst_h)
			{
				$src_h = $dst_h - $dst_y;
			}

			for($x_offset = 0; $x_offset < $src_w; $x_offset++)
			{
				for($y_offset = 0; $y_offset < $src_h; $y_offset++)
				{
					// get source & dest color
					$srccolor = imagecolorsforindex($src_im, imagecolorat($src_im, $src_x + $x_offset, $src_y + $y_offset));
					$dstcolor = imagecolorsforindex($dst_im, imagecolorat($dst_im, $dst_x + $x_offset, $dst_y + $y_offset));

					// apply transparency
					if (is_null($trans) || ($srccolor !== $trans))
					{
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
						if (!imagesetpixel($dst_im, $dst_x + $x_offset, $dst_y + $y_offset, $color))
						{
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