<?php
namespace CurlManager\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;
use CurlManager\Controller\Component\CurlConnectionComponent;

/**
 * CurlManager\Controller\Component\CurlConnectionComponent Test Case
 */
class CurlConnectionComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \CurlManager\Controller\Component\CurlConnectionComponent
     */
    public $CurlConnection;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->CurlConnection = new CurlConnectionComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CurlConnection);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
