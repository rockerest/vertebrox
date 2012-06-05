<?php
	class ImageBase{
		protected $caller;
		protected $handle;
		
		public function __construct($caller){
			$this->caller = $caller;
			$this->handle = $this->caller->handle;
		}
		
		protected function AllocateColor($r, $g, $b, $a = 1){
			$alp = 1 - $a;
			$alpha = $alp * 127;
			
			return imagecolorallocatealpha($this->handle, $r, $g, $b, $alpha);
		}
		
		protected function Type( $in, $forceReturn ){
			$found = 0;
			$GIF = array(	"gif",
							".gif",
							"image/gif"
						);
			
			$JPEG = array(	"jpg",
							"jpeg",
							".jpg",
							".jpeg",
							"image/jpeg"
						);
			
			$PNG = array(	"png",
							".png",
							"image/png"
						);
						
			if( is_string( $in ) ){
				if( in_array($in, $PNG) ){
					$found = IMAGETYPE_PNG;
				}
				elseif( in_array( $in, $GIF ) ){
					$found = IMAGETYPE_GIF;
				}
				elseif( in_array( $in, $JPEG ) ){
					$found = IMAGETYPE_JPEG;
				}
				else{
					$found = -1;
				}
			}
			
			if( is_integer($in) ){
				if( $in == 1 ){
					$found = IMAGETYPE_GIF;
				}
				elseif( $in == 2 ){
					$found = IMAGETYPE_JPEG;
				}
				elseif( $in == 3 ){
					$found = IMAGETYPE_PNG;
				}
				else{
					$found = -1;
				}
			}
			
			if( $forceReturn == 'int' ){
				return $found;
			}
			elseif( $forceReturn == 'string' ){
				if( $in == 1 ){
					return "image/gif";
				}
				elseif( $in == 2 ){
					return "image/jpeg";
				}
				elseif( $in == 3 ){
					return "image/png";
				}
				else{
					return null;
				}
			}
		}
	}
?>