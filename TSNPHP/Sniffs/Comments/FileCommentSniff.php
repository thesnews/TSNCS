<?php
// based on PEAR_Sniffs_Commenting_FileCommentSniff
class TSNPHP_Sniffs_Comments_FileCommentSniff implements PHP_CodeSniffer_Sniff
{

    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()


    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        $tokens = $phpcsFile->getTokens();

        // Find the next non whitespace token.
        $commentStart
            = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);

        // Allow declare() statements at the top of the file.
        if ($tokens[$commentStart]['code'] === T_DECLARE) {
            $semicolon = $phpcsFile->findNext(T_SEMICOLON, ($commentStart + 1));
            $commentStart
                = $phpcsFile->findNext(T_WHITESPACE, ($semicolon + 1), null, true);
        }

        // Ignore vim header.
        if ($tokens[$commentStart]['code'] === T_COMMENT) {
            if (strstr($tokens[$commentStart]['content'], 'vim:') !== false) {
                $commentStart = $phpcsFile->findNext(
                    T_WHITESPACE,
                    ($commentStart + 1),
                    null,
                    true
                );
            }
        }

        $errorToken = ($stackPtr + 1);
        if (isset($tokens[$errorToken]) === false) {
            $errorToken--;
        }

        if ($tokens[$commentStart]['code'] === T_CLOSE_TAG) {
            // We are only interested if this is the first open tag.
            return;
        } else if ($commentStart === false
            || $tokens[$commentStart]['code'] === T_DOC_COMMENT
        ) {
            $phpcsFile->addError('Missing NaturalDocs style', $errorToken, 'Missing');
            return;
        } else {

            // Extract the header comment docblock.
            $commentEnd = $phpcsFile->findNext(
                T_COMMENT,
                ($commentStart + 1),
                null,
                true
            );

            $commentEnd--;

            // Check if there is only 1 doc comment between the
            // open tag and class token.
            $nextToken   = array(
                            T_ABSTRACT,
                            T_CLASS,
                            T_FUNCTION,
                            T_COMMENT,
                           );

            $commentNext = $phpcsFile->findNext($nextToken, ($commentEnd + 1));
            if ($commentNext !== false
                && $tokens[$commentNext]['code'] !== T_COMMENT
            ) {
                // Found a class token right after comment doc block.
                $newlineToken = $phpcsFile->findNext(
                    T_WHITESPACE,
                    ($commentEnd + 1),
                    $commentNext,
                    false,
                    $phpcsFile->eolChar
                );

                if ($newlineToken !== false) {
                    $newlineToken = $phpcsFile->findNext(
                        T_WHITESPACE,
                        ($newlineToken + 1),
                        $commentNext,
                        false,
                        $phpcsFile->eolChar
                    );

                    if ($newlineToken === false) {
                        // No blank line between the class token and the doc block.
                        // The doc block is most likely a class comment.
                        $error = 'Missing file doc comment';
                        $phpcsFile->addError($error, $errorToken, 'Missing');
                        return;
                    }
                }
            }//end if

            $comment = $phpcsFile->getTokensAsString(
                $commentStart,
                ($commentEnd - $commentStart + 1)
            );

			$parser = new NDCommentParser($phpcsFile, $comment);

			if( !$parser->getTitle() ) {
				$phpcsFile->addError('Missing file doc Title', $stackPtr,
					'Missing');
				return;
			}
			
			if( !$parser->getGroup() ) {
				$phpcsFile->addError('Missing file doc Group', $stackPtr,
					'Missing');
				return;
			}
			
			if( !$parser->getVersion() ) {
				$phpcsFile->addError('Missing file doc Version', $stackPtr,
					'Missing');
				return;
			}
			
			if( !$parser->getCopyright() ) {
				$phpcsfile->addError('Missing file doc Copyright', $stackPtr,
					'Missing');
				return;
			}
			
			if( !$parser->getAuthor() ) {
				$phpcsFile->addError('Missing file doc Author', $stackPtr,
					'Missing');
				return;
			}

			if( !$parser->getLicense() ) {
				$phpcsFile->addError('missing file doc License', $stackPtr,
					'Missing');
				return;
			}
			
			
			// check the formatting
		}
		
		$title = $parser->getTitle();
		
		if( strpos($title, '\\') === false ||
			strpos($title, '.') !== false ) {
		
			$phpcsFile->addError('Invalid document Title ("'.$title.'"), must '.
				'be in the form of "namespace\class"', $stackPtr, 'Formatting');
		
		}
		
		$fileName = $parser->getFile();
		
		if( $fileName != basename($phpcsFile->getFilename()) ) {
			$phpcsFile->addError('Invalid File ("'.$fileName.'"), must be'.
				'name of the file itself', $stackPtr, 'Formatting');
		}
		
		$version = explode('.', $parser->getVersion());
		
		if( count($version) != 3 ||
			strlen($version[0]) != 4 ||
			strlen($version[1]) != 2 ||
			strlen($version[2]) != 2 ) {
		
			$phpcsFile->addError('Doc version must be in the form of '.
				'YYYY.MM.DD', $stackPtr, 'Formatting');
		}
		
		
		
		

    }


}//end class

?>
