<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.0
 */

namespace Mathematician\Exception;

use UnexpectedValueException;

/**
 * InvalidTypeException
 *
 * @uses UnexpectedValueException
 * @uses MathematicianExceptionInterface
 * @package Mathematician\Exception
 */
class InvalidTypeException extends UnexpectedValueException implements MathematicianExceptionInterface
{

    /**
     * Default Properties
     */

    protected $message = 'The type of the given value is invalid';
}
