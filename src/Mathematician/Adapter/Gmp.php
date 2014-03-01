<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.0
 */

namespace Mathematician\Adapter;

use Mathematician\Exception\InvalidNumberException;
use Mathematician\Exception\InvalidPrecisionException;
use Mathematician\Exception\InvalidTypeException;

/**
 * Gmp
 *
 * @uses AbstractAdapter
 * @uses AdapterInterface
 * @package Mathematician\Adapter
 */
class Gmp extends AbstractAdapter implements AdapterInterface
{

    /**
     * Constants
     */

    /**
     * The name of the GMP resource type
     *
     * @const string
     */
    const GMP_RESOURCE_TYPE_NAME = 'GMP integer';


    /**
     * Methods
     */

    /**
     * Create an instance of a number adapter
     *
     * @param mixed $number
     * @param int $scale
     * @static
     * @access public
     * @return self
     */
    public static function factory($number, $scale = null)
    {
        if (null !== $scale && ((int) $scale) != 0) {
            throw new InvalidPrecisionException(
                'The GMP extension doesn\'t support float/decimal calculations'
            );
        }

        return new static($number);
    }

    /**
     * Check if a given value is a GMP resource
     *
     * @param mixed $number
     * @static
     * @access public
     * @return boolean
     */
    public static function isGmpResource($number)
    {
        if (is_resource($number)
            && get_resource_type($number) == static::GMP_RESOURCE_TYPE_NAME) {

            return true;
        }

        return false;
    }

    /**
     * Constructor
     *
     * @param mixed $number
     * @param int $base
     * @access public
     */
    public function __construct($number, $base = 0)
    {
        // The PHP "gmp" extension doesn't support floats :/
        if (is_float($number)) {
            throw new InvalidTypeException(
                'The GMP extension doesn\'t support float values.'
                .' Attempt to build a '. get_class($this) .' instance with a float value: '. $number
            );
        } elseif (static::isGmpResource($number)) {
            $this->raw_value = $number;
        } else {
            $this->raw_value = gmp_init($number, (int) $base);
        }

        // Verify the value actually makes sense
        if (false === $this->raw_value) {
            throw new InvalidNumberException(
                'GMP failed to initialize correctly with your value: '. $number
            );
        }
    }

    /**
     * Add numbers
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function add($number)
    {
        $result = gmp_add(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Subtract numbers
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function sub($number)
    {
        $result = gmp_sub(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Multiply numbers
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function mul($number)
    {
        $result = gmp_mul(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Divide numbers
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function div($number)
    {
        $result = gmp_div(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue(),
            GMP_ROUND_ZERO
        );

        return static::factory($result);
    }

    /**
     * Get a string representation of the number
     *
     * @access public
     * @return string
     */
    public function toString()
    {
        return gmp_strval($this->raw_value);
    }
}
