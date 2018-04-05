<?php
namespace MouseOver\Rest\Validation;

use Nette\Forms\Form;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

/**
 * Validation Rule caret
 * @package MouseOver\Rest\Validation
 * @author  Drahomír Hanák
 *
 * @property string $field name
 * @property string $message
 * @property int    $code
 * @property string $expression
 * @property array  $argument
 */
class Rule
{

    use \Nette\SmartObject;

    /** @var string */
    protected $field;

    /** @var string */
    protected $message;

    /** @var int */
    protected $code;

    /** @var string */
    protected $expression;

    /** @var array */
    protected $argument;


    /******************** Getters & setters ********************/

    /**
     * Set rule error code
     *
     * @param int $code
     *
     * @return Rule
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get rule error code
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set field name
     *
     * @param string $field
     *
     * @return Rule
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Get field name
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set rule error message
     *
     * @param string $message
     *
     * @return Rule
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get rule error message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set rule expression
     *
     * @param string $expression
     *
     * @return Rule
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * Get rule expression
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * Set rule argument(s)
     *
     * @param array $argument
     *
     * @return Rule
     */
    public function setArgument($argument)
    {
        $this->argument = (array)$argument;
        return $this;
    }

    /**
     * Get rule arguments
     * @return array
     */
    public function getArgument()
    {
        return $this->argument;
    }

}