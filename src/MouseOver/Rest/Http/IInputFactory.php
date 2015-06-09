<?php
namespace MouseOver\Rest\Http;


/**
 * Interface IInputFactory
 *
 * @package MouseOver\Restful
 */
interface IInputFactory
{

    /**
     * Create input
     *
     * @return IInput
     */
    public function create();
}