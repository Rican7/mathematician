<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.1
 */

namespace Mathematician\Integer\Adapter;

/**
 * AdapterInterface
 *
 * @package Mathematician\Integer\Adapter
 */
interface AdapterInterface
{

    /**
     * Create an instance of a number adapter
     *
     * @param mixed $number The actual numeric value
     * @param int $radix    (Optional) The "base" of the number system represented by $number
     * @static
     * @access public
     * @return self
     */
    public static function factory($number, $radix = null);

    /**
     * Get the raw value
     *
     * @access public
     * @return mixed
     */
    public function getRawValue();

    /**
     * Get a string representation of the number
     *
     * @param int $radix
     * @access public
     * @return string
     */
    public function toString($radix = 10);

    /**
     * Get the native PHP integer value
     *
     * @param bool $strict
     * @access public
     * @return int
     */
    public function toInteger($strict = true);

    /**
     * Compare numbers
     *
     * @param mixed $number
     * @access public
     * @return int
     */
    public function compareTo($number);

    /**
     * Check if the number is negative
     *
     * @access public
     * @return boolean
     */
    public function isNegative();

    /**
     * Check if the value is within the range of the native PHP integer type
     *
     * @link http://www.php.net/manual/en/language.types.integer.php
     * @access public
     * @return boolean
     */
    public function isWithinIntegerRange();

    /**
     * Get the absolute value
     *
     * @access public
     * @return self
     */
    public function abs();

    /**
     * Get the two's complement of the number, without native signed interpretation
     *
     * @param int $bit_length The number of bits to use in the mask
     * @access public
     * @return self
     */
    public function twosComplement($bit_length = 0);

    /**
     * Add numbers
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function add($number);

    /**
     * Subtract numbers
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function sub($number);

    /**
     * Multiply numbers
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function mul($number);

    /**
     * Divide numbers
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function div($number);

    /**
     * Raise to a power
     *
     * @param mixed $power
     * @access public
     * @return self
     */
    public function pow($power);

    /**
     * Raise to a power and reduce by a modulus
     *
     * @param mixed $power
     * @param mixed $modulus
     * @access public
     * @return self
     */
    public function powMod($power, $modulus);

    /**
     * Get the square route
     *
     * @access public
     * @return self
     */
    public function sqrt();

    /**
     * Get the modulus
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function mod($number);

    /**
     * Bitwise "and" (&)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitAnd($number);

    /**
     * Bitwise "or" (|)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitOr($number);

    /**
     * Bitwise "xor" (^)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitXor($number);

    /**
     * Bitwise "not" (~)
     *
     * @access public
     * @return self
     */
    public function bitNot();

    /**
     * Bitwise "shift left" (<<)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitShiftLeft($number);

    /**
     * Bitwise "shift right" (>>)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitShiftRight($number);


    /**
     * Magic Methods
     */

    /**
     * Get a string representation of the number
     *
     * @access public
     * @return string
     */
    public function __toString();
}
