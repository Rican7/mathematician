<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.1
 */

namespace Mathematician\Test\Integer;

use Mathematician\Integer\Adapter\AdapterInterface;
use Mathematician\Integer\Integer;
use Mathematician\Test\AbstractMathematicianTest;

/**
 * IntegerTest
 *
 * @uses AbstractMathematicianTest
 * @package Mathematician\Test\Integer
 */
class IntegerTest extends AbstractMathematicianTest
{

    public function testFactory()
    {
        $result = Integer::factory(PHP_INT_MAX);

        $this->assertInternalType('object', $result);
        $this->assertTrue($result instanceof AdapterInterface);
    }
}
