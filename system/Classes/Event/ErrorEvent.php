<?php

namespace Asylamba\Classes\Event;

use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;

class ErrorEvent
{
    /** @var \Error **/
    protected $error;
    /** @var Request **/
    protected $request;
    /** @var Response **/
    protected $response;
    
    const NAME = 'core.error';
    
    /**
     * @param Request $request
     * @param \Error $error
     */
    public function __construct(Request $request, \Error $error)
    {
        $this->request = $request;
        $this->error = $error;
    }
    
    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @return \Error
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * @param Response $response
     * @return \Asylamba\Classes\Event\ErrorEvent
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        
        return $this;
    }
    
    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
