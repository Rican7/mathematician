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

/**
 * BcMathTest
 *
 * @uses AbstractAdapterTest
 * @package Mathematician\Test\Adapter
 */
class BcMathTest extends AbstractAdapterTest
{

    protected function getTestBcMathNumber()
    {
        return BcMath::factory(PHP_INT_MAX);
    }

    public function bcmathProvider()
    {
        return array(
            array('18446744073709551616', BcMath::factory('18446744073709551616')),
            array('-1', BcMath::factory('-1')),
            array('4564564', BcMath::factory('4564564')),
        );
    }

    public function testConstructor()
    {
        $bcmath = new BcMath(PHP_INT_MAX, 10);

        $this->assertTrue($bcmath instanceof BcMath);
    }

    public function testConstructorRemovesDecimalValues()
    {
        $bcmath = new BcMath(10.5, 10);

        $this->assertTrue($bcmath instanceof BcMath);

        $this->assertSame('10', $bcmath->getRawValue());
    }

    public function testFactory()
    {
        $this->assertTrue(BcMath::factory(PHP_INT_MAX) instanceof BcMath);
    }

    /**
     * @dataProvider numberSystemProvider
     */
    public function testFactoryWithRadix($radix, $value)
    {
        $bcmath = BcMath::factory($value, $radix);

        $this->assertTrue($bcmath instanceof BcMath);

        $this->assertSame('1234567890', $bcmath->getRawValue());
    }

    /**
     * @dataProvider numberBaseRepresentationProvider
     */
    public function testFactoryBaseDetection($radix, $value)
    {
        $bcmath = BcMath::factory($value);

        $this->assertTrue($bcmath instanceof BcMath);

        $this->assertSame('12345', $bcmath->getRawValue());
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
            BcMath::factory(5), // Another instance
        );

        // Our instance
        $bcmath = BcMath::factory(5);

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
        $bcmath = BcMath::factory(5);

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

    public function testCompareTo()
    {
        $bcmath_a = BcMath::factory(256);

        // Equals
        $this->assertSame(0, $bcmath_a->compareTo(256));

        // Less
        $this->assertLessThan(0, $bcmath_a->compareTo(300));

        // Greater
        $this->assertGreaterThan(0, $bcmath_a->compareTo(100));
    }

    public function testAdd()
    {
        $bcmath_a = BcMath::factory(100);

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
        $bcmath_a = BcMath::factory(100);

        // Positive arg and result
        $this->assertSame('98', $bcmath_a->sub(2)->toString());

        // Negative arg and positive result
        $this->assertSame('102', $bcmath_a->sub(-2)->toString());

        // Zero arg
        $this->assertSame('100', $bcmath_a->sub(0)->toString());
    }

    public function testMul()
    {
        $bcmath_a = BcMath::factory(2);

        // Positive arg and result
        $this->assertSame('4', $bcmath_a->mul(2)->toString());

        // Negative arg and result
        $this->assertSame('-20', $bcmath_a->mul(-10)->toString());

        // Zero arg
        $this->assertSame('0', $bcmath_a->mul(0)->toString());
    }

    public function testDiv()
    {
        $bcmath_a = BcMath::factory(20);

        // Positive arg and result
        $this->assertSame('10', $bcmath_a->div(2)->toString());

        // Negative arg and result
        $this->assertSame('-10', $bcmath_a->div(-2)->toString());
    }

    public function testPow()
    {
        $bcmath_a = BcMath::factory(2);

        // Positive arg and result
        $this->assertSame('256', $bcmath_a->pow(8)->toString());

        // Negative arg and zero result
        $this->assertSame('0', $bcmath_a->pow(-2)->toString());

        // Zero arg
        $this->assertSame('1', $bcmath_a->pow(0)->toString());
    }

    public function testPowMod()
    {
        $bcmath_a = BcMath::factory(2);

        // Positive arg and result
        $this->assertSame('6', $bcmath_a->powMod(8, 10)->toString());

        // Negative arg and zero result
        // TODO: Catch invalid negative powers when using powmod operation
        // $this->assertSame('0', $bcmath_a->powMod(-2, 10)->toString());

        // Zero arg
        // TODO: Don't allow 0 modulus param
        // $this->assertSame('0', $bcmath_a->powMod(0, 0)->toString());
    }

    public function testSqrt()
    {
        $bcmath_a = BcMath::factory(256);

        // Positive arg and result
        $this->assertSame('16', $bcmath_a->sqrt()->toString());
    }

    public function testMod()
    {
        $bcmath_a = BcMath::factory(256);

        // Positive arg and result
        $this->assertSame('6', $bcmath_a->mod(10)->toString());

        // Negative arg and zero result
        $this->assertSame('0', $bcmath_a->mod(-2)->toString());
    }

    public function testBitAnd()
    {
        $gmp_a = BcMath::factory(0b101010101);

        $this->assertSame('325', $gmp_a->bitAnd(0b111000111)->toString());
        $this->assertSame('16', $gmp_a->bitAnd(0b000111000)->toString());
        $this->assertSame('273', $gmp_a->bitAnd(0b100111001)->toString());
        $this->assertSame('69', $gmp_a->bitAnd(-0b110011001)->toString());
    }

    public function testBitOr()
    {
        $gmp_a = BcMath::factory(0b101010101);

        $this->assertSame('471', $gmp_a->bitOr(0b111000111)->toString());
        $this->assertSame('381', $gmp_a->bitOr(0b000111000)->toString());
        $this->assertSame('381', $gmp_a->bitOr(0b100111001)->toString());
        $this->assertSame('-137', $gmp_a->bitOr(-0b110011001)->toString());
    }

    public function testBitXor()
    {
        $gmp_a = BcMath::factory(0b101010101);

        $this->assertSame('146', $gmp_a->bitXor(0b111000111)->toString());
        $this->assertSame('365', $gmp_a->bitXor(0b000111000)->toString());
        $this->assertSame('108', $gmp_a->bitXor(0b100111001)->toString());
        $this->assertSame('-206', $gmp_a->bitXor(-0b110011001)->toString());
    }

    public function testBitNot()
    {
        $this->assertSame('-342', BcMath::factory(0b101010101)->bitNot()->toString());
        $this->assertSame('-456', BcMath::factory(0b111000111)->bitNot()->toString());
        $this->assertSame('-57', BcMath::factory(0b000111000)->bitNot()->toString());
        $this->assertSame('-314', BcMath::factory(0b100111001)->bitNot()->toString());
        $this->assertSame('408', BcMath::factory(-0b110011001)->bitNot()->toString());
    }

    public function testBitShiftLeft()
    {
        $gmp_a = BcMath::factory(0b101010101);

        $this->assertSame('682', $gmp_a->bitShiftLeft(1)->toString());
        $this->assertSame('1364', $gmp_a->bitShiftLeft(2)->toString());
        $this->assertSame('2728', $gmp_a->bitShiftLeft(3)->toString());
        $this->assertSame('5456', $gmp_a->bitShiftLeft(4)->toString());
    }

    public function testBitShiftRight()
    {
        $gmp_a = BcMath::factory(0b101010101);

        $this->assertSame('170', $gmp_a->bitShiftRight(1)->toString());
        $this->assertSame('85', $gmp_a->bitShiftRight(2)->toString());
        $this->assertSame('42', $gmp_a->bitShiftRight(3)->toString());
        $this->assertSame('21', $gmp_a->bitShiftRight(4)->toString());
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

        $bc_math = BcMath::factory($decimal_integer);

        foreach ($test_conversion_map as $radix => $string) {
            $this->assertSame($string, $bc_math->toString($radix));
        }
    }

    /**
     * @dataProvider bcmathProvider
     */
    public function testToStringMagic($string, $instance)
    {
        $this->assertSame($string, (string) $instance);
    }
}
