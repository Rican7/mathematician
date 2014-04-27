<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.1
 */

namespace Mathematician\Test\Integer\Adapter;

use Mathematician\Test\AbstractMathematicianTest;

/**
 * AbstractAdapterTest
 *
 * @uses AbstractMathematicianTest
 * @abstract
 * @package Mathematician\Test\Integer\Adapter
 */
abstract class AbstractAdapterTest extends AbstractMathematicianTest
{

    /**
     * Test Helpers
     */

    public function numberSystemProvider()
    {
        // All equal to integer: 1234567890
        return array(
            array(2, '1001001100101100000001011010010'),
            array(3, '10012001001112202200'),
            array(8, '11145401322'),
            array(16, '499602d2'),
            array(32, '14pc0mi'),
            array(36, 'kf12oi'),
            array(37, 'HTR1PR'), // After base36, the chars should uppercase
            array(48, '4eRCaI'),
            array(62, '1LY7VK'),
        );
    }

    public function numberBaseRepresentationProvider()
    {
        // All equal to integer: 12345
        return array(
            array(2, '0b11000000111001'),
            array(8, '030071'),
            array(10, '12345'),
            array(16, '0x3039'),
            array(16, '0X3039'),
        );
    }

    /**
     * Get an instance of the integer adapter being tested
     *
     * @param mixed $value
     * @param int $radix
     * @abstract
     * @access protected
     * @return AbstractAdapter
     */
    abstract protected function factory($value, $radix = null);
}
