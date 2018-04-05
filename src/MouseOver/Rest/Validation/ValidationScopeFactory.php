<?php
namespace MouseOver\Rest\Validation;


/**
 * ValidationScopeFactory
 * @package MouseOver\Rest\Validation
 * @author  Drahomír Hanák
 */
class ValidationScopeFactory implements IValidationScopeFactory
{
    use \Nette\SmartObject;

    /** @var IValidator */
    private $validator;

    /**
     * @param IValidator $validator
     */
    public function __construct(IValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validation schema factory
     * @return \MouseOver\Rest\Validation\IValidationScope
     */
    public function create()
    {
        return new ValidationScope($this->validator);
    }


}
