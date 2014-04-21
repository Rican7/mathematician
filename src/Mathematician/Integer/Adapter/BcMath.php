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

use Mathematician\Exception\UnsupportedNumericFormatException;

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
     * Constructor
     *
     * This adapter's constructor assumes a radix of 10
     *
     * @param mixed $number
     * @access public
     */
    public function __construct($number)
    {
        // Convert the value to our scale by simply adding 0
        $number = bcadd($number, 0, static::DEFAULT_SCALE);

        $this->raw_value = (string) $number;
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
        if (0 === (int) $radix) {
            // Attempt to detect our numeric base
            $radix = static::detectNumericBase($number);

            if (0 === $radix) {
                throw new UnsupportedNumericFormatException();
            }
        }

        // Don't bother converting if we're already in our required radix
        if ($radix !== 10) {
            // First convert our value to a base 10 string
            $number = static::baseToDec($number, $radix);
        }

        return new static($number);
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
     * Bitwise "and" (&)
     *
     * @param mixed $number
     * @access public
     * @return self
     */
    public function bitAnd($number)
    {
        // Get both numbers in reversed binary (base 2) format
        $binary_this = strrev($this->toString(2));
        $binary_other = strrev(static::upgradeParam($number)->toString(2));

        $result = '';

        // Loop through each character in the binary string
        for ($i = 0; $i < strlen($binary_this); $i++) {
            if (isset($binary_other[$i])) {
                if ($binary_this[$i] === '1' && $binary_other[$i] === '1') {
                    $result .= '1';
                } else {
                    $result .= '0';
                }
            } else {
                $result .= '0';
            }
        }

        // Re-reverse our string to get it in normal bit order
        $result = strrev($result);

        return static::factory('0b' . $result);
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
        // Get both numbers in reversed binary (base 2) format
        $binary_this = strrev($this->toString(2));
        $binary_other = strrev(static::upgradeParam($number)->toString(2));

        $result = '';

        // Loop through each character in the binary string
        for ($i = 0; $i < strlen($binary_this); $i++) {
            if ($binary_this[$i] === '1'
                || (isset($binary_other[$i]) && $binary_other[$i] === '1')) {

                $result .= '1';
            } else {
                $result .= '0';
            }
        }

        // Re-reverse our string to get it in normal bit order
        $result = strrev($result);

        return static::factory('0b' . $result);
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
        // Get both numbers in reversed binary (base 2) format
        $binary_this = strrev($this->toString(2));
        $binary_other = strrev(static::upgradeParam($number)->toString(2));

        $result = '';

        // Loop through each character in the binary string
        for ($i = 0; $i < strlen($binary_this); $i++) {
            $other_val = isset($binary_other[$i]) ? $binary_other[$i] : '0';

            if ($binary_this[$i] === '1' && $other_val === '0') {
                $result .= '1';
            } elseif ($binary_this[$i] === '0' && $other_val === '1') {
                $result .= '1';
            } else {
                $result .= '0';
            }
        }

        // Re-reverse our string to get it in normal bit order
        $result = strrev($result);

        return static::factory('0b' . $result);
    }

    /**
     * Bitwise "not" (~)
     *
     * @access public
     * @return self
     */
    public function bitNot()
    {
        // We can cheat two's complement "not"
        $added = $this->add(1);

        if (1 === $added->compareTo(0)) {
            $result = '-' . $added->toString();
        } else {
            $result = substr($added->toString(), 1);
        }

        return static::factory('0b' . $result);
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
        $result = bcmul(
            $this->getRawValue(),
            bcpow(
                2,
                static::upgradeParam($number)->getRawValue()
            )
        );

        return static::factory($result);
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
        $result = bcdiv(
            $this->getRawValue(),
            bcpow(
                2,
                static::upgradeParam($number)->getRawValue()
            )
        );

        return static::factory($result);
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
        // Don't bother converting what we're already representing
        if ($radix !== 10) {
            return static::decToBase(
                $this->getRawValue(),
                $radix
            );
        }

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

    /**
     * Convert a numeric string from decimal (base 10) form to a given base
     *
     * @param string $numeric_string
     * @param int $to_base
     * @static
     * @access protected
     * @return string
     */
    protected static function decToBase($numeric_string, $to_base)
    {
        // Get the alphabet to use
        $alphabet = static::getNumericAlphabetForBase($to_base);

        $converted = '';
        $negative = false;

        // Handle negative prefixes
        if (strpos($numeric_string, '-') === 0) {
            $numeric_string = substr($numeric_string, 1);
            $negative = true;
        }

        // Loop until our original argument is 0
        while (0 !== bccomp($numeric_string, 0, static::DEFAULT_SCALE)) {
            // Reduce our numeric string by our base and grab its remainder
            $remainder = bcmod($numeric_string, $to_base);
            $numeric_string = bcdiv($numeric_string, $to_base, static::DEFAULT_SCALE);

            $converted = $alphabet[$remainder] . $converted;
        }

        if ($negative) {
            $converted = '-' . $converted;
        }

        return (string) $converted;
    }

    /**
     * Convert a numeric string from a given base to decimal (base 10) form
     *
     * @param string $numeric_string
     * @param int $from_base
     * @static
     * @access protected
     * @return string
     */
    protected static function baseToDec($numeric_string, $from_base)
    {
        // Get the alphabet to use
        $alphabet = static::getNumericAlphabetForBase($from_base);

        // Make sure our incoming value aligns with our alphabet's standard
        if ($from_base <= 36) {
            $numeric_string = strtolower($numeric_string);
        }

        $converted = '';
        $negative = false;

        // Handle negative prefixes
        if (strpos($numeric_string, '-') === 0) {
            $numeric_string = substr($numeric_string, 1);
            $negative = true;
        }

        $string_length = strlen($numeric_string);

        // Loop until our original argument is 0
        foreach (str_split($numeric_string) as $index => $digit) {
            $char = strpos($alphabet, $digit);
            $exponent = ($string_length - ($index + 1));
            $power = bcpow($from_base, $exponent, static::DEFAULT_SCALE);
            $raised = bcmul($char, $power, static::DEFAULT_SCALE);

            $converted = bcadd($converted, $raised, static::DEFAULT_SCALE);
        }

        if ($negative) {
            $converted = '-' . $converted;
        }

        return (string) $converted;
    }
}
