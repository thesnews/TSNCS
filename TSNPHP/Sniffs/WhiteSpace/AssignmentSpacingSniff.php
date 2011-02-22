<?php

// ensure only one space exists between a var and the assignment operator and
// the assignment operator and the value
class TSNPHP_Sniffs_WhiteSpace_AssignmentSpacingSniff implements PHP_CodeSniffer_Sniff {

	public function register() {
		return PHP_CodeSniffer_Tokens::$assignmentTokens;
	}
	
	public function process(PHP_CodeSniffer_File $file, $ptr) {
		$tokens = $file->getTokens();
		
		
		if( $tokens[($ptr - 1)]['code'] === T_WHITESPACE ) {
            if (strlen($tokens[($ptr - 1)]['content']) !== 1) {
				$found = strlen($tokens[($ptr - 1)]['content']);
				$error = 'Expected 1 space before "%s"; %s found';
				$data  = array(
						  '=',
						  $found,
						 );
				$file->addError($error, $ptr, 'SpacingBefore', $data);
            }

		}
	}

}

?>