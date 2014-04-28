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

use UnexpectedValueException;

/**
 * UnsupportedNumericFormatException
 *
 * @uses UnexpectedValueException
 * @uses MathematicianExceptionInterface
 * @package Mathematician\Exception
 */
class UnsupportedNumericFormatException extends UnexpectedValueException implements MathematicianExceptionInterface
{

    /**
     * Default Properties
     */

    protected $message = 'The given numeric representation is unsupported';
}
