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
use Mathematician\Number;

/**
 * BcMathTest
 *
 * @uses AbstractAdapterTest
 * @package Mathematician\Test\Adapter
 */
class BcMathTest extends AbstractAdapterTest
{

    protected function setUp()
    {
        parent::setUp();

        if (!Number::isBcMathAvailable()) {
            $this->markTestSkipped(
                'The BcMath extension is not available'
            );
        }
    }

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
     * @expectedException OutOfRangeException
     */
    public function testFactoryBaseDetectionOutOfRange()
    {
        BcMath::factory(0, 10000);
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

    public function testIsNegative()
    {
        $this->assertFalse(BcMath::factory(0)->isNegative());
        $this->assertFalse(BcMath::factory(-0)->isNegative());
        $this->assertFalse(BcMath::factory(1)->isNegative());
        $this->assertFalse(BcMath::factory(10)->isNegative());
        $this->assertFalse(BcMath::factory(PHP_INT_MAX)->isNegative());
        $this->assertTrue(BcMath::factory(-1)->isNegative());
        $this->assertTrue(BcMath::factory(-10)->isNegative());
        $this->assertTrue(BcMath::factory(-PHP_INT_MAX)->isNegative());
    }

    public function testIsWithinIntegerRange()
    {
        $this->assertTrue(BcMath::factory(0)->isWithinIntegerRange());
        $this->assertTrue(BcMath::factory(PHP_INT_MAX)->isWithinIntegerRange());
        $this->assertTrue(BcMath::factory(-PHP_INT_MAX)->isWithinIntegerRange());
        $this->assertTrue(BcMath::factory(~PHP_INT_MAX)->isWithinIntegerRange());

        $this->assertFalse(BcMath::factory('99999999999999999999999999999')->isWithinIntegerRange());
        $this->assertFalse(BcMath::factory('18446744073709551616')->isWithinIntegerRange());
        $this->assertFalse(BcMath::factory('-18446744073709551616')->isWithinIntegerRange());
    }

    public function testAbs()
    {
        $this->assertSame('100', BcMath::factory(-100)->abs()->toString());
        $this->assertSame('123213', BcMath::factory(-123213)->abs()->toString());
        $this->assertSame((string) PHP_INT_MAX, BcMath::factory(-PHP_INT_MAX)->abs()->toString());
    }

    public function testTwosComplement()
    {
        // Positive numbers should return the same number
        $this->assertSame('20', BcMath::factory(20)->twosComplement()->toString());
        $this->assertSame('84', BcMath::factory(84)->twosComplement()->toString());
        $this->assertSame('68', BcMath::factory(68)->twosComplement()->toString());

        // Negative numbers should flip bits and add a 1
        $this->assertSame('44', BcMath::factory(-20)->twosComplement()->toString());
        $this->assertSame('172', BcMath::factory(-84)->twosComplement()->toString());
        $this->assertSame('188', BcMath::factory(-68)->twosComplement()->toString());

        // Bit length mask
        $this->assertSame('101100', BcMath::factory(-20)->twosComplement(0)->toString(2));
        $this->assertSame('101100', BcMath::factory(-20)->twosComplement(-100)->toString(2));
        $this->assertSame('101100', BcMath::factory(-20)->twosComplement(2)->toString(2));
        $this->assertSame('11101100', BcMath::factory(-20)->twosComplement(8)->toString(2));
        $this->assertSame('1111111111101100', BcMath::factory(-20)->twosComplement(16)->toString(2));
        $this->assertSame('111111111111111111111111101100', BcMath::factory(-20)->twosComplement(30)->toString(2));
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
        $bcmath_a = BcMath::factory('0b101010101');
        $bcmath_b = BcMath::factory('-0b101010101');

        $this->assertSame('325', $bcmath_a->bitAnd('0b111000111')->toString());
        $this->assertSame('16', $bcmath_a->bitAnd('0b000111000')->toString());
        $this->assertSame('273', $bcmath_a->bitAnd('0b100111001')->toString());
        $this->assertSame('69', $bcmath_a->bitAnd('-0b110011001')->toString());

        $this->assertSame('131', $bcmath_b->bitAnd('0b111000111')->toString());
        $this->assertSame('40', $bcmath_b->bitAnd('0b000111000')->toString());
        $this->assertSame('41', $bcmath_b->bitAnd('0b100111001')->toString());
        $this->assertSame('-477', $bcmath_b->bitAnd('-0b110011001')->toString());

        $this->assertSame('1', $bcmath_a->bitAnd('0b1001')->toString());
        $this->assertSame('341', $bcmath_a->bitAnd('-0b1001')->toString());
        $this->assertSame('4', $bcmath_a->bitAnd('0b0110')->toString());
        $this->assertSame('336', $bcmath_a->bitAnd('-0b0110')->toString());
        $this->assertSame('21', $bcmath_a->bitAnd('0b111111000000111111')->toString());
        $this->assertSame('321', $bcmath_a->bitAnd('-0b111111000000111111')->toString());
        $this->assertSame('320', $bcmath_a->bitAnd('0b000000111111000000')->toString());
        $this->assertSame('64', $bcmath_a->bitAnd('-0b000000111111000000')->toString());

        $this->assertSame('9', $bcmath_b->bitAnd('0b1001')->toString());
        $this->assertSame('-349', $bcmath_b->bitAnd('-0b1001')->toString());
        $this->assertSame('2', $bcmath_b->bitAnd('0b0110')->toString());
        $this->assertSame('-342', $bcmath_b->bitAnd('-0b0110')->toString());
        $this->assertSame('258091', $bcmath_b->bitAnd('0b111111000000111111')->toString());
        $this->assertSame('-258431', $bcmath_b->bitAnd('-0b111111000000111111')->toString());
        $this->assertSame('3712', $bcmath_b->bitAnd('0b000000111111000000')->toString());
        $this->assertSame('-4096', $bcmath_b->bitAnd('-0b000000111111000000')->toString());
    }

    public function testBitOr()
    {
        $bcmath_a = BcMath::factory('0b101010101');
        $bcmath_b = BcMath::factory('-0b101010101');

        $this->assertSame('471', $bcmath_a->bitOr('0b111000111')->toString());
        $this->assertSame('381', $bcmath_a->bitOr('0b000111000')->toString());
        $this->assertSame('381', $bcmath_a->bitOr('0b100111001')->toString());
        $this->assertSame('-137', $bcmath_a->bitOr('-0b110011001')->toString());

        $this->assertSame('-17', $bcmath_b->bitOr('0b111000111')->toString());
        $this->assertSame('-325', $bcmath_b->bitOr('0b000111000')->toString());
        $this->assertSame('-69', $bcmath_b->bitOr('0b100111001')->toString());
        $this->assertSame('-273', $bcmath_b->bitOr('-0b110011001')->toString());

        $this->assertSame('349', $bcmath_a->bitOr('0b1001')->toString());
        $this->assertSame('-9', $bcmath_a->bitOr('-0b1001')->toString());
        $this->assertSame('343', $bcmath_a->bitOr('0b0110')->toString());
        $this->assertSame('-1', $bcmath_a->bitOr('-0b0110')->toString());
        $this->assertSame('258431', $bcmath_a->bitOr('0b111111000000111111')->toString());
        $this->assertSame('-258091', $bcmath_a->bitOr('-0b111111000000111111')->toString());
        $this->assertSame('4053', $bcmath_a->bitOr('0b000000111111000000')->toString());
        $this->assertSame('-3755', $bcmath_a->bitOr('-0b000000111111000000')->toString());

        $this->assertSame('-341', $bcmath_b->bitOr('0b1001')->toString());
        $this->assertSame('-1', $bcmath_b->bitOr('-0b1001')->toString());
        $this->assertSame('-337', $bcmath_b->bitOr('0b0110')->toString());
        $this->assertSame('-5', $bcmath_b->bitOr('-0b0110')->toString());
        $this->assertSame('-321', $bcmath_b->bitOr('0b111111000000111111')->toString());
        $this->assertSame('-21', $bcmath_b->bitOr('-0b111111000000111111')->toString());
        $this->assertSame('-21', $bcmath_b->bitOr('0b000000111111000000')->toString());
        $this->assertSame('-277', $bcmath_b->bitOr('-0b000000111111000000')->toString());
    }

    public function testBitXor()
    {
        $bcmath_a = BcMath::factory('0b101010101');
        $bcmath_b = BcMath::factory('-0b101010101');

        $this->assertSame('146', $bcmath_a->bitXor('0b111000111')->toString());
        $this->assertSame('365', $bcmath_a->bitXor('0b000111000')->toString());
        $this->assertSame('108', $bcmath_a->bitXor('0b100111001')->toString());
        $this->assertSame('-206', $bcmath_a->bitXor('-0b110011001')->toString());

        $this->assertSame('-148', $bcmath_b->bitXor('0b111000111')->toString());
        $this->assertSame('-365', $bcmath_b->bitXor('0b000111000')->toString());
        $this->assertSame('-110', $bcmath_b->bitXor('0b100111001')->toString());
        $this->assertSame('204', $bcmath_b->bitXor('-0b110011001')->toString());

        $this->assertSame('348', $bcmath_a->bitXor('0b1001')->toString());
        $this->assertSame('-350', $bcmath_a->bitXor('-0b1001')->toString());
        $this->assertSame('339', $bcmath_a->bitXor('0b0110')->toString());
        $this->assertSame('-337', $bcmath_a->bitXor('-0b0110')->toString());
        $this->assertSame('258410', $bcmath_a->bitXor('0b111111000000111111')->toString());
        $this->assertSame('-258412', $bcmath_a->bitXor('-0b111111000000111111')->toString());
        $this->assertSame('3733', $bcmath_a->bitXor('0b000000111111000000')->toString());
        $this->assertSame('-3819', $bcmath_a->bitXor('-0b000000111111000000')->toString());

        $this->assertSame('-350', $bcmath_b->bitXor('0b1001')->toString());
        $this->assertSame('348', $bcmath_b->bitXor('-0b1001')->toString());
        $this->assertSame('-339', $bcmath_b->bitXor('0b0110')->toString());
        $this->assertSame('337', $bcmath_b->bitXor('-0b0110')->toString());
        $this->assertSame('-258412', $bcmath_b->bitXor('0b111111000000111111')->toString());
        $this->assertSame('258410', $bcmath_b->bitXor('-0b111111000000111111')->toString());
        $this->assertSame('-3733', $bcmath_b->bitXor('0b000000111111000000')->toString());
        $this->assertSame('3819', $bcmath_b->bitXor('-0b000000111111000000')->toString());
    }

    public function testBitNot()
    {
        $this->assertSame('-342', BcMath::factory('0b101010101')->bitNot()->toString());
        $this->assertSame('-456', BcMath::factory('0b111000111')->bitNot()->toString());
        $this->assertSame('-57', BcMath::factory('0b000111000')->bitNot()->toString());
        $this->assertSame('-314', BcMath::factory('0b100111001')->bitNot()->toString());
        $this->assertSame('408', BcMath::factory('-0b110011001')->bitNot()->toString());
    }

    public function testBitShiftLeft()
    {
        $bcmath_a = BcMath::factory('0b101010101');

        $this->assertSame('682', $bcmath_a->bitShiftLeft(1)->toString());
        $this->assertSame('1364', $bcmath_a->bitShiftLeft(2)->toString());
        $this->assertSame('2728', $bcmath_a->bitShiftLeft(3)->toString());
        $this->assertSame('5456', $bcmath_a->bitShiftLeft(4)->toString());
    }

    public function testBitShiftRight()
    {
        $bcmath_a = BcMath::factory('0b101010101');

        $this->assertSame('170', $bcmath_a->bitShiftRight(1)->toString());
        $this->assertSame('85', $bcmath_a->bitShiftRight(2)->toString());
        $this->assertSame('42', $bcmath_a->bitShiftRight(3)->toString());
        $this->assertSame('21', $bcmath_a->bitShiftRight(4)->toString());
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

    public function testToStringBaseConversionWithNegative()
    {
        $decimal_integer = '-1234567890';

        $test_conversion_map = array(
            2 => '-1001001100101100000001011010010',
            8 => '-11145401322',
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
