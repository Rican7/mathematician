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
 * AdapterInterface
 *
 * @package Mathematician\Integer\Adapter
 */
interface AdapterInterface
{

    /**
     * Create an instance of a number adapter
     *
     * @param mixed $number
     * @param int $scale
     * @static
     * @access public
     * @return self
     */
    public static function factory($number, $scale = 0);

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
     * @access public
     * @return string
     */
    public function toString();


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
