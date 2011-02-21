<?php
// based on PEAR_Sniffs_Commenting_FunctionCommentSniff
require_once dirname(__FILE__).'/../../commentParser.class.php';

class TSNPHP_Sniffs_Comments_FunctionCommentSniff implements PHP_CodeSniffer_Sniff
{

    public function register() {
        return array(T_FUNCTION);

    }//end register()


    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
        $find = array(
                 T_COMMENT,
                 T_DOC_COMMENT,
                 T_CLASS,
                 T_FUNCTION,
                 T_OPEN_TAG,
                );

        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1));

        if ($commentEnd === false) {
            return;
        }

        $tokens            = $phpcsFile->getTokens();


        // If the token that we found was a class or a function, then this
        // function has no doc comment.
        $code = $tokens[$commentEnd]['code'];

      	if ($code === T_DOC_COMMENT) {
            $error = 'You must use NaturalDocs style comments for a function '.
            	'comment';
            $phpcsFile->addError($error, $stackPtr, 'WrongStyle');
            return;
        }

        // If there is any code between the function keyword and the doc block
        // then the doc block is not for us.
        $ignore    = PHP_CodeSniffer_Tokens::$scopeModifiers;
        $ignore[]  = T_STATIC;
        $ignore[]  = T_WHITESPACE;
        $ignore[]  = T_ABSTRACT;
        $ignore[]  = T_FINAL;
        $prevToken = $phpcsFile->findPrevious($ignore, ($stackPtr - 1), null, true);
        if ($prevToken !== $commentEnd) {
//            $phpcsFile->addError('Missing function doc comment', $stackPtr, 'Missing');
            return;
        }


        // If the first T_OPEN_TAG is right before the comment, it is probably
        // a file comment.
        $commentStart = ($phpcsFile->findPrevious(T_COMMENT, ($commentEnd - 1), null, true) + 1);
        
        if( strpos($tokens[$commentStart]['content'], '//') !== false ) {
        	return;
        }
        
        $prevToken    = $phpcsFile->findPrevious(T_WHITESPACE, ($commentStart - 1), null, true);
        if ($tokens[$prevToken]['code'] === T_OPEN_TAG) {
            // Is this the first open tag?
            if ($stackPtr === 0 || $phpcsFile->findPrevious(T_OPEN_TAG, ($prevToken - 1)) === false) {
                $phpcsFile->addError('Missing function doc comment', $stackPtr, 'Missing');
                return;
            }
        }

        $comment = $phpcsFile->getTokensAsString($commentStart, ($commentEnd - $commentStart + 1));

		$parser = new NDCommentParser($phpcsFile, $comment);
		
		if( !$parser->getFunction() && !$parser->getMethod() ) {
			$phpcsFile->addError('Missing function doc title',
				$stackPtr, 'Missing');
			return;
		}
		
		if( !$parser->getFunctionDescription() &&
			!$parser->getMethodDescription() ) {
			$phpcsFile->addError('Missing function doc description',
				$stackPtr, 'Missing');
			return;
		}
		
		if( !$parser->getParameters() ) {
			$phpcsFile->addError('Missing function doc parameters', $stackPtr,
				'Missing');
			return;
		}
		
		if( !$parser->getReturns() ) {
			$phpcsFile->addError('Missing function doc returns', $stackPtr,
				'Missing');
			return;
		}
		
		if( $parser->getFunction() && !$parser->getNamespace() ) {
			$phpcsFile->addError('Missing function doc namespace',
				$stackPtr, 'Missing');
				
			return;
		}
		
    }//end processParams()


}//end class

?>
