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

use RuntimeException;

/**
 * AdapterSupportException
 *
 * @uses RuntimeException
 * @uses MathematicianExceptionInterface
 * @package Mathematician\Exception
 */
class AdapterSupportException extends RuntimeException implements MathematicianExceptionInterface
{

    /**
     * Default Properties
     */

    protected $message = 'No supported adapters available';
}
