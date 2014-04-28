<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.2.0
 */

namespace Mathematician\Exception;

use RangeException;

/**
 * InvalidPrecisionException
 *
 * @uses RangeException
 * @uses MathematicianExceptionInterface
 * @package Mathematician\Exception
 */
class InvalidPrecisionException extends RangeException implements MathematicianExceptionInterface
{

    /**
     * Default Properties
     */

    protected $message = 'The given precision is out of range';
}
