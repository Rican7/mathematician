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
 * AbstractAdapter
 *
 * @uses AdapterInterface
 * @abstract
 * @package Mathematician\Integer\Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{

    /**
     * Constants
     */

    /**
     * The regular expression used to match
     * against the DECIMAL numeric base
     *
     * @const string
     */
    const NUMERIC_BASE_REGEX_DECIMAL = '`\\b(?:[1-9][0-9]*|0)\\b`';

    /**
     * The regular expression used to match
     * against the HEXADECIMAL numeric base
     *
     * @const string
     */
    const NUMERIC_BASE_REGEX_HEXADECIMAL = '`\\b(?:0[xX][0-9a-fA-F]+)\\b`';

    /**
     * The regular expression used to match
     * against the OCTAL numeric base
     *
     * @const string
     */
    const NUMERIC_BASE_REGEX_OCTAL = '`\\b(?:0[0-7]+)\\b`';

    /**
     * The regular expression used to match
     * against the BINARY numeric base
     *
     * @const string
     */
    const NUMERIC_BASE_REGEX_BINARY = '`\\b(?:0b[01]+)\\b`';


    /**
     * Properties
     */

    /**
     * The raw value of the number
     *
     * @var mixed
     * @access protected
     */
    protected $raw_value;


    /**
     * Methods
     */

    /**
     * Get the raw value
     *
     * @access public
     * @return mixed
     */
    public function getRawValue()
    {
        return $this->raw_value;
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
     * Check if the value is within the range of the native PHP integer type
     *
     * @link http://www.php.net/manual/en/language.types.integer.php
     * @access public
     * @return boolean
     */
    public function isWithinIntegerRange()
    {
        return (
            $this->compareTo(PHP_INT_MAX) <= 0
            && $this->compareTo(~PHP_INT_MAX) >= 0
        );
    }

    /**
     * Get a string representation of the number
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Attempt to detect the numeric base of a given numeric
     * representation as a string
     *
     * If the base can't be confidently detected, this method
     * will return an int(0)
     *
     * @param string $numeric_string
     * @static
     * @access public
     * @return int
     */
    public static function detectNumericBase($numeric_string)
    {
        if (preg_match(static::NUMERIC_BASE_REGEX_DECIMAL, $numeric_string)) {
            return 10;
        } elseif (preg_match(static::NUMERIC_BASE_REGEX_HEXADECIMAL, $numeric_string)) {
            return 16;
        } elseif (preg_match(static::NUMERIC_BASE_REGEX_OCTAL, $numeric_string)) {
            return 8;
        } elseif (preg_match(static::NUMERIC_BASE_REGEX_BINARY, $numeric_string)) {
            return 2;
        }

        return 0;
    }

    /**
     * Upgrade a given parameter to an instance
     * of the current adapter
     *
     * This enables methods to more easily allow
     * loose arguments to their mathematic methods
     * while keeping each method body DRY
     *
     * @param mixed $number
     * @static
     * @access protected
     * @return static
     */
    protected static function upgradeParam($number)
    {
        if ($number instanceof static) {
            return $number;
        }

        return static::factory($number);
    }
}
