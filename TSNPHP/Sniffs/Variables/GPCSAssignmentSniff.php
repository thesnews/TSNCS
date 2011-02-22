<?php

// looks for assignments of _GET, _POST, _COOKIE and _SERVER properties to
// variables
class TSNPHP_Sniffs_Variables_GPCSAssignmentSniff implements PHP_CodeSniffer_Sniff {

	public function register() {
		return PHP_CodeSniffer_Tokens::$assignmentTokens;
	}		

	
	public function process(PHP_CodeSniffer_File $file, $ptr) {
		$tokens = $file->getTokens();
		
        $comparison = PHP_CodeSniffer_Tokens::$comparisonTokens;
        $operators  = PHP_CodeSniffer_Tokens::$operators;
        $assignment = PHP_CodeSniffer_Tokens::$assignmentTokens;

		$lineEnd = $file->findNext(T_WHITESPACE, $ptr, null, false, $file->eolChar);

		$next = $file->findNext(T_VARIABLE, $ptr, $lineEnd);
		
		$bad = array(
			'$_GET',
			'$_POST',
			'$_SERVER',
			'$_COOKIE',
			'$_REQUEST',
			'$_SESSION'
		);
		
		if( in_array($tokens[$next]['content'], $bad) ) {
			$file->addError('Direct assignment from superglobal is prohibited',
				$ptr);
		}
		
	}

}

?>