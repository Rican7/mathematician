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

/**
 * MathematicianExceptionInterface
 *
 * Exception interface that all Mathematician exceptions should implement
 *
 * This is mostly for having a simple, common Interface class/namespace
 * that can be type-hinted/instance-checked against, therefore making it
 * easier to handle Mathematician exceptions while still allowing the different
 * exception classes to properly extend the corresponding SPL Exception type
 *
 * @package Mathematician\Exception
 */
interface MathematicianExceptionInterface
{
}
