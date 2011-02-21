<?php

class NDCommentParser {

	private $rawString = false;
	
	private $tags = array(
		'File',
		'Title',
		'Group',
		'Version',
		'Copyright',
		'Author',
		'License',
		'Method',
		'Function',
		'Class',
		'Namespace',
		'Returns',
		'Parameter',
		'Parameters'
	);
	
	private $stack = array();

	private $phpcsFile = false;

	public function __construct($file, $str) {
	
		$this->phpcsFile = $file;
		$this->rawString = $str;
		
		$this->stack = array_combine($this->tags, array_fill(
			0, count($this->tags), array()));
	
		$this->parse();
	}
	
	public function parse() {
//		$lines = preg_split("/\\r|\\n|\\r\\n/", $this->rawString);
		$lines = explode($this->phpcsFile->eolChar, $this->rawString);
		
		$data = array();
		
		$inData = false;
		
		foreach( $lines as $line ) {
			if( $line == '/*' || $line == '*/' ) {
				continue;
			}
			
			if( strpos($line, ':') !== false ) {
				$kw = explode(':', trim($line));
				
				if( array_key_exists($kw[0], $this->stack) ) {
					if( $inData ) {
						$this->stack[$inData] = $data;
					}
				
					$inData = $kw[0];
					$data = array();
					
					if( $kw[1] ) {
						$data[] = $kw[1];
					}
					
					continue;
				}
			}
			
			if( $inData ) {
				$data[] = trim($line);
			}
			
		}
		
		if( $inData ) {
			$this->stack[$inData] = $data;
		}
	}
	
	public function __call($s, $v) {
		$prop = str_replace('get', '', $s);
		if( method_exists($this, 'handle'.$prop) ) {
			
			return call_user_func(array($this, 'handle'.$prop));
			
		} elseif( array_key_exists($prop, $this->stack) ) {
			
			return trim(implode("\n", $this->stack[$prop]));
			
		}
		
		return false;
	}
	
	public function handleFunction() {
		if( !count($this->stack['Function']) ) {
			return false;
		}
		return trim($this->stack['Function'][0]);
	}
	
	public function handleFunctionDescription() {
		if( !count($this->stack['Function']) ) {
			return false;
		}
		return trim(implode("\n", array_slice($this->stack['Function'], 1)));
	}

	public function handleMethod() {
		if( !count($this->stack['Method']) ) {
			return false;
		}
		return trim($this->stack['Method'][0]);
	}
	
	public function handleMethodDescription() {
		if( !count($this->stack['Method']) ) {
			return false;
		}
		return trim(implode("\n", array_slice($this->stack['Method'], 1)));
	}

	public function handleFile() {
		if( !count($this->stack['File']) ) {
			return false;
		}
		return trim($this->stack['File'][0]);
	}
	
	public function handleFileDescription() {
		if( !count($this->stack['File']) ) {
			return false;
		}
		return trim(implode("\n", array_slice($this->stack['File'], 1)));
	}
	

}

?>