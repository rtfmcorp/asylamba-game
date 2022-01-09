<?php

namespace App\Classes\Library\Http;

use App\Classes\Exception\RequestException;

class RequestFactory
{
    protected string $requestPart;
    
    const REQUEST_PART_PROTOCOL = 'protocol';
    const REQUEST_PART_HEADERS = 'headers';
    const REQUEST_PART_BODY = 'body';

    public function createRequestFromInput(string $input): Request
    {
        $rows = explode("\n", $input);
        $request = new Request();
        
        $checkForEnding = false;
        
        $this->requestPart = self::REQUEST_PART_PROTOCOL;
        foreach ($rows as $row) {
            $row = trim($row);
            if ($this->isStreamEnd($row, $checkForEnding)) {
                break;
            }
            switch ($this->requestPart) {
                case self::REQUEST_PART_PROTOCOL:
                    $this->parseProtocol($request, $row);
                    break;
                case self::REQUEST_PART_HEADERS:
                    $this->parseHeader($request, $row);
                    break;
                case self::REQUEST_PART_BODY:
                    $request->body .= $row;
                    break;
            }
        }
        $this->parseCookies($request);
        $this->parseBody($request);
        return $request;
    }
    
    /**
     * This method checks if a stream pipe has read all of the current content
     * The rule is, if two empty lines are following one another, we consider the stream as fully read
     * 
     * @param string $row
     * @param boolean $checkForEnding
     * @return boolean
     */
    protected function isStreamEnd($row, &$checkForEnding)
    {
        $isEmpty = empty($row);
        if ($isEmpty && $checkForEnding === true) {
            return true;
        }
        // If the row is empty, we set the check for the next row
        // If not, we disable the check
        $checkForEnding = $isEmpty;
        return false;
    }
    
    /**
     * @param Request $request
     * @param string $row
     */
    protected function parseProtocol(Request $request, $row)
    {
        if (empty($row)) {
            throw new RequestException('Invalid request format');
        }
        $data = explode(' ', $row);
        $request->setMethod($data[0]);
        
        $pathData = explode('?', $data[1]);
        $request->setPath($pathData[0]);
        $request->setProtocol($data[2]);
        
        if (isset($pathData[1])) {
            $this->parseQueryParameters($request, $pathData[1]);
        }
        $this->requestPart = self::REQUEST_PART_HEADERS;
    }
    
    /**
     * @param Request $request
     * @param string $query
     */
    protected function parseQueryParameters(Request $request, $query)
    {
        $parameters = explode('&', $query);
        
        foreach($parameters as $parameter) {
            $data = explode('=', $parameter);
            
            $request->query->set($data[0], urldecode($data[1]));
        }
    }
    
    /**
     * @param Request $request
     * @param string $row
     */
    protected function parseHeader(Request $request, $row)
    {
        if (empty($row)) {
            $this->requestPart = self::REQUEST_PART_BODY;
            return;
        }
        $data = explode(': ', $row);
        $request->headers->set(strtolower($data[0]), $data[1]);
    }
    
    /**
     * @param Request $request
     * @param string $row
     */
    protected function parseBody(Request $request)
    {
        if (empty($request->body) || !$request->headers->has('content-type')) {
            return;
        }
        switch ($request->headers->get('content-type')) {
			case 'application/json':
				$this->processJsonBody($request);
				break;
			case 'application/x-www-form-urlencoded':
				$this->processFormBody($request);
				break;
        }
    }
    
    protected function processJsonBody(Request $request)
    {
        $data = json_decode($request->body);
        
        foreach($data as $key => $element) {
            $request->request->set($key, $element);
        }
    }
	
	protected function processFormBody(Request $request)
	{
		$data = explode('&', $request->body);
		
		foreach($data as $parameter) {
			$parsedParameter = explode('=', $parameter);
			$request->request->set(htmlspecialchars($parsedParameter[0]), htmlspecialchars(urldecode($parsedParameter[1])));
		}
	}
    
    /**
     * @param Request $request
     */
    protected function parseCookies(Request $request)
    {
        if (!$request->headers->has('cookie')) {
            return;
        }
        $cookies = explode(';', $request->headers->get('cookie'));
        foreach ($cookies as $cookie) {
            $data = explode('=', $cookie);
            $request->cookies->add(trim($data[0]), urldecode($data[1]));
        }
    }
}
