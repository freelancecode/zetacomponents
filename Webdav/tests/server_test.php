<?php
/**
 * Basic test cases for the server class.
 *
 * @package Webdav
 * @subpackage Tests
 * @version //autogentag//
 * @copyright Copyright (C) 2005-2007 eZ systems as. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 */

/**
 * Reqiuire base test
 */
require_once 'test_case.php';

/**
 * Additional transport for testing. 
 */
require_once 'classes/transport_test_mock.php';

/**
 * Tests for ezcWebdavServer class.
 * 
 * @package Webdav
 * @subpackage Tests
 */
class ezcWebdavBasicServerTest extends ezcWebdavTestCase
{
	public static function suite()
	{
		return new PHPUnit_Framework_TestSuite( 'ezcWebdavBasicServerTest' );
	}

    public function testSingleton()
    {
        $srv = ezcWebdavServer::getInstance();
        $srv2 = ezcWebdavServer::getInstance();

        $this->assertSame( $srv, $srv2 );
    }

    public function testCtor()
    {
        $srv = ezcWebdavServer::getInstance();

        $this->assertAttributeEquals(
            array(
                'transport'      => null,
                'backend'        => null,
                'transports'     => new ezcWebdavTransportDispatcher(),
                'pluginRegistry' => new ezcWebdavPluginRegistry(),
            ),
            'properties',
            $srv
        );
    }

    public function testGetPropertiesDefaultSuccess()
    {
        $srv = ezcWebdavServer::getInstance();

        $defaults = array(
            'transport'      => null,
            'backend'        => null,
            'transports'     => new ezcWebdavTransportDispatcher(),
            'pluginRegistry' => new ezcWebdavPluginRegistry(),
        );

        foreach ( $defaults as $property => $value )
        {
            $this->assertEquals(
                $value,
                $srv->$property,
                "Property $property has incorrect default."
            );
        }
    }

    public function testGetPropertiesFailure()
    {
        $srv = ezcWebdavServer::getInstance();

        try
        {
            echo $srv->foo;
        }
        catch ( ezcBasePropertyNotFoundException $e )
        {
            return;
        }
        $this->fail( 'Property not thrown on get access of non-existent property.' );
    }

    public function testSetPropertiesGetPropertiesSuccess()
    {
        $srv = ezcWebdavServer::getInstance();

        $setValues = array(
            'transports'     => new ezcWebdavTransportDispatcher(),
            'backend'        => new ezcWebdavMemoryBackend(),
        );
        $checkValues = array(
            'transport'      => null,
            'backend'        => new ezcWebdavMemoryBackend(),
            'transports'     => new ezcWebdavTransportDispatcher(),
            'pluginRegistry' => new ezcWebdavPluginRegistry(),
        );

        foreach( $setValues as $property => $value )
        {
            $srv->$property = $value;
        }

        $this->assertAttributeEquals(
            $checkValues,
            'properties',
            $srv
        );

        foreach ( $checkValues as $property => $value )
        {
            $this->assertEquals(
                $value,
                $srv->$property,
                "Property $property has incorrect value after ctor setting."
            );
        }
    }

    public function testSetAccessFailure()
    {
        $typicalFails = array(
            '',
            23,
            23.42,
            true,
            false,
            array(),
            new stdClass(),
        );

        $invalidValues = array(
            'transports' => $typicalFails, 
            'backend'    => $typicalFails, 
        );

        foreach ( $invalidValues as $propertyName => $propertyValues )
        {
            $this->assertSetPropertyFailure( $propertyName, $propertyValues, 'ezcBaseValueException' );
        }

        try
        {
            $srv = ezcWebdavServer::getInstance();
            $srv->pluginRegistry = 23;
            $this->fail( 'Exception not thrown on set access to read-only property.' );
        }
        catch ( ezcBasePropertyPermissionException $e ){}

        try
        {
            $srv = ezcWebdavServer::getInstance();
            $srv->transport = 23;
            $this->fail( 'Exception not thrown on set access to read-only property.' );
        }
        catch ( ezcBasePropertyPermissionException $e ){}

        try
        {
            $srv = ezcWebdavServer::getInstance();
            $srv->fooBar = 23;
            $this->fail( 'Exception not thrown on set access to non-existent property.' );
        }
        catch ( ezcBasePropertyNotFoundException $e ){}
    }

    public function testPropertiesIssetAccessDefaultCtorSuccess()
    {
        $srv = ezcWebdavServer::getInstance();

        $properties =array(
            'transports', 
            'backend', 
            'pluginRegistry', 
            'transport', 
        );

        foreach( $properties as $propertyName )
        {
            $this->assertTrue(
                isset( $srv->$propertyName ),
                "Property not set after default construction: '$propertyName'."
            );
        }
    }

    public function testPropertyIssetAccessFailure()
    {
        $srv = ezcWebdavServer::getInstance();

        $this->assertFalse(
            isset( $srv->foo ),
            'Non-existent property $foo seems to be set.'
        );
        $this->assertFalse(
            isset( $srv->properties ),
            'Non-existent property $properties seems to be set.'
        );
    }

    public function testDefaultHandlerWithUnknowClient()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'ezcUnknownClient';

        $webdav  = ezcWebdavServer::getInstance();
        $backend = new ezcWebdavMemoryBackend();

        ob_start();

        $webdav->handle( $backend );

        $body = ob_get_clean();
    }

    public function testDefaultHandlerWithUnknowClientAdditionalHandler()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'ezcUnknownClient';
        $_SERVER['REQUEST_METHOD']  = 'OPTIONS';

        $webdav  = ezcWebdavServer::getInstance();
        $webdav->transports->insertBefore(
            new ezcWebdavTransportConfiguration(
                '(.*SomeAgent.*)',
                'ezcWebdavTransportTestMock'
            )
        );

        $backend = new ezcWebdavMemoryBackend();

        ob_start();

        $webdav->handle( $backend );

        $body = ob_get_clean();
    }

    protected function assertSetPropertyFailure( $propertyName, array $propertyValues, $exceptionClass )
    {
        foreach ( $propertyValues as $value )
        {
            try
            {
                $srv = ezcWebdavServer::getInstance();
                $srv->$propertyName = $value;
                $this->fail( "Exception not thrown on invalid ___set() value for property '$propertyName'." );
            }
            catch( Exception $e )
            {
                $this->assertTrue(
                    ( $e instanceof $exceptionClass ),
                    "Exception thrown on invalid value set for property '$propertyName'. '" . get_class( $e ) . "' instead of '$exceptionClass'."
                );
            }
        }
    }
}
?>
