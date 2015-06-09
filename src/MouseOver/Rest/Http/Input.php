<?php
namespace MouseOver\Rest\Http;

use Nette\MemberAccessException;
use Nette\Object;


/**
 * Class Input
 *
 * @package MouseOver\Restful\Http
 */
class Input extends Object implements \IteratorAggregate, IInput
{

    /** @var array */
    private $data;

    /**
     * Constructor
     *
     * @param array $data Input data
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * Get parsed input data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set input data
     *
     * @param array $data Input data
     *
     * @return Input
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Return's input parameter
     *
     * @param string     $name    Parameter name
     * @param mixed|null $default Optional default value
     *
     * @return mixed
     */
    public function getParameter($name, $default = null)
    {
        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }


    /******************** Iterator aggregate interface ********************/

    /**
     * Get input data iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getData());
    }
}