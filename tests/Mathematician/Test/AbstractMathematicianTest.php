<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.2.0
 */

namespace Mathematician\Test;

use PHPUnit\Framework\TestCase;

/**
 * AbstractMathematicianTest
 *
 * Abstract base test class to make
 * unit testing less of a pain
 *
 * @uses PHPUnit_Framework_TestCase
 * @abstract
 * @package Mathematician\Test
 */
abstract class AbstractMathematicianTest extends TestCase
{

    /**
     * Properties
     */

    /**
     * The directory containing the test files
     *
     * @var string
     * @access private
     */
    private $tests_dir;


    /**
     * Methods
     */

    /**
     * Setup our test
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        // Get our tests directory
        $this->tests_dir = dirname($GLOBALS['__PHPUNIT_BOOTSTRAP']);
    }

    /**
     * Get the directory containing the test files
     *
     * @access protected
     * @return string
     */
    protected function getTestsDirectory()
    {
        return $this->tests_dir;
    }
}
