<?php
/**
 * File containing the ezcLogWriterUnix class.
 *
 * @package EventLog
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
**/

/**
 * Writes the log messages to file in Unix style. 
 *
 * @package EventLog
 * @version //autogentag//
 * @copyright Copyright (C) 2005 eZ systems as. All rights reserved.
 * @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
**/
abstract class ezcLogWriterUnix extends ezcLogWriterFile
{
    /**
     * Write the logEntries to a file in the unix style.
     * 
     * @param string group 
     *      Group of the log data.
     * @param array logEntries  
     *      An array with strings specifying each column to be written.
     */
    public function writeLogEntry( $group, $logData )
    {
    }
}


?>
