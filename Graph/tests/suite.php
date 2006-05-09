<?php
/**
* ezcGraphSuite
*
* @package Graph
* @subpackage Tests
* @version //autogentag//
* @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
* @license LGPL {@link http://www.gnu.org/copyleft/lesser.html}
*/

/**
* Require test suite for Graph class.
*/
require_once 'graph_test.php';

/**
* Require test suite for ezcGraphColor class.
*/
require_once 'color_test.php';

/**
* Require test suite for ezcGraphChart class.
*/
require_once 'chart_test.php';

/**
* Test suite for ImageAnalysis package.
*
* @package ImageAnalysis
* @subpackage Tests
*/
class ezcGraphSuite extends ezcTestSuite
{
    public function __construct()
    {
        parent::__construct();
        $this->setName( "Graph" );

        $this->addTest( ezcGraphTest::suite() );
        $this->addTest( ezcGraphColorTest::suite() );
        $this->addTest( ezcGraphChartTest::suite() );
    }

    public static function suite()
    {
        return new ezcGraphSuite( "ezcGraphSuite" );
    }
}
?>
