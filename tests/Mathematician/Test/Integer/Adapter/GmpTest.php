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
use Mathematician\Number;

/**
 * GmpTest
 *
 * @uses AbstractAdapterTest
 * @package Mathematician\Test\Adapter
 */
class GmpTest extends AbstractAdapterTest
{

    protected function setUp()
    {
        parent::setUp();

        if (!Number::isGmpAvailable()) {
            $this->markTestSkipped(
                'The GMP extension is not available'
            );
        }
    }

    protected function getTestGmpNumber()
    {
        return Gmp::factory(PHP_INT_MAX);
    }

    public function gmpProvider()
    {
        return array(
            array('18446744073709551616', Gmp::factory('18446744073709551616')),
            array('-1', Gmp::factory('-1')),
            array('4564564', Gmp::factory('4564564')),
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

    public function testConstructor()
    {
        $gmp = new Gmp(PHP_INT_MAX, 10);

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
        $gmp = new Gmp($value, $radix);

        $this->assertTrue($gmp instanceof Gmp);

        $this->assertSame('1234567890', gmp_strval($gmp->getRawValue(), 10));
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
        $gmp = new Gmp('doge', 10);
    }

    public function testFactory()
    {
        $this->assertTrue(Gmp::factory(PHP_INT_MAX) instanceof Gmp);
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
            Gmp::factory(5), // Another instance
            gmp_init(5), // A gmp resource
        );

        // Our instance
        $gmp = Gmp::factory(5);

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
        $gmp = Gmp::factory(5);

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
        $gmp_a = Gmp::factory(256);

        // Equals
        $this->assertSame(0, $gmp_a->compareTo(256));

        // Less
        $this->assertLessThan(0, $gmp_a->compareTo(300));

        // Greater
        $this->assertGreaterThan(0, $gmp_a->compareTo(100));
    }

    public function testIsNegative()
    {
        $this->assertFalse(Gmp::factory(0)->isNegative());
        $this->assertFalse(Gmp::factory(-0)->isNegative());
        $this->assertFalse(Gmp::factory(1)->isNegative());
        $this->assertFalse(Gmp::factory(10)->isNegative());
        $this->assertFalse(Gmp::factory(PHP_INT_MAX)->isNegative());
        $this->assertTrue(Gmp::factory(-1)->isNegative());
        $this->assertTrue(Gmp::factory(-10)->isNegative());
        $this->assertTrue(Gmp::factory(-PHP_INT_MAX)->isNegative());
    }

    public function testAbs()
    {
        $this->assertSame('100', Gmp::factory(-100)->abs()->toString());
        $this->assertSame('123213', Gmp::factory(-123213)->abs()->toString());
        $this->assertSame((string) PHP_INT_MAX, Gmp::factory(-PHP_INT_MAX)->abs()->toString());
    }

    public function testTwosComplement()
    {
        // Positive numbers should return the same number
        $this->assertSame('20', Gmp::factory(20)->twosComplement()->toString());
        $this->assertSame('84', Gmp::factory(84)->twosComplement()->toString());
        $this->assertSame('68', Gmp::factory(68)->twosComplement()->toString());

        // Negative numbers should flip bits and add a 1
        $this->assertSame('44', Gmp::factory(-20)->twosComplement()->toString());
        $this->assertSame('172', Gmp::factory(-84)->twosComplement()->toString());
        $this->assertSame('188', Gmp::factory(-68)->twosComplement()->toString());

        // Bit length mask
        $this->assertSame('101100', Gmp::factory(-20)->twosComplement(0)->toString(2));
        $this->assertSame('101100', Gmp::factory(-20)->twosComplement(-100)->toString(2));
        $this->assertSame('101100', Gmp::factory(-20)->twosComplement(2)->toString(2));
        $this->assertSame('11101100', Gmp::factory(-20)->twosComplement(8)->toString(2));
        $this->assertSame('1111111111101100', Gmp::factory(-20)->twosComplement(16)->toString(2));
        $this->assertSame('111111111111111111111111101100', Gmp::factory(-20)->twosComplement(30)->toString(2));
    }

    public function testAdd()
    {
        $gmp_a = Gmp::factory(100);

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
        $gmp_a = Gmp::factory(100);

        // Positive arg and result
        $this->assertSame('98', $gmp_a->sub(2)->toString());

        // Negative arg and positive result
        $this->assertSame('102', $gmp_a->sub(-2)->toString());

        // Zero arg
        $this->assertSame('100', $gmp_a->sub(0)->toString());
    }

    public function testMul()
    {
        $gmp_a = Gmp::factory(2);

        // Positive arg and result
        $this->assertSame('4', $gmp_a->mul(2)->toString());

        // Negative arg and result
        $this->assertSame('-20', $gmp_a->mul(-10)->toString());

        // Zero arg
        $this->assertSame('0', $gmp_a->mul(0)->toString());
    }

    public function testDiv()
    {
        $gmp_a = Gmp::factory(20);

        // Positive arg and result
        $this->assertSame('10', $gmp_a->div(2)->toString());

        // Negative arg and result
        $this->assertSame('-10', $gmp_a->div(-2)->toString());
    }

    public function testPow()
    {
        $gmp_a = Gmp::factory(2);

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
        $gmp_a = Gmp::factory(2);

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
        $gmp_a = Gmp::factory(256);

        // Positive arg and result
        $this->assertSame('16', $gmp_a->sqrt()->toString());
    }

    public function testMod()
    {
        $gmp_a = Gmp::factory(256);

        // Positive arg and result
        $this->assertSame('6', $gmp_a->mod(10)->toString());

        // Negative arg and zero result
        $this->assertSame('0', $gmp_a->mod(-2)->toString());
    }

    public function testBitAnd()
    {
        $gmp_a = Gmp::factory('0b101010101');
        $gmp_b = Gmp::factory('-0b101010101');

        $this->assertSame('325', $gmp_a->bitAnd('0b111000111')->toString());
        $this->assertSame('16', $gmp_a->bitAnd('0b000111000')->toString());
        $this->assertSame('273', $gmp_a->bitAnd('0b100111001')->toString());
        $this->assertSame('69', $gmp_a->bitAnd('-0b110011001')->toString());

        $this->assertSame('131', $gmp_b->bitAnd('0b111000111')->toString());
        $this->assertSame('40', $gmp_b->bitAnd('0b000111000')->toString());
        $this->assertSame('41', $gmp_b->bitAnd('0b100111001')->toString());
        $this->assertSame('-477', $gmp_b->bitAnd('-0b110011001')->toString());

        $this->assertSame('1', $gmp_a->bitAnd('0b1001')->toString());
        $this->assertSame('341', $gmp_a->bitAnd('-0b1001')->toString());
        $this->assertSame('4', $gmp_a->bitAnd('0b0110')->toString());
        $this->assertSame('336', $gmp_a->bitAnd('-0b0110')->toString());
        $this->assertSame('21', $gmp_a->bitAnd('0b111111000000111111')->toString());
        $this->assertSame('321', $gmp_a->bitAnd('-0b111111000000111111')->toString());
        $this->assertSame('320', $gmp_a->bitAnd('0b000000111111000000')->toString());
        $this->assertSame('64', $gmp_a->bitAnd('-0b000000111111000000')->toString());

        $this->assertSame('9', $gmp_b->bitAnd('0b1001')->toString());
        $this->assertSame('-349', $gmp_b->bitAnd('-0b1001')->toString());
        $this->assertSame('2', $gmp_b->bitAnd('0b0110')->toString());
        $this->assertSame('-342', $gmp_b->bitAnd('-0b0110')->toString());
        $this->assertSame('258091', $gmp_b->bitAnd('0b111111000000111111')->toString());
        $this->assertSame('-258431', $gmp_b->bitAnd('-0b111111000000111111')->toString());
        $this->assertSame('3712', $gmp_b->bitAnd('0b000000111111000000')->toString());
        $this->assertSame('-4096', $gmp_b->bitAnd('-0b000000111111000000')->toString());
    }

    public function testBitOr()
    {
        $gmp_a = Gmp::factory('0b101010101');
        $gmp_b = Gmp::factory('-0b101010101');

        $this->assertSame('471', $gmp_a->bitOr('0b111000111')->toString());
        $this->assertSame('381', $gmp_a->bitOr('0b000111000')->toString());
        $this->assertSame('381', $gmp_a->bitOr('0b100111001')->toString());
        $this->assertSame('-137', $gmp_a->bitOr('-0b110011001')->toString());

        $this->assertSame('-17', $gmp_b->bitOr('0b111000111')->toString());
        $this->assertSame('-325', $gmp_b->bitOr('0b000111000')->toString());
        $this->assertSame('-69', $gmp_b->bitOr('0b100111001')->toString());
        $this->assertSame('-273', $gmp_b->bitOr('-0b110011001')->toString());

        $this->assertSame('349', $gmp_a->bitOr('0b1001')->toString());
        $this->assertSame('-9', $gmp_a->bitOr('-0b1001')->toString());
        $this->assertSame('343', $gmp_a->bitOr('0b0110')->toString());
        $this->assertSame('-1', $gmp_a->bitOr('-0b0110')->toString());
        $this->assertSame('258431', $gmp_a->bitOr('0b111111000000111111')->toString());
        $this->assertSame('-258091', $gmp_a->bitOr('-0b111111000000111111')->toString());
        $this->assertSame('4053', $gmp_a->bitOr('0b000000111111000000')->toString());
        $this->assertSame('-3755', $gmp_a->bitOr('-0b000000111111000000')->toString());

        $this->assertSame('-341', $gmp_b->bitOr('0b1001')->toString());
        $this->assertSame('-1', $gmp_b->bitOr('-0b1001')->toString());
        $this->assertSame('-337', $gmp_b->bitOr('0b0110')->toString());
        $this->assertSame('-5', $gmp_b->bitOr('-0b0110')->toString());
        $this->assertSame('-321', $gmp_b->bitOr('0b111111000000111111')->toString());
        $this->assertSame('-21', $gmp_b->bitOr('-0b111111000000111111')->toString());
        $this->assertSame('-21', $gmp_b->bitOr('0b000000111111000000')->toString());
        $this->assertSame('-277', $gmp_b->bitOr('-0b000000111111000000')->toString());
    }

    public function testBitXor()
    {
        $gmp_a = Gmp::factory('0b101010101');
        $gmp_b = Gmp::factory('-0b101010101');

        $this->assertSame('146', $gmp_a->bitXor('0b111000111')->toString());
        $this->assertSame('365', $gmp_a->bitXor('0b000111000')->toString());
        $this->assertSame('108', $gmp_a->bitXor('0b100111001')->toString());
        $this->assertSame('-206', $gmp_a->bitXor('-0b110011001')->toString());

        $this->assertSame('-148', $gmp_b->bitXor('0b111000111')->toString());
        $this->assertSame('-365', $gmp_b->bitXor('0b000111000')->toString());
        $this->assertSame('-110', $gmp_b->bitXor('0b100111001')->toString());
        $this->assertSame('204', $gmp_b->bitXor('-0b110011001')->toString());

        $this->assertSame('348', $gmp_a->bitXor('0b1001')->toString());
        $this->assertSame('-350', $gmp_a->bitXor('-0b1001')->toString());
        $this->assertSame('339', $gmp_a->bitXor('0b0110')->toString());
        $this->assertSame('-337', $gmp_a->bitXor('-0b0110')->toString());
        $this->assertSame('258410', $gmp_a->bitXor('0b111111000000111111')->toString());
        $this->assertSame('-258412', $gmp_a->bitXor('-0b111111000000111111')->toString());
        $this->assertSame('3733', $gmp_a->bitXor('0b000000111111000000')->toString());
        $this->assertSame('-3819', $gmp_a->bitXor('-0b000000111111000000')->toString());

        $this->assertSame('-350', $gmp_b->bitXor('0b1001')->toString());
        $this->assertSame('348', $gmp_b->bitXor('-0b1001')->toString());
        $this->assertSame('-339', $gmp_b->bitXor('0b0110')->toString());
        $this->assertSame('337', $gmp_b->bitXor('-0b0110')->toString());
        $this->assertSame('-258412', $gmp_b->bitXor('0b111111000000111111')->toString());
        $this->assertSame('258410', $gmp_b->bitXor('-0b111111000000111111')->toString());
        $this->assertSame('-3733', $gmp_b->bitXor('0b000000111111000000')->toString());
        $this->assertSame('3819', $gmp_b->bitXor('-0b000000111111000000')->toString());
    }

    public function testBitNot()
    {
        $this->assertSame('-342', Gmp::factory('0b101010101')->bitNot()->toString());
        $this->assertSame('-456', Gmp::factory('0b111000111')->bitNot()->toString());
        $this->assertSame('-57', Gmp::factory('0b000111000')->bitNot()->toString());
        $this->assertSame('-314', Gmp::factory('0b100111001')->bitNot()->toString());
        $this->assertSame('408', Gmp::factory('-0b110011001')->bitNot()->toString());
    }

    public function testBitShiftLeft()
    {
        $gmp_a = Gmp::factory('0b101010101');

        $this->assertSame('682', $gmp_a->bitShiftLeft(1)->toString());
        $this->assertSame('1364', $gmp_a->bitShiftLeft(2)->toString());
        $this->assertSame('2728', $gmp_a->bitShiftLeft(3)->toString());
        $this->assertSame('5456', $gmp_a->bitShiftLeft(4)->toString());
    }

    public function testBitShiftRight()
    {
        $gmp_a = Gmp::factory('0b101010101');

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

        $gmp = Gmp::factory($decimal_integer);

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
