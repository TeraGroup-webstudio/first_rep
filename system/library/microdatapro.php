<?php
class MicrodataPro {

private $data = array();

	public function opencart_version($d){
		
		$opencart_version = explode(".", VERSION);
		
		return $opencart_version[$d];
	}
	
	public function clear_array($data_array) {
		if(isset($data_array) and is_array($data_array)){
			foreach($data_array as $key => $value){
				$data_array[$key] = $this->clear($value);
			}
		}
		
		return $data_array; 
	}
	
	public function clear($text = '', $decode = false) {
		if(is_string($text)){
			if($decode) $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
			$text = str_replace("><","> <",$text);
			if($text) $text = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', ' ', strip_tags((string)$text));
			$text = str_replace(array(PHP_EOL, "\r\n", "\r", "\n", "\t", '  ', '    ', '    ', '&nbsp;'), ' ', $text);
			$text = str_replace('"', "'", $text);
			$text = str_replace("'", " ", $text);
			$text = str_replace("\\", " ", $text);
			$text = str_replace('&quot;', " ", $text);
			$text = str_replace("\"", " ", $text);			
		}
		return $text;
	}
	
}
?>