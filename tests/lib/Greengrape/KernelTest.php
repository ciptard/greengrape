<?php
/**
 * Kernel test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Kernel;
use Greengrape\Config;

/**
 * Kernel Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class KernelTest extends \BaseTestCase
{
    /**
     * setUpBeforeClass
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        self::createConfigIni();
    }

    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        $config = new Config('testconfig.ini');

        $this->_object = new Kernel($config);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * tearDownAfterClass
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        self::deleteConfigIni();
    }

    /**
     * Test constructor
     *
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testConstructNoArgs()
    {
        $kernel = new Kernel();
    }

    public function testConstruct()
    {
        $config = new Config('testconfig.ini');

        $kernel = new Kernel($config);

        $this->assertTrue($kernel instanceof Kernel);
    }

    public function testSetConfig()
    {
        $config = array(
            'a' => 'b',
        );

        $this->_object->setConfig($config);

        $this->assertEquals('b', $this->_object->getConfig('a'));
        $this->assertEquals('fulcrum', $this->_object->getConfig('theme'));
    }

    public function testGetConfig()
    {
        $config = array(
            'a' => 'b',
        );

        $this->_object->setConfig($config);

        $config['theme'] = 'fulcrum';

        $this->assertEquals($config, $this->_object->getConfig());
        $this->assertNull($this->_object->getConfig('notsetvalue'));
    }

    public function testExecute()
    {
    }

    /**
     * createConfigIni
     *
     * @param string $contents
     * @return void
     */
    public static function createConfigIni($contents = '')
    {
        $filename = 'testconfig.ini';

        if ($contents == '') {
            $contents = "foo=bar\nenable_cache=false";
        }

        file_put_contents($filename, $contents);
    }

    /**
     * deleteConfigIni
     *
     * @return void
     */
    public static function deleteConfigIni()
    {
        $filename = 'testconfig.ini';
        unlink($filename);
    }
}