<?php
namespace MouseOver\Rest\Http;


interface IInput
{

    /**
     * Return's input parameter
     *
     * @param string     $name    Parameter name
     * @param mixed|null $default Optional default value
     *
     * @return mixed
     */
    public function getParameter($name, $default = null);

    /**
     * Get parsed input data
     *
     * @return array
     */
    public function getData();

}