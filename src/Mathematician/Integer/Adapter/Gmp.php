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

use Mathematician\Exception\InvalidNumberException;
use Mathematician\Exception\InvalidPrecisionException;
use Mathematician\Exception\InvalidTypeException;

/**
 * Gmp
 *
 * @uses AbstractAdapter
 * @uses AdapterInterface
 * @package Mathematician\Integer\Adapter
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
     * Constructor
     *
     * @param mixed $number
     * @param int $radix
     * @access public
     */
    public function __construct($number, $radix = 0)
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
            $this->raw_value = gmp_init($number, (int) $radix);
        }

        // Verify the value actually makes sense
        if (false === $this->raw_value) {
            throw new InvalidNumberException(
                'GMP failed to initialize correctly with your value: '. $number
            );
        }
    }

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
        return new static($number, $radix);
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
     * Compare numbers
     *
     * @param mixed $number
     * @access public
     * @return int
     */
    public function compareTo($number)
    {
        return gmp_cmp(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );
    }

    /**
     * Check if the number is negative
     *
     * @access public
     * @return boolean
     */
    public function isNegative()
    {
        return ($this->compareTo(0) === -1);
    }

    /**
     * Get the absolute value
     *
     * @access public
     * @return self
     */
    public function abs()
    {
        $result = gmp_abs(
            $this->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Get the two's complement of the number, without native signed interpretation
     *
     * @param int $bit_length The number of bits to use in the mask
     * @access public
     * @return self
     */
    public function twosComplement($bit_length = 0)
    {
        if ($this->isNegative()) {
            $abs = $this->abs();

            // Get the bit length
            $min_bit_length = strlen($abs->toString(2)) + 1;

            if ($bit_length < $min_bit_length) {
                $bit_length = $min_bit_length;
            }

            $bit_length_multiple = static::factory(2)->pow($bit_length - 1);

            // Get the one's complement
            $ones_comp = $abs->bitNot()->bitAnd($bit_length_multiple->sub(1));

            // Add our leading 1's with our bit multiple and add 1
            return $ones_comp->add($bit_length_multiple)->add(1);
        }

        return static::factory($this->toString());
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
     * Raise to a power
     *
     * @param mixed $power
     * @access public
     * @return self
     */
    public function pow($power)
    {
        $result = gmp_pow(
            $this->getRawValue(),
            static::upgradeParam($power)->toString()
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
        $result = gmp_powm(
            $this->getRawValue(),
            static::upgradeParam($power)->getRawValue(),
            static::upgradeParam($modulus)->getRawValue()
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
        $result = gmp_sqrt(
            $this->getRawValue()
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
        $result = gmp_mod(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Bitwise "and" (&)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitAnd($number)
    {
        $result = gmp_and(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Bitwise "or" (|)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitOr($number)
    {
        $result = gmp_or(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Bitwise "xor" (^)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitXor($number)
    {
        $result = gmp_xor(
            $this->getRawValue(),
            static::upgradeParam($number)->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Bitwise "not" (~)
     *
     * @access public
     * @return self
     */
    public function bitNot()
    {
        $result = gmp_com(
            $this->getRawValue()
        );

        return static::factory($result);
    }

    /**
     * Bitwise "shift left" (<<)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitShiftLeft($number)
    {
        return $this->mul(
            static::factory(2)->pow($number)
        );
    }

    /**
     * Bitwise "shift right" (>>)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitShiftRight($number)
    {
        return $this->div(
            static::factory(2)->pow($number)
        );
    }

    /**
     * Get a string representation of the number
     *
     * @param int $radix
     * @access public
     * @return string
     */
    public function toString($radix = 10)
    {
        return gmp_strval($this->raw_value, $radix);
    }
}
