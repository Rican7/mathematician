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

use Mathematician\Exception\AdapterSupportException;
use Mathematician\Integer\Integer;

/**
 * Number
 *
 * @abstract
 * @package Mathematician
 */
abstract class Number
{

    /**
     * Constants
     */

    /**
	 * The GMP extension name
     *
     * @const string
     */
    const EXTENSION_GMP = 'gmp';

    /**
	 * The BC Math extension name
     *
     * @const string
     */
    const EXTENSION_BCMATH = 'bcmath';


    /**
     * Methods
     */

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
        if (is_float($number) || ((int) $scale) !== 0) {
            // TODO: Floating-point/decimal adapter
            throw new AdapterSupportException(
                'Floating point adapter not yet implemented'
            );

        } else {

            return Integer::factory($number);
        }
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
        return extension_loaded(static::EXTENSION_GMP);
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
        return extension_loaded(static::EXTENSION_BCMATH);
    }
}
