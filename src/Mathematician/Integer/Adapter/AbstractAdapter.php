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
 * AbstractAdapter
 *
 * @uses AdapterInterface
 * @abstract
 * @package Mathematician\Integer\Adapter
 */
abstract class AbstractAdapter implements AdapterInterface
{

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
