<?php
namespace MouseOver\Rest\Resource;

/**
 * Media resource representation object
 * @package Drahak\Restful\Resource
 * @author Drahomír Hanák
 *
 * @property-read string $content
 * @property-read string $contentType
 */
class Media implements \MouseOver\Rest\Resource\IResource
{
    use \Nette\SmartObject;

	/** @var string */
	private $content;

	/** @var string|NULL */
	private $contentType;
	
	/** @var string */
	private $name;
	
	/** @var boolean */
	private $forceDownload;

	/**
	 * @param string $content
	 * @param string|NULL $contentType
     * @param string|NULL $name
     * @param boolean $forceDownload                        
	 */
	public function __construct($content, $contentType = NULL, $name = NULL, $forceDownload = false)
	{
		$this->content = $content;
		$this->contentType = $contentType ? $contentType : MimeTypeDetector::fromString($content);
		$this->name = $name;
		$this->forceDownload = $forceDownload;
	}

    /**
     * @return bool
     */
    public function isForceDownload()
    {
        return $this->forceDownload;
    }

    /**
     * @param bool $forceDownload
     */
    public function setForceDownload($forceDownload)
    {
        $this->forceDownload = $forceDownload;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

	/**
	 * Get media mime type
	 * @return NULL|string
	 */
	public function getContentType()
	{
		return $this->contentType;
	}

	/**
	 * Get file
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * Converts media to string
	 * @return string
	 */
	public function __toString()
	{
		return $this->getContent();
	}

	/******************** Media simple factory ********************/

	/**
	 * Create media from file
	 * @param string $filePath
	 * @param string|NULL $mimeType
	 * @return Media
	 */
	public static function fromFile($filePath, $mimeType = NULL)
	{
		if (!$mimeType) {
            if (!is_file($file)) {
                throw new Nette\FileNotFoundException("File '$file' not found.");
            }
            $type = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file);
            $mimeType = strpos($type, '/') ? $type : 'application/octet-stream';
		}
		return new Media(file_get_contents($filePath), $mimeType);
	}

    /**
     * Get element value or array data
     * @return array|\Traversable
     */
    public function getData()
    {
        return $this;
    }
}
