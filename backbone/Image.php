<?php
	require_once('Color.php');
	
	//include Image libraries
	require_once('ImageBase.php');
	require_once('ImageCombine.php');
	require_once('ImageDraw.php');
	require_once('ImageManipulate.php');	
	require_once('ImageWrite.php');

	class Image extends ImageBase
	{
		private $Write;
		private $Manipulate;
		private $Draw;
		private $Combine;
		
		protected $height;
		protected $width;
		protected $type;
		protected $handle;
		
		protected $source;
		protected $destination;
		
		public function __construct( $width = 400, $height = 300, $type = "png" )
		{
			$this->width = $width;
			$this->height = $height;
			
			$num = $this->Type($type, 'int');
			if( $num > 0 )
			{
				$this->type = $num;
			}
			else
			{
				return false;
			}
			
			$this->newImage();
			
			return true;
		}
		
		public function __get( $var )
		{
			if( $var == "Combine" )
			{
				return new ImageCombine(&$this);
			}
			if( $var == "Draw" )
			{
				return new ImageDraw(&$this);
			}
			if( $var == "Manipulate" )
			{
				return new ImageManipulate(&$this);
			}
			if( $var == "Write" )
			{
				return new ImageWrite(&$this);
			}
			return $this->$var;
		}
		
		public function __set( $var, $val )
		{
			if( is_string($var) )
			{
				$var = array( $var => $val );
			}
			if( is_array($var) )
			{
				foreach( $var as $name => $content )
				{
					$this->$name = $content;
					if( $name == "source" )
					{
						$this->size();
						$this->loadImage();						
					}
				}
			}
		}
		
		public function __toString()
		{
			return $this->handle;
		}
		
		public function output()
		{
			$dest = isset( $this->destination );
			
			switch( $this->type )
			{
				case 1:
						if( $dest )
						{
							imagegif( $this->handle, $this->destination );
						}
						else
						{
							imagegif( $this->handle );
						}
						break;
				case 2:
						if( $dest )
						{
							imagejpeg( $this->handle, $this->destination, 100 );
						}
						else
						{
							imagejpeg( $this->handle, null, 100 );
						}						
						break;
				case 3:
						if( $dest )
						{
							imagepng( $this->handle, $this->destination, 0 );
						}
						else
						{
							imagepng( $this->handle, null, 0, PNG_NO_FILTER );
						}
						break;
				default:
						return false;
						break;
			}
			
			return true;
		}
		
		public function copy()
		{
			$im = new Image();
			$im->handle = $this->handle;
			return $im;
		}

		public function check()
		{
			if( !isset($this->destination) )
			{
				return false;
			}
			else
			{
				$res = file_exists( $this->destination );
				return $res;
			}
		}
		
		//if you resize a 15Mpixel image and then don't call clean(), don't come
		//crying to me when PHP decides that you've run out of memory.
		public function clean()
		{
			imagedestroy($this->handle);
			return true;
		}
		
		private function size()
		{
			if( $this->source == null )
			{
				return false;
			}
			else
			{
				$info = getimagesize($this->source);
				if( $info[0] != 0 && $info[1] != 0 )
				{
					$this->width = $info[0];
					$this->height = $info[1];
					$this->type = $info[2];
					return true;
				}
				else
				{
					return false;
				}
			}		
		}
		
		private function newImage()
		{
			$this->handle = imagecreatetruecolor($this->width, $this->height);
			imagealphablending($this->handle, false);
			imagesavealpha($this->handle, true);
		}
		
		private function loadImage()
		{
			switch( $this->type )
			{
				case 1:
						$this->handle = imagecreatefromgif($this->source);
						break;
				case 2:
						$this->handle = imagecreatefromjpeg($this->source);
						break;
				case 3:
						$this->handle = imagecreatefrompng($this->source);
						break;
				default:
						break;
			}
			return true;
		}
	}
?>