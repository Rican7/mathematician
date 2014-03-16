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
use Mathematician\Integer\Adapter\Gmp;
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
     * @dataProvider numberSystemProvider
     */
    public function testConstructorWithRadix($radix, $value)
    {
        $bcmath = new Gmp($value, $radix);

        $this->assertTrue($bcmath instanceof Gmp);

        $this->assertSame('1234567890', gmp_strval($bcmath->getRawValue(), 10));
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

    public function testCompareTo()
    {
        $gmp_a = new Gmp(256);

        // Equals
        $this->assertSame(0, $gmp_a->compareTo(256));

        // Less
        $this->assertLessThan(0, $gmp_a->compareTo(300));

        // Greater
        $this->assertGreaterThan(0, $gmp_a->compareTo(100));
    }

    public function testAdd()
    {
        $gmp_a = new Gmp(100);

        // Positive arg and result
        $this->assertSame('102', $gmp_a->add(2)->toString());

        // Negative arg and positive result
        $this->assertSame('98', $gmp_a->add(-2)->toString());

        // Negative arg and result
        $this->assertSame('-50', $gmp_a->add(-150)->toString());

        // Zero arg
        $this->assertSame('100', $gmp_a->add(0)->toString());
    }

    public function testSub()
    {
        $gmp_a = new Gmp(100);

        // Positive arg and result
        $this->assertSame('98', $gmp_a->sub(2)->toString());

        // Negative arg and positive result
        $this->assertSame('102', $gmp_a->sub(-2)->toString());

        // Zero arg
        $this->assertSame('100', $gmp_a->sub(0)->toString());
    }

    public function testMul()
    {
        $gmp_a = new Gmp(2);

        // Positive arg and result
        $this->assertSame('4', $gmp_a->mul(2)->toString());

        // Negative arg and result
        $this->assertSame('-20', $gmp_a->mul(-10)->toString());

        // Zero arg
        $this->assertSame('0', $gmp_a->mul(0)->toString());
    }

    public function testDiv()
    {
        $gmp_a = new Gmp(20);

        // Positive arg and result
        $this->assertSame('10', $gmp_a->div(2)->toString());

        // Negative arg and result
        $this->assertSame('-10', $gmp_a->div(-2)->toString());
    }

    public function testPow()
    {
        $gmp_a = new Gmp(2);

        // Positive arg and result
        $this->assertSame('256', $gmp_a->pow(8)->toString());

        // Negative arg and zero result
        // TODO: Fix warning
        // $this->assertSame('0', $gmp_a->pow(-2)->toString());

        // Zero arg
        $this->assertSame('1', $gmp_a->pow(0)->toString());
    }

    public function testPowMod()
    {
        $gmp_a = new Gmp(2);

        // Positive arg and result
        $this->assertSame('6', $gmp_a->powMod(8, 10)->toString());

        // Negative arg and zero result
        // TODO: Fix warning
        // $this->assertSame('0', $gmp_a->powMod(-2, 10)->toString());

        // Zero arg
        $this->assertSame('0', $gmp_a->powMod(0, 0)->toString());
    }

    public function testSqrt()
    {
        $gmp_a = new Gmp(256);

        // Positive arg and result
        $this->assertSame('16', $gmp_a->sqrt()->toString());
    }

    public function testMod()
    {
        $gmp_a = new Gmp(256);

        // Positive arg and result
        $this->assertSame('6', $gmp_a->mod(10)->toString());

        // Negative arg and zero result
        $this->assertSame('0', $gmp_a->mod(-2)->toString());
    }

    public function testToStringBaseConversion()
    {
        $decimal_integer = '1234567890';

        $test_conversion_map = array(
            2 => '1001001100101100000001011010010',
            3 => '10012001001112202200',
            8 => '11145401322',
            16 => '499602d2',
            32 => '14pc0mi',
            36 => 'kf12oi',
            37 => 'HTR1PR', // After base36, the chars should uppercase
            48 => '4eRCaI',
            62 => '1LY7VK',
        );

        $gmp = new Gmp($decimal_integer);

        foreach ($test_conversion_map as $radix => $string) {
            $this->assertSame($string, $gmp->toString($radix));
        }
    }

    /**
     * @dataProvider gmpProvider
     */
    public function testToStringMagic($string, $instance)
    {
        $this->assertSame($string, (string) $instance);
    }
}
