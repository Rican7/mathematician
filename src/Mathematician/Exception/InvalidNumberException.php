<?php
/**
 * Mathematician
 *
 * @author      Trevor Suarez (Rican7)
 * @copyright   2014 Trevor Suarez
 * @license     MIT
 * @version     0.1.1
 */

namespace Mathematician\Exception;

use UnexpectedValueException;

/**
 * InvalidNumberException
 *
 * @uses UnexpectedValueException
 * @uses MathematicianExceptionInterface
 * @package Mathematician\Exception
 */
class InvalidNumberException extends UnexpectedValueException implements MathematicianExceptionInterface
{

    /**
     * Default Properties
     */

    protected $message = 'The given number is invalid';
}
