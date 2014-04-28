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
 * OutOfTypeRangeException
 *
 * @uses RangeException
 * @uses MathematicianExceptionInterface
 * @package Mathematician\Exception
 */
class OutOfTypeRangeException extends RangeException implements MathematicianExceptionInterface
{

    /**
     * Default Properties
     */

    protected $message = 'The value is outside of the range of the requested type';
}
