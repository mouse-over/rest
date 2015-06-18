<?php


namespace MouseOver\Rest\Crud;


interface ICrudAdapter
{
    /**
     * Initialize
     *
     * @param string                             $name   Name
     * @param \MouseOver\Rest\Http\IInput        $input  Input data
     * @param \MouseOver\Rest\Resource\IResource $output Output
     *
     * @return void
     */
    public function initialize($name, $input, $output);
}