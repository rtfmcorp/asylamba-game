<?php

namespace Asylamba\Classes\Library\Http;

use Asylamba\Classes\Library\ParameterBag;

class Response
{
    /** @var string **/
    protected $protocol;
    /** @var int **/
    protected $statusCode = self::STATUS_OK;
    /** @var string **/
    protected $title;
    /** @var string **/
    protected $page;
    /** @var Request **/
    protected $request;
    /** @var string **/
    protected $redirect;
    /** @var array **/
    protected $templates = [];
    /** @var string **/
    protected $body;
    /** @var ParameterBag **/
    public $headers;
    /** @var array **/
    protected $statuses = [
        200 => 'OK',
		302 => 'Found',
        400 => 'Bad Request',
		404 => 'Not Found',
		500 => 'Internal Server Error'
    ];
	
	const STATUS_OK = 200;
	const STATUS_REDIRECT = 302;
	const STATUS_NOT_FOUND = 404;
	const STATUS_INTERNAL_SERVER_ERROR = 500;

    public function __construct() {
        $this->headers = new ParameterBag();
    }
    
    /**
     * @param string $protocol
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }
    
    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
    
    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }
    
    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $path
     */
    public function redirect($path) {
        $this->redirect = $path;
		$this->statusCode = self::STATUS_REDIRECT;
    }

    /**
     * @return string
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @param string $template
     */
    public function addTemplate($template)
    {
        $this->templates[] = $template;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }
    
    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
    
    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->statuses[$this->statusCode];
    }
	
	public function send()
	{
        $this->headers->set('Content-Length', strlen($this->body));
		$message = "{$this->protocol} {$this->statusCode} {$this->statuses[$this->statusCode]}\n";
		
		foreach ($this->headers->all() as $header => $value) {
			$message .= "$header: $value\n";
		}
		$message .= "\n";
		$message .= $this->body;
		return $message;
	}
}