<?php
namespace MouseOver\Rest\Mapping;

use MouseOver\Rest\Resource\Media;
use Nette\Utils\Strings;
use MouseOver\Rest\InvalidArgumentException;

/**
 * DataUrlMapper - encode or decode base64 file
 * @package Drahak\Restful\Mapping
 * @author Drahomír Hanák
 */
class DataUrlMapper implements IMapper
{
    use \Nette\SmartObject;

	/**
	 * Create DATA URL from file
	 * @param Media $data
	 * @param bool $prettyPrint
	 * @return string
	 *
	 * @throws InvalidArgumentException
	 */
	public function stringify($data, $prettyPrint = TRUE)
	{
		if (!$data instanceof Media) {
			throw new InvalidArgumentException(
				'DataUrlMapper expects object of type Media, ' . (gettype($data)) . ' given'
			);
		}
		return self::dataStream((string)$data, $data->getContentType());
	}

	/**
	 * Convert client request data to array or traversable
	 * @param string $data
	 * @return Media
	 *
	 * @throws MappingException
	 */
	public function parse($data)
	{
		$matches = Strings::match($data, "@^data:([\w/]+?);(\w+?),(.*)$@si");
		if (!$matches) {
			throw new MappingException('Given data URL is invalid.');
		}

		return new Media(base64_decode($matches[3]), $matches[1]);
	}

    /**
     * The data: URI generator.
     * @param  string plain text
     * @param  string
     * @return string plain text
     */
    public static function dataStream($data, $type = null)
    {
        if ($type === null) {
            $type = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $data);
        }
        return 'data:' . ($type ? "$type;" : '') . 'base64,' . base64_encode($data);
    }

}
