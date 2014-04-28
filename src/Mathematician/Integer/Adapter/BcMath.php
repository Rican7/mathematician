<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.2.0
 */

namespace Mathematician\Integer\Adapter;

use Mathematician\Exception\OutOfTypeRangeException;
use Mathematician\Exception\UnsupportedNumericFormatException;
use OutOfRangeException;

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
     * Get the absolute value
     *
     * @access public
     * @return self
     */
    public function abs()
    {
        $string = $this->toString();

        if (strpos($string, '-') === 0) {
            $string = substr($string, 1);
        }

        return static::factory($string);
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
            $binary = $this->abs()->toString(2);
            $result = static::flipBits($binary);

            $min_bit_length = strlen($result) + 1;

            if ($bit_length < $min_bit_length) {
                $bit_length = $min_bit_length;
            }

            // Fill with 1's left of the most significant bit
            $result = str_pad($result, $bit_length, '1', STR_PAD_LEFT);

            return static::factory($result, 2)->add(1);
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
        $number = static::upgradeParam($number);

        // Get both numbers in reversed binary (base 2) format based on their raw two's complement
        $binary_this = $this->twosComplement()->toString(2);
        $binary_other = $number->twosComplement()->toString(2);

        $length = max(strlen($binary_this), strlen($binary_other));

        // Pad our bit strings for even string comparison
        $this_pad_bit = $this->isNegative() ? '1' : '0';
        $other_pad_bit = $number->isNegative() ? '1' : '0';
        $binary_this = str_pad($binary_this, $length, $this_pad_bit, STR_PAD_LEFT);
        $binary_other = str_pad($binary_other, $length, $other_pad_bit, STR_PAD_LEFT);

        $result = '';

        // Loop through each character in the binary string
        for ($i = 0; $i < $length; $i++) {
            if ($binary_this[$i] === '1' && $binary_other[$i] === '1') {
                $result .= '1';
            } else {
                $result .= '0';
            }
        }

        if ($this->isNegative() && $number->isNegative()) {
            $result = static::flipBits($result);

            return static::factory($result, 2)->bitNot();
        }

        return static::factory($result, 2);
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
        $number = static::upgradeParam($number);

        // Get both numbers in reversed binary (base 2) format based on their raw two's complement
        $binary_this = $this->twosComplement()->toString(2);
        $binary_other = $number->twosComplement()->toString(2);

        $length = max(strlen($binary_this), strlen($binary_other));

        // Pad our bit strings for even string comparison
        $this_pad_bit = $this->isNegative() ? '1' : '0';
        $other_pad_bit = $number->isNegative() ? '1' : '0';
        $binary_this = str_pad($binary_this, $length, $this_pad_bit, STR_PAD_LEFT);
        $binary_other = str_pad($binary_other, $length, $other_pad_bit, STR_PAD_LEFT);

        $result = '';

        // Loop through each character in the binary string
        for ($i = 0; $i < $length; $i++) {
            if ($binary_this[$i] === '1' || $binary_other[$i] === '1') {
                $result .= '1';
            } else {
                $result .= '0';
            }
        }

        if ($this->isNegative() || $number->isNegative()) {
            $result = static::flipBits($result);

            return static::factory($result, 2)->bitNot();
        }

        return static::factory($result, 2);
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
        $number = static::upgradeParam($number);

        // Get both numbers in reversed binary (base 2) format based on their raw two's complement
        $binary_this = $this->twosComplement()->toString(2);
        $binary_other = $number->twosComplement()->toString(2);

        $length = max(strlen($binary_this), strlen($binary_other));

        // Pad our bit strings for even string comparison
        $this_pad_bit = $this->isNegative() ? '1' : '0';
        $other_pad_bit = $number->isNegative() ? '1' : '0';
        $binary_this = str_pad($binary_this, $length, $this_pad_bit, STR_PAD_LEFT);
        $binary_other = str_pad($binary_other, $length, $other_pad_bit, STR_PAD_LEFT);

        $result = '';

        // Loop through each character in the binary string
        for ($i = 0; $i < $length; $i++) {
            if ($binary_this[$i] === '1' && $binary_other[$i] === '0') {
                $result .= '1';
            } elseif ($binary_this[$i] === '0' && $binary_other[$i] === '1') {
                $result .= '1';
            } else {
                $result .= '0';
            }
        }

        if ($this->isNegative() ^ $number->isNegative()) {
            $result = static::flipBits($result);

            return static::factory($result, 2)->bitNot();
        }

        return static::factory($result, 2);
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

        if ($added->isNegative()) {
            return $added->abs();
        }

        return static::factory('-' . $added->toString());
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
     * Get the native PHP integer value
     *
     * @param bool $strict
     * @access public
     * @return int
     */
    public function toInteger($strict = true)
    {
        if ($strict && !$this->isWithinIntegerRange()) {
            throw new OutOfTypeRangeException(
                'The value "'. $this->toString() .'" is outside of the native integer range: '
                . ~PHP_INT_MAX .'...'. PHP_INT_MAX
            );
        }

        return (int) $this->getRawValue();
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
                'Given base "'. $base .'" is outside of range: 2..'. strlen(static::NUMERIC_ALPHABET)
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

    /**
     * Flip the bits of a given binary string
     *
     * @param string $bit_string
     * @static
     * @access protected
     * @return string
     */
    protected static function flipBits($bit_string)
    {
        $result = '';

        // Flip the bits
        for ($i = 0; $i < strlen($bit_string); $i++) {
            if ($bit_string[$i] === '1') {
                $result .= '0';
            } elseif ($bit_string[$i] === '0') {
                $result .= '1';
            }
        }

        return $result;
    }
}
