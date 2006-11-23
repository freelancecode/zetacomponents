<?php
/**
 * File containing the ezcQueryVariableParameterException class.
 *
 * @package Database
 * @version //autogentag//
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Exception thrown when a method does not receive the variables it requires.
 *
 * @package Database
 */
class ezcQueryVariableParameterException extends ezcQueryException
{
    /**
     * Constructs an ezcQueryVariableParameterException with the method $method
     * and the arguments $numProvided and $numExpected.
     *
     * @param string $method
     * @param int $numProvided
     * @param int $numExpected
     */
    public function __construct( $method, $numProvided, $numExpected )
    {
        $expectedString ="{$numExpected} parameter";
        if ( $numExpected > 1 )
        {
            $expectedString .= 's';
        }

        $providedString = "none were provided";
        if ( $numProvided == 1 )
        {
            $providedString = "only one was provided";
        }
        else if ( $numProvided > 1 )
        {
            $providedString = "only {$numProvided} was provided";
        }
        $info = "The method '{$method}' expected at least {$expectedString} but {$providedString}.";
        parent::__construct( $info );
    }
}
?>
