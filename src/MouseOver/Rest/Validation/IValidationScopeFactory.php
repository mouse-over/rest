<?php
namespace MouseOver\Rest\Validation;

/**
 * IValidationScopeFactory
 * @package MouseOver\Rest\Validation
 * @author  Drahomír Hanák
 */
interface IValidationScopeFactory
{

    /**
     * Validation schema factory
     * @return \MouseOver\Rest\Validation\IValidationScope
     */
    public function create();

}
