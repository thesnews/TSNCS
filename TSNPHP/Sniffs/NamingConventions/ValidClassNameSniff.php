<?php

class TSNPHP_Sniffs_NamingConventions_ValidClassNameSniff implements PHP_CodeSniffer_Sniff {

    public function register() {
		return array(
			T_CLASS,
			T_INTERFACE,
		);
		
    }

	public function process(PHP_CodeSniffer_File $file, $ptr) {
		$tokens = $file->getTokens();
		
		$className = $file->findNext(T_STRING, $ptr);
		$name = $tokens[$className]['content'];
		
		// ensure first letter is lower and camelcased
		
        if( preg_match('|^[a-z]|', $name) === 0) {
            $error = '%s name must begin with a lower letter';
            $phpcsFile->addError($error, $stackPtr, 'StartWithCaptial', 	
            	'Format');
        }
        if( strpos($name, '_') !== false ) {
            $error = '%s cannot contain an underscore';
            $phpcsFile->addError($error, $stackPtr, 'StartWithCaptial', 	
            	'Format');
        }
	}

}


?>