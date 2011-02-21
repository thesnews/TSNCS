<?php

class TSNPHP_Sniffs_Classes_ClassDeclarationSniff implements PHP_CodeSniffer_Sniff {

    public function register() {
		return array(
			T_CLASS,
			T_INTERFACE,
		);
		
    }

	public function process(PHP_CodeSniffer_File $file, $ptr) {
		$tokens = $file->getTokens();
		
        if (isset($tokens[$ptr]['scope_opener']) === false) {
            return;
        }

        $openingBrace = $tokens[$ptr]['scope_opener'];
		
		if( $tokens[$ptr]['line'] != $tokens[$openingBrace]['line'] ) {
            $error = 'Opening brace should be on the same line as the declaration';
            $file->addError($error, $openingBrace, 'BraceOnNewLine');
            return;
		}

	}

}


?>