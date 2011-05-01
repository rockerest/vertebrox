<?php
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
		private $newXW;
		private $newYH;
		
		//future implementation of start points to crop
		//private $xP;
		//private $yP;
		
		public function __construct($src = null, $dest = null)
		{
			if( $src == null || $dest == null )
			{
				$this->logStat("SRC_or_DEST_!set", false);
			}
			elseif( $src != null && $dest != null )
			{
				$this->source = $src;
				$this->destination = $dest;
				$this->logStat("NEW_IMAGE_CREATED", true);
				$this->logStat("SRC_CREATED", true);
				$this->logStat("DEST_CREATED", true);
			}
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
					$this->newXW = $newWidth;
					$this->newYH = $newHeight;
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
			$this->logStat("OUTPUT_TO_TYPE:".$this->origType,true);
			return true;
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
	}
?>