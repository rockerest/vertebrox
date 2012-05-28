<?php
	class Config
	{
		private $filename;
		protected $config;
		
		public function __construct( $root, $conf ){
			$this->root = $root;
			$this->conf = $conf;
			$this->loadConfig();
		}

		public function loadConfig(){
			$options = file( $this->conf, FILE_USE_INCLUDE_PATH);
			foreach( $options as $conf_entry ){
				eval($conf_entry);
			}

			$this->config = $config;
		}
		
		public function __get($var){
			if( !in_array($var, array('config','conf','configuration')) ){
				return $this->config[$var];
			}
			else{
				return $this->config;
			}
		}
	}