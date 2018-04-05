<?php
namespace MouseOver\Rest\Resource;

/**
 * IResource determines REST service result set
 * @package Drahak\Restful
 * @author Drahomír Hanák
 */
interface IResource 
{

	/** Result types */
	const XML = 'application/xml';
	const JSON = 'application/json';
	const JSONP = 'application/javascript';
	const QUERY = 'application/x-www-form-urlencoded';
	const DATA_URL = 'application/x-data-url';
	const FILE = 'application/octet-stream';
	const FORM = 'multipart/form-data';
	const NULL = 'NULL';

	/**
	 * Get element value or array data
	 * @return array|\Traversable
	 */
	public function getData();

}
