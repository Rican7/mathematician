<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.0
 */

namespace Mathematician\Integer\Adapter;

/**
 * BcMath
 *
 * @uses AbstractAdapter
 * @uses AdapterInterface
 * @package Mathematician\Integer\Adapter
 */
class BcMath extends AbstractAdapter implements AdapterInterface
{

    /**
     * Constants
     */

    /**
     * The default "scale" to use for bcmath functions
     *
     * @const int
     */
    const DEFAULT_SCALE = 0;

    /**
     * The numeric alphabet used when representing numbers as strings
     *
     * @const string
     */
    const NUMERIC_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';


    /**
     * Methods
     */

    /**
     * Create an instance of a number adapter
     *
     * @param mixed $number
     * @param int $radix
     * @static
     * @access public
     * @return self
     */
    public static function factory($number, $radix = null)
    {
        return new static($number);
    }

    /**
     * Constructor
     *
     * @param mixed $number
     * @access public
     */
    public function __construct($number)
    {
        // Convert the value to our scale by simply adding 0
        $this->raw_value = bcadd($number, 0, static::DEFAULT_SCALE);
    }

    /**
     * Compare numbers
     *
     * @param mixed $number
     * @access public
     * @return int
     */
    public function compareTo($number)
    {
        return bccomp(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );
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
        $result = bcadd(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue(),
            static::DEFAULT_SCALE
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
        $result = bcsub(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue(),
            static::DEFAULT_SCALE
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
        $result = bcmul(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue(),
            static::DEFAULT_SCALE
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
        $result = bcdiv(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue(),
            static::DEFAULT_SCALE
        );

        return static::factory($result);
    }

    /**
     * Raise to a power
     *
     * @param mixed $power
     * @access public
     * @return self
     */
    public function pow($power)
    {
        $result = bcpow(
            $this->getRawValue(),
            static::upgradeParam($power)->getRawValue(),
            static::DEFAULT_SCALE
        );

        return static::factory($result);
    }

    /**
     * Raise to a power and reduce by a modulus
     *
     * @param mixed $power
     * @param mixed $modulus
     * @access public
     * @return self
     */
    public function powMod($power, $modulus)
    {
        $result = bcpowmod(
            $this->getRawValue(),
            static::upgradeParam($power)->getRawValue(),
            static::upgradeParam($modulus)->getRawValue(),
            static::DEFAULT_SCALE
        );

        return static::factory($result);
    }

    /**
     * Get the square route
     *
     * @access public
     * @return self
     */
    public function sqrt()
    {
        $result = bcsqrt(
            $this->getRawValue(),
            static::DEFAULT_SCALE
        );

        return static::factory($result);
    }

    /**
     * Get the modulus
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function mod($number)
    {
        $result = bcmod(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
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
        return (string) $this->raw_value;
    }

    /**
     * Get the numeric alphabet to use for string representation of a given base
     *
     * @param int $base
     * @static
     * @access protected
	 * @throws OutOfRangeException If the given base is out of our alphabet's range
     * @return string
     */
    protected static function getNumericAlphabetForBase($base)
    {
        if ($base < 2 || $base > strlen(static::NUMERIC_ALPHABET)) {
            throw new OutOfRangeException(
                'Given base "'. $base .'" is out of range: 2..'. strlen(static::NUMERIC_ALPHABET)
            );
        }

        $alphabet = substr(static::NUMERIC_ALPHABET, 0, $base);

        if ($base <= 36) {
            $alphabet = strtolower($alphabet);
        }

        return $alphabet;
    }
}
