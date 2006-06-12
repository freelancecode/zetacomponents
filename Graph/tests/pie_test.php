<?php
/**
 * ezcGraphPieChartTest 
 * 
 * @package Graph
 * @version //autogen//
 * @subpackage Tests
 * @copyright Copyright (C) 2005, 2006 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Tests for ezcGraph class.
 * 
 * @package ImageAnalysis
 * @subpackage Tests
 */
class ezcGraphPieChartTest extends ezcTestCase
{

    protected $basePath;

    protected $tempDir;

	public static function suite()
	{
		return new ezcTestSuite( "ezcGraphPieChartTest" );
	}

    /**
     * setUp 
     * 
     * @access public
     */
    public function setUp()
    {
        static $i = 0;
        $this->tempDir = $this->createTempDir( 'ezcGraphGdDriverTest' . sprintf( '_%03d_', ++$i ) ) . '/';
        $this->basePath = dirname( __FILE__ ) . '/data/';
    }

    /**
     * tearDown 
     * 
     * @access public
     */
    public function tearDown()
    {
        $this->removeTempDir();
    }

    public function testElementGenerationLegend()
    {
        $chart = ezcGraph::create( 'Pie' );
        $chart->sampleData = array( 'sample 1' => 234, 'sample 2' => 21, 'sample 3' => 324, 'sample 4' => 120, 'sample 5' => 1);
        $chart->render( 500, 200 );
        
        $legend = $this->getNonPublicProperty( $chart->legend, 'labels' );

        $this->assertEquals(
            5,
            count( $legend ),
            'Count of legends items should be <5>'
        );

        $this->assertEquals(
            'sample 1',
            $legend[0]['label'],
            'Label of first legend item should be <sample 1>.'
        );

        $this->assertEquals(
            ezcGraphColor::fromHex( '#CC0000' ),
            $legend[1]['color'],
            'Default color for single label is wrong.'
        );

        $this->assertEquals(
            ezcGraphColor::fromHex( '#EDD400' ),
            $legend[2]['color'],
            'Special color for single label is wrong.'
        );
    }

    public function testPieRenderPieSegments()
    {
        $chart = ezcGraph::create( 'Pie' );
        $chart->sample = array(
            'Mozilla' => 4375,
            'IE' => 345,
            'Opera' => 1204,
            'wget' => 231,
            'Safari' => 987,
        );

        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2D', array(
            'drawPieSegment',
        ) );

        $mockedRenderer
            ->expects( $this->at( 0 ) )
            ->method( 'drawPieSegment' )
            ->with(
                $this->equalTo( ezcGraphColor::fromHex( '#4E9A06' ) ),
                $this->equalTo( new ezcGraphCoordinate( 240, 100 ) ),
                $this->equalTo( 100 ),
                $this->equalTo( 0 ),
                $this->equalTo( 220.52646317558 ),
                $this->equalTo( 0 )
            );

        $mockedRenderer
            ->expects( $this->at( 1 ) )
            ->method( 'drawPieSegment' )
            ->with(
                $this->equalTo( ezcGraphColor::fromHex( '#4E9A06' ) ),
                $this->equalTo( new ezcGraphCoordinate( 240, 100 ) ),
                $this->equalTo( 100 ),
                $this->equalTo( 220.52646317558 ),
                $this->equalTo( 0 ),
                $this->equalTo( 0 )
            );

        $chart->renderer = $mockedRenderer;
        $chart->render( 400, 200 );
    }

    public function testPieRenderPieLables()
    {
        $chart = ezcGraph::create( 'Pie' );
        $chart->sample = array(
            'Mozilla' => 4375,
            'IE' => 345,
            'Opera' => 1204,
            'wget' => 231,
            'Safari' => 987,
        );

        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2D', array(
            'drawTextBox',
        ) );

        $mockedRenderer
            ->expects( $this->at( 5 ) )
            ->method( 'drawTextBox' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 80, 0 ) ),
                $this->equalTo( 'Opera: 1204 (16.9%)' ),
                $this->equalTo( 95 ),
                $this->equalTo( 30 ),
                $this->equalTo( ezcGraph::RIGHT | ezcGraph::MIDDLE )
            );
        $mockedRenderer
            ->expects( $this->at( 6 ) )
            ->method( 'drawTextBox' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 80, 30 ) ),
                $this->equalTo( 'IE: 345 (4.8%)' ),
                $this->equalTo( 64 ),
                $this->equalTo( 30 ),
                $this->equalTo( ezcGraph::RIGHT | ezcGraph::MIDDLE )
            );
        $mockedRenderer
            ->expects( $this->at( 7 ) )
            ->method( 'drawTextBox' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 80, 134 ) ),
                $this->equalTo( 'Mozilla: 4375 (61.3%)' ),
                $this->equalTo( 61 ),
                $this->equalTo( 30 ),
                $this->equalTo( ezcGraph::RIGHT | ezcGraph::MIDDLE )
            );
        $mockedRenderer
            ->expects( $this->at( 8 ) )
            ->method( 'drawTextBox' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 321, 13 ) ),
                $this->equalTo( 'wget: 231 (3.2%)' ),
                $this->equalTo( 79 ),
                $this->equalTo( 30 ),
                $this->equalTo( ezcGraph::LEFT | ezcGraph::MIDDLE )
            );
        $mockedRenderer
            ->expects( $this->at( 9 ) )
            ->method( 'drawTextBox' )
            ->with(
                $this->equalTo( new ezcGraphCoordinate( 347, 54 ) ),
                $this->equalTo( 'Safari: 987 (13.8%)' ),
                $this->equalTo( 53 ),
                $this->equalTo( 30 ),
                $this->equalTo( ezcGraph::LEFT | ezcGraph::MIDDLE )
            );

        $chart->renderer = $mockedRenderer;
        $chart->render( 400, 200 );
    }

    public function testPieRenderPieLableIdentifiers()
    {
        $chart = ezcGraph::create( 'Pie' );
        $chart->sample = array(
            'Mozilla' => 4375,
            'IE' => 345,
            'Opera' => 1204,
            'wget' => 231,
            'Safari' => 987,
        );

        $mockedRenderer = $this->getMock( 'ezcGraphRenderer2D', array(
            'drawLine',
            'drawSymbol',
        ) );

        $mockedRenderer
            ->expects( $this->at( 5 ) )
            ->method( 'drawLine' )
            ->with(
                $this->equalTo( ezcGraphColor::fromHex( '#2E3436' ) ),
                $this->equalTo( new ezcGraphCoordinate( 238, 33 ) ),
                $this->equalTo( new ezcGraphCoordinate( 181, 15 ) ),
                $this->equalTo( false )
            );
        $mockedRenderer
            ->expects( $this->at( 6 ) )
            ->method( 'drawSymbol' )
            ->with(
                $this->equalTo( ezcGraphColor::fromHex( '#2E3436' ) ),
                $this->equalTo( new ezcGraphCoordinate( 235, 30 ) ),
                $this->equalTo( 6 ),
                $this->equalTo( 6 ),
                $this->equalTo( ezcGraph::BULLET )
            );
        $mockedRenderer
            ->expects( $this->at( 7 ) )
            ->method( 'drawSymbol' )
            ->with(
                $this->equalTo( ezcGraphColor::fromHex( '#2E3436' ) ),
                $this->equalTo( new ezcGraphCoordinate( 178, 12 ) ),
                $this->equalTo( 6 ),
                $this->equalTo( 6 ),
                $this->equalTo( ezcGraph::BULLET )
            );

        $chart->renderer = $mockedRenderer;
        $chart->render( 400, 200 );
    }

    public function testCompleteRendering()
    {
        $filename = $this->tempDir . __FUNCTION__ . '.png';

        $chart = ezcGraph::create( 'Pie' );

        $chart->sample = array(
            'Mozilla' => 4375,
            'IE' => 345,
            'Opera' => 1204,
            'wget' => 231,
            'Safari' => 987,
        );

        $chart->driver = new ezcGraphGdDriver();
        $chart->options->font = $this->basePath . 'font.ttf';
        $chart->render( 400, 200, $filename );

        $this->assertTrue(
            file_exists( $filename ),
            'No image was generated.'
        );

        $this->assertEquals(
            'b1de7ecb51784885c11d86091489d12d',
            md5_file( $filename ),
            'Incorrect image rendered.'
        );
    }
}
?>
