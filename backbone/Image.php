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
				return new ImageCombine($this);
			}
			if( $var == "Draw" )
			{
				return new ImageDraw($this);
			}
			if( $var == "Manipulate" )
			{
				return new ImageManipulate($this);
			}
			if( $var == "Write" )
			{
				return new ImageWrite($this);
			}
			return $this->$var;
		}
		
		public function __set( $var, $val )
		{
			if( is_string($var) )
			{
				$val = array( $var => $val );
			}
			if( is_array($val) )
			{
				foreach( $val as $name => $content )
				{
					$this->$name = $content;
					if( $name == "source" )
					{
						$this->size();
						$this->loadImage();						
					}
					elseif( $name == "handle" )
					{
						$this->size();
					}
					elseif( $name == "type" )
					{
						$this->type = $this->Type($content, "int");
					}
				}
			}
		}
		
		public function __toString()
		{
			return $this->handle;
		}
		
		public function SafeSource($source)
		{
			// =====> THIS IS TOTALLY UNSAFE.
			//The user will have a source file that doesn't match his string representation.
			//I'm creating this ONLY so that returned Image objects match as closely as possible
			//	with the input Object.
			//If this proves to be a mistake, I'll leave it out.
			//After all, the new object coming back ISN'T the original, so maintaining that
			//	ubiquity is not very important.
			
			$this->source = $source;
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
		
		public function contentType()
		{
			return $this->Type($this->type, "string");
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
				if( $this->handle == null )
				{
					return false;
				}
				else
				{
					//leaves default file type as .png since .png rocks
					$this->width = imagesx($this->handle);
					$this->height = imagesy($this->handle);
				}
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
						$loadImg = imagecreatefromgif($this->source);
						break;
				case 2:
						$loadImg = imagecreatefromjpeg($this->source);
						break;
				case 3:
						$loadImg = imagecreatefrompng($this->source);
						break;
				default:
						break;
			}
			
			$this->newImage();
			if( $loadImg && imagecopyresampled($this->handle, $loadImg, 0, 0, 0, 0, $this->width, $this->height, $this->width, $this->height) )
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