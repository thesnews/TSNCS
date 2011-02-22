<?php
// based on Squiz_Sniffs_ControlStructures_ForEachLoopDeclarationSniff
/*
	removes check for whitespace before opening bracket
*/
class TSNPHP_Sniffs_ControlStructures_ForEachLoopDeclarationSniff implements PHP_CodeSniffer_Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_FOREACH);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $openingBracket = $phpcsFile->findNext(T_OPEN_PARENTHESIS, $stackPtr);
        $closingBracket = $tokens[$openingBracket]['parenthesis_closer'];

        $asToken = $phpcsFile->findNext(T_AS, $openingBracket);
        $content = $tokens[$asToken]['content'];
        if ($content !== strtolower($content)) {
            $expected = strtolower($content);
            $error    = 'AS keyword must be lowercase; expected "%s" but found "%s"';
            $data     = array(
                         $expected,
                         $content,
                        );
            $phpcsFile->addError($error, $stackPtr, 'AsNotLower', $data);
        }

        $doubleArrow = $phpcsFile->findNext(T_DOUBLE_ARROW, $openingBracket, $closingBracket);

        if ($doubleArrow !== false) {
            if ($tokens[($doubleArrow - 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space before "=>"; 0 found';
                $phpcsFile->addError($error, $stackPtr, 'NoSpaceBeforeArrow');
            } else {
                if (strlen($tokens[($doubleArrow - 1)]['content']) !== 1) {
                    $spaces = strlen($tokens[($doubleArrow - 1)]['content']);
                    $error  = 'Expected 1 space before "=>"; %s found';
                    $data   = array($spaces);
                    $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeArrow', $data);
                }

            }

            if ($tokens[($doubleArrow + 1)]['code'] !== T_WHITESPACE) {
                $error = 'Expected 1 space after "=>"; 0 found';
                $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterArrow');
            } else {
                if (strlen($tokens[($doubleArrow + 1)]['content']) !== 1) {
                    $spaces = strlen($tokens[($doubleArrow + 1)]['content']);
                    $error  = 'Expected 1 space after "=>"; %s found';
                    $data   = array($spaces);
                    $phpcsFile->addError($error, $stackPtr, 'SpacingAfterArrow', $data);
                }

            }

        }//end if

        if ($tokens[($asToken - 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space before "as"; 0 found';
            $phpcsFile->addError($error, $stackPtr, 'NoSpaceBeforeAs');
        } else {
            if (strlen($tokens[($asToken - 1)]['content']) !== 1) {
                $spaces = strlen($tokens[($asToken - 1)]['content']);
                $error  = 'Expected 1 space before "as"; %s found';
                $data   = array($spaces);
                $phpcsFile->addError($error, $stackPtr, 'SpacingBeforeAs', $data);
            }
        }

        if ($tokens[($asToken + 1)]['code'] !== T_WHITESPACE) {
            $error = 'Expected 1 space after "as"; 0 found';
            $phpcsFile->addError($error, $stackPtr, 'NoSpaceAfterAs');
        } else {
            if (strlen($tokens[($asToken + 1)]['content']) !== 1) {
                $spaces = strlen($tokens[($asToken + 1)]['content']);
                $error  = 'Expected 1 space after "as"; %s found';
                $data   = array($spaces);
                $phpcsFile->addError($error, $stackPtr, 'SpacingAfterAs', $data);
            }
        }

    }//end process()


}//end class

?>
