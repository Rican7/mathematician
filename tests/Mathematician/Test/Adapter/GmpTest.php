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

    public function testToString()
    {
        $gmps = array(
            '18446744073709551616' => new Gmp('18446744073709551616'),
            '-1' => new Gmp('-1'),
            '4564564' => new Gmp('4564564'),
        );

        foreach ($gmps as $string => $gmp) {
            $this->assertSame((string) $string, $gmp->toString());
        }
    }
}
