<?php

namespace Asylamba\Classes\Library\Http;

use Asylamba\Classes\Library\ParameterBag;

class Response
{
    protected string $protocol;
    protected int $statusCode = self::STATUS_OK;
    protected string $title;
    protected string $page;
    protected Request $request;
    protected ?string $redirect = null;
    protected array $templates = [];
    protected string $body;
    public ParameterBag $headers;
    /** @var array<int, string> **/
    protected array $statuses = [
        self::STATUS_OK => 'OK',
		self::STATUS_REDIRECT => 'Found',
		self::STATUS_NOT_FOUND => 'Not Found',
		self::STATUS_INTERNAL_SERVER_ERROR => 'Internal Server Error'
    ];
	
	const STATUS_OK = 200;
	const STATUS_REDIRECT = 302;
	const STATUS_NOT_FOUND = 404;
	const STATUS_INTERNAL_SERVER_ERROR = 500;

    public function __construct()
	{
        $this->headers = new ParameterBag();
    }

    public function setProtocol(string $protocol): void
    {
        $this->protocol = $protocol;
    }
    
    public function getProtocol(): string
    {
        return $this->protocol;
    }
    
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setPage(string $page): void
    {
        $this->page = $page;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function redirect(string $path): void
	{
        $this->redirect = $path;
		$this->statusCode = self::STATUS_REDIRECT;
    }

    public function getRedirect(): ?string
    {
        return $this->redirect;
    }

    public function addTemplate(string $template): void
    {
        $this->templates[] = $template;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getBody(): string
    {
        return $this->body ?? '';
    }

    public function getStatus(): string
    {
        return $this->statuses[$this->statusCode];
    }
	
	public function send(): string
	{
		$this->headers->set('Content-Length', strlen($this->getBody()));
		$message = "{$this->protocol} {$this->statusCode} {$this->statuses[$this->statusCode]}\n";
		
		foreach ($this->headers->all() as $header => $value) {
			$message .= "$header: $value\n";
		}
		$message .= "\n";
		$message .= $this->getBody();
		return $message;
	}
}
