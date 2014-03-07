<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.0
 */

namespace Mathematician\Test\Integer\Adapter;

use Exception;
use Mathematician\Integer\Adapter\BcMath;
use Mathematician\Test\AbstractMathematicianTest;

/**
 * BcMathTest
 *
 * @uses AbstractMathematicianTest
 * @package Mathematician\Test\Adapter
 */
class BcMathTest extends AbstractMathematicianTest
{

    protected function getTestBcMathNumber()
    {
        return new BcMath(PHP_INT_MAX);
    }

    public function bcmathProvider()
    {
        return array(
            array('18446744073709551616', new BcMath('18446744073709551616')),
            array('-1', new BcMath('-1')),
            array('4564564', new BcMath('4564564')),
        );
    }

    public function testFactory()
    {
        $this->assertTrue(BcMath::factory(PHP_INT_MAX) instanceof BcMath);
    }

    public function testConstructor()
    {
        $bcmath = new BcMath(PHP_INT_MAX);

        $this->assertTrue($bcmath instanceof BcMath);
    }

    public function testConstructorRemovesDecimalValues()
    {
        $bcmath = new BcMath(10.5);

        $this->assertTrue($bcmath instanceof BcMath);

        $this->assertSame('10', $bcmath->getRawValue());
    }

    /**
     * @dataProvider bcmathProvider
     */
    public function testToString($string, $instance)
    {
        $this->assertSame($string, $instance->toString());
    }

    public function testMethodsAcceptLooseNumericArguments()
    {
        // Our test number arguments
        $numbers = array(
            5, // int
            -5, // negative integer
            5.5, // float
            '18446744073709551618', // Big int String
            '18446744073709551618.18446744073709551618', // Big float String
            new BcMath(5), // Another instance
        );

        // Our instance
        $bcmath = new BcMath(5);

        // Make sure we actually loop through each arg
        $loop_count = 0;

        foreach ($numbers as $num_arg) {
            // Create an assertion for each method
            // able to take a loose argument here

            $this->assertTrue($bcmath->add($num_arg) instanceof BcMath);
            $this->assertTrue($bcmath->sub($num_arg) instanceof BcMath);
            $this->assertTrue($bcmath->mul($num_arg) instanceof BcMath);
            $this->assertTrue($bcmath->div($num_arg) instanceof BcMath);

            $loop_count++;
        }

        $this->assertSame($loop_count, count($numbers));
    }

    public function testMethodsFailsWithBadArguments()
    {
        // Our test number arguments
        $numbers = array(
            1.2, // float
            -1.2, // negative float
            '1.2', // float string
            'doge', // non-numeric string
        );

        // Our instance
        $bcmath = new BcMath(5);

        // Make sure we actually loop through each arg
        $loop_count = 0;

        foreach ($numbers as $num_arg) {
            // Create an assertion for each method
            // able to take a loose argument here

            try {
                $bcmath->add($num_arg);
                $this->assertFalse(true);
            } catch (Exception $e) {
            }

            try {
                $bcmath->sub($num_arg);
                $this->assertFalse(true);
            } catch (Exception $e) {
            }

            try {
                $bcmath->mul($num_arg);
                $this->assertFalse(true);
            } catch (Exception $e) {
            }

            try {
                $bcmath->div($num_arg);
                $this->assertFalse(true);
            } catch (Exception $e) {
            }

            $loop_count++;
        }

        $this->assertSame($loop_count, count($numbers));
    }

    public function testAdd()
    {
        $bcmath_a = new BcMath(100);

        // Positive arg and result
        $this->assertSame('102', $bcmath_a->add(2)->toString());

        // Negative arg and positive result
        $this->assertSame('98', $bcmath_a->add(-2)->toString());

        // Negative arg and result
        $this->assertSame('-50', $bcmath_a->add(-150)->toString());

        // Zero arg
        $this->assertSame('100', $bcmath_a->add(0)->toString());
    }

    public function testSub()
    {
        $bcmath_a = new BcMath(100);

        // Positive arg and result
        $this->assertSame('98', $bcmath_a->sub(2)->toString());

        // Negative arg and positive result
        $this->assertSame('102', $bcmath_a->sub(-2)->toString());

        // Zero arg
        $this->assertSame('100', $bcmath_a->sub(0)->toString());
    }

    public function testMul()
    {
        $bcmath_a = new BcMath(2);

        // Positive arg and result
        $this->assertSame('4', $bcmath_a->mul(2)->toString());

        // Negative arg and result
        $this->assertSame('-20', $bcmath_a->mul(-10)->toString());

        // Zero arg
        $this->assertSame('0', $bcmath_a->mul(0)->toString());
    }

    public function testDiv()
    {
        $bcmath_a = new BcMath(20);

        // Positive arg and result
        $this->assertSame('10', $bcmath_a->div(2)->toString());

        // Negative arg and result
        $this->assertSame('-10', $bcmath_a->div(-2)->toString());
    }

    /**
     * @dataProvider bcmathProvider
     */
    public function testToStringMagic($string, $instance)
    {
        $this->assertSame($string, (string) $instance);
    }
}
