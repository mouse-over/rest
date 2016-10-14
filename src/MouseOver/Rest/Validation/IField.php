<?php
namespace MouseOver\Rest\Validation;

/**
 * Validation field interface
 * @package MouseOver\Rest\Validation
 * @author  Drahomír Hanák
 */
interface IField
{

    /**
     * Add rule to validation field
     *
     * @param string $expression or identifier
     *
     * @return IField
     */
    public function addRule($expression);

    /**
     * Validate field
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function validate($value);

    /**
     * Get field name
     * @return string
     */
    public function getName();

}