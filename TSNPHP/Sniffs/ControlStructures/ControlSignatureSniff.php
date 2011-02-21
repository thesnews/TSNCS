<?php
// based on PEAR_Sniffs_ControlStructures_ControlSignatureSniff

if (class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

class TSNPHP_Sniffs_ControlStructures_ControlSignatureSniff extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{


    /**
     * Constructs a PEAR_Sniffs_ControlStructures_ControlSignatureSniff.
     */
    public function __construct()
    {
        parent::__construct(true);

    }//end __construct()


    /**
     * Returns the patterns that this test wishes to verify.
     *
     * @return array(string)
     */
    protected function getPatterns()
    {
        return array(
                'do{EOL...} while( ... );EOL',
                'while( ... ) {EOL',
                'for( ... ) {EOL',
                'if( ...) ) {EOL',
                'if( ... ) {EOL',
                'foreach( ... ) {EOL',
                '} else if( ... ) {EOL',
                '} elseif( ...) ) {EOL',
                '} elseif( ... ) {EOL',
                '} else {EOL',
                'do {EOL',
               );

    }//end getPatterns()


}//end class

?>
