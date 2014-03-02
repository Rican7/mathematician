<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.0
 */

namespace Mathematician\Test\Adapter;

use Exception;
use Mathematician\Adapter\Gmp;
use Mathematician\Test\AbstractMathematicianTest;

/**
 * GmpTest
 *
 * @uses AbstractMathematicianTest
 * @package Mathematician\Test\Adapter
 */
class GmpTest extends AbstractMathematicianTest
{

    protected function getTestGmpNumber()
    {
        return new Gmp(PHP_INT_MAX);
    }

    public function gmpProvider()
    {
        return array(
            array('18446744073709551616', new Gmp('18446744073709551616')),
            array('-1', new Gmp('-1')),
            array('4564564', new Gmp('4564564')),
        );
    }

    public function testIsGmpResource()
    {
        $this->assertTrue(Gmp::isGmpResource(gmp_init(0)));

        $this->assertFalse(Gmp::isGmpResource(tmpfile()));
        $this->assertFalse(Gmp::isGmpResource(1));
        $this->assertFalse(Gmp::isGmpResource('1'));
        $this->assertFalse(Gmp::isGmpResource((float) 1.0));
        $this->assertFalse(Gmp::isGmpResource(array()));
    }

    public function testFactory()
    {
        $this->assertTrue(Gmp::factory(PHP_INT_MAX) instanceof Gmp);
    }

    /**
     * @expectedException Mathematician\Exception\InvalidPrecisionException
     */
    public function testFactoryFailsWithAGivenScale()
    {
        Gmp::factory(PHP_INT_MAX, 2);
    }

    public function testConstructor()
    {
        $gmp = new Gmp(PHP_INT_MAX);

        $this->assertTrue($gmp instanceof Gmp);
    }

    public function testConstructorWithGmpResource()
    {
        $gmp = new Gmp(gmp_init('1337'));

        $this->assertTrue($gmp instanceof Gmp);
    }

    /**
     * @expectedException Mathematician\Exception\InvalidTypeException
     */
    public function testConstructorWithFloat()
    {
        $gmp = new Gmp((float) 1.337);
    }

    /**
     * @expectedException Mathematician\Exception\InvalidNumberException
     */
    public function testConstructorWithInvalidNumber()
    {
        $gmp = new Gmp('doge');
    }

    /**
     * @dataProvider gmpProvider
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
            '18446744073709551618', // Big int String
            new Gmp(5), // Another instance
            gmp_init(5), // A gmp resource
        );

        // Our instance
        $gmp = new Gmp(5);

        // Make sure we actually loop through each arg
        $loop_count = 0;

        foreach ($numbers as $num_arg) {
            // Create an assertion for each method
            // able to take a loose argument here

            $this->assertTrue($gmp->add($num_arg) instanceof Gmp);
            $this->assertTrue($gmp->sub($num_arg) instanceof Gmp);
            $this->assertTrue($gmp->mul($num_arg) instanceof Gmp);
            $this->assertTrue($gmp->div($num_arg) instanceof Gmp);

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
        $gmp = new Gmp(5);

        // Make sure we actually loop through each arg
        $loop_count = 0;

        foreach ($numbers as $num_arg) {
            // Create an assertion for each method
            // able to take a loose argument here

            try {
                $gmp->add($num_arg);
                $this->assertFalse(true);
            } catch (Exception $e) {
            }

            try {
                $gmp->sub($num_arg);
                $this->assertFalse(true);
            } catch (Exception $e) {
            }

            try {
                $gmp->mul($num_arg);
                $this->assertFalse(true);
            } catch (Exception $e) {
            }

            try {
                $gmp->div($num_arg);
                $this->assertFalse(true);
            } catch (Exception $e) {
            }

            $loop_count++;
        }

        $this->assertSame($loop_count, count($numbers));
    }

    /**
     * @dataProvider gmpProvider
     */
    public function testToStringMagic($string, $instance)
    {
        $this->assertSame($string, (string) $instance);
    }
}
