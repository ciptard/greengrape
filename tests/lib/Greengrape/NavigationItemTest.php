<?php
/**
 * NavigationItem test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\NavigationItem;

/**
 * NavigationItem Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class NavigationItemTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        //$this->_object = new NavigationItem();
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
     * Test constructor
     *
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testConstructNoArgs()
    {
        $navigationItem = new NavigationItem();
    }
}