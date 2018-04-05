<?php
namespace MouseOver\Rest\Resource;

use ArrayAccess;
use Serializable;
use ArrayIterator;
use IteratorAggregate;
use Nette\Utils\Json;
use Nette\MemberAccessException;

/**
 * REST resource
 *
 * @package Drahak\Restful
 * @author  Drahomír Hanák
 *
 * @property string     $contentType Allowed result content type
 * @property-read array $data
 */
class Resource implements ArrayAccess, Serializable, IteratorAggregate, IResource
{

    use \Nette\SmartObject  {
        __set as protected __smartSet;
        __get as protected __smartGet;
        __unset as protected __smartUnset;
        __isset as protected __smartIsset;
    }

    /** @var array */
    private $data = array();

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * Serialize result set
     *
     * @return string
     */
    public function serialize()
    {
        return Json::encode($this->data);
    }

    /******************** Serializable ********************/

    /**
     * Unserialize Resource
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->data = Json::decode($serialized);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /******************** ArrayAccess interface ********************/

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if ($offset === NULL) {
            $offset = count($this->data);
        }
        $this->data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Get resource data iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getData());
    }

    /******************** Iterator aggregate interface ********************/

    /**
     * Get result set data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /******************** Magic methods ********************/

    /**
     * Magic getter from $this->data
     *
     * @param string $name
     *
     * @throws \Exception|\Nette\MemberAccessException
     * @return mixed
     */
    public function &__get($name)
    {
        try {
            return $this->__smartGet($name);
        } catch (MemberAccessException $e) {
            if (isset($this->data[$name])) {
                return $this->data[$name];
            }
            throw $e;
        }

    }

    /**
     * Magic setter to $this->data
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        try {
           $this->__smartSet($name, $value);
        } catch (MemberAccessException $e) {
            $this->data[$name] = $value;
        }
    }

    /**
     * Magic isset to $this->data
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return !$this->__smartIsset($name) ? isset($this->data[$name]) : TRUE;
    }

    /**
     * Magic unset from $this->data
     *
     * @param string $name
     *
     * @throws \Exception|\Nette\MemberAccessException
     */
    public function __unset($name)
    {
        try {
            $this->__smartUnset($name);
        } catch (MemberAccessException $e) {
            if (isset($this->data[$name])) {
                unset($this->data[$name]);
                return;
            }
            throw $e;
        }
    }


}
