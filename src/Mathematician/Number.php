<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.0
 */

namespace Mathematician;

use Mathematician\Integer\Adapter\AdapterInterface;
use Mathematician\Integer\Adapter\BcMath;
use Mathematician\Integer\Adapter\Gmp;
use Mathematician\Exception\AdapterSupportException;

/**
 * Number
 *
 * @abstract
 * @package Mathematician
 */
abstract class Number
{

    /**
     * Create an instance of a Mathematician adapter instance
     *
     * @param mixed $number
     * @param int $scale
     * @static
     * @access public
     * @return AdapterInterface
     */
    public static function factory($number, $scale = 0)
    {
        // If we have a float or scale set, then use BcMath so
        // we don't lose the ability to work on float values
        if ((is_float($number) || ((int) $scale) != 0)
            && static::isBcMathAvailable()) {

            return BcMath::factory($number, $scale);

        // Otherwise, use GMP if we have it, as its fast as hell
        } elseif (static::isGmpAvailable()) {
            return Gmp::factory($number, $scale);

        } elseif (static::isBcMathAvailable()) {
            return BcMath::factory($number, $scale);
        }

        // TODO: Fall back to native?
        throw new AdapterSupportException();
    }

    /**
     * Check if the GMP extension is available or not
     *
     * @static
     * @access public
     * @return boolean
     */
    public static function isGmpAvailable()
    {
        return extension_loaded('gmp');
    }

    /**
     * Check if the BCMATH extension is available or not
     *
     * @static
     * @access public
     * @return boolean
     */
    public static function isBcMathAvailable()
    {
        return extension_loaded('bcmath');
    }
}
