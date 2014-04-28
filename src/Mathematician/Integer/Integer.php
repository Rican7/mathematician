<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.2.0
 */

namespace Mathematician\Integer;

use Mathematician\Exception\AdapterSupportException;
use Mathematician\Integer\Adapter\AdapterInterface;
use Mathematician\Integer\Adapter\BcMath;
use Mathematician\Integer\Adapter\Gmp;
use Mathematician\Number;

/**
 * Integer
 *
 * @abstract
 * @package Mathematician
 */
abstract class Integer
{

    /**
     * Methods
     */

    /**
     * Create an instance of an Integer adapter
     *
     * @param mixed $number
     * @static
     * @access public
     * @throws AdapterSupportException
     * @return AdapterInterface
     */
    public static function factory($number)
    {
        // Use GMP if we can... its MUCH faster
        if (Number::isGmpAvailable()) {
            return Gmp::factory($number);

        } elseif (Number::isBcMathAvailable()) {
            return BcMath::factory($number);
        }

        // TODO: Fall back to native?
        throw new AdapterSupportException();
    }
}
