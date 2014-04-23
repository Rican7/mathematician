<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.0
 */

namespace Mathematician\Test;

use Mathematician\Number;

/**
 * NumberTest
 *
 * @uses AbstractMathematicianTest
 * @package Mathematician\Test
 */
class NumberTest extends AbstractMathematicianTest
{

    public function testFactory()
    {
        $result = Number::factory(PHP_INT_MAX);

        $this->assertInternalType('object', $result);
    }

    /**
     * @expectedException Mathematician\Exception\AdapterSupportException
     */
    public function testFactoryFailsWithBadAdapter()
    {
        Number::factory(123.0001);
    }

    public function testIsGmpAvailable()
    {
        $available = Number::isGmpAvailable();

        if (function_exists('gmp_init')) {
            $this->assertTrue($available);
        } else {
            $this->assertFalse($available);
        }
    }

    public function testIsBcMathAvailable()
    {
        $available = Number::isBcMathAvailable();

        if (function_exists('bcadd')) {
            $this->assertTrue($available);
        } else {
            $this->assertFalse($available);
        }
    }
}
