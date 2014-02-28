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

/**
 * AbstractAdapter
 *
 * @uses AdapterInterface
 * @abstract
 * @package Mathematician\Adapter
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
}
