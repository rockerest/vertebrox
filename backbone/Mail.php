<?php
	class Mail{
		public static function sendMail( $to, $from, $subject, $body ){
			$headers = 'From: ' . $from . "\r\n";
			$headers .= 'Reply-To: ' . $from . "\r\n";
			$headers .= 'Return-Path: ' . $from . "\r\n";
			$headers .= 'Content-Type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";
			$headers .= 'MIME-Version: 1.0' . "\r\n";
			
			return mail($to, $subject, $body, $headers);
		}
		
		private $headers;
		private $recipients;
		private $sender;
		private $subject;
		private $body;
		
		public function __construct($to = null, $from = null, $subject = null, $body = null){
			$this->recipients = $this->headers = array();
			
			if( $to ){
				$this->addRecipient($to);
			}
			
			if( $from ){
				$this->setSender($from);
			}
			
			if( $subject ){
				$this->setSubject($subject);
			}
			
			if( $body ){
				$this->setBody($body);
			}
		}
		
		public function setHeader($key, $val){
			$this->headers[$key] = $val;
		}
		
		public function setDefaultHeaders(){
			$this->setHeader('From', $this->sender);
			$this->setHeader('Reply-To', $this->sender);
			$this->setHeader('Return-Path', $this->sender);
			$this->setHeader('Content-Type', 'text/html; charset=iso-8859-1');
			$this->setHeader('X-Mailer', 'PHP/' . phpversion());
			$this->setHeader('MIME-Version', '1.0');
		}
		
		public function addRecipient($to){
			array_push($this->recipients, $to);
		}
		
		public function setSender($from){
			$this->sender = $from;
			$this->setDefaultHeaders();
		}
		
		public function setSubject($sub){
			$this->subject = $sub;
		}
		
		public function setBody($body){
			$this->body = $body;
		}
		
		public function send($split = false){
			if( $this->allSet() ){
				$headers = '';
				$results = array();
				foreach( $this->headers as $k => $v ){
					$headers .= $k . ': ' . $v . "\r\n";
				}
				
				if( $split ){
					foreach( $this->recipients as $to ){
						array_push($results, mail($to, $this->subject, $this->body, $headers));
					}
				}
				else{
					$to = implode(',',$this->recipients);
					array_push($results, mail($to, $this->subject, $this->body, $headers));
				}
				
				return implode("\r\n\r\n", $results);
			}
			else{
				return false;
			}
		}
		
		private function allSet(){
			if( $this->headers && $this->recipients && $this->sender && $this->subject && $this->body ){
				return true;
			}
			else{
				return false;
			}
		}
	}
?>