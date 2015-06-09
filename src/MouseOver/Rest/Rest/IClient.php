<?php

namespace MouseOver\Rest\Client;


interface IClient {

    public function authorize();

    public function get();

    public function put();

    public function post();

    public function delete();

    public function path();
}